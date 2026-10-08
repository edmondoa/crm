<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\CrmSetting;
use App\Models\Property;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PropertyController extends Controller
{
    /**
     * Display properties.
     */
    public function index(Request $request): View
    {
        $properties = Property::query()
            ->with([
                'customer',
                'primaryContact',
            ])
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
                        'property_type',
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

        $customers = Customer::query()
            ->orderBy('company_name')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $stats = [
            'total' => Property::count(),

            'active' => Property::where(
                'status',
                'active'
            )->count(),

            'inactive' => Property::where(
                'status',
                'inactive'
            )->count(),

            'commercial' => Property::where(
                'property_type',
                'commercial'
            )->count(),
        ];

        return view(
            'crm.properties.index',
            compact(
                'properties',
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

        $selectedCustomerId = $request->integer(
            'customer_id'
        );

        $contacts = collect();

        if ($selectedCustomerId) {
            $contacts = Contact::query()
                ->where(
                    'customer_id',
                    $selectedCustomerId
                )
                ->active()
                ->orderByDesc('is_primary')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();
        }

        return view(
            'crm.properties.create',
            compact(
                'customers',
                'contacts',
                'selectedCustomerId'
            )
        );
    }

    /**
     * Store property.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProperty($request);

        DB::transaction(function () use (&$validated) {

            $validated['property_code'] =
                $this->generatePropertyCode();

            $this->validatePrimaryContact(
                $validated['customer_id'],
                $validated['primary_contact_id'] ?? null
            );

            Property::create($validated);
        });

        $property = Property::query()
            ->where(
                'property_code',
                $validated['property_code']
            )
            ->firstOrFail();

        return redirect()
            ->route(
                'crm.properties.show',
                $property
            )
            ->with(
                'success',
                'Property created successfully.'
            );
    }

    /**
     * Display property.
     */
    public function show(Property $property): View
    {
        $property->load([
            'customer',
            'primaryContact',
            'locations',
            'activities.propertyLocation',
            'tasks' => function ($query) {
                $query
                    ->with('contact')
                    ->latest('due_at')
                    ->limit(10);
            },
             'jobs' => function ($query) {
                $query
                    ->with([
                        'contact',
                        'propertyLocation',
                    ])
                    ->latest('scheduled_start_at')
                    ->limit(10);
            },
        ]);

        return view(
            'crm.properties.show',
            compact('property')
        );
    }

    /**
     * Show edit form.
     */
    public function edit(Property $property): View
    {
        $customers = Customer::query()
            ->active()
            ->orderBy('company_name')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $contacts = Contact::query()
            ->where(
                'customer_id',
                $property->customer_id
            )
            ->active()
            ->orderByDesc('is_primary')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view(
            'crm.properties.edit',
            compact(
                'property',
                'customers',
                'contacts'
            )
        );
    }

    /**
     * Update property.
     */
    public function update(
        Request $request,
        Property $property
    ): RedirectResponse {

        $validated = $this->validateProperty(
            $request
        );

        $this->validatePrimaryContact(
            $validated['customer_id'],
            $validated['primary_contact_id'] ?? null
        );

        $property->update($validated);

        return redirect()
            ->route(
                'crm.properties.show',
                $property
            )
            ->with(
                'success',
                'Property updated successfully.'
            );
    }

    /**
     * Delete property.
     */
    public function destroy(
        Property $property
    ): RedirectResponse {

        $property->delete();

        return redirect()
            ->route('crm.properties.index')
            ->with(
                'success',
                'Property deleted successfully.'
            );
    }

    /**
     * Validate property.
     */
    private function validateProperty(
        Request $request
    ): array {

        return $request->validate([

            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id',
            ],

            'primary_contact_id' => [
                'nullable',
                'integer',
                'exists:contacts,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'property_type' => [
                'required',
                'in:residential,commercial,industrial,office,retail,warehouse,multi_family,land,other',
            ],

            'description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);
    }

    /**
     * Ensure selected contact belongs to customer.
     */
    private function validatePrimaryContact(
        int $customerId,
        ?int $contactId
    ): void {

        if (!$contactId) {
            return;
        }

        $exists = Contact::query()
            ->where('id', $contactId)
            ->where('customer_id', $customerId)
            ->exists();

        if (!$exists) {
            abort(
                422,
                'The selected contact does not belong to the selected customer.'
            );
        }
    }

    /**
     * Generate property code.
     */
    private function generatePropertyCode(): string
    {
        $settings = CrmSetting::active();

        $prefix = $settings->property_code_prefix;

        $lastProperty = Property::query()
            ->whereNotNull('property_code')
            ->orderByDesc('id')
            ->first();

        if (!$lastProperty) {
            $number = 1;
        } else {

            $lastCode = $lastProperty->property_code;

            $number = (int) preg_replace(
                '/\D/',
                '',
                str_replace(
                    $prefix,
                    '',
                    $lastCode
                )
            );

            $number++;
        }

        return $prefix .
            str_pad(
                $number,
                5,
                '0',
                STR_PAD_LEFT
            );
    }

    public function byProperty(Property $property) {

        $locations = $property
            ->locations()
            ->active()
            ->orderByDesc('is_primary')
            ->orderBy('location_name')
            ->get([
                'id',
                'location_name',
                'city',
                'state',
                'zip_code',
            ]);

        return response()->json(
            $locations
        );
    }
}