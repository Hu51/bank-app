@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Mapping Profile Details</h2>
                    <div>
                        <a href="{{ route('mapping-profiles.edit', $mappingProfile) }}" class="btn btn-primary">Edit Profile</a>
                        <a href="{{ route('mapping-profiles.index') }}" class="btn btn-secondary">Back to Profiles</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4>Profile Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Title</th>
                                    <td>{{ $mappingProfile->title }}</td>
                                </tr>
                                <tr>
                                    <th>Skip Rows</th>
                                    <td>{{ $mappingProfile->skip_rows }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $mappingProfile->created_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $mappingProfile->updated_at->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4>Field Mappings</h4>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>CSV Column Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Transaction Title</td>
                                        <td>{{ $mappingProfile->transaction_title }}</td>
                                    </tr>
                                    <tr>
                                        <td>Description</td>
                                        <td>{{ $mappingProfile->description }}</td>
                                    </tr>
                                    <tr>
                                        <td>Counterparty</td>
                                        <td>{{ $mappingProfile->counterparty }}</td>
                                    </tr>
                                    <tr>
                                        <td>Location</td>
                                        <td>{{ $mappingProfile->location }}</td>
                                    </tr>
                                    <tr>
                                        <td>Transaction Date</td>
                                        <td>{{ $mappingProfile->transaction_date }}</td>
                                    </tr>
                                    <tr>
                                        <td>Amount</td>
                                        <td>{{ $mappingProfile->amount }}</td>
                                    </tr>
                                    <tr>
                                        <td>Type</td>
                                        <td>{{ $mappingProfile->type }}</td>
                                    </tr>
                                    <tr>
                                        <td>Reference ID</td>
                                        <td>{{ $mappingProfile->reference_id }}</td>
                                    </tr>
                                    <tr>
                                        <td>Card Number</td>
                                        <td>{{ $mappingProfile->card_number ?? 'Not mapped' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 