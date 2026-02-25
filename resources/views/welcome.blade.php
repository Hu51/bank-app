@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="text-center py-5 py-md-5">
                <div class="mb-4">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mb-3" style="width: 5rem; height: 5rem;">
                        <i class="fas fa-piggy-bank fa-2x"></i>
                    </span>
                </div>
                <h1 class="display-4 fw-bold mb-3">Welcome to {{ config('app.name', 'Bank App') }}</h1>
                <p class="lead text-muted mb-4 mx-auto" style="max-width: 32rem;">
                    Your personal finance management solution. Track income and expenses, categorize transactions, and get clear reports.
                </p>
                <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
                    <a href="{{ route('transactions.index') }}" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-list me-2"></i>View Transactions
                    </a>
                    <a href="{{ route('transactions.import') }}" class="btn btn-outline-primary btn-lg px-4">
                        <i class="fas fa-file-import me-2"></i>Import Data
                    </a>
                    <a href="{{ route('transactions.monthly-summary') }}" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="fas fa-chart-line me-2"></i>Reports
                    </a>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="text-primary mb-3"><i class="fas fa-exchange-alt fa-2x"></i></div>
                            <h5 class="card-title fw-semibold">Track transactions</h5>
                            <p class="card-text text-muted small mb-0">Import bank exports, categorize and filter with dates and categories.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="text-success mb-3"><i class="fas fa-tags fa-2x"></i></div>
                            <h5 class="card-title fw-semibold">Categories & keywords</h5>
                            <p class="card-text text-muted small mb-0">Define categories and auto-assign with keywords and mapping profiles.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="text-info mb-3"><i class="fas fa-chart-pie fa-2x"></i></div>
                            <h5 class="card-title fw-semibold">Reports & insights</h5>
                            <p class="card-text text-muted small mb-0">Monthly and yearly summaries, category breakdowns and counterparty views.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
