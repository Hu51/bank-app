@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h2 class="mb-0">Categories</h2>
                        @if($categories->count() > 0)
                            <span class="badge bg-light text-dark fw-normal">{{ $categories->count() }} categories</span>
                        @endif
                    </div>
                    <a href="{{ route('categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Category
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($categories->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-tags fa-3x mb-3 opacity-50"></i>
                            <p class="mb-1">No categories yet.</p>
                            <p class="small mb-0"><a href="{{ route('categories.create') }}">Create your first category</a></p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th class="text-center">Keywords</th>
                                        <th class="text-center">Default</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td>
                                                <span class="d-flex align-items-center gap-2">
                                                    @if($category->icon)
                                                        <i class="{{ $category->icon }} text-muted"></i>
                                                    @endif
                                                    <span style="color: {{ $category->color ?? '#212529' }}">{{ $category->name }}</span>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $category->type === 'income' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($category->type) }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($category->keywords->count() > 0)
                                                    <span class="badge bg-info">{{ $category->keywords->count() }} keywords</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($category->is_default)
                                                    <span class="badge bg-primary">Default</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex flex-wrap gap-1 justify-content-end">
                                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit me-1"></i> Edit
                                                    </a>
                                                    <a href="{{ route('categories.keywords', $category) }}" class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-key me-1"></i> Keywords
                                                    </a>
                                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash-alt me-1"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
