<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GooglePlacesAutocompleteTest extends TestCase
{
    public function test_autocomplete_returns_google_place_predictions(): void
    {
        Config::set('services.google_maps.key', 'test-google-key');

        Http::fake([
            'places.googleapis.com/v1/places:autocomplete' => Http::response([
                'suggestions' => [
                    [
                        'placePrediction' => [
                            'placeId' => 'ChIJ123',
                            'text' => ['text' => '123 Main Street, Austin, TX, USA'],
                            'structuredFormat' => [
                                'mainText' => ['text' => '123 Main Street'],
                                'secondaryText' => ['text' => 'Austin, TX, USA'],
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $response = $this->getJson(route('crm.google-places.autocomplete', [
            'input' => '123 Main',
            'sessionToken' => '123e4567-e89b-42d3-a456-426614174000',
        ]))
            ->assertOk()
            ->assertJsonPath('suggestions.0.placeId', 'ChIJ123')
            ->assertJsonPath('suggestions.0.mainText', '123 Main Street')
            ->assertJsonPath('suggestions.0.secondaryText', 'Austin, TX, USA');

        $this->assertStringContainsString(
            'no-store',
            $response->headers->get('Cache-Control')
        );

        Http::assertSent(fn ($request): bool => $request->hasHeader(
            'X-Goog-Api-Key',
            'test-google-key'
        ) && $request['input'] === '123 Main');
    }

    public function test_place_details_return_address_fields_for_form_autofill(): void
    {
        Config::set('services.google_maps.key', 'test-google-key');

        Http::fake([
            'places.googleapis.com/v1/places/ChIJ123*' => Http::response([
                'formattedAddress' => '123 Main Street, Austin, TX 78701, USA',
                'addressComponents' => [
                    ['longText' => '123', 'shortText' => '123', 'types' => ['street_number']],
                    ['longText' => 'Main Street', 'shortText' => 'Main St', 'types' => ['route']],
                    ['longText' => 'Austin', 'shortText' => 'Austin', 'types' => ['locality']],
                    ['longText' => 'Texas', 'shortText' => 'TX', 'types' => ['administrative_area_level_1']],
                    ['longText' => '78701', 'shortText' => '78701', 'types' => ['postal_code']],
                    ['longText' => 'United States', 'shortText' => 'US', 'types' => ['country']],
                ],
                'location' => ['latitude' => 30.2672, 'longitude' => -97.7431],
            ]),
        ]);

        $response = $this->getJson(route('crm.google-places.details', [
            'placeId' => 'ChIJ123',
            'sessionToken' => '123e4567-e89b-42d3-a456-426614174000',
        ]))
            ->assertOk()
            ->assertJsonPath('addressLine1', '123 Main Street')
            ->assertJsonPath('city', 'Austin')
            ->assertJsonPath('state', 'TX')
            ->assertJsonPath('zipCode', '78701')
            ->assertJsonPath('country', 'United States')
            ->assertJsonPath('latitude', 30.2672)
            ->assertJsonPath('longitude', -97.7431);

        $this->assertStringContainsString(
            'no-store',
            $response->headers->get('Cache-Control')
        );
    }

    public function test_google_places_requests_require_a_configured_api_key(): void
    {
        Config::set('services.google_maps.key', null);

        $this->getJson(route('crm.google-places.autocomplete', [
            'input' => '123 Main',
            'sessionToken' => '123e4567-e89b-42d3-a456-426614174000',
        ]))
            ->assertStatus(503)
            ->assertJsonPath('message', 'Google Places is not configured yet.');

        Http::assertNothingSent();
    }

    public function test_autocomplete_requires_a_valid_search_term_and_session_token(): void
    {
        Config::set('services.google_maps.key', 'test-google-key');

        $this->getJson(route('crm.google-places.autocomplete', [
            'input' => 'ab',
            'sessionToken' => 'invalid',
        ]))->assertUnprocessable();

        Http::assertNothingSent();
    }
}
