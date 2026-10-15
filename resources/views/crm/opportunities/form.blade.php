
@extends('layouts.admin')

@section('content')
@php
    $editing = isset($opportunity) && $opportunity;
    $action = $editing
        ? route('crm.opportunities.update', $opportunity)
        : route('crm.opportunities.store');
@endphp

<div class="container-fluid py-4">
    <h1 class="h3 mb-4">
        {{ $editing ? 'Edit Opportunity' : 'Create Opportunity' }}
    </h1>

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
                    <div class="col-md-8">
                        <label class="form-label">Opportunity Name</label>
                        <input name="name" class="form-control" required
                               value="{{ old('name', $opportunity?->name) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Estimated Value</label>
                        <input type="number" name="estimated_value"
                               min="0" step="0.01" required
                               class="form-control"
                               value="{{ old('estimated_value', $opportunity?->estimated_value ?? 0) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Lead</label>
                        <select name="lead_id" class="form-select">
                            <option value="">No lead linked</option>
                            @foreach($leads as $lead)
                                <option value="{{ $lead->id }}"
                                    @selected((string) old('lead_id', $opportunity?->lead_id) === (string) $lead->id)>
                                    {{ $lead->full_name }} — {{ $lead->lead_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-select">
                            <option value="">No customer linked</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    @selected((string) old('customer_id', $opportunity?->customer_id) === (string) $customer->id)>
                                    {{ $customer->name ?? ('Customer #' . $customer->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Stage</label>
                        <select name="stage" class="form-select" required>
                            @foreach([
                                'qualification','needs_analysis','proposal',
                                'negotiation','won','lost'
                            ] as $stage)
                                <option value="{{ $stage }}"
                                    @selected(old('stage', $opportunity?->stage ?? 'qualification') === $stage)>
                                    {{ str($stage)->replace('_', ' ')->title() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Win Probability (%)</label>
                        <input type="number" name="probability" min="0"
                               max="100" required class="form-control"
                               value="{{ old('probability', $opportunity?->probability ?? 20) }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Expected Close Date</label>
                        <input type="date" name="expected_close_date"
                               class="form-control"
                               value="{{ old('expected_close_date', $opportunity?->expected_close_date?->format('Y-m-d')) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Assigned To</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    @selected((string) old('assigned_to', $opportunity?->assigned_to) === (string) $user->id)>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Reason for Loss</label>
                        <input name="loss_reason" class="form-control"
                               value="{{ old('loss_reason', $opportunity?->loss_reason) }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="4"
                                  class="form-control">{{ old('description', $opportunity?->description) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('crm.opportunities.index') }}"
                       class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">
                        {{ $editing ? 'Save Changes' : 'Create Opportunity' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
