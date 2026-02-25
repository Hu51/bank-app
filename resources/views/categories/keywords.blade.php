@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        @if($category->icon)
                            <i class="{{ $category->icon }}"></i>
                        @endif
                        <h2 class="mb-0">Keywords for {{ $category->name }}</h2>
                    </div>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to Categories
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="rounded border bg-light bg-opacity-50 p-3 mb-4">
                        <p class="mb-1 small text-muted">Category</p>
                        <p class="mb-0 fw-medium">
                            <span style="color: {{ $category->color ?? '#212529' }}">{{ $category->name }}</span>
                            <span class="badge {{ $category->type === 'income' ? 'bg-success' : 'bg-danger' }} ms-2">{{ ucfirst($category->type) }}</span>
                        </p>
                    </div>

                    <form method="POST" action="{{ route('categories.keywords.update', $category) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Keywords</label>
                            <p class="small text-muted mb-2">Used to auto-categorize transactions. Add one keyword per row.</p>
                            <div id="keywords-container">
                                @if($category->keywords->count() > 0)
                                    @foreach($category->keywords as $keyword)
                                        <div class="input-group mb-2 keyword-row">
                                            <input type="text" name="keywords[]" class="form-control form-control-sm" value="{{ $keyword->keyword }}" placeholder="Keyword">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-keyword"><i class="fas fa-times"></i></button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2 keyword-row">
                                        <input type="text" name="keywords[]" class="form-control form-control-sm" placeholder="Keyword">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-keyword"><i class="fas fa-times"></i></button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" id="add-keyword" class="btn btn-outline-primary btn-sm mt-2">
                                <i class="fas fa-plus me-1"></i> Add Keyword
                            </button>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Keywords
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('keywords-container');
    const addBtn = document.getElementById('add-keyword');

    addBtn.addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'input-group mb-2 keyword-row';
        row.innerHTML = '<input type="text" name="keywords[]" class="form-control form-control-sm" placeholder="Keyword"><button type="button" class="btn btn-outline-danger btn-sm remove-keyword"><i class="fas fa-times"></i></button>';
        container.appendChild(row);
    });

    container.addEventListener('click', function(e) {
        if (!e.target.closest('.remove-keyword')) return;
        const row = e.target.closest('.keyword-row');
        const rows = container.querySelectorAll('.keyword-row');
        if (rows.length > 1) row.remove();
        else row.querySelector('input').value = '';
    });
});
</script>
@endpush
@endsection
