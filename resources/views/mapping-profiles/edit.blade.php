@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Edit Mapping Profile</h2>
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

                    <form action="{{ route('mapping-profiles.update', $mappingProfile) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @include('mapping-profiles.partials.mapping-fields', [
                            'values' => [
                                'title' => old('title', $mappingProfile->title),
                                'skip_rows' => old('skip_rows', $mappingProfile->skip_rows),
                                'transaction_title' => old('transaction_title', $mappingProfile->transaction_title),
                                'description' => old('description', $mappingProfile->description),
                                'counterparty' => old('counterparty', $mappingProfile->counterparty),
                                'location' => old('location', $mappingProfile->location),
                                'transaction_date' => old('transaction_date', $mappingProfile->transaction_date),
                                'amount' => old('amount', $mappingProfile->amount),
                                'type' => old('type', $mappingProfile->type),
                                'reference_id' => old('reference_id', $mappingProfile->reference_id),
                                'card_number' => old('card_number', $mappingProfile->card_number),
                            ],
                        ])

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
