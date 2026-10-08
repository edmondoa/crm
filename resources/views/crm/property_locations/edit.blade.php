@extends('layouts.admin')

@section('title', 'Edit Property Location')

@section('page-title', 'Edit Property Location')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Edit Property Location
            </h1>

            <p class="page-subtitle">
                Update the address and location information.
            </p>

        </div>

        <div>

            <a
                href="{{ route(
                    'crm.property-locations.show',
                    $propertyLocation
                ) }}"
                class="btn btn-light"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>

        </div>

    </div>

</div>


<div class="card">

    <div class="card-body">

        <form
            method="POST"
            action="{{ route(
                'crm.property-locations.update',
                $propertyLocation
            ) }}"
        >

            @csrf
            @method('PUT')

            @include(
                'crm.property_locations._form',
                [
                    'propertyLocation' => $propertyLocation,
                    'selectedPropertyId' => $propertyLocation->property_id,
                ]
            )

        </form>

    </div>

</div>

@endsection