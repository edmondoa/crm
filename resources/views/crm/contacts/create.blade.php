@extends('layouts.admin')

@section('title', 'Add Contact')

@section('page-title', 'Add Contact')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Add Contact
            </h1>

            <p class="page-subtitle">
                Create a new contact for a CRM customer.
            </p>

        </div>

    </div>

</div>


<div class="card">

    <div class="card-body">

        <form
            method="POST"
            action="{{ route('crm.contacts.store') }}"
        >

            @csrf

            @include(
                'crm.contacts._form',
                [
                    'contact' => null
                ]
            )

        </form>

    </div>

</div>

@endsection