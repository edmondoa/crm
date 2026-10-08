<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\CrmSetting;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display customer list.
     */
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->search($request->input('search'))
            ->when(
                $request->filled('type'),
                fn ($query) =>
                    $query->where(
                        'customer_type',
                        $request->input('type')
                    )
            )
            ->when(
                $request->filled('status'),
                fn ($query) =>
                    $query->where(
                        'status',
                        $request->input('status')
                    )
            )
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Customer::count(),

            'active' => Customer::where(
                'status',
                'active'
            )->count(),

            'inactive' => Customer::where(
                'status',
                'inactive'
            )->count(),

            'companies' => Customer::where(
                'customer_type',
                'company'
            )->count(),
        ];

        return view(
            'crm.customers.index',
            compact(
                'customers',
                'stats'
            )
        );
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('crm.customers.create');
    }

    /**
     * Store customer.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_type' => [
                'required',
                'in:individual,company',
            ],

            'company_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'first_name' => [
                'required_if:customer_type,individual',
                'nullable',
                'string',
                'max:100',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required_if:customer_type,individual',
                'nullable',
                'string',
                'max:100',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'mobile' => [
                'nullable',
                'string',
                'max:50',
            ],

            'tax_id' => [
                'nullable',
                'string',
                'max:100',
            ],

            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:2'],
            'zip_code' => ['nullable', 'string', 'max:10'],
            'country' => ['required', 'string', 'max:100'],

            'notes' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);

        DB::transaction(function () use (&$validated) {

            $validated['customer_code'] =
                $this->generateCustomerCode();

            Customer::create($validated);
        });

        return redirect()
            ->route('crm.customers.index')
            ->with(
                'success',
                'Customer created successfully.'
            );
    }

    /**
     * Display customer.
     */
    public function show(Customer $customer): View
    {
        $customer->load([
            'contacts',
            'properties',
            'properties.primaryContact',
            'tasks' => function ($query) {
                $query
                    ->with([
                        'contact',
                        'property',
                    ])
                    ->latest('due_at')
                    ->limit(10);
            },
            'jobs' => function ($query) {
                $query
                    ->with([
                        'contact',
                        'property',
                        'propertyLocation',
                    ])
                    ->latest('scheduled_start_at')
                    ->limit(10);
            },
        ]);

        return view(
            'crm.customers.show',
            compact('customer')
        );
    }

    /**
     * Show edit form.
     */
    public function edit(Customer $customer): View
    {
        return view(
            'crm.customers.edit',
            compact('customer')
        );
    }

    /**
     * Update customer.
     */
    public function update(
        Request $request,
        Customer $customer
    ): RedirectResponse {

        $validated = $request->validate([
            'customer_type' => [
                'required',
                'in:individual,company',
            ],

            'company_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'first_name' => [
                'required_if:customer_type,individual',
                'nullable',
                'string',
                'max:100',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required_if:customer_type,individual',
                'nullable',
                'string',
                'max:100',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'mobile' => [
                'nullable',
                'string',
                'max:50',
            ],

            'tax_id' => [
                'nullable',
                'string',
                'max:100',
            ],

            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:2'],
            'zip_code' => ['nullable', 'string', 'max:10'],
            'country' => ['required', 'string', 'max:100'],

            'notes' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);

        $customer->update($validated);

        return redirect()
            ->route(
                'crm.customers.show',
                $customer
            )
            ->with(
                'success',
                'Customer updated successfully.'
            );
    }

    /**
     * Delete customer.
     */
    public function destroy(
        Customer $customer
    ): RedirectResponse {

        /*
         * Prevent deletion if the customer
         * already has related properties.
         */
        if (
            method_exists($customer, 'properties') &&
            $customer->properties()->exists()
        ) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'This customer cannot be deleted because it has related properties.'
                );
        }

        $customer->delete();

        return redirect()
            ->route('crm.customers.index')
            ->with(
                'success',
                'Customer deleted successfully.'
            );
    }

    /**
     * Generate customer code.
     */
    private function generateCustomerCode(): string
    {
        $settings = CrmSetting::active();

        $prefix = $settings->customer_code_prefix;

        $lastCustomer = Customer::query()
            ->whereNotNull('customer_code')
            ->orderByDesc('id')
            ->first();

        if (!$lastCustomer) {
            $number = 1;
        } else {
            $lastCode = $lastCustomer->customer_code;

            $number = (int) preg_replace(
                '/\D/',
                '',
                str_replace($prefix, '', $lastCode)
            );

            $number++;
        }

        return $prefix . str_pad(
            $number,
            5,
            '0',
            STR_PAD_LEFT
        );
    }

    public function contacts(Customer $customer)
    {
        $contacts = $customer->contacts()
            ->active()
            ->orderByDesc('is_primary')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get([
                'id',
                'first_name',
                'middle_name',
                'last_name',
                'job_title',
            ]);

        return response()->json(
            $contacts->map(function ($contact) {

                return [
                    'id' => $contact->id,

                    'name' => trim(implode(' ', array_filter([
                        $contact->first_name,
                        $contact->middle_name,
                        $contact->last_name,
                    ]))),

                    'job_title' => $contact->job_title,
                ];

            })
        );
    }
}