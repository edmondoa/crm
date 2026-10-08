<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCrmEstimateRequest;
use App\Http\Requests\UpdateCrmEstimateRequest;
use App\Models\CrmEstimate;
use App\Models\Job;
use App\Models\Customer;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EstimateController extends Controller
{
    public function index(): View
    {
        $estimates = CrmEstimate::query()
            ->with([
                'customer',
                'property',
                'job',
            ])
            ->latest()
            ->paginate(20);
     
        return view(
            'crm.estimates.index',
            compact('estimates')
        );
    }

    public function create(): View
    {
        $customers = Customer::query()
            ->orderBy('last_name')
            ->get();

        $properties = Property::query()
            ->orderBy('name')
            ->get();

        $jobs = Job::query()
            ->latest()
            ->get();

        $estimate = new CrmEstimate();
        $estimate->customer_id = request('customer_id');
        $estimate->property_id = request('property_id');
        $estimate->job_id = request('job_id');
        $estimate->estimate_date = now();
        $estimate->status = 'draft';

        return view(
            'crm.estimates.create',
            compact(
                'customers',
                'properties',
                'jobs',
                'estimate'
            )
        );
    }

    public function store(StoreCrmEstimateRequest $request) {
       
        $estimate = DB::transaction(function () use ($request) {

            $data = $request->validated();
           
            $estimate = CrmEstimate::create([
                'estimate_number' => $this->generateEstimateNumber(),
                'customer_id' => $data['customer_id'],
                'property_id' => $data['property_id'] ?? null,
                'job_id' => $data['job_id'] ?? null,
                'estimate_date' => $data['estimate_date'],
                'expiration_date' => $data['expiration_date'] ?? null,
                'status' => $data['status'],
                'discount' => $data['discount'] ?? 0,
                'tax' => $data['tax'] ?? 0,
                'other_charges' => $data['other_charges'] ?? 0,
                'notes' => $data['notes'] ?? null,
                'terms' => $data['terms'] ?? null,
                'subtotal' => 0,
                'grand_total' => 0,
            ]);

            foreach ($data['items'] as $index => $itemData) {

                $item = new \App\Models\CrmEstimateItem(
                    $itemData
                );

                $item->estimate_id = $estimate->id;
                $item->sort_order = $index;

                $item->calculateTotals();

                $item->save();
            }

            $estimate->recalculateTotals();

            return $estimate;
        });

        return redirect()
            ->route('crm.estimates.show', $estimate)
            ->with(
                'success',
                'Estimate created successfully.'
            );
    }

    public function show(
        CrmEstimate $estimate
    ): View {
        $estimate->load([
            'customer',
            'property',
            'job',
            'items',
        ]);

        return view(
            'crm.estimates.show',
            compact('estimate')
        );
    }

    public function edit(
        CrmEstimate $estimate
    ): View {
        $estimate->load('items');

        $customers = Customer::query()
            ->orderBy('last_name')
            ->get();

        $properties = Property::query()
            ->orderBy('name')
            ->get();

        $jobs = Job::query()
            ->latest()
            ->get();

        return view(
            'crm.estimates.edit',
            compact(
                'estimate',
                'customers',
                'properties',
                'jobs'
            )
        );
    }

    public function update( UpdateCrmEstimateRequest $request, CrmEstimate $estimate ): RedirectResponse {
        DB::transaction(function () use (
            $request,
            $estimate
        ) {

            $data = $request->validated();

            $estimate->update([
                'customer_id' => $data['customer_id'],

                'property_id' => $data['property_id'] ?? null, 
                'job_id' => $data['job_id'] ?? null, 
                'estimate_date' => $data['estimate_date'], 
                'expiration_date' => $data['expiration_date'] ?? null, 
                'status' => $data['status'], 
                'discount' => $data['discount'] ?? 0,
                'tax' => $data['tax'] ?? 0,
                'other_charges' => $data['other_charges'] ?? 0,
                'notes' => $data['notes'] ?? null,
                'terms' => $data['terms'] ?? null,
            ]);

            $estimate->items()->delete();

            foreach ($data['items'] as $index => $itemData) {

                $item = new \App\Models\CrmEstimateItem(
                    $itemData
                );

                $item->estimate_id = $estimate->id;
                $item->sort_order = $index;

                $item->calculateTotals();

                $item->save();
            }

            $estimate->recalculateTotals();
        });

        return redirect()
            ->route('crm.estimates.show', $estimate)
            ->with(
                'success',
                'Estimate updated successfully.'
            );
    }

    public function destroy( CrmEstimate $estimate  ): RedirectResponse {
        $estimate->delete();

        return redirect()
            ->route('crm.estimates.index')
            ->with(
                'success',
                'Estimate deleted successfully.'
            );
    }

    private function generateEstimateNumber(): string
    {
        $prefix = 'EST-' . now()->format('Ym') . '-';

        $last = CrmEstimate::query()
            ->where(
                'estimate_number',
                'like',
                $prefix . '%'
            )
            ->latest('id')
            ->first();

        $number = $last
            ? ((int) substr(
                $last->estimate_number,
                -4
            )) + 1
            : 1;

        return $prefix . str_pad(
            $number,
            4,
            '0',
            STR_PAD_LEFT
        );
    }
}