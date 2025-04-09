@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Mapping Profiles</h2>
                    <a href="{{ route('mapping-profiles.create') }}" class="btn btn-primary">Create New Profile</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Skip Rows</th>
                                    <th>Transaction Title</th>
                                    <th>Description</th>
                                    <th>Counterparty</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mappingProfiles as $profile)
                                    <tr>
                                        <td>{{ $profile->title }}</td>
                                        <td>{{ $profile->skip_rows }}</td>
                                        <td>{{ $profile->transaction_title }}</td>
                                        <td>{{ $profile->description }}</td>
                                        <td>{{ $profile->counterparty }}</td>
                                        <td>
                                            <a href="{{ route('mapping-profiles.edit', $profile) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <a href="{{ route('mapping-profiles.show', $profile) }}" class="btn btn-sm btn-info">View</a>
                                            <form action="{{ route('mapping-profiles.destroy', $profile) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this profile?')">Delete</button>
                                            </form>
                                        </td>
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
@endsection 