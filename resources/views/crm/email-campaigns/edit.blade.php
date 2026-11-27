@extends('layouts.admin')

@section('title', 'Edit Email Campaign')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('crm.email-campaigns.index') }}" class="text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Email Campaigns
        </a>
        <h1 class="h3 mt-2 mb-1">Edit campaign plan</h1>
        <p class="text-muted mb-0">{{ $campaign->name }}</p>
    </div>

    @include('crm.email-templates.partials.alerts')

    <form method="POST" action="{{ route('crm.email-campaigns.update', $campaign) }}">
        @csrf @method('PUT')
        @include('crm.email-campaigns.form')
        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="{{ route('crm.email-campaigns.index') }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Save changes</button>
        </div>
    </form>
</div>
@endsection
