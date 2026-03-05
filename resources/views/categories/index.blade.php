@extends('layouts.app')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Categories</h1>
        <p class="mb-0 text-muted">Organize PC parts by category</p>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Add Category
    </a>
</div>

<div class="row">
    @forelse($categories as $category)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ $category->code }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $category->name }}</div>
                        <div class="mt-2 text-muted small">
                            <span class="me-3"><i class="fas fa-box"></i> {{ $category->products_count }} products</span>
                            <span><i class="fas fa-cubes"></i> {{ $category->products_sum_quantity ?? 0 }} units</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas {{ $category->icon ?? 'fa-folder' }} fa-2x text-gray-300"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('categories.show', $category->id) }}" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger" data-confirm-delete="Delete this category? This action cannot be undone.">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-tags fa-3x text-gray-300 mb-3"></i>
                <p class="text-muted mb-3">No categories found</p>
                <a href="{{ route('categories.create') }}" class="btn btn-primary">Create First Category</a>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
