<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use App\Services\EmailCampaignAudienceResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmailCampaignController extends Controller
{
    private const STATUSES = ['draft', 'scheduled', 'cancelled'];

    public function __construct(
        private readonly EmailCampaignAudienceResolver $audienceResolver
    ) {
    }

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $query = EmailCampaign::query()
            ->with('emailTemplate')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q')->toString().'%';
                $query->where(function ($subQuery) use ($term) {
                    $subQuery->where('name', 'like', $term)
                        ->orWhere('audience_description', 'like', $term);
                });
            })
            ->when(in_array($status, self::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->latest('updated_at');

        return view('crm.email-campaigns.index', [
            'campaigns' => $query->paginate(15)->withQueryString(),
            'counts' => [
                'all' => EmailCampaign::count(),
                'draft' => EmailCampaign::where('status', 'draft')->count(),
                'scheduled' => EmailCampaign::where('status', 'scheduled')->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('crm.email-campaigns.create', $this->formData(
            new EmailCampaign(['status' => 'draft'])
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()?->id;
        $data['updated_by'] = $request->user()?->id;
        $campaign = EmailCampaign::create($data);

        return redirect()->route('crm.email-campaigns.edit', $campaign)
            ->with('success', 'Campaign plan created.');
    }

    public function edit(EmailCampaign $email_campaign): View
    {
        return view('crm.email-campaigns.edit', $this->formData($email_campaign));
    }

    public function update(Request $request, EmailCampaign $email_campaign): RedirectResponse
    {
        $data = $this->validated($request);
        $data['updated_by'] = $request->user()?->id;
        $email_campaign->update($data);

        return redirect()->route('crm.email-campaigns.edit', $email_campaign)
            ->with('success', 'Campaign plan updated.');
    }

    public function destroy(EmailCampaign $email_campaign): RedirectResponse
    {
        $email_campaign->delete();

        return redirect()->route('crm.email-campaigns.index')
            ->with('success', 'Campaign plan deleted.');
    }

    private function validated(Request $request): array
    {
        $status = $request->input('status');
        $scheduledAtRules = $status === 'scheduled'
            ? ['required', 'date', 'after:now']
            : ['nullable', 'date'];

        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email_template_id' => ['required', 'integer', 'exists:email_templates,id'],
            'target_type' => ['required', Rule::in(array_keys(EmailCampaign::CAMPAIGN_TYPES))],
            'audience_description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'scheduled_at' => $scheduledAtRules,
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function formData(EmailCampaign $campaign): array
    {
        return [
            'campaign' => $campaign,
            'templates' => EmailTemplate::query()->orderBy('name')->get(),
            'targetTypes' => EmailCampaign::CAMPAIGN_TYPES,
            'audienceCounts' => $this->audienceResolver->countsByType($campaign),
            'campaignTypeDescriptions' => [
                'general' => 'All active customers with an email address.',
                'birthday' => 'Active customers whose birthday is today.',
                'new_customers' => 'Active customers created since this campaign was created or last run.',
                'pending_invoices' => 'Customers with an unpaid sent, viewed, partial, or overdue invoice.',
                'monthly_invoices' => 'Customers with an unpaid invoice due during the current month.',
            ],
        ];
    }
}
