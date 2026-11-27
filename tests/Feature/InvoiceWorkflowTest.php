<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_create_page_renders_and_prefills_customer_context(): void
    {
        $this->withoutVite();
        $customer = $this->createCustomer();

        $this->get(route('crm.invoices.create', [
            'customer_id' => $customer->id,
        ]))
            ->assertOk()
            ->assertSee('Create Invoice')
            ->assertSee('Ada Lovelace')
            ->assertViewHas('invoice', fn (Invoice $invoice): bool => (
                (int) $invoice->customer_id === $customer->id
            ));
    }

    public function test_invoice_store_calculates_line_and_invoice_totals(): void
    {
        $customer = $this->createCustomer();

        $response = $this->callWithCsrf(
            'POST',
            route('crm.invoices.store'),
            $this->invoicePayload($customer)
        );

        $invoice = Invoice::query()->firstOrFail();
        $response->assertRedirect(route('crm.invoices.show', $invoice));

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'subtotal' => 200,
            'discount' => 20,
            'tax' => 18,
            'other_charges' => 5,
            'grand_total' => 203,
            'amount_paid' => 0,
            'balance_due' => 203,
        ]);
        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'line_subtotal' => 200,
            'discount_amount' => 20,
            'tax_amount' => 18,
            'line_total' => 198,
        ]);
    }

    public function test_draft_invoice_can_be_edited_and_recalculated(): void
    {
        $customer = $this->createCustomer();
        $invoice = $this->createInvoice($customer);
        $item = $invoice->items()->firstOrFail();

        $this->withoutVite();
        $this->get(route('crm.invoices.edit', $invoice))
            ->assertOk()
            ->assertSee($invoice->invoice_number)
            ->assertSee('Update Invoice');

        $payload = $this->invoicePayload($customer);
        $payload['items'][0]['id'] = $item->id;
        $payload['items'][0]['quantity'] = 3;

        $this->callWithCsrf(
            'PUT',
            route('crm.invoices.update', $invoice),
            $payload
        )
            ->assertRedirect(route('crm.invoices.show', $invoice));

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'subtotal' => 300,
            'discount' => 30,
            'tax' => 27,
            'grand_total' => 302,
        ]);
        $this->assertDatabaseHas('invoice_items', [
            'id' => $item->id,
            'quantity' => 3,
            'line_total' => 297,
        ]);
    }

    public function test_only_unpaid_draft_invoices_can_be_deleted(): void
    {
        $customer = $this->createCustomer();
        $invoice = $this->createInvoice($customer);

        $this->callWithCsrf('DELETE', route('crm.invoices.destroy', $invoice))
            ->assertRedirect(route('crm.invoices.index'))
            ->assertSessionHas('success');
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);

        $sentInvoice = $this->createInvoice($customer, ['status' => 'sent']);
        $this->callWithCsrf('DELETE', route('crm.invoices.destroy', $sentInvoice))
            ->assertRedirect(route('crm.invoices.index'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('invoices', ['id' => $sentInvoice->id]);
    }

    public function test_recording_payment_updates_invoice_balance_and_status(): void
    {
        $customer = $this->createCustomer();
        $invoice = $this->createInvoice($customer);

        $this->callWithCsrf('POST', route('crm.payments.store'), [
            'invoice_id' => $invoice->id,
            'amount' => '50.00',
            'payment_date' => now()->toDateString(),
            'payment_method' => 'cash',
        ])->assertRedirect();

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'amount_paid' => 50,
            'balance_due' => 153,
            'status' => 'partial',
        ]);

        $this->withoutVite();
        $this->get(route('crm.invoices.index'))
            ->assertOk()
            ->assertSee('₱50.00')
            ->assertSee('₱153.00');
    }

    private function createCustomer(): Customer
    {
        return Customer::query()->create([
            'customer_code' => 'CUST-'.uniqid(),
            'customer_type' => 'individual',
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
        ]);
    }

    private function callWithCsrf(string $method, string $uri, array $data = [])
    {
        $token = str_repeat('a', 40);

        return $this->withSession(['_token' => $token])
            ->withHeader('X-CSRF-TOKEN', $token)
            ->json($method, $uri, $data);
    }

    private function invoicePayload(Customer $customer): array
    {
        return [
            'customer_id' => $customer->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'status' => 'draft',
            'other_charges' => '5.00',
            'items' => [
                [
                    'item_type' => 'service',
                    'description' => 'Service work',
                    'quantity' => '2.000',
                    'unit' => 'hour',
                    'unit_price' => '100.00',
                    'discount_percent' => '10.00',
                    'tax_percent' => '10.00',
                ],
            ],
        ];
    }

    private function createInvoice(Customer $customer, array $overrides = []): Invoice
    {
        $invoice = Invoice::query()->create(array_merge([
            'invoice_number' => 'INV-TEST-'.uniqid(),
            'customer_id' => $customer->id,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'status' => 'draft',
            'other_charges' => 5,
            'subtotal' => 0,
            'discount' => 0,
            'tax' => 0,
            'grand_total' => 0,
            'amount_paid' => 0,
            'balance_due' => 0,
        ], $overrides));

        $item = new InvoiceItem([
            'item_type' => 'service',
            'description' => 'Service work',
            'quantity' => 2,
            'unit' => 'hour',
            'unit_price' => 100,
            'discount_percent' => 10,
            'tax_percent' => 10,
            'sort_order' => 0,
        ]);
        $item->calculateTotals();
        $invoice->items()->save($item);
        $invoice->recalculateTotals();
        $invoice->updatePaymentStatus();

        return $invoice->fresh('items');
    }
}
