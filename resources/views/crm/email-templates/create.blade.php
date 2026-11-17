@extends('layouts.admin')
@section('title', 'Create Email Template')
@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('crm.email-templates.index') }}" class="text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> 
            Email Templates
        </a>
        <h1 class="h3 mt-2 mb-1">Create email template</h1>
        <p class="text-muted mb-0">Build a reusable message and preview it before sending.</p>
</div>
@include('crm.email-templates.partials.alerts')
<form method="POST" action="{{ route('crm.email-templates.store') }}">
    @csrf @include('crm.email-templates.form')
    <div class="d-flex justify-content-end gap-2 mt-3">
        <a href="{{ route('crm.email-templates.index') }}" class="btn btn-light">Cancel</a>
        <button class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i> 
            Save template
        </button>
    </div>
</form>
</div>
@endsection
