@extends('layouts.admin')

@section('title', 'Edit Activity')

@section('page-title', 'Edit Activity')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Edit Activity
            </h1>

            <p class="page-subtitle">
                Update the CRM activity information.
            </p>

        </div>

        <div>

            <a
                href="{{ route(
                    'crm.activities.show',
                    $activity
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
                'crm.activities.update',
                $activity
            ) }}"
        >

            @csrf
            @method('PUT')

            @include(
                'crm.activities._form',
                [
                    'activity' => $activity,
                    'selectedCustomerId' => $activity->customer_id,
                    'selectedPropertyId' => $activity->property_id,
                    'selectedContactId' => $activity->contact_id,
                    'selectedLocationId' => $activity->property_location_id,
                ]
            )

        </form>

    </div>

</div>

@endsection