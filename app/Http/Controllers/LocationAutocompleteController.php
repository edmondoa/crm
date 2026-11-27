<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationAutocompleteController extends Controller
{
    private const PHOTON_URL = 'https://photon.komoot.io/api/';

    public function suggestions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'input' => ['required', 'string', 'min:3', 'max:200'],
        ]);

        try {
            $response = Http::acceptJson()
                ->withUserAgent('CRM Location Search/1.0')
                ->timeout(8)
                ->get(self::PHOTON_URL, [
                    'q' => $validated['input'],
                    'limit' => 5,
                    'lang' => 'en',
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('Photon location search connection failed.', [
                'exception' => $exception::class,
            ]);

            return response()->json([
                'message' => 'Location suggestions are temporarily unavailable.',
            ], 502)->header('Cache-Control', 'no-store');
        }

        if (! $response->successful()) {
            Log::warning('Photon location search request failed.', [
                'status' => $response->status(),
            ]);

            return response()->json([
                'message' => 'Location suggestions are temporarily unavailable.',
            ], 502)->header('Cache-Control', 'no-store');
        }

        $suggestions = collect($response->json('features', []))
            ->map(static function (array $feature, int $index): array {
                $properties = $feature['properties'] ?? [];
                $coordinates = $feature['geometry']['coordinates'] ?? [];
                $streetAddress = trim(
                    ($properties['housenumber'] ?? '').' '.($properties['street'] ?? '')
                );
                $mainText = $properties['name']
                    ?? ($streetAddress !== '' ? $streetAddress : null)
                    ?? $properties['city']
                    ?? $properties['locality']
                    ?? $properties['state']
                    ?? 'Unnamed location';
                $secondaryText = implode(', ', array_filter([
                    $streetAddress !== '' && $mainText !== $streetAddress ? $streetAddress : null,
                    $properties['city'] ?? $properties['locality'] ?? null,
                    $properties['state'] ?? null,
                    $properties['country'] ?? null,
                ]));

                return [
                    'id' => ($properties['osm_type'] ?? 'location').'-'.($properties['osm_id'] ?? $index),
                    'mainText' => $mainText,
                    'secondaryText' => $secondaryText,
                    'text' => implode(', ', array_filter([$mainText, $secondaryText])),
                    'addressLine1' => $streetAddress !== ''
                        ? $streetAddress
                        : ($properties['name'] ?? ''),
                    'city' => $properties['city'] ?? $properties['locality'] ?? '',
                    'state' => $properties['state'] ?? '',
                    'zipCode' => $properties['postcode'] ?? '',
                    'county' => $properties['county'] ?? '',
                    'country' => $properties['country'] ?? '',
                    'latitude' => $coordinates[1] ?? null,
                    'longitude' => $coordinates[0] ?? null,
                ];
            })
            ->values();

        return response()->json([
            'suggestions' => $suggestions,
        ])->header('Cache-Control', 'no-store');
    }
}
