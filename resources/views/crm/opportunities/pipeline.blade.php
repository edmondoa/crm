
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Sales Pipeline</h1>
            <p class="text-muted mb-0">Monitor active construction opportunities.</p>
        </div>
        <a href="{{ route('crm.opportunities.create') }}"
           class="btn btn-primary">New Opportunity</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Open Pipeline Value</div>
                    <div class="fs-3 fw-bold">
                        {{ number_format($pipelineValue, 2) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted">Weighted Forecast</div>
                    <div class="fs-3 fw-bold text-primary">
                        {{ number_format($weightedValue, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="pipeline-board">
        @foreach($stages as $stage)
            @continue(in_array($stage, ['won', 'lost'], true))

            <section class="pipeline-column">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">
                        {{ str($stage)->replace('_', ' ')->title() }}
                    </h6>
                    <span class="badge bg-secondary">
                        {{ ($opportunities->get($stage) ?? collect())->count() }}
                    </span>
                </div>

                @foreach($opportunities->get($stage, collect()) as $opportunity)
                    <article class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <a class="fw-semibold text-decoration-none"
                               href="{{ route('crm.opportunities.show', $opportunity) }}">
                                {{ $opportunity->name }}
                            </a>

                            <div class="small text-muted mt-1">
                                {{ $opportunity->customer?->name
                                    ?? $opportunity->lead?->full_name
                                    ?? 'No linked customer' }}
                            </div>

                            <div class="fs-5 fw-bold mt-3">
                                {{ number_format($opportunity->estimated_value, 2) }}
                            </div>

                            <div class="small text-muted">
                                {{ $opportunity->probability }}% probability
                            </div>

                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar"
                                     style="width: {{ $opportunity->probability }}%">
                                </div>
                            </div>

                            <div class="small text-muted mt-2">
                                Close:
                                {{ $opportunity->expected_close_date?->format('M d, Y') ?? 'Not set' }}
                            </div>

                            <form method="POST"
                                  action="{{ route('crm.opportunities.stage', $opportunity) }}"
                                  class="mt-3">
                                @csrf
                                @method('PATCH')

                                <label class="form-label small">Move to stage</label>
                                <select name="stage" class="form-select form-select-sm"
                                        onchange="this.form.querySelector('.loss-reason').classList.toggle('d-none', this.value !== 'lost');">
                                    @foreach($stages as $option)
                                        <option value="{{ $option }}"
                                            @selected($stage === $option)>
                                            {{ str($option)->replace('_', ' ')->title() }}
                                        </option>
                                    @endforeach
                                </select>

                                <input name="loss_reason"
                                       class="form-control form-control-sm mt-2 loss-reason d-none"
                                       placeholder="Reason for loss">

                                <button class="btn btn-sm btn-outline-primary mt-2 w-100">
                                    Update Stage
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach

                @if(($opportunities->get($stage) ?? collect())->isEmpty())
                    <div class="text-muted small text-center py-3">
                        No opportunities
                    </div>
                @endif
            </section>
        @endforeach
    </div>
</div>

<style>
    .pipeline-board {
        display: grid;
        grid-template-columns: repeat(4, minmax(250px, 1fr));
        gap: 1rem;
        overflow-x: auto;
        align-items: start;
    }

    .pipeline-column {
        background: #f5f6f8;
        border-radius: .75rem;
        padding: 1rem;
        min-width: 250px;
    }

    @media (max-width: 768px) {
        .pipeline-board {
            grid-template-columns: repeat(4, minmax(260px, 1fr));
        }
    }
</style>
@endsection
