<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\CrmSetting;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Property;
use App\Models\PropertyLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::query()
            ->with([
                'customer',
                'contact',
                'property',
                'propertyLocation',
            ])
            ->search($request->search);

        if ($request->filled('customer_id')) {
            $query->where(
                'customer_id',
                $request->customer_id
            );
        }

        if ($request->filled('job_type')) {
            $query->where(
                'job_type',
                $request->job_type
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('priority')) {
            $query->where(
                'priority',
                $request->priority
            );
        }

        if ($request->filled('from_date')) {
            $query->whereDate(
                'scheduled_start_at',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {
            $query->whereDate(
                'scheduled_start_at',
                '<=',
                $request->to_date
            );
        }

        $jobs = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'completed' THEN 4
                    WHEN status = 'cancelled' THEN 5
                    ELSE 1
                END
            ")
            ->orderByRaw("
                CASE
                    WHEN scheduled_start_at IS NULL THEN 1
                    ELSE 0
                END
            ")
            ->orderBy('scheduled_start_at')
            ->paginate(15)
            ->withQueryString();

        $customers = Customer::query()
            ->active()
            ->orderByRaw("
                CASE
                    WHEN customer_type = 'company'
                    THEN company_name
                    ELSE last_name
                END
            ")
            ->get();

        $stats = [
            'total' => Job::count(),

            'open' => Job::open()->count(),

            'scheduled' => Job::where(
                'status',
                'scheduled'
            )->count(),

            'in_progress' => Job::where(
                'status',
                'in_progress'
            )->count(),

            'completed' => Job::where(
                'status',
                'completed'
            )->count(),

            'overdue' => Job::open()
                ->whereNotNull('scheduled_end_at')
                ->where(
                    'scheduled_end_at',
                    '<',
                    now()
                )
                ->count(),
        ];

        return view(
            'crm.jobs.index',
            compact(
                'jobs',
                'customers',
                'stats'
            )
        );
    }

    public function create(Request $request)
    {
        $customers = Customer::query()
            ->active()
            ->orderByRaw("
                CASE
                    WHEN customer_type = 'company'
                    THEN company_name
                    ELSE last_name
                END
            ")
            ->get();

        $selectedCustomer = null;
        $contacts = collect();
        $properties = collect();
        $locations = collect();

        if ($request->filled('customer_id')) {

            $selectedCustomer =
                Customer::findOrFail(
                    $request->customer_id
                );

            $contacts = $selectedCustomer
                ->contacts()
                ->active()
                ->orderByDesc('is_primary')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();

            $properties = $selectedCustomer
                ->properties()
                ->active()
                ->orderBy('name')
                ->get();
        }

        if ($request->filled('property_id')) {

            $property = Property::query()
                ->where(
                    'id',
                    $request->property_id
                )
                ->when(
                    $selectedCustomer,
                    fn ($query) =>
                        $query->where(
                            'customer_id',
                            $selectedCustomer->id
                        )
                )
                ->first();

            if ($property) {

                $locations = PropertyLocation::query()
                    ->where(
                        'property_id',
                        $property->id
                    )
                    ->where(
                        'status',
                        'active'
                    )
                    ->orderByDesc('is_primary')
                    ->orderBy('location_name')
                    ->get();
            }
        }

        return view(
            'crm.jobs.create',
            compact(
                'customers',
                'selectedCustomer',
                'contacts',
                'properties',
                'locations'
            )
        );
    }

    public function store(Request $request)
    {
        $validated = $this->validateJob(
            $request
        );

        $this->validateRelationships(
            $validated
        );

        $job = DB::transaction(
            function () use ($validated) {

                $settings =
                    CrmSetting::active();

                $job = Job::create([
                    ...$validated,

                    'job_code' =>
                        'TMP-' . Str::uuid(),

                    /*
                     * Work order number is generated
                     * from the database ID after creation.
                     */
                    'work_order_number' => null,
                ]);

                $code =
                    $settings->job_code_prefix .
                    str_pad(
                        $job->id,
                        5,
                        '0',
                        STR_PAD_LEFT
                    );

                $job->updateQuietly([
                    'job_code' => $code,
                    'work_order_number' => $code,
                ]);

                return $job;
            }
        );

        return redirect()
            ->route(
                'crm.jobs.show',
                $job
            )
            ->with(
                'success',
                'Job / Work Order created successfully.'
            );
    }

    public function show(Job $job)
    {
        $job->load([
            'customer',
            'contact',
            'property',
            'propertyLocation',
            'assignments.employee',
        ]);

        return view(
            'crm.jobs.show',
            compact('job')
        );
    }

    public function edit(Job $job)
    {
        $customers = Customer::query()
            ->active()
            ->orderByRaw("
                CASE
                    WHEN customer_type = 'company'
                    THEN company_name
                    ELSE last_name
                END
            ")
            ->get();

        $job->load([
            'customer',
            'contact',
            'property',
            'propertyLocation',
        ]);

        $contacts = $job->customer
            ? $job->customer
                ->contacts()
                ->active()
                ->orderByDesc('is_primary')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get()
            : collect();

        $properties = $job->customer
            ? $job->customer
                ->properties()
                ->active()
                ->orderBy('name')
                ->get()
            : collect();

        $locations = $job->property
            ? PropertyLocation::query()
                ->where(
                    'property_id',
                    $job->property->id
                )
                ->where(
                    'status',
                    'active'
                )
                ->orderByDesc('is_primary')
                ->orderBy('location_name')
                ->get()
            : collect();

        return view(
            'crm.jobs.edit',
            compact(
                'job',
                'customers',
                'contacts',
                'properties',
                'locations'
            )
        );
    }

    public function update(
        Request $request,
        Job $job
    ) {
        $validated = $this->validateJob(
            $request
        );

        $this->validateRelationships(
            $validated
        );

        /*
         * Maintain timestamps automatically.
         */
        if (
            $validated['status'] ===
                'in_progress' &&
            !$job->started_at
        ) {
            $validated['started_at'] = now();
        }

        if (
            $validated['status'] ===
                'completed' &&
            !$job->completed_at
        ) {
            $validated['completed_at'] = now();

            /*
             * If completed without a start
             * timestamp, preserve a sensible
             * operational history.
             */
            if (!$job->started_at) {
                $validated['started_at'] =
                    $job->created_at ?: now();
            }
        }

        if (
            $validated['status'] !==
                'completed'
        ) {
            $validated['completed_at'] = null;
        }

        if (
            $validated['status'] ===
                'draft'
        ) {
            $validated['started_at'] = null;
            $validated['completed_at'] = null;
        }

        $job->update($validated);

        return redirect()
            ->route(
                'crm.jobs.show',
                $job
            )
            ->with(
                'success',
                'Job / Work Order updated successfully.'
            );
    }

    public function destroy(Job $job)
    {
        /*
         * In later phases, jobs may have labor,
         * material and invoice records.
         *
         * For now deletion is allowed.
         */
        $job->delete();

        return redirect()
            ->route('crm.jobs.index')
            ->with(
                'success',
                'Job / Work Order deleted successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX: Customer context
    |--------------------------------------------------------------------------
    */

    public function customerContext(
        Customer $customer
    ) {
        $contacts = $customer
            ->contacts()
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

        $properties = $customer
            ->properties()
            ->active()
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'property_code',
                'property_type',
            ]);

        return response()->json([
            'contacts' => $contacts->map(
                function ($contact) {
                    return [
                        'id' => $contact->id,
                        'name' =>
                            $contact->display_name,
                        'job_title' =>
                            $contact->job_title,
                    ];
                }
            ),

            'properties' => $properties->map(
                function ($property) {
                    return [
                        'id' => $property->id,
                        'name' => $property->name,
                        'property_code' =>
                            $property->property_code,
                        'property_type' =>
                            $property->property_type_label,
                    ];
                }
            ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX: Property locations
    |--------------------------------------------------------------------------
    */

    public function propertyLocations(
        Property $property
    ) {
        $locations =
            PropertyLocation::query()
                ->where(
                    'property_id',
                    $property->id
                )
                ->where(
                    'status',
                    'active'
                )
                ->orderByDesc('is_primary')
                ->orderBy('location_name')
                ->get();

        return response()->json(
            $locations->map(
                function ($location) {

                    return [
                        'id' =>
                            $location->id,

                        'name' =>
                            $location->location_name,

                        'type' =>
                            $location->location_type,

                        'address' =>
                            $this->formatLocationAddress(
                                $location
                            ),
                    ];
                }
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    private function validateJob(
        Request $request
    ): array {

        return $request->validate([

            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id',
            ],

            'contact_id' => [
                'nullable',
                'integer',
                'exists:contacts,id',
            ],

            'property_id' => [
                'nullable',
                'integer',
                'exists:properties,id',
                'required_with:property_location_id',
            ],

            'property_location_id' => [
                'nullable',
                'integer',
                'exists:property_locations,id',
            ],

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'job_type' => [
                'required',
                'in:' . implode(',', [
                    'service',
                    'repair',
                    'maintenance',
                    'installation',
                    'inspection',
                    'replacement',
                    'construction',
                    'renovation',
                    'emergency',
                    'other',
                ]),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'scope_of_work' => [
                'nullable',
                'string',
            ],

            'scheduled_start_at' => [
                'nullable',
                'date',
            ],

            'scheduled_end_at' => [
                'nullable',
                'date',
                'after_or_equal:scheduled_start_at',
            ],

            'priority' => [
                'required',
                'in:low,normal,high,urgent',
            ],

            'status' => [
                'required',
                'in:draft,scheduled,in_progress,on_hold,completed,cancelled',
            ],

            'estimated_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'approved_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'customer_notes' => [
                'nullable',
                'string',
            ],

            'internal_notes' => [
                'nullable',
                'string',
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationship validation
    |--------------------------------------------------------------------------
    */

    private function validateRelationships(
        array $data
    ): void {

        $customerId =
            $data['customer_id'];

        /*
         * Contact must belong to customer.
         */
        if (!empty($data['contact_id'])) {

            $valid = Contact::query()
                ->where(
                    'id',
                    $data['contact_id']
                )
                ->where(
                    'customer_id',
                    $customerId
                )
                ->exists();

            if (!$valid) {
                abort(
                    422,
                    'The selected contact does not belong to the selected customer.'
                );
            }
        }

        /*
         * Property must belong to customer.
         */
        if (!empty($data['property_id'])) {

            $valid = Property::query()
                ->where(
                    'id',
                    $data['property_id']
                )
                ->where(
                    'customer_id',
                    $customerId
                )
                ->exists();

            if (!$valid) {
                abort(
                    422,
                    'The selected property does not belong to the selected customer.'
                );
            }
        }

        /*
         * Location must belong to the
         * selected property and customer.
         */
        if (!empty(
            $data['property_location_id']
        )) {

            $valid =
                PropertyLocation::query()
                    ->where(
                        'id',
                        $data['property_location_id']
                    )
                    ->where(
                        'property_id',
                        $data['property_id']
                    )
                    ->whereHas(
                        'property',
                        function ($query) use (
                            $customerId
                        ) {
                            $query->where(
                                'customer_id',
                                $customerId
                            );
                        }
                    )
                    ->exists();

            if (!$valid) {
                abort(
                    422,
                    'The selected property location is not valid for this customer.'
                );
            }
        }
    }

    private function formatLocationAddress(
        PropertyLocation $location
    ): string {

        return trim(
            implode(
                ', ',
                array_filter([
                    $location->address_line_1,
                    $location->address_line_2,
                    $location->city,
                    $location->state,
                    $location->zip_code,
                ])
            )
        );
    }
}