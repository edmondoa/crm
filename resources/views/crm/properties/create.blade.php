@extends('layouts.admin')

@section('title', 'Add Property')

@section('page-title', 'Add Property')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Add Property
            </h1>

            <p class="page-subtitle">
                Create a property and associate it with a CRM customer.
            </p>

        </div>

    </div>

</div>


<div class="card">

    <div class="card-body">

        <form
            method="POST"
            action="{{ route('crm.properties.store') }}"
        >

            @csrf

            @include(
                'crm.properties._form',
                [
                    'property' => null
                ]
            )

        </form>

    </div>

</div>

@endsection