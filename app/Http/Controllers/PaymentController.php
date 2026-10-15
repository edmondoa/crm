<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = Payment::with(['invoice', 'creator'])
            ->latest('payment_date')
            ->paginate(15);

        return view('crm.payments.index', compact('payments'));
    }

    public function create(): View
    {
        $invoices = Invoice::query()
            ->orderByDesc('id')
            ->get();

        return view('crm.payments.create', compact('invoices'));
    }

    public function store(
        StorePaymentRequest $request
    ): RedirectResponse {
        $data = $request->validated();

        $payment = DB::transaction(function () use (
            $data,
            $request
        ) {
            $invoice = Invoice::query()
                ->lockForUpdate()
                ->findOrFail($data['invoice_id']);

            // IMPORTANT: Replace "total" if your invoice
            // uses a different final-total column.
            $invoiceTotal = (float) $invoice->grand_total;

            $alreadyPaid = (float) $invoice->payments()
                ->sum('amount');

            $balance = round(
                $invoiceTotal - $alreadyPaid,
                2
            );

            $amount = round((float) $data['amount'], 2);

            if ($balance <= 0) {
                throw ValidationException::withMessages([
                    'invoice_id' =>
                        'This invoice has already been fully paid.',
                ]);
            }

            if ($amount > $balance) {
                throw ValidationException::withMessages([
                    'amount' =>
                        'Payment exceeds the outstanding balance of ' .
                        number_format($balance, 2) . '.',
                ]);
            }

            $payment = $invoice->payments()->create([
                'payment_number' =>
                    'PAY-' . Str::upper((string) Str::ulid()),
                'amount' => $amount,
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'],
                'reference_number' =>
                    $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => $request->user()?->id,
            ]);

            // If your schema has a dedicated payment_status
            // column, synchronize it here. Avoid overwriting
            // invoice workflow statuses without checking them.

            return $payment;
        });

        return redirect()
            ->route('crm.payments.show', $payment)
            ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment): View
    {
        $payment->load(['invoice', 'creator']);

        return view('crm.payments.show', compact('payment'));
    }

    public function edit(Payment $payment): View
    {
        abort(404, 'Posted payments cannot be edited.');
    }

    public function update(
        StorePaymentRequest $request,
        Payment $payment
    ): RedirectResponse {
        abort(404, 'Posted payments cannot be edited.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        abort(404, 'Posted payments cannot be deleted.');
    }
}
