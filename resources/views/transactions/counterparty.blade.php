@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Counterparties</h2>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('transactions.counterparty') }}" class="mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search counterparties..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-auto">
                                <input type="date" name="start_date" class="form-control" 
                                       value="{{ request('start_date') }}"
                                       placeholder="Start Date">
                            </div>
                            <div class="col-auto">
                                <input type="date" name="end_date" class="form-control" 
                                       value="{{ request('end_date') }}"
                                       placeholder="End Date">
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-secondary">
                                    Search
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Counterparty</th>
                                    <th>Transaction Count</th>
                                    <th>Total Amount (Income)</th>
                                    <th>Total Amount (Expense)</th>
                                    <th>Last Transaction</th>
                                    <th>Most Common Category</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($counterparties as $counterparty)
                                    <tr>
                                        <td>
                                            <a href="{{ route('transactions.index', ['counterparty' => $counterparty->counterparty]) }}">
                                                {{ $counterparty->counterparty }}
                                            </a>
                                        </td>
                                        <td>{{ $counterparty->transaction_count }}</td>
                                        <td class="text-success text-end">
                                            {{ number_format($counterparty->total_income, 2) }}
                                        </td>
                                        <td class="text-danger text-end">
                                            {{ number_format($counterparty->total_expenses, 2) }}
                                        </td>
                                        <td>{{ substr($counterparty->last_transaction_date, 0, 10) }}</td>
                                        <td>
                                            @if($counterparty->most_common_category)
                                                <span class="d-flex align-items-center">
                                                    @if($counterparty->most_common_category->icon)
                                                        <i class="{{ $counterparty->most_common_category->icon }} me-1"></i>
                                                    @endif
                                                    <span style="color: {{ $counterparty->most_common_category->color ?? '#000000' }}">
                                                        {{ $counterparty->most_common_category->name }}
                                                    </span>
                                                </span>
                                            @else
                                                Uncategorized
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $counterparties->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 