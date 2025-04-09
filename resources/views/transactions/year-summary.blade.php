@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Year Summary {{ $year }}</h2>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('transactions.year-summary') }}" class="mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <select name="year" class="form-select">
                                    @foreach($years as $y)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-secondary">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Yearly Overview Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Income</h5>
                                    <p class="card-text display-6 text-end">{{ number_format($yearlyTotal->total_income ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Expenses</h5>
                                    <p class="card-text display-6 text-end">{{ number_format($yearlyTotal->total_expenses ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card {{ ($yearlyTotal->total_income ?? 0) - ($yearlyTotal->total_expenses ?? 0) >= 0 ? 'bg-primary' : 'bg-warning' }} text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Net Balance</h5>
                                    <p class="card-text display-6 text-end">{{ number_format(($yearlyTotal->total_income ?? 0) - ($yearlyTotal->total_expenses ?? 0), 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Trends Chart -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h3>Monthly Trends</h3>
                            <canvas id="monthlyTrendsChart"></canvas>
                        </div>
                    </div>

                    <!-- Monthly Details Table -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h3>Monthly Details</h3>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th>Income</th>
                                            <th>Expenses</th>
                                            <th>Net</th>
                                            <th>Transactions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($monthlyTotals as $monthly)
                                            <tr>
                                                <td><a href="{{ route('transactions.monthly-summary', ['month' => substr($monthly->month, -2), 'year' => $year]) }}">{{ $monthly->month }}</a></td>
                                                <td class="text-end text-success">{{ number_format($monthly->total_income, 0) }}</td>
                                                <td class="text-end text-danger">{{ number_format($monthly->total_expenses, 0) }}</td>
                                                <td class="text-end {{ $monthly->total_income - $monthly->total_expenses >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($monthly->total_income - $monthly->total_expenses, 0) }}
                                                </td>
                                                <td class="text-center">{{ $monthly->transaction_count }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Category Distribution -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h3>Expense Distribution by Category</h3>
                            <canvas id="categoryChart"></canvas>
                        </div>
                        <div class="col-md-12">
                            <h3>Top Counterparties - by income</h3>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Counterparty</th>
                                            <th>Income</th>
                                            <th>Expenses</th>
                                            <th>Transactions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topCounterparties as $counterparty)
                                            <tr>
                                                <td>{{ $counterparty['counterparty'] }}</td>
                                                <td class="text-success text-end">{{ number_format($counterparty['total_income'], 0) }}</td>
                                                <td class="text-danger text-end">{{ number_format($counterparty['total_expenses'], 0) }}</td>
                                                <td class="text-center">{{ $counterparty['transaction_count'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <h3>Top Counterparties - by expenses</h3>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Counterparty</th>
                                            <th>Expenses</th>
                                            <th>Income</th>
                                            <th>Transactions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topCounterpartiesExpenses as $counterparty)
                                            <tr>
                                                <td>{{ $counterparty['counterparty'] }}</td>
                                                <td class="text-danger text-end">{{ number_format($counterparty['total_expenses'], 0) }}</td>
                                                <td class="text-success text-end">{{ number_format($counterparty['total_income'], 0) }}</td>
                                                <td class="text-center">{{ $counterparty['transaction_count'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trends Chart
    const monthlyData = @json($monthlyTotals);
    const monthlyLabels = monthlyData.map(m => {
        return m.month ;
    });

    new Chart(document.getElementById('monthlyTrendsChart'), {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: 'Income',
                data: monthlyData.map(m => m.total_income),
                borderColor: '#198754',
                tension: 0.1
            }, {
                label: 'Expenses',
                data: monthlyData.map(m => m.total_expenses),
                borderColor: '#dc3545',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Category Distribution Chart
    const categoryData = @json($categoryTotals);
    new Chart(document.getElementById('categoryChart'), {
        type: 'pie',
        data: {
            labels: categoryData.map(c => {
                const total = categoryData.reduce((sum, item) => sum + item.total, 0);
                const percentage = ((c.total / total) * 100).toFixed(1);
                return `${c.category ? c.category.name : 'Uncategorized'} (${percentage}%)`;
            }),
            datasets: [{
                data: categoryData.map(c => c.total),
                // backgroundColor: categoryData.map(c => c.category ? c.category.color : '#6c757d')
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
});
</script>
@endpush
@endsection 