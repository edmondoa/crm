<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Display contacts.
     */
    public function index(Request $request): View
    {
        $contacts = Contact::query()
            ->with('customer')
            ->search($request->input('search'))
            ->when(
                $request->filled('customer_id'),
                fn ($query) =>
                    $query->where(
                        'customer_id',
                        $request->input('customer_id')
                    )
            )
            ->when(
                $request->filled('type'),
                fn ($query) =>
                    $query->where(
                        'contact_type',
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
            ->orderByDesc('is_primary')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        $customers = Customer::query()
            ->orderBy('company_name')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $stats = [
            'total' => Contact::count(),
            'active' => Contact::where('status', 'active')->count(),
            'inactive' => Contact::where('status', 'inactive')->count(),
            'primary' => Contact::where('is_primary', true)->count(),
        ];

        return view(
            'crm.contacts.index',
            compact(
                'contacts',
                'customers',
                'stats'
            )
        );
    }

    /**
     * Show create form.
     */
    public function create(Request $request): View
    {
        $customers = Customer::query()
            ->active()
            ->orderBy('company_name')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $selectedCustomerId = $request->integer('customer_id');

        return view(
            'crm.contacts.create',
            compact(
                'customers',
                'selectedCustomerId'
            )
        );
    }

    /**
     * Store contact.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateContact($request);

        if ($validated['is_primary'] ?? false) {
            Contact::where('customer_id', $validated['customer_id'])
                ->update(['is_primary' => false]);
        }

        $contact = Contact::create($validated);

        return redirect()
            ->route('crm.contacts.show', $contact)
            ->with(
                'success',
                'Contact created successfully.'
            );
    }

    /**
     * Display contact.
     */
    public function show(Contact $contact): View
    {
        $contact->load('customer');

        return view(
            'crm.contacts.show',
            compact('contact')
        );
    }

    /**
     * Show edit form.
     */
    public function edit(Contact $contact): View
    {
        $customers = Customer::query()
            ->active()
            ->orderBy('company_name')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view(
            'crm.contacts.edit',
            compact(
                'contact',
                'customers'
            )
        );
    }

    /**
     * Update contact.
     */
    public function update(
        Request $request,
        Contact $contact
    ): RedirectResponse {

        $validated = $this->validateContact($request);

        if ($validated['is_primary'] ?? false) {

            Contact::where('customer_id', $validated['customer_id'])
                ->where('id', '!=', $contact->id)
                ->update(['is_primary' => false]);
        }

        $contact->update($validated);

        return redirect()
            ->route('crm.contacts.show', $contact)
            ->with(
                'success',
                'Contact updated successfully.'
            );
    }

    /**
     * Delete contact.
     */
    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();

        return redirect()
            ->route('crm.contacts.index')
            ->with(
                'success',
                'Contact deleted successfully.'
            );
    }

    /**
     * Validate contact.
     */
    private function validateContact(Request $request): array
    {
        return $request->validate([

            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id',
            ],

            'contact_type' => [
                'required',
                'in:primary,billing,project,site,technical,emergency,other',
            ],

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'job_title' => [
                'nullable',
                'string',
                'max:150',
            ],

            'department' => [
                'nullable',
                'string',
                'max:150',
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

            'address_line_1' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address_line_2' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'state' => [
                'nullable',
                'string',
                'size:2',
            ],

            'zip_code' => [
                'nullable',
                'string',
                'max:10',
            ],

            'country' => [
                'required',
                'string',
                'max:100',
            ],

            'is_primary' => [
                'nullable',
                'boolean',
            ],

            'preferred_contact_method' => [
                'nullable',
                'in:email,phone,mobile',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],
        ]);
    }
}