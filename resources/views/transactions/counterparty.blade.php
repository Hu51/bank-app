@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h2 class="mb-0">Counterparties</h2>
                    @if($counterparties->total() > 0)
                        <span class="badge bg-light text-dark fw-normal">{{ number_format($counterparties->total()) }} counterparties</span>
                    @endif
                </div>

                <div class="card-body">
                    <div class="rounded border bg-light bg-opacity-50 p-3 mb-4">
                        <form method="GET" action="{{ route('transactions.counterparty') }}">
                            <div class="row g-3 align-items-end flex-wrap">
                                <div class="col-12 col-md-auto flex-grow-1">
                                    <label class="form-label small text-muted mb-1">Search</label>
                                    <input type="text" name="search" class="form-control form-control-sm"
                                           placeholder="Search by name…"
                                           value="{{ request('search') }}">
                                </div>
                                <div class="col-6 col-md-auto">
                                    <label class="form-label small text-muted mb-1">From</label>
                                    <input type="date" name="start_date" class="form-control form-control-sm"
                                           value="{{ request('start_date') }}"
                                           aria-label="Start date">
                                </div>
                                <div class="col-6 col-md-auto">
                                    <label class="form-label small text-muted mb-1">To</label>
                                    <input type="date" name="end_date" class="form-control form-control-sm"
                                           value="{{ request('end_date') }}"
                                           aria-label="End date">
                                </div>
                                <div class="col-12 col-md-auto d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i> Apply</button>
                                    <a href="{{ route('transactions.counterparty') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if($counterparties->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-users fa-3x mb-3 opacity-50"></i>
                            <p class="mb-1">No counterparties match your filters.</p>
                            <p class="small mb-0"><a href="{{ route('transactions.counterparty') }}">Clear filters</a></p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Counterparty</th>
                                        <th class="text-center">Count</th>
                                        <th class="text-end">Income</th>
                                        <th class="text-end">Expense</th>
                                        <th class="text-nowrap">Last transaction</th>
                                        <th>Top category</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($counterparties as $cp)
                                        <tr>
                                            <td>
                                                <a href="{{ route('transactions.index', ['search' => $cp->counterparty]) }}" class="fw-medium text-decoration-none">
                                                    {{ $cp->counterparty }}
                                                </a>
                                            </td>
                                            <td class="text-center">{{ number_format($cp->transaction_count) }}</td>
                                            <td class="text-end text-success fw-medium">{{ number_format($cp->total_income, 2) }}</td>
                                            <td class="text-end text-danger fw-medium">{{ number_format($cp->total_expenses, 2) }}</td>
                                            <td class="text-nowrap text-muted small">
                                                @if($cp->last_transaction_date)
                                                    {{ date('Y-m-d', strtotime($cp->last_transaction_date)) }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>
                                                @if($cp->most_common_category)
                                                    <span class="d-flex align-items-center">
                                                        @if($cp->most_common_category->icon)
                                                            <i class="{{ $cp->most_common_category->icon }} me-1 small"></i>
                                                        @endif
                                                        <span style="color: {{ $cp->most_common_category->color ?? '#495057' }}">{{ $cp->most_common_category->name }}</span>
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light border-top">
                                    <tr class="fw-semibold">
                                        <td>Sum</td>
                                        <td class="text-center">{{ number_format($totals->total_count ?? 0) }}</td>
                                        <td class="text-end text-success">{{ number_format($totals->total_income ?? 0, 2) }}</td>
                                        <td class="text-end text-danger">{{ number_format($totals->total_expenses ?? 0, 2) }}</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $counterparties->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
