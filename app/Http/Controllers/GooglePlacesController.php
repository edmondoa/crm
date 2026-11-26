<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GooglePlacesController extends Controller
{
    private const AUTOCOMPLETE_URL = 'https://places.googleapis.com/v1/places:autocomplete';

    private const PLACE_DETAILS_URL = 'https://places.googleapis.com/v1/places/';

    public function autocomplete(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'input' => ['required', 'string', 'min:3', 'max:200'],
            'sessionToken' => ['required', 'uuid'],
        ]);

        $apiKey = config('services.google_maps.key');

        if (! is_string($apiKey) || $apiKey === '') {
            return response()->json([
                'message' => 'Google Places is not configured yet.',
            ], 503)->header('Cache-Control', 'no-store');
        }

        try {
            $response = Http::acceptJson()
                ->withHeaders([
                    'X-Goog-Api-Key' => $apiKey,
                    'X-Goog-FieldMask' => implode(',', [
                        'suggestions.placePrediction.placeId',
                        'suggestions.placePrediction.text.text',
                        'suggestions.placePrediction.structuredFormat.mainText.text',
                        'suggestions.placePrediction.structuredFormat.secondaryText.text',
                    ]),
                ])
                ->timeout(8)
                ->post(self::AUTOCOMPLETE_URL, [
                    'input' => $validated['input'],
                    'sessionToken' => $validated['sessionToken'],
                    'languageCode' => 'en',
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('Google Places autocomplete connection failed.', [
                'exception' => $exception::class,
            ]);

            return response()->json([
                'message' => 'Google Places could not return suggestions.',
            ], 502)->header('Cache-Control', 'no-store');
        }

        if (! $response->successful()) {
            Log::warning('Google Places autocomplete request failed.', [
                'status' => $response->status(),
            ]);

            return response()->json([
                'message' => 'Google Places could not return suggestions.',
            ], 502)->header('Cache-Control', 'no-store');
        }

        $suggestions = collect($response->json('suggestions', []))
            ->map(static function (array $suggestion): ?array {
                $place = $suggestion['placePrediction'] ?? null;

                if (! is_array($place) || empty($place['placeId'])) {
                    return null;
                }

                return [
                    'placeId' => $place['placeId'],
                    'mainText' => $place['structuredFormat']['mainText']['text']
                        ?? $place['text']['text']
                        ?? '',
                    'secondaryText' => $place['structuredFormat']['secondaryText']['text']
                        ?? '',
                    'text' => $place['text']['text'] ?? '',
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
            'placeId' => ['required', 'string', 'max:255'],
            'sessionToken' => ['required', 'uuid'],
        ]);

        $apiKey = config('services.google_maps.key');

        if (! is_string($apiKey) || $apiKey === '') {
            return response()->json([
                'message' => 'Google Places is not configured yet.',
            ], 503)->header('Cache-Control', 'no-store');
        }

        try {
            $response = Http::acceptJson()
                ->withHeaders([
                    'X-Goog-Api-Key' => $apiKey,
                    'X-Goog-FieldMask' => 'formattedAddress,addressComponents,location',
                ])
                ->timeout(8)
                ->get(
                    self::PLACE_DETAILS_URL.rawurlencode($validated['placeId']),
                    [
                        'sessionToken' => $validated['sessionToken'],
                        'languageCode' => 'en',
                    ]
                );
        } catch (ConnectionException $exception) {
            Log::warning('Google Places details connection failed.', [
                'exception' => $exception::class,
            ]);

            return response()->json([
                'message' => 'Google Places could not load this address.',
            ], 502)->header('Cache-Control', 'no-store');
        }

        if (! $response->successful()) {
            Log::warning('Google Places details request failed.', [
                'status' => $response->status(),
            ]);

            return response()->json([
                'message' => 'Google Places could not load this address.',
            ], 502)->header('Cache-Control', 'no-store');
        }

        $place = $response->json();
        $components = collect($place['addressComponents'] ?? []);
        $component = static function (array $types) use ($components): ?array {
            return $components->first(
                static fn (array $item): bool => count(
                    array_intersect($types, $item['types'] ?? [])
                ) > 0
            );
        };

        $streetNumber = $component(['street_number'])['longText'] ?? '';
        $streetName = $component(['route'])['longText'] ?? '';
        $streetAddress = trim($streetNumber.' '.$streetName);

        return response()->json([
            'addressLine1' => $streetAddress !== ''
                ? $streetAddress
                : ($place['formattedAddress'] ?? ''),
            'city' => $component(['locality', 'postal_town', 'sublocality_level_1'])['longText'] ?? '',
            'state' => $component(['administrative_area_level_1'])['shortText'] ?? '',
            'zipCode' => $component(['postal_code'])['longText'] ?? '',
            'country' => $component(['country'])['longText'] ?? '',
            'latitude' => $place['location']['latitude'] ?? null,
            'longitude' => $place['location']['longitude'] ?? null,
        ])->header('Cache-Control', 'no-store');
    }
}
