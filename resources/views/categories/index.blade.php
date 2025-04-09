@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Categories</h2>
                        <a href="{{ route('categories.create') }}" class="btn btn-primary">
                            Add Category
                        </a>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Keywords</th>
                                        <th>Default</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td>
                                                <span class="d-flex align-items-center">
                                                    @if($category->icon)
                                                        <i class="{{ $category->icon }} me-2"></i>
                                                    @endif
                                                    <span style="color: {{ $category->color ?? '#000000' }}">
                                                        {{ $category->name }}
                                                    </span>
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $category->type === 'income' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucfirst($category->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($category->keywords->count() > 0)
                                                    <span class="badge bg-info">
                                                        {{ $category->keywords->count() }} keywords
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">No keywords</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($category->is_default)
                                                    <span class="badge bg-primary">Default</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('categories.edit', $category) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        Edit
                                                    </a>
                                                    <a href="{{ route('categories.keywords', $category) }}"
                                                        class="btn btn-sm btn-outline-info">
                                                        Keywords
                                                    </a>
                                                    <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Are you sure you want to delete this category?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
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