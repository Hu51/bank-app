@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Create Mapping Profile</h2>
                    <a href="{{ route('mapping-profiles.index') }}" class="btn btn-secondary">Back to Profiles</a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('mapping-profiles.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title" class="form-label">Profile Title</label>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="skip_rows" class="form-label">Skip Rows</label>
                                    <input type="number" class="form-control" id="skip_rows" name="skip_rows" value="{{ old('skip_rows', 0) }}" required min="0">
                                </div>
                            </div>
                        </div>

                        <h4 class="mt-4 mb-3">Field Mappings</h4>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="transaction_title" class="form-label">Transaction Title Field</label>
                                    <input type="text" class="form-control" id="transaction_title" name="transaction_title" value="{{ old('transaction_title') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description" class="form-label">Description Field</label>
                                    <input type="text" class="form-control" id="description" name="description" value="{{ old('description') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="counterparty" class="form-label">Counterparty Field</label>
                                    <input type="text" class="form-control" id="counterparty" name="counterparty" value="{{ old('counterparty') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="location" class="form-label">Source Information Field</label>
                                    <input type="text" class="form-control" id="location" name="location" value="{{ old('location') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="transaction_date" class="form-label">Transaction Date Field</label>
                                    <input type="text" class="form-control" id="transaction_date" name="transaction_date" value="{{ old('transaction_date') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount" class="form-label">Amount Field</label>
                                    <input type="text" class="form-control" id="amount" name="amount" value="{{ old('amount') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type" class="form-label">Type Field</label>
                                    <input type="text" class="form-control" id="type" name="type" value="{{ old('type') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reference_id" class="form-label">Reference ID Field</label>
                                    <input type="text" class="form-control" id="reference_id" name="reference_id" value="{{ old('reference_id') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="card_number" class="form-label">Card Number Field</label>
                                    <input type="text" class="form-control" id="card_number" name="card_number" value="{{ old('card_number') }}">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Create Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 