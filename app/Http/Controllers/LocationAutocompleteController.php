<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationAutocompleteController extends Controller
{
    private const AUTOCOMPLETE_URL = 'https://places.googleapis.com/v1/places:autocomplete';

    private const DETAILS_URL = 'https://places.googleapis.com/v1/places/';

    private const AUTOCOMPLETE_FIELD_MASK = 'suggestions.placePrediction.placeId,suggestions.placePrediction.text.text,suggestions.placePrediction.structuredFormat.mainText.text,suggestions.placePrediction.structuredFormat.secondaryText.text';

    private const DETAILS_FIELD_MASK = 'id,formattedAddress,addressComponents,location';

    public function suggestions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'input' => ['required', 'string', 'min:3', 'max:200'],
            'mode' => ['nullable', 'in:address,search'],
            'session_token' => ['nullable', 'uuid'],
        ]);

        $mode = $validated['mode'] ?? 'search';
        $sessionToken = $validated['session_token'] ?? null;

        if ($mode === 'address' && ! $sessionToken) {
            return response()->json([
                'message' => 'Start a new address search and try again.',
            ], 422)->header('Cache-Control', 'no-store');
        }

        $key = config('services.google_maps.key');
        if (! $key) {
            return $this->unavailable('Google Places is not configured.', 503);
        }

        $payload = [
            'input' => $validated['input'],
            'languageCode' => 'en',
            'regionCode' => 'us',
        ];

        if ($mode === 'address') {
            $payload['includedRegionCodes'] = ['us'];
            $payload['includedPrimaryTypes'] = ['street_address', 'premise', 'subpremise'];
            $payload['sessionToken'] = $sessionToken;
        }

        try {
            $response = Http::acceptJson()
                ->withHeaders([
                    'X-Goog-Api-Key' => $key,
                    'X-Goog-FieldMask' => self::AUTOCOMPLETE_FIELD_MASK,
                ])
                ->timeout(8)
                ->post(self::AUTOCOMPLETE_URL, $payload);
        } catch (ConnectionException $exception) {
            Log::warning('Google Places autocomplete connection failed.', [
                'exception' => $exception::class,
            ]);

            return $this->unavailable();
        }

        if (! $response->successful()) {
            Log::warning(
                'Google Places autocomplete request failed.',
                $this->providerErrorContext($response, $key)
            );

            return $this->unavailable();
        }

        $suggestions = collect($response->json('suggestions', []))
            ->map(static function (array $suggestion): ?array {
                $prediction = $suggestion['placePrediction'] ?? null;
                if (! $prediction || empty($prediction['placeId'])) {
                    return null;
                }

                $mainText = $prediction['structuredFormat']['mainText']['text']
                    ?? $prediction['text']['text']
                    ?? '';
                $secondaryText = $prediction['structuredFormat']['secondaryText']['text']
                    ?? '';

                return [
                    'placeId' => $prediction['placeId'],
                    'mainText' => $mainText,
                    'secondaryText' => $secondaryText,
                    'text' => $prediction['text']['text']
                        ?? implode(', ', array_filter([$mainText, $secondaryText])),
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'suggestions' => $suggestions,
        ])->header('Cache-Control', 'no-store');
    }

    public function details(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'place_id' => ['required', 'string', 'max:255'],
            'session_token' => ['required', 'uuid'],
        ]);

        $key = config('services.google_maps.key');
        if (! $key) {
            return $this->unavailable('Google Places is not configured.', 503);
        }

        try {
            $response = Http::acceptJson()
                ->withHeaders([
                    'X-Goog-Api-Key' => $key,
                    'X-Goog-FieldMask' => self::DETAILS_FIELD_MASK,
                ])
                ->timeout(8)
                ->get(self::DETAILS_URL.rawurlencode($validated['place_id']), [
                    'sessionToken' => $validated['session_token'],
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('Google Places details connection failed.', [
                'exception' => $exception::class,
            ]);

            return $this->unavailable();
        }

        if (! $response->successful()) {
            Log::warning(
                'Google Places details request failed.',
                $this->providerErrorContext($response, $key)
            );

            return $this->unavailable();
        }

        $place = $response->json();
        $components = collect($place['addressComponents'] ?? []);
        $component = static fn (array $types, string $field = 'longText'): string => (string) (
            $components->first(
                static fn (array $item): bool => count(array_intersect($types, $item['types'] ?? [])) > 0
            )[$field] ?? ''
        );
        $streetNumber = $component(['street_number']);
        $street = $component(['route']);

        return response()->json([
            'placeId' => $place['id'] ?? $validated['place_id'],
            'formattedAddress' => $place['formattedAddress'] ?? '',
            'addressLine1' => trim(implode(' ', array_filter([$streetNumber, $street]))),
            'city' => $component(['locality'])
                ?: $component(['postal_town'])
                ?: $component(['sublocality_level_1']),
            'state' => $component(['administrative_area_level_1'], 'shortText'),
            'zipCode' => $component(['postal_code']),
            'county' => $component(['administrative_area_level_2']),
            'country' => $component(['country']),
            'latitude' => $place['location']['latitude'] ?? null,
            'longitude' => $place['location']['longitude'] ?? null,
        ])->header('Cache-Control', 'no-store');
    }

    private function unavailable(
        string $message = 'Location suggestions are temporarily unavailable.',
        int $status = 502
    ): JsonResponse {
        return response()->json([
            'message' => $message,
        ], $status)->header('Cache-Control', 'no-store');
    }

    private function providerErrorContext(
        Response $response,
        string $key
    ): array {
        $message = $response->json('error.message');
        if (is_string($message)) {
            $message = str_replace($key, '[redacted]', $message);
        }

        $reasons = collect($response->json('error.details', []))
            ->pluck('reason')
            ->filter()
            ->values()
            ->all();

        return [
            'status' => $response->status(),
            'provider_status' => $response->json('error.status'),
            'provider_reasons' => $reasons,
            'provider_message' => $message,
        ];
    }
}
