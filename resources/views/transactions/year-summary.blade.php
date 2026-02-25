@extends('layouts.app')

@section('content')
@php
    $monthNames = ['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Aug', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dec'];
    $chartCategoryData = [];
    $defaultColors = ['#0d6efd', '#198754', '#fd7e14', '#6f42c1', '#20c997', '#ffc107', '#dc3545', '#0dcaf0', '#6c757d', '#e83e8c'];
    $hexOnly = function ($s) {
        $s = trim((string) $s);
        if ($s === '') return null;
        if (strpos($s, '#') !== 0) $s = '#' . $s;
        return (preg_match('/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/', $s) && strtolower($s) !== '#000' && strtolower($s) !== '#000000') ? $s : null;
    };
    foreach ($categoryTotals as $index => $row) {
        $name = 'Uncategorized';
        $color = $defaultColors[$index % count($defaultColors)];
        if (!empty($row['category'])) {
            $name = $row['category']['name'] ?? $name;
            $custom = $hexOnly($row['category']['color'] ?? '');
            if ($custom !== null) $color = $custom;
        }
        $chartCategoryData[] = ['name' => $name, 'total' => (float) ($row['total'] ?? 0), 'color' => $color];
    }
@endphp
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h2 class="mb-0">Year Summary</h2>
                    <form method="GET" action="{{ route('transactions.year-summary') }}" class="d-flex align-items-center gap-2 flex-wrap">
                        <select name="year" class="form-select form-select-sm" style="width: auto;">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i> Apply</button>
                        <a href="{{ route('transactions.index', ['start_date' => $year . '-01-01', 'end_date' => $year . '-12-31']) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list me-1"></i> View transactions
                        </a>
                    </form>
                </div>

                <div class="card-body">
                    {{-- Summary cards --}}
                    @php $net = ($yearlyTotal->total_income ?? 0) - ($yearlyTotal->total_expenses ?? 0); @endphp
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-lg-4">
                            <div class="card border-0 bg-success bg-opacity-10 text-success h-100">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="small text-muted text-uppercase mb-1 fw-semibold">Total Income</p>
                                        <p class="fs-3 fw-bold mb-0">{{ number_format($yearlyTotal->total_income ?? 0, 2) }}</p>
                                    </div>
                                    <span class="rounded-circle bg-success bg-opacity-25 p-3"><i class="fas fa-arrow-down fa-lg"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4">
                            <div class="card border-0 bg-danger bg-opacity-10 text-danger h-100">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="small text-muted text-uppercase mb-1 fw-semibold">Total Expenses</p>
                                        <p class="fs-3 fw-bold mb-0">{{ number_format($yearlyTotal->total_expenses ?? 0, 2) }}</p>
                                    </div>
                                    <span class="rounded-circle bg-danger bg-opacity-25 p-3"><i class="fas fa-arrow-up fa-lg"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-4">
                            <div class="card border-0 h-100 {{ $net >= 0 ? 'bg-primary bg-opacity-10 text-primary' : 'bg-warning bg-opacity-10 text-dark' }}">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="small text-muted text-uppercase mb-1 fw-semibold">Net Balance</p>
                                        <p class="fs-3 fw-bold mb-0">{{ number_format($net, 2) }}</p>
                                    </div>
                                    <span class="rounded-circle {{ $net >= 0 ? 'bg-primary bg-opacity-25' : 'bg-warning bg-opacity-25' }} p-3"><i class="fas fa-balance-scale fa-lg"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Monthly trends chart --}}
                    <section class="mb-4">
                        <h6 class="text-uppercase small fw-semibold text-muted mb-3">Monthly trends</h6>
                        <div class="bg-light rounded p-3">
                            <canvas id="monthlyTrendsChart" height="80"></canvas>
                        </div>
                    </section>

                    <div class="row">
                        {{-- Monthly details table --}}
                        <div class="col-lg-7 mb-4 mb-lg-0">
                            <h6 class="text-uppercase small fw-semibold text-muted mb-3">Monthly details</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Month</th>
                                            <th class="text-end">Income</th>
                                            <th class="text-end">Expenses</th>
                                            <th class="text-end">Net</th>
                                            <th class="text-center">Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($monthlyTotals as $m)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('transactions.monthly-summary', ['year' => $year, 'month' => substr($m->month, -2)]) }}" class="text-decoration-none fw-medium">
                                                        {{ $monthNames[substr($m->month, -2)] ?? $m->month }} {{ $year }}
                                                    </a>
                                                </td>
                                                <td class="text-end text-success">{{ number_format($m->total_income, 0) }}</td>
                                                <td class="text-end text-danger">{{ number_format($m->total_expenses, 0) }}</td>
                                                <td class="text-end fw-medium {{ $m->total_income - $m->total_expenses >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($m->total_income - $m->total_expenses, 0) }}
                                                </td>
                                                <td class="text-center text-muted">{{ number_format($m->transaction_count) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Expense distribution pie --}}
                        <div class="col-lg-5 mb-4">
                            <h6 class="text-uppercase small fw-semibold text-muted mb-3">Expense distribution by category</h6>
                            <div class="bg-light rounded p-3 d-flex align-items-center justify-content-center" style="min-height: 240px;">
                                <canvas id="categoryChart" style="max-height: 220px;"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Top counterparties --}}
                    <div class="row">
                        <div class="col-lg-6 mb-4 mb-lg-0">
                            <h6 class="text-uppercase small fw-semibold text-muted mb-3">Top counterparties by income</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Counterparty</th>
                                            <th class="text-end">Income</th>
                                            <th class="text-end">Expenses</th>
                                            <th class="text-center">Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topCounterparties as $cp)
                                            <tr>
                                                <td><a href="{{ route('transactions.index', ['search' => $cp['counterparty'], 'start_date' => $year . '-01-01', 'end_date' => $year . '-12-31']) }}" class="text-decoration-none">{{ $cp['counterparty'] }}</a></td>
                                                <td class="text-end text-success fw-medium">{{ number_format($cp['total_income'], 0) }}</td>
                                                <td class="text-end text-danger">{{ number_format($cp['total_expenses'], 0) }}</td>
                                                <td class="text-center text-muted">{{ $cp['transaction_count'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h6 class="text-uppercase small fw-semibold text-muted mb-3">Top counterparties by expenses</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Counterparty</th>
                                            <th class="text-end">Expenses</th>
                                            <th class="text-end">Income</th>
                                            <th class="text-center">Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topCounterpartiesExpenses as $cp)
                                            <tr>
                                                <td><a href="{{ route('transactions.index', ['search' => $cp['counterparty'], 'start_date' => $year . '-01-01', 'end_date' => $year . '-12-31']) }}" class="text-decoration-none">{{ $cp['counterparty'] }}</a></td>
                                                <td class="text-end text-danger fw-medium">{{ number_format($cp['total_expenses'], 0) }}</td>
                                                <td class="text-end text-success">{{ number_format($cp['total_income'], 0) }}</td>
                                                <td class="text-center text-muted">{{ $cp['transaction_count'] }}</td>
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthlyData = @json($monthlyTotals);
    const monthlyLabels = monthlyData.map(m => m.month || '');

    const trendsEl = document.getElementById('monthlyTrendsChart');
    if (trendsEl) {
        new Chart(trendsEl, {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Income',
                    data: monthlyData.map(m => parseFloat(m.total_income) || 0),
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: true,
                    tension: 0.2
                }, {
                    label: 'Expenses',
                    data: monthlyData.map(m => parseFloat(m.total_expenses) || 0),
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    fill: true,
                    tension: 0.2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: { beginAtZero: true },
                    x: { title: { display: false } }
                }
            }
        });
    }

    const categoryData = @json($chartCategoryData);
    const categoryEl = document.getElementById('categoryChart');
    const yearDefaultColors = ['#0d6efd', '#198754', '#fd7e14', '#6f42c1', '#20c997', '#ffc107', '#dc3545', '#0dcaf0', '#6c757d', '#e83e8c'];
    if (categoryEl && categoryData.length > 0) {
        const total = categoryData.reduce((sum, c) => sum + c.total, 0);
        const bgColors = categoryData.map(function(c, i) {
            const x = (c.color || '').toString().trim();
            if (!x || x === '#000' || x === '#000000') return yearDefaultColors[i % yearDefaultColors.length];
            return x.indexOf('#') === 0 ? x : '#' + x;
        });
        new Chart(categoryEl, {
            type: 'pie',
            data: {
                labels: categoryData.map(c => c.name),
                datasets: [{
                    data: categoryData.map(c => c.total),
                    backgroundColor: bgColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'right' },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                const pct = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : 0;
                                return ctx.label + ': ' + ctx.raw.toFixed(2) + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
