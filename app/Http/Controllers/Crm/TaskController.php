<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\CrmSetting;
use App\Models\Customer;
use App\Models\Property;
use App\Models\PropertyLocation;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::query()
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

        if ($request->filled('task_type')) {
            $query->where(
                'task_type',
                $request->task_type
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
                'due_at',
                '>=',
                $request->from_date
            );
        }

        if ($request->filled('to_date')) {
            $query->whereDate(
                'due_at',
                '<=',
                $request->to_date
            );
        }

        $tasks = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'completed' THEN 3
                    WHEN status = 'cancelled' THEN 4
                    ELSE 1
                END
            ")
            ->orderByRaw("
                CASE
                    WHEN due_at IS NULL THEN 1
                    ELSE 0
                END
            ")
            ->orderBy('due_at')
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
            'total' => Task::count(),

            'pending' => Task::where(
                'status',
                'pending'
            )->count(),

            'in_progress' => Task::where(
                'status',
                'in_progress'
            )->count(),

            'completed' => Task::where(
                'status',
                'completed'
            )->count(),

            'overdue' => Task::open()
                ->whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->count(),
        ];

        return view(
            'crm.tasks.index',
            compact(
                'tasks',
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
            $selectedCustomer = Customer::findOrFail(
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
                ->where('id', $request->property_id)
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
                    ->where('status', 'active')
                    ->orderByDesc('is_primary')
                    ->orderBy('location_name')
                    ->get();
            }
        }

        return view(
            'crm.tasks.create',
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
        $validated = $this->validateTask($request);

        $this->validateRelationships($validated);

        $task = DB::transaction(function () use ($validated) {

            $settings = CrmSetting::active();

            $task = Task::create([
                ...$validated,
                'task_code' => 'TMP-' . Str::uuid(),
            ]);

            $task->updateQuietly([
                'task_code' =>
                    $settings->task_code_prefix .
                    str_pad(
                        $task->id,
                        5,
                        '0',
                        STR_PAD_LEFT
                    ),
            ]);

            return $task;
        });

        return redirect()
            ->route('crm.tasks.show', $task)
            ->with(
                'success',
                'Task created successfully.'
            );
    }

    public function show(Task $task)
    {
        $task->load([
            'customer',
            'contact',
            'property',
            'propertyLocation',
        ]);

        return view(
            'crm.tasks.show',
            compact('task')
        );
    }

    public function edit(Task $task)
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

        $task->load([
            'customer',
            'contact',
            'property',
            'propertyLocation',
        ]);

        $contacts = $task->customer
            ? $task->customer
                ->contacts()
                ->active()
                ->orderByDesc('is_primary')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get()
            : collect();

        $properties = $task->customer
            ? $task->customer
                ->properties()
                ->active()
                ->orderBy('name')
                ->get()
            : collect();

        $locations = $task->property
            ? PropertyLocation::query()
                ->where(
                    'property_id',
                    $task->property->id
                )
                ->where('status', 'active')
                ->orderByDesc('is_primary')
                ->orderBy('location_name')
                ->get()
            : collect();

        return view(
            'crm.tasks.edit',
            compact(
                'task',
                'customers',
                'contacts',
                'properties',
                'locations'
            )
        );
    }

    public function update(
        Request $request,
        Task $task
    ) {
        $validated = $this->validateTask($request);

        $this->validateRelationships($validated);

        /*
         * Automatically maintain completed_at.
         */
        if (
            $validated['status'] === 'completed' &&
            !$task->completed_at
        ) {
            $validated['completed_at'] = now();
        }

        if (
            $validated['status'] !== 'completed'
        ) {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return redirect()
            ->route('crm.tasks.show', $task)
            ->with(
                'success',
                'Task updated successfully.'
            );
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()
            ->route('crm.tasks.index')
            ->with(
                'success',
                'Task deleted successfully.'
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
                        'name' => $contact->display_name,
                        'job_title' => $contact->job_title,
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
        $locations = PropertyLocation::query()
            ->where(
                'property_id',
                $property->id
            )
            ->where('status', 'active')
            ->orderByDesc('is_primary')
            ->orderBy('location_name')
            ->get();

        return response()->json(
            $locations->map(
                function ($location) {
                    return [
                        'id' => $location->id,
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

    private function validateTask(
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

            'task_type' => [
                'required',
                'in:' . implode(',', [
                    'follow_up',
                    'call',
                    'email',
                    'meeting',
                    'site_visit',
                    'estimate',
                    'proposal',
                    'document',
                    'other',
                ]),
            ],

            'subject' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'due_at' => [
                'nullable',
                'date',
            ],

            'priority' => [
                'required',
                'in:low,normal,high,urgent',
            ],

            'status' => [
                'required',
                'in:pending,in_progress,completed,cancelled',
            ],

            'outcome' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);
    }

    private function validateRelationships(
        array $data
    ): void {
        $customerId = $data['customer_id'];

        if (!empty($data['contact_id'])) {
            $valid = Contact::query()
                ->where('id', $data['contact_id'])
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

        if (!empty($data['property_id'])) {
            $valid = Property::query()
                ->where('id', $data['property_id'])
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

        if (!empty($data['property_location_id'])) {
            $valid = PropertyLocation::query()
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
                    function ($query) use ($customerId) {
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
            implode(', ', array_filter([
                $location->address_line_1,
                $location->address_line_2,
                $location->city,
                $location->state,
                $location->zip_code,
            ]))
        );
    }
}