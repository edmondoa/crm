<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\EmailCampaign;
use Illuminate\Database\Eloquent\Builder;

class EmailCampaignAudienceResolver
{
    public function query(EmailCampaign $campaign, ?string $campaignType = null): Builder
    {
        $campaignType ??= $campaign->target_type ?? 'general';

        $customers = Customer::query()
            ->active()
            ->whereNotNull('email')
            ->where('email', '<>', '');

        return match ($campaignType) {
            'birthday' => $customers
                ->whereNotNull('date_of_birth')
                ->whereMonth('date_of_birth', today()->month)
                ->whereDay('date_of_birth', today()->day),

            'new_customers' => $this->newCustomersQuery($customers, $campaign),

            'pending_invoices' => $customers->whereHas('invoices', function (Builder $invoices) {
                $invoices
                    ->where('balance_due', '>', 0)
                    ->whereIn('status', ['sent', 'viewed', 'partial', 'overdue']);
            }),

            'monthly_invoices' => $customers->whereHas('invoices', function (Builder $invoices) {
                $invoices
                    ->whereBetween('due_date', [
                        now()->startOfMonth()->toDateString(),
                        now()->endOfMonth()->toDateString(),
                    ])
                    ->where('balance_due', '>', 0)
                    ->whereIn('status', ['sent', 'viewed', 'partial', 'overdue']);
            }),

            default => $customers,
        };
    }

    /**
     * @return array<string, int>
     */
    public function countsByType(EmailCampaign $campaign): array
    {
        $counts = [];

        foreach (array_keys(EmailCampaign::CAMPAIGN_TYPES) as $type) {
            $counts[$type] = $this->query($campaign, $type)->count();
        }

        return $counts;
    }

    private function newCustomersQuery(Builder $customers, EmailCampaign $campaign): Builder
    {
        $cutoff = $campaign->last_run_at ?? $campaign->created_at;

        if (!$cutoff) {
            return $customers->whereRaw('1 = 0');
        }

        return $customers->where('created_at', '>', $cutoff);
    }
}
