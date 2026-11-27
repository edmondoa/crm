<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\CrmEstimate;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Job;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            ])
            ->withSum('payments as paid_amount', 'amount');

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

        $paymentTotals = DB::table('payments')
            ->select('invoice_id')
            ->selectRaw('SUM(amount) as paid_total')
            ->groupBy('invoice_id');

        $totalOutstanding = DB::table('invoices')
            ->leftJoinSub($paymentTotals, 'payment_totals', function ($join) {
                $join->on('invoices.id', '=', 'payment_totals.invoice_id');
            })
            ->where('invoices.status', '!=', 'cancelled')
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN invoices.grand_total - COALESCE(payment_totals.paid_total, 0) > 0
                    THEN invoices.grand_total - COALESCE(payment_totals.paid_total, 0)
                    ELSE 0 END), 0) as total'
            )
            ->value('total');

        $totalPaid = DB::table('payments')->sum('amount');

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

            $existingInvoice = Invoice::query()
                ->where('estimate_id', $estimate->id)
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($existingInvoice) {
                return redirect()
                    ->route('crm.invoices.show', $existingInvoice)
                    ->with('warning', 'An invoice already exists for this estimate.');
            }
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

        $properties = Property::query()
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Jobs
        |--------------------------------------------------------------------------
        */

        $jobs = Job::query()
            ->orderByDesc('id')
            ->get();

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

        $invoice = new Invoice;

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

        $selectedCustomerId = $estimate?->customer_id
            ?? ($request->integer('customer_id') ?: null);
        $selectedPropertyId = $estimate?->property_id
            ?? ($request->integer('property_id') ?: null);
        $selectedJobId = $estimate?->job_id
            ?? ($request->integer('job_id') ?: null);

        $invoice->customer_id = old(
            'customer_id',
            $selectedCustomerId
        );

        $invoice->property_id = old(
            'property_id',
            $selectedPropertyId
        );

        $invoice->job_id = old(
            'job_id',
            $selectedJobId
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
                        'item_type' => $item->item_type ?? 'service',
                        'description' => $item->description,
                        'sku' => $item->sku,
                        'quantity' => $item->quantity,
                        'unit' => $item->unit ?? 'unit',
                        'unit_price' => $item->unit_price ?? 0,
                        'discount_percent' => $item->discount_percent ?? 0,
                        'tax_percent' => $item->tax_percent ?? 0,
                        'notes' => $item->notes,
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
        $estimateId = $request->validated()['estimate_id'] ?? null;
        if ($estimateId) {
            $existingInvoice = Invoice::query()
                ->where('estimate_id', $estimateId)
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($existingInvoice) {
                return redirect()
                    ->route('crm.invoices.show', $existingInvoice)
                    ->with('warning', 'An invoice already exists for this estimate.');
            }
        }

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

                $item = new InvoiceItem;

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
                'Invoice created successfully.'
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
    public function edit(Invoice $invoice): View
    {
        abort_unless($invoice->status === 'draft', 404);

        $invoice->load('items');
        $customers = Customer::query()->orderBy('last_name')->get();
        $properties = Property::query()->orderBy('name')->get();
        $jobs = Job::query()->orderByDesc('id')->get();
        $estimates = CrmEstimate::query()
            ->with('customer')
            ->orderByDesc('estimate_date')
            ->orderByDesc('id')
            ->get();

        return view('crm.invoices.edit', compact(
            'invoice',
            'customers',
            'properties',
            'jobs',
            'estimates'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['estimate_id'])) {
            $existingInvoice = Invoice::query()
                ->where('estimate_id', $data['estimate_id'])
                ->where('id', '!=', $invoice->id)
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($existingInvoice) {
                return redirect()
                    ->route('crm.invoices.show', $existingInvoice)
                    ->with('warning', 'An invoice already exists for this estimate.');
            }
        }

        DB::transaction(function () use (
            $data,
            $invoice
        ) {

            $invoice->update([
                'customer_id' => $data['customer_id'],

                'property_id' => $data['property_id'] ?? null,

                'job_id' => $data['job_id'] ?? null,

                'estimate_id' => $data['estimate_id'] ?? null,

                'invoice_date' => $data['invoice_date'],

                'due_date' => $data['due_date'],

                'status' => $data['status'],

                'other_charges' => $data['other_charges'] ?? 0,

                'notes' => $data['notes'] ?? null,

                'terms' => $data['terms'] ?? null,
            ]);

            $submittedIds = [];

            foreach ($data['items'] as $index => $itemData) {

                if (! empty($itemData['id'])) {

                    $item = $invoice
                        ->items()
                        ->findOrFail(
                            $itemData['id']
                        );

                } else {

                    $item = new InvoiceItem;
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
            $invoice->updatePaymentStatus();
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
    public function destroy(Invoice $invoice): RedirectResponse
    {
        if ($invoice->status !== 'draft') {
            return redirect()
                ->route('crm.invoices.index')
                ->with('error', 'Only draft invoices can be deleted.');
        }

        if ($invoice->payments()->exists()) {
            return redirect()
                ->route('crm.invoices.index')
                ->with('error', 'Invoices with payments cannot be deleted.');
        }

        $invoice->delete();

        return redirect()
            ->route('crm.invoices.index')
            ->with('success', 'Draft invoice deleted successfully.');
    }

    /**
     * Generate a unique invoice number.
     */
    private function generateInvoiceNumber(): string
    {
        $year = now()->format('Y');

        $lastInvoice = Invoice::where('invoice_number', 'like', "INV-{$year}-%")
            ->orderByDesc('id')
            ->first();

        if (! $lastInvoice) {
            $sequence = 1;
        } else {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -5);
            $sequence = $lastNumber + 1;
        }

        return sprintf('INV-%s-%05d', $year, $sequence);
    }
}
