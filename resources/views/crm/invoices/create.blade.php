@extends('layouts.admin')

@section('title', 'Create Invoice')

@section('content')

<div class="crm-page">

    <div class="page-header mb-4">

        <div class="page-header-row">

            <div>

                <div class="page-breadcrumb mb-2">

                    <a href="{{ route('crm.invoices.index') }}">
                        Invoices
                    </a>

                    <i class="bi bi-chevron-right mx-1"></i>

                    <span>New Invoice</span>

                </div>

                <h1 class="page-title">
                    Create Invoice
                </h1>

                <p class="page-subtitle">
                    Create a new customer invoice.
                </p>

            </div>


            <div>

                <a href="{{ route('crm.invoices.index') }}"
                   class="btn btn-outline-secondary">

                    <i class="bi bi-arrow-left me-1"></i>
                    Back

                </a>

            </div>

        </div>

    </div>


    @if($errors->any())

        <div class="alert alert-danger">

            <div class="fw-semibold mb-2">
                Please correct the following errors:
            </div>

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <form method="POST"
          action="{{ route('crm.invoices.store') }}"
          id="invoiceForm">

        @csrf

        @include('crm.invoices._form', [
            'mode' => 'create',
            'invoice' => $invoice ?? null,
            'customers' => $customers,
            'properties' => $properties ?? collect(),
            'jobs' => $jobs ?? collect(),
            'estimates' => $estimates ?? collect(),
        ])

    </form>

</div>

@endsection

@push('styles')
<link rel="stylesheet"
      href="{{ asset('resources/css/invoices.css') }}">
@endpush

@push('scripts')
    @vite('resources/js/invoices.js')
@endpush