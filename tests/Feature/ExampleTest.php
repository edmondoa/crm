<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('crm.dashboard'));
    }

    public function test_the_crm_dashboard_loads_on_a_fresh_database(): void
    {
        $this->get(route('crm.dashboard'))
            ->assertOk()
            ->assertSee('CRM Dashboard');
    }

    public function test_https_proxy_requests_keep_redirects_secure(): void
    {
        $this->withHeaders([
            'X-Forwarded-Proto' => 'https',
            'X-Forwarded-Port' => '443',
        ])->get('/')->assertRedirect('https://localhost/crm');
    }

    public function test_background_queue_and_crm_jobs_use_separate_tables(): void
    {
        $this->assertTrue(Schema::hasColumns('queue_jobs', ['queue', 'payload', 'attempts']));
        $this->assertTrue(Schema::hasColumns('jobs', ['job_code', 'customer_id', 'status']));
        $this->assertSame('queue_jobs', config('queue.connections.database.table'));
    }
}
