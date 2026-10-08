@extends('layouts.admin')

@section('title', 'Create Task')

@section('page-title', 'Create Task')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>
            <h1 class="page-title">
                Create Task
            </h1>

            <p class="page-subtitle">
                Create a new CRM task and assign it
                to a customer or property.
            </p>
        </div>

        <div>
            <a
                href="{{ route('crm.tasks.index') }}"
                class="btn btn-light"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to Tasks
            </a>
        </div>

    </div>

</div>

@if($errors->any())
    <div class="alert alert-danger">
        <strong>
            Please correct the following errors:
        </strong>

        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@include('crm.tasks._form')

@endsection