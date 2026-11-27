<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LocationAutocompleteTest extends TestCase
{
    public function test_location_suggestions_are_normalized_for_the_autocomplete_ui(): void
    {
        Http::fake([
            'photon.komoot.io/api/*' => Http::response([
                'features' => [
                    [
                        'type' => 'Feature',
                        'properties' => [
                            'name' => '123 Main Street',
                            'housenumber' => '123',
                            'street' => 'Main Street',
                            'city' => 'Austin',
                            'state' => 'Texas',
                            'county' => 'Travis County',
                            'postcode' => '78701',
                            'country' => 'United States',
                            'osm_id' => 123,
                            'osm_type' => 'W',
                        ],
                        'geometry' => [
                            'type' => 'Point',
                            'coordinates' => [-97.7431, 30.2672],
                        ],
                    ],
                ],
            ]),
        ]);

        $response = $this->getJson(route('crm.location-suggestions', [
            'input' => '123 Main',
        ]))
            ->assertOk()
            ->assertJsonPath('suggestions.0.mainText', '123 Main Street')
            ->assertJsonPath('suggestions.0.secondaryText', 'Austin, Texas, United States')
            ->assertJsonPath('suggestions.0.addressLine1', '123 Main Street')
            ->assertJsonPath('suggestions.0.city', 'Austin')
            ->assertJsonPath('suggestions.0.state', 'Texas')
            ->assertJsonPath('suggestions.0.zipCode', '78701')
            ->assertJsonPath('suggestions.0.county', 'Travis County')
            ->assertJsonPath('suggestions.0.country', 'United States')
            ->assertJsonPath('suggestions.0.latitude', 30.2672)
            ->assertJsonPath('suggestions.0.longitude', -97.7431);

        $this->assertStringContainsString(
            'no-store',
            $response->headers->get('Cache-Control')
        );
        Http::assertSent(fn ($request): bool => str_contains(
            $request->url(),
            'photon.komoot.io/api/'
        ) && $request['q'] === '123 Main' && $request['limit'] === 5);
    }

    public function test_location_suggestions_require_a_search_term_of_at_least_three_characters(): void
    {
        $this->getJson(route('crm.location-suggestions', [
            'input' => 'ab',
        ]))->assertUnprocessable();

        Http::assertNothingSent();
    }

    public function test_location_suggestions_report_provider_errors_without_caching_them(): void
    {
        Http::fake([
            'photon.komoot.io/api/*' => Http::response([], 503),
        ]);

        $response = $this->getJson(route('crm.location-suggestions', [
            'input' => 'Austin, TX',
        ]))
            ->assertStatus(502)
            ->assertJsonPath('message', 'Location suggestions are temporarily unavailable.');

        $this->assertStringContainsString(
            'no-store',
            $response->headers->get('Cache-Control')
        );
    }
}
