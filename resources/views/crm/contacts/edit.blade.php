@extends('layouts.admin')

@section('title', 'Edit Contact')

@section('page-title', 'Edit Contact')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>

            <h1 class="page-title">
                Edit Contact
            </h1>

            <p class="page-subtitle">
                Update contact and relationship information.
            </p>

        </div>

        <div class="page-header-actions">

            <a
                href="{{ route('crm.contacts.show', $contact) }}"
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
            action="{{ route('crm.contacts.update', $contact) }}"
        >

            @csrf
            @method('PUT')

            @include(
                'crm.contacts._form',
                [
                    'contact' => $contact
                ]
            )

        </form>

    </div>

</div>

@endsection