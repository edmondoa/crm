<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOpportunityRequest;
use App\Http\Requests\UpdateOpportunityRequest;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OpportunityController extends Controller
{
    private const STAGES = [
        'qualification',
        'needs_analysis',
        'proposal',
        'negotiation',
        'won',
        'lost',
    ];

    public function index(Request $request): View
    {
        $opportunities = Opportunity::query()
            ->with(['lead', 'customer', 'assignee'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->string('search')->toString();

                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere(
                            'opportunity_number',
                            'like',
                            "%{$search}%"
                        );
                });
            })
            ->when($request->filled('stage'), fn ($q) =>
                $q->where('stage', $request->stage))
            ->when($request->filled('assigned_to'), fn ($q) =>
                $q->where('assigned_to', $request->assigned_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('crm.opportunities.index', compact('opportunities'));
    }

    public function pipeline(): View
    {
        $stages = self::STAGES;

        $opportunities = Opportunity::query()
            ->with(['lead', 'customer', 'assignee'])
            ->whereNotIn('stage', ['won', 'lost'])
            ->orderBy('expected_close_date')
            ->get()
            ->groupBy('stage');

        $open = Opportunity::query()
            ->whereNotIn('stage', ['won', 'lost']);

        $pipelineValue = (clone $open)->sum('estimated_value');

        $weightedValue = (clone $open)
            ->selectRaw(
                'COALESCE(SUM(estimated_value * probability / 100), 0)
                 AS weighted_total'
            )
            ->value('weighted_total');

        return view('crm.opportunities.pipeline', compact(
            'stages',
            'opportunities',
            'pipelineValue',
            'weightedValue'
        ));
    }

    public function create(): View
    {
        $opportunity = null;
        $leads = Lead::whereIn('status', ['qualified', 'contacted'])
            ->orderBy('first_name')
            ->get();

        $customers = Customer::orderBy('id', 'desc')->get();
        $users = User::orderBy('name')->get();

        return view('crm.opportunities.create', compact(
            'opportunity',
            'leads',
            'customers',
            'users'
        ));
    }

    public function store(
        StoreOpportunityRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $data['opportunity_number'] =
            'OPP-' . Str::upper((string) Str::ulid());

        $data['created_by'] = $request->user()->id ?? null;

        if ($data['stage'] === 'won' || $data['stage'] === 'lost') {
            $data['closed_at'] = now();
        }

        $opportunity = Opportunity::create($data);

        return redirect()
            ->route('crm.opportunities.show', $opportunity)
            ->with('success', 'Opportunity created successfully.');
    }

    public function show(Opportunity $opportunity): View
    {
        $opportunity->load([
            'lead',
            'customer',
            'assignee',
            'creator',
        ]);

        return view('crm.opportunities.show', compact('opportunity'));
    }

    public function edit(Opportunity $opportunity): View
    {
        $leads = Lead::orderBy('first_name')->get();
        $customers = Customer::orderBy('id', 'desc')->get();
        $users = User::orderBy('name')->get();

        return view('crm.opportunities.edit', compact(
            'opportunity',
            'leads',
            'customers',
            'users'
        ));
    }

    public function update(
        UpdateOpportunityRequest $request,
        Opportunity $opportunity
    ): RedirectResponse {
        $data = $request->validated();

        if (in_array($data['stage'], ['won', 'lost'], true)) {
            $data['closed_at'] ??= $opportunity->closed_at ?? now();
        } else {
            $data['closed_at'] = null;
            $data['loss_reason'] = null;
        }

        $opportunity->update($data);

        return redirect()
            ->route('crm.opportunities.show', $opportunity)
            ->with('success', 'Opportunity updated successfully.');
    }

    public function destroy(
        Opportunity $opportunity
    ): RedirectResponse {
        $opportunity->delete();

        return redirect()
            ->route('crm.opportunities.index')
            ->with('success', 'Opportunity archived successfully.');
    }

    public function updateStage(
        Request $request,
        Opportunity $opportunity
    ): RedirectResponse {
        $data = $request->validate([
            'stage' => [
                'required',
                'in:qualification,needs_analysis,proposal,negotiation,won,lost',
            ],
            'loss_reason' => [
                'nullable', 'string', 'max:1000',
            ],
        ]);

        if ($data['stage'] === 'lost' && empty($data['loss_reason'])) {
            return back()->withErrors([
                'loss_reason' => 'Please provide a reason for losing this opportunity.',
            ]);
        }

        $opportunity->stage = $data['stage'];
        $opportunity->loss_reason =
            $data['stage'] === 'lost' ? $data['loss_reason'] : null;
        $opportunity->closed_at =
            in_array($data['stage'], ['won', 'lost'], true)
                ? ($opportunity->closed_at ?? now())
                : null;

        $opportunity->save();

        return back()->with('success', 'Opportunity stage updated.');
    }
}
