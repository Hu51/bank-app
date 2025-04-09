@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Import Transactions</div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {!! session('error') !!}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('transactions.import.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="csv_file" class="form-label">CSV File</label>
                                <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt">

                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="overwrite" name="overwrite"
                                        value="1">
                                    <label class="form-check-label" for="overwrite">
                                        Overwrite existing transactions
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="mapping_profile" class="form-label">Mapping Profile</label>
                                <select class="form-select" id="mapping_profile" name="mapping_profile">
                                    @foreach ($mappingProfiles as $id => $title)
                                        <option value="{{ $id }}">{{ $title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <h5>CSV Format Requirements:</h5>
                                <ul class="list-unstyled">
                                    <li>✓ File must be in CSV format</li>
                                    <li>✓ Maximum file size: 2MB</li>
                                    <li>✓ First row should contain headers</li>
                                    <li>✓ Required columns: date, description, amount</li>
                                </ul>
                            </div>

                            <button type="submit" class="btn btn-primary">Import Transactions</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection