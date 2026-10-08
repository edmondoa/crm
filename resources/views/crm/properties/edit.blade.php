@extends('layouts.admin')

@section('title', 'Edit Property')

@section('page-title', 'Edit Property')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Edit Property
            </h1>

            <p class="page-subtitle">
                Update property information and customer relationship.
            </p>

        </div>

        <div class="page-header-actions">

            <a
                href="{{ route('crm.properties.show', $property) }}"
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
            action="{{ route('crm.properties.update', $property) }}"
        >

            @csrf
            @method('PUT')

            @include(
                'crm.properties._form',
                [
                    'property' => $property
                ]
            )

        </form>

    </div>

</div>

@endsection