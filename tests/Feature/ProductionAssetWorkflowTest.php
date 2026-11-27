<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionAssetWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_and_invoice_pages_reference_assets_from_the_production_build(): void
    {
        $manifestPath = public_path('build/manifest.json');

        $this->assertFileExists(
            $manifestPath,
            'Run npm run build before testing production asset references.'
        );

        $manifest = json_decode(
            file_get_contents($manifestPath),
            true,
            flags: JSON_THROW_ON_ERROR
        );

        $entries = [
            'resources/css/app.css',
            'resources/js/app.js',
            'resources/css/invoices.css',
            'resources/js/invoices.js',
        ];

        foreach ($entries as $entry) {
            $this->assertArrayHasKey($entry, $manifest);
            $this->assertFileExists(public_path('build/'.$manifest[$entry]['file']));
        }

        $pages = [
            route('crm.dashboard') => [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            route('crm.invoices.index') => [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/invoices.css',
            ],
            route('crm.invoices.create') => [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/invoices.css',
                'resources/js/invoices.js',
            ],
        ];

        foreach ($pages as $url => $requiredEntries) {
            $response = $this->get($url)->assertOk();

            foreach ($requiredEntries as $entry) {
                $response->assertSee('/build/'.$manifest[$entry]['file'], false);
            }
        }
    }
}
