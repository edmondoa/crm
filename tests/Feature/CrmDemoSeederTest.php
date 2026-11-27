<?php

namespace Tests\Feature;

use Database\Seeders\CrmDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CrmDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_every_crm_domain_table_and_is_safe_to_repeat(): void
    {
        $expectedCounts = [
            'users' => 1,
            'crm_settings' => 1,
            'customers' => 2,
            'contacts' => 2,
            'properties' => 2,
            'property_locations' => 2,
            'activities' => 2,
            'tasks' => 2,
            'employees' => 2,
            'jobs' => 2,
            'job_assignments' => 2,
            'job_schedules' => 2,
            'leads' => 1,
            'opportunities' => 1,
            'lead_activities' => 1,
            'crm_estimates' => 1,
            'crm_estimate_items' => 1,
            'invoices' => 1,
            'invoice_items' => 1,
            'payments' => 1,
            'email_templates' => 1,
            'email_campaigns' => 2,
        ];

        $this->seed(CrmDemoSeeder::class);
        $this->assertSeedCounts($expectedCounts);

        $this->seed(CrmDemoSeeder::class);
        $this->assertSeedCounts($expectedCounts);

        $this->assertSame(0, DB::table('cache')->count());
        $this->assertSame(0, DB::table('cache_locks')->count());
        $this->assertSame(0, DB::table('queue_jobs')->count());
        $this->assertSame(0, DB::table('job_batches')->count());
        $this->assertSame(0, DB::table('failed_jobs')->count());
        $this->assertSame(0, DB::table('sessions')->count());
        $this->assertSame(0, DB::table('password_reset_tokens')->count());
    }

    private function assertSeedCounts(array $expectedCounts): void
    {
        foreach ($expectedCounts as $table => $expectedCount) {
            $this->assertDatabaseCount($table, $expectedCount);
        }
    }
}
