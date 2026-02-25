@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h2 class="mb-0">Monthly Summary</h2>
                    <form method="GET" action="{{ route('transactions.monthly-summary') }}" class="d-flex align-items-center gap-2 flex-wrap">
                        <select name="year" class="form-select form-select-sm" style="width: auto;">
                            @foreach($years as $value)
                                <option value="{{ $value }}" {{ $year == $value ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        <select name="month" class="form-select form-select-sm" style="width: auto;">
                            @foreach($months as $value)
                                <option value="{{ $value }}" {{ $month == $value ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i> Apply</button>
                        <a href="{{ route('transactions.index', ['start_date' => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01', 'end_date' => date('Y-m-t', strtotime($year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01'))]) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-list me-1"></i> View transactions
                        </a>
                    </form>
                </div>

                <div class="card-body">
                    {{-- Summary cards --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-lg-4">
                            <div class="card border-0 bg-success bg-opacity-10 text-success h-100">
                                <div class="card-body d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="small text-muted text-uppercase mb-1 fw-semibold">Total Income</p>
                                        <p class="fs-3 fw-bold mb-0">{{ number_format($summary->total_income ?? 0, 2) }}</p>
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
                                        <p class="fs-3 fw-bold mb-0">{{ number_format($summary->total_expenses ?? 0, 2) }}</p>
                                    </div>
                                    <span class="rounded-circle bg-danger bg-opacity-25 p-3"><i class="fas fa-arrow-up fa-lg"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-4">
                            @php $net = ($summary->total_income ?? 0) - ($summary->total_expenses ?? 0); @endphp
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

                    <div class="row">
                        {{-- Expenses by category table --}}
                        <div class="col-lg-6 mb-4 mb-lg-0">
                            <h6 class="text-uppercase small fw-semibold text-muted mb-3">Expenses by Category</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-end">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($categorySummary as $category)
                                            <tr>
                                                <td>
                                                    @if($category->category)
                                                        <i class="{{ $category->category->icon ?? 'fas fa-tag' }} me-2 text-muted"></i>
                                                        <a href="{{ route('transactions.index', ['start_date' => $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01', 'end_date' => date('Y-m-t', strtotime($year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01')), 'category' => $category->category->id ?? 'all']) }}" class="text-decoration-none" style="color: {{ $category->category->color ?? '#495057' }};">
                                                            {{ $category->category->name ?? 'Uncategorized' }}
                                                        </a>
                                                    @else
                                                        <i class="fas fa-tag me-2 text-muted"></i> Uncategorized
                                                    @endif
                                                </td>
                                                <td class="text-end fw-medium">{{ number_format($category->total, 2) }}</td>
                                                <td class="text-end text-muted">
                                                    {{ $summary->total_expenses > 0 ? number_format(($category->total / $summary->total_expenses) * 100, 1) : 0 }}%
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Pie chart --}}
                        <div class="col-lg-6">
                            <h6 class="text-uppercase small fw-semibold text-muted mb-3">Expense Distribution</h6>
                            <div class="bg-light rounded p-3 d-flex align-items-center justify-content-center" style="min-height: 280px;">
                                <canvas id="expenseChart" style="max-height: 260px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
    $chartCategories = [];
    $defaultColors = ['#0d6efd', '#198754', '#fd7e14', '#6f42c1', '#20c997', '#ffc107', '#dc3545', '#0dcaf0', '#6c757d', '#e83e8c'];
    $hexOnly = function ($s) {
        $s = trim((string) $s);
        if ($s === '') return null;
        if (strpos($s, '#') !== 0) $s = '#' . $s;
        return (preg_match('/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/', $s) && strtolower($s) !== '#000' && strtolower($s) !== '#000000') ? $s : null;
    };
    foreach ($categorySummary as $index => $cat) {
        $name = 'Uncategorized';
        $color = $defaultColors[$index % count($defaultColors)];
        if ($cat->category) {
            $name = $cat->category->name ?? $name;
            $custom = $hexOnly($cat->category->color ?? '');
            if ($custom !== null) $color = $custom;
        }
        $chartCategories[] = ['name' => $name, 'total' => (float) $cat->total, 'color' => $color];
    }
@endphp
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('expenseChart');
    if (!ctx) return;
    const defaultColors = ['#0d6efd', '#198754', '#fd7e14', '#6f42c1', '#20c997', '#ffc107', '#dc3545', '#0dcaf0', '#6c757d', '#e83e8c'];
    const categories = @json($chartCategories);
    if (categories.length === 0) return;
    const total = categories.reduce((sum, c) => sum + c.total, 0);
    const backgroundColors = categories.map(function(cat, i) {
        const c = (cat.color || '').toString().trim();
        if (!c || c === '#000' || c === '#000000') return defaultColors[i % defaultColors.length];
        return c.indexOf('#') === 0 ? c : '#' + c;
    });
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: categories.map(cat => cat.name),
            datasets: [{
                data: categories.map(cat => cat.total),
                backgroundColor: backgroundColors,
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
                        label: function(context) {
                            const pct = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + context.raw.toFixed(2) + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
