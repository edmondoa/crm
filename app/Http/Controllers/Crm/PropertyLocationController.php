<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PropertyLocationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ): View {

        $locations = PropertyLocation::query()
            ->with('property.customer')

            ->search(
                $request->input('search')
            )

            ->when(
                $request->filled('property_id'),
                fn ($query) =>
                    $query->where(
                        'property_id',
                        $request->input('property_id')
                    )
            )

            ->when(
                $request->filled('type'),
                fn ($query) =>
                    $query->where(
                        'location_type',
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
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $properties = Property::query()
            ->with('customer')
            ->orderBy('name')
            ->get();

        $stats = [
            'total' => PropertyLocation::count(),

            'active' =>
                PropertyLocation::where(
                    'status',
                    'active'
                )->count(),

            'inactive' =>
                PropertyLocation::where(
                    'status',
                    'inactive'
                )->count(),

            'primary' =>
                PropertyLocation::where(
                    'is_primary',
                    true
                )->count(),
        ];

        return view(
            'crm.property_locations.index',
            compact(
                'locations',
                'properties',
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

        $properties = Property::query()
            ->with('customer')
            ->active()
            ->orderBy('name')
            ->get();

        $selectedPropertyId = $request->integer(
            'property_id'
        );

        return view(
            'crm.property_locations.create',
            compact(
                'properties',
                'selectedPropertyId'
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

        $validated = $this->validateLocation(
            $request
        );

        DB::transaction(function () use (&$validated) {

            if ($validated['is_primary'] ?? false) {

                PropertyLocation::query()
                    ->where(
                        'property_id',
                        $validated['property_id']
                    )
                    ->update([
                        'is_primary' => false,
                    ]);
            }

            $validated['location_code'] =
                $this->generateLocationCode();

            PropertyLocation::create(
                $validated
            );
        });

        $location = PropertyLocation::query()
            ->where(
                'location_code',
                $validated['location_code']
            )
            ->firstOrFail();

        return redirect()
            ->route(
                'crm.property-locations.show',
                $location
            )
            ->with(
                'success',
                'Property location created successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(
        PropertyLocation $propertyLocation
    ): View {

        $propertyLocation->load([
            'property.customer',
            'property.primaryContact',
        ]);

        return view(
            'crm.property_locations.show',
            compact('propertyLocation')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(
        PropertyLocation $propertyLocation
    ): View {

        $properties = Property::query()
            ->with('customer')
            ->active()
            ->orderBy('name')
            ->get();

        return view(
            'crm.property_locations.edit',
            compact(
                'propertyLocation',
                'properties'
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
        PropertyLocation $propertyLocation
    ): RedirectResponse {

        $validated = $this->validateLocation(
            $request
        );

        DB::transaction(function () use (
            &$validated,
            $propertyLocation
        ) {

            if ($validated['is_primary'] ?? false) {

                PropertyLocation::query()
                    ->where(
                        'property_id',
                        $validated['property_id']
                    )
                    ->where(
                        'id',
                        '!=',
                        $propertyLocation->id
                    )
                    ->update([
                        'is_primary' => false,
                    ]);
            }

            $propertyLocation->update(
                $validated
            );
        });

        return redirect()
            ->route(
                'crm.property-locations.show',
                $propertyLocation
            )
            ->with(
                'success',
                'Property location updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function destroy(
        PropertyLocation $propertyLocation
    ): RedirectResponse {

        $propertyId =
            $propertyLocation->property_id;

        $wasPrimary =
            $propertyLocation->is_primary;

        $propertyLocation->delete();

        /*
        |--------------------------------------------------------------------------
        | Promote another location if the primary location was deleted
        |--------------------------------------------------------------------------
        */

        if ($wasPrimary) {

            $replacement =
                PropertyLocation::query()
                    ->where(
                        'property_id',
                        $propertyId
                    )
                    ->where(
                        'status',
                        'active'
                    )
                    ->orderBy('id')
                    ->first();

            if ($replacement) {

                $replacement->update([
                    'is_primary' => true,
                ]);
            }
        }

        return redirect()
            ->route(
                'crm.properties.show',
                $propertyId
            )
            ->with(
                'success',
                'Property location deleted successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    private function validateLocation(
        Request $request
    ): array {

        $validated = $request->validate([

            'property_id' => [
                'required',
                'integer',
                'exists:properties,id',
            ],

            'location_name' => [
                'required',
                'string',
                'max:255',
            ],

            'location_type' => [
                'required',
                'in:office,warehouse,job_site,billing,mailing,residential,retail,other',
            ],

            'address_line_1' => [
                'required',
                'string',
                'max:255',
            ],

            'address_line_2' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'required',
                'string',
                'max:100',
            ],

            'state' => [
                'required',
                'string',
                'size:2',
            ],

            'zip_code' => [
                'required',
                'string',
                'max:10',
            ],

            'county' => [
                'nullable',
                'string',
                'max:100',
            ],

            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],

            'is_primary' => [
                'nullable',
                'boolean',
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

        $validated['state'] =
            strtoupper(
                $validated['state']
            );

        $validated['is_primary'] =
            $request->boolean('is_primary');

        return $validated;
    }

    /*
    |--------------------------------------------------------------------------
    | Location Code
    |--------------------------------------------------------------------------
    */

    private function generateLocationCode(): string
    {
        $lastLocation =
            PropertyLocation::query()
                ->whereNotNull('location_code')
                ->orderByDesc('id')
                ->first();

        if (!$lastLocation) {
            $number = 1;
        } else {

            $number = (int) preg_replace(
                '/\D/',
                '',
                $lastLocation->location_code
            );

            $number++;
        }

        return 'LOC-' .
            str_pad(
                $number,
                5,
                '0',
                STR_PAD_LEFT
            );
    }
}