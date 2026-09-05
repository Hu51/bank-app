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

                            @include('mapping-profiles.partials.mapping-fields', [
                                'values' => [
                                    'title' => old('title'),
                                    'skip_rows' => old('skip_rows', 0),
                                    'transaction_title' => old('transaction_title'),
                                    'description' => old('description'),
                                    'counterparty' => old('counterparty'),
                                    'location' => old('location'),
                                    'transaction_date' => old('transaction_date'),
                                    'amount' => old('amount'),
                                    'type' => old('type'),
                                    'reference_id' => old('reference_id'),
                                    'card_number' => old('card_number'),
                                ],
                            ])

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
