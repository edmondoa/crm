<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LocationAutocompleteTest extends TestCase
{
    private const SESSION_TOKEN = '3519edfe-0f75-4a30-bfe4-7cbd89340b2c';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.google_maps.key' => 'test-google-maps-key']);
    }

    public function test_address_suggestions_use_google_places_and_session_tokens(): void
    {
        Http::fake([
            'places.googleapis.com/v1/places:autocomplete' => Http::response([
                'suggestions' => [
                    [
                        'placePrediction' => [
                            'placeId' => 'ChIJtest123',
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

        $response = $this->getJson(route('crm.location-suggestions', [
            'input' => '123 Main',
            'mode' => 'address',
            'session_token' => self::SESSION_TOKEN,
        ]))
            ->assertOk()
            ->assertJsonPath('suggestions.0.placeId', 'ChIJtest123')
            ->assertJsonPath('suggestions.0.mainText', '123 Main Street')
            ->assertJsonPath('suggestions.0.secondaryText', 'Austin, TX, USA')
            ->assertJsonPath('suggestions.0.text', '123 Main Street, Austin, TX, USA');

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        Http::assertSent(fn ($request): bool => str_contains(
            $request->url(),
            'places.googleapis.com/v1/places:autocomplete'
        )
            && $request->data()['input'] === '123 Main'
            && $request->data()['sessionToken'] === self::SESSION_TOKEN
            && $request->data()['includedRegionCodes'] === ['us']
            && $request->hasHeader('X-Goog-Api-Key', 'test-google-maps-key'));
    }

    public function test_search_suggestions_do_not_open_a_place_details_session(): void
    {
        Http::fake([
            'places.googleapis.com/v1/places:autocomplete' => Http::response([
                'suggestions' => [],
            ]),
        ]);

        $this->getJson(route('crm.location-suggestions', [
            'input' => 'Austin, Texas',
            'mode' => 'search',
        ]))->assertOk()->assertJsonPath('suggestions', []);

        Http::assertSent(fn ($request): bool => ! array_key_exists(
            'sessionToken',
            $request->data()
        ) && ! array_key_exists('includedPrimaryTypes', $request->data()));
    }

    public function test_place_details_fill_address_fields_from_google_components(): void
    {
        Http::fake([
            'places.googleapis.com/v1/places/ChIJtest123*' => Http::response([
                'id' => 'ChIJtest123',
                'formattedAddress' => '123 Main Street, Austin, TX 78701, USA',
                'addressComponents' => [
                    [
                        'longText' => '123',
                        'shortText' => '123',
                        'types' => ['street_number'],
                    ],
                    [
                        'longText' => 'Main Street',
                        'shortText' => 'Main St',
                        'types' => ['route'],
                    ],
                    [
                        'longText' => 'Austin',
                        'shortText' => 'Austin',
                        'types' => ['locality'],
                    ],
                    [
                        'longText' => 'Texas',
                        'shortText' => 'TX',
                        'types' => ['administrative_area_level_1'],
                    ],
                    [
                        'longText' => 'Travis County',
                        'shortText' => 'Travis County',
                        'types' => ['administrative_area_level_2'],
                    ],
                    [
                        'longText' => '78701',
                        'shortText' => '78701',
                        'types' => ['postal_code'],
                    ],
                    [
                        'longText' => 'United States',
                        'shortText' => 'US',
                        'types' => ['country'],
                    ],
                ],
                'location' => [
                    'latitude' => 30.2672,
                    'longitude' => -97.7431,
                ],
            ]),
        ]);

        $csrfToken = str_repeat('a', 40);
        $response = $this->withSession(['_token' => $csrfToken])
            ->withHeader('X-CSRF-TOKEN', $csrfToken)
            ->postJson(route('crm.location-details'), [
                'place_id' => 'ChIJtest123',
                'session_token' => self::SESSION_TOKEN,
            ])
            ->assertOk()
            ->assertJsonPath('placeId', 'ChIJtest123')
            ->assertJsonPath('formattedAddress', '123 Main Street, Austin, TX 78701, USA')
            ->assertJsonPath('addressLine1', '123 Main Street')
            ->assertJsonPath('city', 'Austin')
            ->assertJsonPath('state', 'TX')
            ->assertJsonPath('zipCode', '78701')
            ->assertJsonPath('county', 'Travis County')
            ->assertJsonPath('country', 'United States')
            ->assertJsonPath('latitude', 30.2672)
            ->assertJsonPath('longitude', -97.7431);

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        Http::assertSent(fn ($request): bool => str_contains(
            $request->url(),
            'places.googleapis.com/v1/places/ChIJtest123'
        )
            && str_contains($request->url(), 'sessionToken='.self::SESSION_TOKEN)
            && $request->hasHeader('X-Goog-Api-Key', 'test-google-maps-key')
            && $request->hasHeader('X-Goog-FieldMask', 'id,formattedAddress,addressComponents,location'));
    }

    public function test_location_suggestions_require_a_search_term_and_address_session_token(): void
    {
        $this->getJson(route('crm.location-suggestions', [
            'input' => 'ab',
            'mode' => 'address',
        ]))->assertUnprocessable();

        $this->getJson(route('crm.location-suggestions', [
            'input' => 'Austin, TX',
            'mode' => 'address',
        ]))->assertUnprocessable();

        Http::assertNothingSent();
    }

    public function test_missing_google_key_returns_a_clear_configuration_error(): void
    {
        config(['services.google_maps.key' => null]);

        $this->getJson(route('crm.location-suggestions', [
            'input' => 'Austin, TX',
        ]))
            ->assertStatus(503)
            ->assertJsonPath('message', 'Google Places is not configured.');

        Http::assertNothingSent();
    }

    public function test_provider_errors_are_not_cached_or_exposed_to_the_browser(): void
    {
        Http::fake([
            'places.googleapis.com/v1/places:autocomplete' => Http::response([
                'error' => ['message' => 'Internal provider error details'],
            ], 503),
        ]);

        $response = $this->getJson(route('crm.location-suggestions', [
            'input' => 'Austin, TX',
        ]))
            ->assertStatus(502)
            ->assertJsonPath('message', 'Location suggestions are temporarily unavailable.')
            ->assertDontSee('Internal provider error details');

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }
}
