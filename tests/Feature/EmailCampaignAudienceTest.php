<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\EmailCampaign;
use App\Models\Invoice;
use App\Services\EmailCampaignAudienceResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailCampaignAudienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_birthday_campaign_matches_active_customers_with_an_email_and_birthday_today(): void
    {
        $birthdayCustomer = $this->createCustomer([
            'date_of_birth' => today()->subYears(30)->toDateString(),
        ]);
        $this->createCustomer([
            'date_of_birth' => today()->subDay()->subYears(30)->toDateString(),
        ]);
        $this->createCustomer([
            'date_of_birth' => today()->subYears(30)->toDateString(),
            'status' => 'inactive',
        ]);
        $this->createCustomer([
            'date_of_birth' => today()->subYears(30)->toDateString(),
            'email' => null,
        ]);

        $campaign = $this->createCampaign('birthday');
        $matchedIds = app(EmailCampaignAudienceResolver::class)
            ->query($campaign)
            ->pluck('id')
            ->all();

        $this->assertSame([$birthdayCustomer->id], $matchedIds);
    }

    public function test_new_customers_campaign_uses_the_last_run_as_its_cutoff(): void
    {
        $cutoff = now()->subDays(2)->startOfMinute();
        $campaign = $this->createCampaign('new_customers', [
            'created_at' => $cutoff->copy()->subDays(5),
            'last_run_at' => $cutoff,
        ]);
        $this->createCustomer([
            'created_at' => $cutoff->copy()->subMinute(),
            'updated_at' => $cutoff->copy()->subMinute(),
        ]);
        $newCustomer = $this->createCustomer([
            'created_at' => $cutoff->copy()->addMinute(),
            'updated_at' => $cutoff->copy()->addMinute(),
        ]);

        $matchedIds = app(EmailCampaignAudienceResolver::class)
            ->query($campaign)
            ->pluck('id')
            ->all();

        $this->assertSame([$newCustomer->id], $matchedIds);
    }

    public function test_pending_invoice_campaign_matches_only_customers_with_open_sent_invoices(): void
    {
        $eligible = $this->createCustomer();
        $paid = $this->createCustomer();
        $draft = $this->createCustomer();

        $this->createInvoice($eligible, ['status' => 'partial', 'balance_due' => 35]);
        $this->createInvoice($paid, ['status' => 'paid', 'balance_due' => 0]);
        $this->createInvoice($draft, ['status' => 'draft', 'balance_due' => 90]);

        $matchedIds = app(EmailCampaignAudienceResolver::class)
            ->query($this->createCampaign('pending_invoices'))
            ->pluck('id')
            ->all();

        $this->assertSame([$eligible->id], $matchedIds);
    }

    public function test_monthly_invoice_campaign_matches_only_open_invoices_due_this_month(): void
    {
        $eligible = $this->createCustomer();
        $nextMonth = $this->createCustomer();
        $paid = $this->createCustomer();

        $this->createInvoice($eligible, [
            'status' => 'sent',
            'balance_due' => 100,
            'due_date' => now()->startOfMonth()->toDateString(),
        ]);
        $this->createInvoice($nextMonth, [
            'status' => 'sent',
            'balance_due' => 100,
            'due_date' => now()->endOfMonth()->addDay()->toDateString(),
        ]);
        $this->createInvoice($paid, [
            'status' => 'paid',
            'balance_due' => 0,
            'due_date' => now()->toDateString(),
        ]);

        $matchedIds = app(EmailCampaignAudienceResolver::class)
            ->query($this->createCampaign('monthly_invoices'))
            ->pluck('id')
            ->all();

        $this->assertSame([$eligible->id], $matchedIds);
    }

    public function test_campaign_editor_shows_audience_match_counts_for_each_type(): void
    {
        $birthdayCustomer = $this->createCustomer([
            'date_of_birth' => today()->subYears(20)->toDateString(),
        ]);
        $campaign = $this->createCampaign('birthday');

        $this->withoutVite()
            ->get(route('crm.email-campaigns.edit', $campaign))
            ->assertOk()
            ->assertSee('Birthday (1 match)')
            ->assertSee('New customers added (0 matches)')
            ->assertSee('Active customers whose birthday is today.');

        $this->assertNotNull($birthdayCustomer->id);
    }

    public function test_customer_can_save_email_and_date_of_birth_without_welcome_email_failure(): void
    {
        $dateOfBirth = today()->subYears(25)->toDateString();

        $this->callWithCsrf('POST', route('crm.customers.store'), [
            'customer_type' => 'individual',
            'first_name' => 'Birthday',
            'last_name' => 'Customer',
            'email' => 'birthday-customer@example.test',
            'date_of_birth' => $dateOfBirth,
            'country' => 'United States',
            'status' => 'active',
        ])->assertRedirect(route('crm.customers.index'));

        $customer = Customer::query()->where('first_name', 'Birthday')->firstOrFail();
        $this->assertSame($dateOfBirth, $customer->date_of_birth->toDateString());
        $this->assertSame('birthday-customer@example.test', $customer->email);
    }

    private function createCustomer(array $overrides = []): Customer
    {
        $suffix = bin2hex(random_bytes(6));
        $createdAt = $overrides['created_at'] ?? null;
        $updatedAt = $overrides['updated_at'] ?? null;
        unset($overrides['created_at'], $overrides['updated_at']);

        $customer = Customer::query()->create(array_merge([
            'customer_code' => 'AUD-'.$suffix,
            'customer_type' => 'individual',
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => 'audience-'.$suffix.'@example.test',
            'status' => 'active',
        ], $overrides));

        if ($createdAt || $updatedAt) {
            $timestamps = array_filter([
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);
            $customer->forceFill($timestamps)->saveQuietly();
        }

        return $customer->fresh();
    }

    private function createCampaign(string $type, array $overrides = []): EmailCampaign
    {
        return EmailCampaign::query()->create(array_merge([
            'name' => 'Audience test',
            'target_type' => $type,
            'status' => 'draft',
        ], $overrides));
    }

    private function createInvoice(Customer $customer, array $overrides = []): Invoice
    {
        return Invoice::query()->create(array_merge([
            'invoice_number' => 'AUD-INV-'.bin2hex(random_bytes(6)),
            'customer_id' => $customer->id,
            'invoice_date' => today()->toDateString(),
            'due_date' => today()->toDateString(),
            'status' => 'sent',
            'subtotal' => 100,
            'discount' => 0,
            'tax' => 0,
            'other_charges' => 0,
            'grand_total' => 100,
            'amount_paid' => 0,
            'balance_due' => 100,
        ], $overrides));
    }

    private function callWithCsrf(string $method, string $uri, array $data = [])
    {
        $token = str_repeat('a', 40);

        return $this->withSession(['_token' => $token])
            ->withHeader('X-CSRF-TOKEN', $token)
            ->json($method, $uri, $data);
    }
}
