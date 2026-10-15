
@extends('layouts.admin')

@section('content')
@php
    $editing = isset($lead) && $lead;
    $action = $editing
        ? route('crm.leads.update', $lead)
        : route('crm.leads.store');
@endphp

<div class="container-fluid py-4">
    <h1 class="h3 mb-4">{{ $editing ? 'Edit Lead' : 'Create Lead' }}</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ $action }}">
                @csrf
                @if($editing)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    @foreach([
                        'first_name' => 'First Name',
                        'last_name' => 'Last Name',
                        'company_name' => 'Company Name',
                        'email' => 'Email',
                        'phone' => 'Phone',
                        'job_title' => 'Job Title',
                    ] as $field => $label)
                        <div class="col-md-6">
                            <label class="form-label" for="{{ $field }}">
                                {{ $label }}
                            </label>
                            <input id="{{ $field }}"
                                   name="{{ $field }}"
                                   class="form-control"
                                   value="{{ old($field, $lead?->$field) }}"
                                   @if($field === 'first_name') required @endif>
                        </div>
                    @endforeach

                    <div class="col-md-4">
                        <label class="form-label">Source</label>
                        <select name="source" class="form-select" required>
                            @foreach([
                                'website','referral','social_media',
                                'phone','walk_in','other'
                            ] as $value)
                                <option value="{{ $value }}"
                                    @selected(old('source', $lead?->source ?? 'other') === $value)>
                                    {{ str($value)->replace('_', ' ')->title() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            @foreach(['new','contacted','qualified','unqualified'] as $value)
                                <option value="{{ $value }}"
                                    @selected(old('status', $lead?->status ?? 'new') === $value)>
                                    {{ ucfirst($value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Rating</label>
                        <select name="rating" class="form-select" required>
                            @foreach(['cold','warm','hot'] as $value)
                                <option value="{{ $value }}"
                                    @selected(old('rating', $lead?->rating ?? 'warm') === $value)>
                                    {{ ucfirst($value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Assigned To</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    @selected((string) old('assigned_to', $lead?->assigned_to) === (string) $user->id)>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Follow-up Date</label>
                        <input type="date" name="follow_up_at"
                               class="form-control"
                               value="{{ old('follow_up_at', $lead?->follow_up_at?->format('Y-m-d')) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description / Requirements</label>
                        <textarea name="description" class="form-control" rows="4">{{ old('description', $lead?->description) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('crm.leads.index') }}"
                       class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">
                        {{ $editing ? 'Save Changes' : 'Create Lead' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
