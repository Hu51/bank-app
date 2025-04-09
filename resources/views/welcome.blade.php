@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Welcome</div>

            <div class="card-body">
                <h1 class="display-4">Welcome to {{ config('app.name', 'Laravel') }}</h1>
            <p class="lead">Your personal finance management solution</p>
            <p>Track your income and expenses, categorize transactions, and generate insightful reports to better understand your financial health.</p>
            </div>
        </div>
    </div>
</div>
@endsection
