<?php

namespace Tests\Feature;

use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailCampaignManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_campaign_pages_render_with_templates_available(): void
    {
        $this->withoutVite();
        $template = $this->createTemplate();

        $this->get(route('crm.email-campaigns.index'))
            ->assertOk()
            ->assertSee('Email Campaigns')
            ->assertSee('planning records only');

        $this->get(route('crm.email-campaigns.create'))
            ->assertOk()
            ->assertSee('Create campaign plan')
            ->assertSee($template->name)
            ->assertSee('Birthday')
            ->assertSee('New customers added')
            ->assertSee('Pending invoices')
            ->assertSee('Monthly invoices');
    }

    public function test_campaign_can_be_created_updated_and_deleted(): void
    {
        $template = $this->createTemplate();
        $payload = [
            'name' => 'Spring service update',
            'email_template_id' => $template->id,
            'target_type' => 'pending_invoices',
            'audience_description' => 'Existing customers',
            'status' => 'draft',
            'scheduled_at' => '',
            'notes' => 'Confirm final copy before launch.',
        ];

        $response = $this->callWithCsrf('POST', route('crm.email-campaigns.store'), $payload);
        $campaign = EmailCampaign::query()->firstOrFail();
        $response->assertRedirect(route('crm.email-campaigns.edit', $campaign));
        $this->assertDatabaseHas('email_campaigns', [
            'id' => $campaign->id,
            'name' => 'Spring service update',
            'status' => 'draft',
            'target_type' => 'pending_invoices',
            'audience_description' => 'Existing customers',
        ]);

        $scheduledAt = now()->addDays(3)->startOfMinute();
        $this->callWithCsrf('PUT', route('crm.email-campaigns.update', $campaign), array_merge($payload, [
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt->format('Y-m-d\TH:i'),
        ]))->assertRedirect(route('crm.email-campaigns.edit', $campaign));

        $this->assertDatabaseHas('email_campaigns', [
            'id' => $campaign->id,
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt->format('Y-m-d H:i:s'),
        ]);

        $this->callWithCsrf('DELETE', route('crm.email-campaigns.destroy', $campaign))
            ->assertRedirect(route('crm.email-campaigns.index'));
        $this->assertDatabaseMissing('email_campaigns', ['id' => $campaign->id]);
    }

    public function test_scheduled_campaign_requires_a_future_planned_date(): void
    {
        $template = $this->createTemplate();
        $payload = [
            'name' => 'Upcoming service update',
            'email_template_id' => $template->id,
            'target_type' => 'birthday',
            'audience_description' => 'Active customers',
            'status' => 'scheduled',
            'notes' => '',
        ];

        $this->callWithCsrf('POST', route('crm.email-campaigns.store'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('scheduled_at');

        $this->callWithCsrf('POST', route('crm.email-campaigns.store'), array_merge($payload, [
                'scheduled_at' => now()->subDay()->format('Y-m-d\TH:i'),
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('scheduled_at');

        $this->assertDatabaseCount('email_campaigns', 0);
    }

    private function createTemplate(): EmailTemplate
    {
        return EmailTemplate::create([
            'name' => 'Monthly update',
            'slug' => 'monthly-update',
            'category' => 'campaign',
            'subject' => 'News from our team',
            'body_html' => '<p>Hello there</p>',
            'status' => true,
        ]);
    }

    private function callWithCsrf(string $method, string $uri, array $data = [])
    {
        $token = str_repeat('a', 40);

        return $this->withSession(['_token' => $token])
            ->withHeader('X-CSRF-TOKEN', $token)
            ->json($method, $uri, $data);
    }
}
