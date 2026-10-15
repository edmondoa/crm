<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Models\Customer;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(Request $request): View
    {
        $leads = Lead::query()
            ->with('assignee')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($q) use ($search) {
                    $q->where('lead_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($q) =>
                $q->where('status', $request->status))
            ->when($request->filled('source'), fn ($q) =>
                $q->where('source', $request->source))
            ->when($request->filled('assigned_to'), fn ($q) =>
                $q->where('assigned_to', $request->assigned_to))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('crm.leads.index', compact('leads'));
    }

    public function create(): View
    {
        $users = \App\Models\User::query()->orderBy('name')->get();
        $lead = null;
        return view('crm.leads.create', compact('users', 'lead'));
    }

    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $lead = Lead::create([
            ...$request->validated(),
            'lead_number' => 'LEAD-' . Str::upper((string) Str::ulid()),
            'created_by' => $request->user()->id ?? null,
        ]);

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead): View
    {
        $lead->load([
            'assignee',
            'creator',
            'convertedCustomer',
            'opportunities' => fn ($q) => $q->latest(),
            'activities.user',
        ]);

        return view('crm.leads.show', compact('lead'));
    }

    public function edit(Lead $lead): View
    {
        abort_if($lead->status === 'converted', 403);

        $users = \App\Models\User::query()->orderBy('name')->get();

        return view('crm.leads.edit', compact('lead', 'users'));
    }

    public function update(
        UpdateLeadRequest $request,
        Lead $lead
    ): RedirectResponse {
        abort_if($lead->status === 'converted', 403);

        $lead->update($request->validated());

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        abort_if($lead->status === 'converted', 403);

        $lead->delete();

        return redirect()
            ->route('crm.leads.index')
            ->with('success', 'Lead archived successfully.');
    }

    public function convert(Lead $lead): RedirectResponse
    {
        if ($lead->status !== 'qualified') {
            return back()->withErrors([
                'conversion' => 'Only qualified leads can be converted.',
            ]);
        }

        if ($lead->converted_customer_id) {
            return redirect()
                ->route('crm.customers.show', $lead->converted_customer_id);
        }

        $customer = DB::transaction(function () use ($lead) {
            $lead = Lead::query()
                ->lockForUpdate()
                ->findOrFail($lead->id);

            if ($lead->converted_customer_id) {
                return Customer::findOrFail(
                    $lead->converted_customer_id
                );
            }

            if ($lead->status !== 'qualified') {
                abort(422, 'Lead is no longer qualified.');
            }

            /*
             * Adapt these fields to your existing Customer schema.
             * Do not use this block unchanged if Customer requires
             * different fields or a customer-number generator.
             */
            $customer = Customer::create([
                'name' => $lead->company_name
                    ?: $lead->full_name,
                'email' => $lead->email,
                'phone' => $lead->phone,
            ]);

            $lead->update([
                'status' => 'converted',
                'converted_customer_id' => $customer->id,
                'converted_at' => now(),
            ]);

            return $customer;
        });

        return redirect()
            ->route('crm.customers.show', $customer)
            ->with('success', 'Lead converted to customer.');
    }
}
