@extends('layouts.admin')

@section('title', 'Edit Invoice')

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

                    <a href="{{ route('crm.invoices.show', $invoice) }}">
                        {{ $invoice->invoice_number }}
                    </a>

                    <i class="bi bi-chevron-right mx-1"></i>

                    <span>Edit</span>

                </div>

                <h1 class="page-title">
                    Edit Invoice
                </h1>

                <p class="page-subtitle">
                    Update invoice information and line items.
                </p>

            </div>


            <div class="d-flex gap-2">

                <a href="{{ route('crm.invoices.show', $invoice) }}"
                   class="btn btn-outline-secondary">

                    <i class="bi bi-eye me-1"></i>
                    View

                </a>

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
          action="{{ route('crm.invoices.update', $invoice) }}"
          id="invoiceForm">

        @csrf
        @method('PUT')

        @include('crm.invoices._form', [
            'mode' => 'edit',
            'invoice' => $invoice,
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
      href="{{ asset('css/invoices.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/invoices.js') }}"></script>
@endpush