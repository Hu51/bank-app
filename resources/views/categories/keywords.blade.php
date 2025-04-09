@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Edit Keywords for {{ $category->name }}</h2>
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                        Back to Categories
                    </a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('categories.keywords.update', $category) }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="category-info" class="form-label">Category Information</label>
                            <div id="category-info" class="p-3 bg-light rounded">
                                <p class="mb-1">
                                    <strong>Name:</strong> {{ $category->name }}
                                </p>
                                <p class="mb-1">
                                    <strong>Type:</strong> 
                                    <span class="badge {{ $category->type === 'income' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($category->type) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="keywords" class="form-label">Keywords</label>
                            <div id="keywords-container">
                                @if($category->keywords->count() > 0)
                                    @foreach($category->keywords as $index => $keyword)
                                        <div class="input-group mb-2 keyword-row">
                                            <input type="text" name="keywords[]" class="form-control" value="{{ $keyword->keyword }}" required>
                                            <button type="button" class="btn btn-outline-danger remove-keyword">Remove</button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2 keyword-row">
                                        <input type="text" name="keywords[]" class="form-control" required>
                                        <button type="button" class="btn btn-outline-danger remove-keyword">Remove</button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" id="add-keyword" class="btn btn-outline-primary mt-2">
                                Add Keyword
                            </button>
                            <small class="form-text text-muted">
                                Keywords are used to automatically categorize transactions. Add one keyword per line.
                            </small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                Save Keywords
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
        const keywordsContainer = document.getElementById('keywords-container');
        const addKeywordButton = document.getElementById('add-keyword');
        
        // Add new keyword row
        addKeywordButton.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.className = 'input-group mb-2 keyword-row';
            newRow.innerHTML = `
                <input type="text" name="keywords[]" class="form-control" required>
                <button type="button" class="btn btn-outline-danger remove-keyword">Remove</button>
            `;
            keywordsContainer.appendChild(newRow);
        });
        
        // Remove keyword row
        keywordsContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-keyword')) {
                const row = e.target.closest('.keyword-row');
                if (keywordsContainer.querySelectorAll('.keyword-row').length > 1) {
                    row.remove();
                } else {
                    // If it's the last row, just clear the input
                    row.querySelector('input').value = '';
                }
            }
        });
    });
</script>
@endpush
@endsection 