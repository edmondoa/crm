@extends('layouts.admin')

@section('title', 'Add Property Location')

@section('page-title', 'Add Property Location')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Add Property Location
            </h1>

            <p class="page-subtitle">
                Add a physical address associated with a property.
            </p>

        </div>

        <div>

            <a
                href="{{ route('crm.property-locations.index') }}"
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
            action="{{ route('crm.property-locations.store') }}"
        >

            @csrf

            @include(
                'crm.property_locations._form',
                [
                    'propertyLocation' => null
                ]
            )

        </form>

    </div>

</div>

@endsection