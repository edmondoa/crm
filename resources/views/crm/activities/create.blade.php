@extends('layouts.admin')

@section('title', 'Add Activity')

@section('page-title', 'Add Activity')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Add Activity
            </h1>

            <p class="page-subtitle">
                Record a customer interaction, meeting, call, site visit, or note.
            </p>

        </div>

        <div>

            <a
                href="{{ route('crm.activities.index') }}"
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
            action="{{ route('crm.activities.store') }}"
        >

            @csrf

            @include(
                'crm.activities._form',
                [
                    'activity' => null
                ]
            )

        </form>

    </div>

</div>

@endsection