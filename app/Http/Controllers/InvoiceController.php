<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Property;
use App\Models\CrmEstimate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::query()
            ->with([
                'customer',
                'property',
                'job',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {

            $search = trim($request->input('search'));

            $query->where(function ($q) use ($search) {

                $q->where('invoice_number', 'like', "%{$search}%")

                    ->orWhereHas('customer', function ($customerQuery) use ($search) {

                        $customerQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('company_name', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");

                    });

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */
        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->input('status')
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Invoice Date Range
        |--------------------------------------------------------------------------
        */
        if ($request->filled('date_from')) {

            $query->whereDate(
                'invoice_date',
                '>=',
                $request->input('date_from')
            );

        }


        if ($request->filled('date_to')) {

            $query->whereDate(
                'invoice_date',
                '<=',
                $request->input('date_to')
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $invoices = $query
            ->latest('invoice_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Dashboard Totals
        |--------------------------------------------------------------------------
        */
        $totalInvoiced = Invoice::sum('grand_total');

        $totalOutstanding = Invoice::sum('balance_due');

        $totalPaid = Invoice::sum('amount_paid');


        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */
        return view('crm.invoices.index', compact(
            'invoices',
            'totalInvoiced',
            'totalOutstanding',
            'totalPaid'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (!empty($data['estimate_id'])) {

            $existingInvoice = CrmInvoice::where(
                'estimate_id',
                $data['estimate_id']
            )
            ->whereNotIn('status', ['cancelled'])
            ->first();

            if ($existingInvoice) {
                return redirect()
                    ->route('crm.invoices.show', $existingInvoice)
                    ->with(
                        'warning',
                        'An invoice already exists for this estimate.'
                    );
            }
        }
        $estimate = null;

        if ($request->filled('estimate_id')) {
            $estimate = CrmEstimate::with([
                'customer',
                'property',
                'job',
                'items',
            ])->findOrFail(
                $request->integer('estimate_id')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Customers
        |--------------------------------------------------------------------------
        */

        $customers = Customer::query()
            ->orderBy('last_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Properties
        |--------------------------------------------------------------------------
        */

        $properties = collect();

        if ($estimate?->customer_id) {
            $properties = Property::query()
                ->where('customer_id', $estimate->customer_id)
                ->orderBy('name')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Jobs
        |--------------------------------------------------------------------------
        */

        $jobs = collect();

        if ($estimate?->customer_id) {
            $jobs = Job::query()
                ->where('customer_id', $estimate->customer_id)
                ->orderByDesc('id')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Estimates
        |--------------------------------------------------------------------------
        */

        $estimates = CrmEstimate::query()
            ->with('customer')
            ->orderByDesc('estimate_date')
            ->orderByDesc('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | New Invoice
        |--------------------------------------------------------------------------
        */

        $invoice = new Invoice();

        $invoice->invoice_date = old(
            'invoice_date',
            now()->toDateString()
        );

        $invoice->due_date = old(
            'due_date',
            now()->addDays(30)->toDateString()
        );

        $invoice->status = old(
            'status',
            'draft'
        );

        $invoice->customer_id = old(
            'customer_id',
            $estimate?->customer_id
        );

        $invoice->property_id = old(
            'property_id',
            $estimate?->property_id
        );

        $invoice->job_id = old(
            'job_id',
            $estimate?->job_id
        );

        $invoice->estimate_id = old(
            'estimate_id',
            $estimate?->id
        );

        $invoice->discount = old(
            'discount',
            $estimate?->discount ?? 0
        );

        $invoice->tax = old(
            'tax',
            $estimate?->tax ?? 0
        );

        $invoice->other_charges = old(
            'other_charges',
            $estimate?->other_charges ?? 0
        );

        $invoice->notes = old(
            'notes',
            $estimate?->notes
        );

        $invoice->terms = old(
            'terms',
            $estimate?->terms
        );

        /*
        |--------------------------------------------------------------------------
        | Invoice Items
        |--------------------------------------------------------------------------
        |
        | Convert Estimate Items -> Invoice Items for the Blade form.
        |
        */

        $items = old('items');

        if ($items === null) {
            $items = $estimate
                ? $estimate->items->map(function ($item) {
                    return [
                        'item_type'        => $item->item_type ?? 'service',
                        'description'      => $item->description,
                        'sku'              => $item->sku,
                        'quantity'         => $item->quantity,
                        'unit'             => $item->unit ?? 'unit',
                        'unit_price'       => $item->unit_price ?? 0,
                        'discount_percent' => $item->discount_percent ?? 0,
                        'tax_percent'      => $item->tax_percent ?? 0,
                        'notes'            => $item->notes,
                    ];
                })->toArray()
                : [];
        }
       
        return view('crm.invoices.create', compact(
            'invoice',
            'customers',
            'properties',
            'jobs',
            'estimates',
            'estimate',
            'items'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        $invoice = DB::transaction(function () use ($request) {

            $data = $request->validated();

            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),

                'customer_id' => $data['customer_id'],

                'property_id' => $data['property_id'] ?? null,

                'job_id' => $data['job_id'] ?? null,

                'estimate_id' => $data['estimate_id'] ?? null,

                'invoice_date' => $data['invoice_date'],

                'due_date' => $data['due_date'],

                'status' => $data['status'] ?? 'draft',

                'discount' => 0,

                'tax' => 0,

                'other_charges' => $data['other_charges'] ?? 0,

                'subtotal' => 0,

                'grand_total' => 0,

                'amount_paid' => 0,

                'balance_due' => 0,

                'notes' => $data['notes'] ?? null,

                'terms' => $data['terms'] ?? null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Invoice Items
            |--------------------------------------------------------------------------
            */

            foreach ($data['items'] as $index => $itemData) {

                $item = new InvoiceItem();

                $item->invoice_id = $invoice->id;

                $item->item_type = $itemData['item_type'];

                $item->description = $itemData['description'];

                $item->sku = $itemData['sku'] ?? null;

                $item->quantity = $itemData['quantity'];

                $item->unit = $itemData['unit'];

                $item->unit_price = $itemData['unit_price'];

                $item->discount_percent =
                    $itemData['discount_percent'] ?? 0;

                $item->tax_percent =
                    $itemData['tax_percent'] ?? 0;

                $item->notes =
                    $itemData['notes'] ?? null;

                $item->sort_order = $index;

                /*
                |--------------------------------------------------------------------------
                | Server-side Calculation
                |--------------------------------------------------------------------------
                */

                $item->calculateTotals();

                $item->save();
            }

            /*
            |--------------------------------------------------------------------------
            | Calculate Invoice Totals
            |--------------------------------------------------------------------------
            */

            $invoice->recalculateTotals();

            /*
            |--------------------------------------------------------------------------
            | Payment Status
            |--------------------------------------------------------------------------
            */

            $invoice->updatePaymentStatus();

            return $invoice;
        });

        return redirect()
            ->route('crm.invoices.show', $invoice)
            ->with(
                'success',
                'Invoice created successfully from the estimate.'
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
{
    $invoice->load([
        'customer',
        'property',
        'job',
        'estimate',
        'items',
    ]);

    return view('crm.invoices.show', compact('invoice'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request,Invoice $invoice) {
        $data = $request->validated();

        DB::transaction(function () use (
            $data,
            $invoice
        ) {

            $invoice->update([
                'customer_id' =>
                    $data['customer_id'],

                'property_id' =>
                    $data['property_id'] ?? null,

                'job_id' =>
                    $data['job_id'] ?? null,

                'estimate_id' =>
                    $data['estimate_id'] ?? null,

                'invoice_date' =>
                    $data['invoice_date'],

                'due_date' =>
                    $data['due_date'],

                'status' =>
                    $data['status'],

                'other_charges' =>
                    $data['other_charges'] ?? 0,

                'notes' =>
                    $data['notes'] ?? null,

                'terms' =>
                    $data['terms'] ?? null,
            ]);

            $submittedIds = [];

            foreach ($data['items'] as $index => $itemData) {

                if (!empty($itemData['id'])) {

                    $item = $invoice
                        ->items()
                        ->findOrFail(
                            $itemData['id']
                        );

                } else {

                    $item = new InvoiceItem();
                }

                $item->fill($itemData);

                $item->invoice_id =
                    $invoice->id;

                $item->sort_order =
                    $index;

                $item->calculateTotals();

                $item->save();

                $submittedIds[] =
                    $item->id;
            }

            $invoice->items()
                ->whereNotIn(
                    'id',
                    $submittedIds
                )
                ->delete();

            $invoice->recalculateTotals();
        });

        return redirect()
            ->route(
                'crm.invoices.show',
                $invoice
            )
            ->with(
                'success',
                'Invoice updated successfully.'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Generate a unique invoice number.
     */
    private function generateInvoiceNumber(): string
    {
        $year = now()->format('Y');

        $lastInvoice = \App\Models\Invoice::where('invoice_number', 'like', "INV-{$year}-%")
            ->orderByDesc('id')
            ->first();

        if (!$lastInvoice) {
            $sequence = 1;
        } else {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -5);
            $sequence = $lastNumber + 1;
        }

        return sprintf('INV-%s-%05d', $year, $sequence);
    }
}
