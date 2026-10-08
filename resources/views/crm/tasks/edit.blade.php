@extends('layouts.admin')

@section('title', 'Edit Task')

@section('page-title', 'Edit Task')

@section('content')

<div class="page-header">

    <div class="page-header-row">

        <div>
            <h1 class="page-title">
                Edit Task
            </h1>

            <p class="page-subtitle">
                Update task information and status.
            </p>
        </div>

        <div>
            <a
                href="{{ route('crm.tasks.show', $task) }}"
                class="btn btn-light"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to Task
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

                <li>
                    {{ $error }}
                </li>

            @endforeach

        </ul>

    </div>
@endif

@include('crm.tasks._form')

@endsection