@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Monthly Summary</h2>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('transactions.monthly-summary') }}" class="mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <select name="year" class="form-select">
                                    @foreach($years as $value)
                                        <option value="{{ $value }}" {{ $year == $value ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <select name="month" class="form-select">
                                    @foreach($months as $value)
                                        <option value="{{ $value }}" {{ $month == $value ? 'selected' : '' }}>
                                            {{ $value }}
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

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Income</h5>
                                    <p class="card-text display-6 text-end">{{ number_format($summary->total_income ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Expenses</h5>
                                    <p class="card-text display-6 text-end">{{ number_format($summary->total_expenses ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card {{ ($summary->total_income ?? 0) - ($summary->total_expenses ?? 0) >= 0 ? 'bg-primary' : 'bg-warning' }} text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Net Balance</h5>
                                    <p class="card-text display-6 text-end">{{ number_format(($summary->total_income ?? 0) - ($summary->total_expenses ?? 0), 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h3 class="mb-4">Expenses by Category</h3>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categorySummary as $category)
                                    <tr>
                                        <td>
                                            @if($category->category)
                                                <i class="{{ $category->category->icon ?? 'fas fa-tag' }}"></i>
                                            @endif
                                            <a href="{{ route('transactions.index', ['start_date' => $year . '-' . $month . '-01', 'end_date' => $year . '-' . $month . '-31', 'category' => $category->category->id]) }}">{{ $category->category->name ?? 'Uncategorized' }}</a>
                                        </td>
                                        <td class="text-end">{{ number_format($category->total, 2) }}</td>
                                        <td class="text-end">
                                            {{ $summary->total_expenses > 0 ? number_format(($category->total / $summary->total_expenses) * 100, 1) : 0 }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                <h3 class="mb-4">Expense Distribution</h3>
                <div class="row">
                    <div class="col-md-6">
                        <canvas id="expenseChart"></canvas>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('expenseChart').getContext('2d');
                        const categories = @json($categorySummary->map(function($cat) {
                            return [
                                'name' => $cat->category ? $cat->category->name : 'Uncategorized',
                                'total' => $cat->total,
                                'color' => $cat->category ? $cat->category->color : '#6c757d'
                            ];
                        }));
                        
                        new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: categories.map(cat => `${cat.name} (${((cat.total / categories.reduce((sum, c) => sum + c.total, 0)) * 100).toFixed(1)}%)`),
                                datasets: [{
                                    data: categories.map(cat => cat.total),
                                    // backgroundColor: categories.map(cat => cat.color),
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'right'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const value = context.raw;
                                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                const percentage = ((value / total) * 100).toFixed(1);
                                                return `${context.label}: ${value} (${percentage}%)`;
                                            }
                                        }
                                    },
                                    datalabels: {
                                        formatter: (value, ctx) => {
                                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = ((value / total) * 100).toFixed(1);
                                            return `${percentage}%`;
                                        },
                                        color: '#fff',
                                        font: {
                                            weight: 'bold'
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection