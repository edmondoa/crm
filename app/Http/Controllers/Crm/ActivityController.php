<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\Property;
use App\Models\PropertyLocation;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ): View {

        $activities = Activity::query()

            ->with([
                'customer',
                'contact',
                'property',
                'propertyLocation',
            ])

            ->search(
                $request->input('search')
            )

            ->when(
                $request->filled('type'),
                fn ($query) =>
                    $query->where(
                        'activity_type',
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

            ->when(
                $request->filled('priority'),
                fn ($query) =>
                    $query->where(
                        'priority',
                        $request->input('priority')
                    )
            )

            ->orderByRaw(
                "CASE
                    WHEN status = 'planned' THEN 0
                    WHEN status = 'completed' THEN 1
                    ELSE 2
                END"
            )

            ->orderByDesc('scheduled_at')
            ->orderByDesc('id')

            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' =>
                Activity::count(),

            'planned' =>
                Activity::where(
                    'status',
                    'planned'
                )->count(),

            'completed' =>
                Activity::where(
                    'status',
                    'completed'
                )->count(),

            'high_priority' =>
                Activity::where(
                    'priority',
                    'high'
                )
                ->where(
                    'status',
                    '!=',
                    'completed'
                )
                ->count(),
        ];

        return view(
            'crm.activities.index',
            compact(
                'activities',
                'stats'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(
        Request $request
    ): View {

        $customers = Customer::query()
            ->active()
            ->orderBy('company_name')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $properties = Property::query()
            ->with('customer')
            ->active()
            ->orderBy('name')
            ->get();

        $selectedCustomerId =
            $request->integer(
                'customer_id'
            );

        $selectedPropertyId =
            $request->integer(
                'property_id'
            );

        $selectedContactId =
            $request->integer(
                'contact_id'
            );

        $selectedLocationId =
            $request->integer(
                'property_location_id'
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

        $locations = collect();

        if ($selectedPropertyId) {

            $locations = PropertyLocation::query()
                ->where(
                    'property_id',
                    $selectedPropertyId
                )
                ->active()
                ->orderByDesc('is_primary')
                ->orderBy('location_name')
                ->get();
        }

        return view(
            'crm.activities.create',
            compact(
                'customers',
                'properties',
                'contacts',
                'locations',
                'selectedCustomerId',
                'selectedPropertyId',
                'selectedContactId',
                'selectedLocationId'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request
    ): RedirectResponse {

        $validated =
            $this->validateActivity(
                $request
            );

        $this->validateRelationships(
            $validated
        );

        $activity = DB::transaction(
            function () use (&$validated) {

                if (
                    ($validated['status'] ?? null)
                    === 'completed'
                    &&
                    empty($validated['completed_at'])
                ) {
                    $validated['completed_at'] =
                        now();
                }

                return Activity::create(
                    $validated
                );
            }
        );

        return redirect()
            ->route(
                'crm.activities.show',
                $activity
            )
            ->with(
                'success',
                'Activity created successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(
        Activity $activity
    ): View {

        $activity->load([
            'customer',
            'contact',
            'property',
            'propertyLocation',
        ]);

        return view(
            'crm.activities.show',
            compact('activity')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(
        Activity $activity
    ): View {

        $customers = Customer::query()
            ->active()
            ->orderBy('company_name')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $properties = Property::query()
            ->with('customer')
            ->active()
            ->orderBy('name')
            ->get();

        $contacts = collect();

        if ($activity->customer_id) {

            $contacts = Contact::query()
                ->where(
                    'customer_id',
                    $activity->customer_id
                )
                ->active()
                ->orderByDesc('is_primary')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();
        }

        $locations = collect();

        if ($activity->property_id) {

            $locations = PropertyLocation::query()
                ->where(
                    'property_id',
                    $activity->property_id
                )
                ->active()
                ->orderByDesc('is_primary')
                ->orderBy('location_name')
                ->get();
        }

        return view(
            'crm.activities.edit',
            compact(
                'activity',
                'customers',
                'properties',
                'contacts',
                'locations'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Activity $activity
    ): RedirectResponse {

        $validated =
            $this->validateActivity(
                $request
            );

        $this->validateRelationships(
            $validated
        );

        if (
            ($validated['status'] ?? null)
            === 'completed'
            &&
            empty($validated['completed_at'])
        ) {
            $validated['completed_at'] =
                now();
        }

        $activity->update(
            $validated
        );

        return redirect()
            ->route(
                'crm.activities.show',
                $activity
            )
            ->with(
                'success',
                'Activity updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Activity $activity
    ): RedirectResponse {

        $activity->delete();

        return redirect()
            ->route(
                'crm.activities.index'
            )
            ->with(
                'success',
                'Activity deleted successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Complete Activity
    |--------------------------------------------------------------------------
    */

    public function complete(
        Activity $activity
    ): RedirectResponse {

        $activity->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Activity marked as completed.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    private function validateActivity(
        Request $request
    ): array {

        return $request->validate([

            'customer_id' => [
                'nullable',
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
            ],

            'property_location_id' => [
                'nullable',
                'integer',
                'exists:property_locations,id',
            ],

            'activity_type' => [
                'required',
                'in:call,email,meeting,site_visit,note,sms,other',
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

            'scheduled_at' => [
                'nullable',
                'date',
            ],

            'completed_at' => [
                'nullable',
                'date',
            ],

            'status' => [
                'required',
                'in:planned,completed,cancelled',
            ],

            'priority' => [
                'required',
                'in:low,normal,high',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationship Validation
    |--------------------------------------------------------------------------
    */

    private function validateRelationships(
        array $validated
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Contact belongs to Customer
        |--------------------------------------------------------------------------
        */

        if (
            !empty($validated['contact_id'])
        ) {

            $query =
                Contact::query()
                    ->where(
                        'id',
                        $validated['contact_id']
                    );

            if (
                !empty($validated['customer_id'])
            ) {

                $query->where(
                    'customer_id',
                    $validated['customer_id']
                );
            }

            if (!$query->exists()) {

                abort(
                    422,
                    'The selected contact does not belong to the selected customer.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Property belongs to Customer
        |--------------------------------------------------------------------------
        */

        if (
            !empty($validated['property_id'])
            &&
            !empty($validated['customer_id'])
        ) {

            $exists =
                Property::query()
                    ->where(
                        'id',
                        $validated['property_id']
                    )
                    ->where(
                        'customer_id',
                        $validated['customer_id']
                    )
                    ->exists();

            if (!$exists) {

                abort(
                    422,
                    'The selected property does not belong to the selected customer.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Location belongs to Property
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $validated['property_location_id']
            )
        ) {

            $query =
                PropertyLocation::query()
                    ->where(
                        'id',
                        $validated['property_location_id']
                    );

            if (
                !empty($validated['property_id'])
            ) {

                $query->where(
                    'property_id',
                    $validated['property_id']
                );
            }

            if (!$query->exists()) {

                abort(
                    422,
                    'The selected location does not belong to the selected property.'
                );
            }
        }
    }
}