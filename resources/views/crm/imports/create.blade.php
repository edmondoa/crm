@extends('layouts.admin')

@section('title', "Import {$label}")
@section('page-title', "Import {$label}")

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Import {{ $label }}</h1>
            <p class="text-muted mb-0">Upload a CSV or Excel workbook to add records in bulk.</p>
        </div>
        <a href="{{ route($listRoute) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to {{ $label }}
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-1">The file could not be imported.</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('import_result'))
        @php($result = session('import_result'))
        <div class="alert {{ $result['imported'] > 0 ? 'alert-success' : 'alert-warning' }}" role="status">
            Imported {{ number_format($result['imported']) }} {{ strtolower($label) }};
            skipped {{ number_format($result['skipped']) }} {{ $result['skipped'] === 1 ? 'row' : 'rows' }}.
        </div>

        @if (!empty($result['errors']))
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-0">Rows skipped</h2>
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($result['errors'] as $rowError)
                        <li class="list-group-item">{{ $rowError }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif

    <div class="row g-3">
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-1">Upload file</h2>
                    <div class="small text-muted">Maximum file size: 10 MB. Import up to 2,000 non-empty rows.</div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route($storeRoute) }}" enctype="multipart/form-data">
                        @csrf
                        <label for="import-file" class="form-label">CSV or Excel file</label>
                        <input
                            id="import-file"
                            type="file"
                            name="file"
                            class="form-control @error('file') is-invalid @enderror"
                            accept=".csv,.xlsx"
                            required
                        >
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <p class="form-text">Supported formats: .csv and .xlsx. The first non-empty row must contain column headers.</p>
                        <button type="submit" class="btn btn-primary mt-2">
                            <i class="bi bi-upload me-1"></i> Import {{ $label }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-1">Prepare your spreadsheet</h2>
                    <div class="small text-muted">Use the template to match the CRM fields.</div>
                </div>
                <div class="card-body">
                    <a href="{{ route($templateRoute) }}" class="btn btn-outline-primary mb-3">
                        <i class="bi bi-download me-1"></i> Download CSV template
                    </a>
                    <p class="small text-muted mb-2">Required columns:</p>
                    <ul class="small mb-3">
                        @foreach ($requiredColumns as $column)
                            <li><code>{{ $column }}</code></li>
                        @endforeach
                    </ul>
                    <p class="small text-muted mb-2">Available columns:</p>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach ($headers as $header)
                            <code class="border rounded px-2 py-1">{{ $header }}</code>
                        @endforeach
                    </div>
                    <hr>
                    <p class="small text-muted mb-0">
                        Existing {{ strtolower($label) }} with a matching unique code/number, and duplicate identifiers in the uploaded file, are skipped. Existing records are never overwritten. Invalid rows are skipped and listed after import.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
