@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Edit Category</h2>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('categories.update', $category) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required placeholder="e.g. Groceries">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select type</option>
                                <option value="income" {{ old('type', $category->type) === 'income' ? 'selected' : '' }}>Income</option>
                                <option value="expense" {{ old('type', $category->type) === 'expense' ? 'selected' : '' }}>Expense</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="color" class="form-label">Color</label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" class="form-control form-control-color w-auto @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', $category->color ?? '#0d6efd') }}" title="Choose color">
                                    <input type="text" class="form-control form-control-sm" id="color_hex" value="{{ old('color', $category->color ?? '#0d6efd') }}" placeholder="#000000" maxlength="7" style="max-width: 7rem;">
                                </div>
                                @error('color')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="icon" class="form-label">Icon (Font Awesome)</label>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', $category->icon) }}" placeholder="fas fa-home">
                                <small class="text-muted">e.g. fas fa-home, fas fa-car</small>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Category
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
    const colorPick = document.getElementById('color');
    const colorHex = document.getElementById('color_hex');
    if (colorPick && colorHex) {
        colorPick.addEventListener('input', function() { colorHex.value = this.value; });
        colorHex.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) colorPick.value = this.value;
        });
    }
</script>
@endpush
@endsection
