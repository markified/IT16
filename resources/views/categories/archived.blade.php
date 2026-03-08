@extends('layouts.app')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Archived Categories</h1>
        <p class="mb-0 text-muted">{{ $categories->count() }} archived categories</p>
    </div>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Active Categories
    </a>
</div>

@if(Session::has('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ Session::get('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(Session::has('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ Session::get('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    @forelse($categories as $category)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                            {{ $category->code }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $category->name }}</div>
                        <div class="mt-2 text-muted small">
                            <span class="badge bg-secondary">Archived</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas {{ $category->icon ?? 'fa-folder' }} fa-2x text-gray-300"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="btn-group btn-group-sm">
                        <form action="{{ route('categories.restore', $category->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-undo me-1"></i> Restore
                            </button>
                        </form>
                        <form action="{{ route('categories.permanent-delete', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to PERMANENTLY delete this category? This action cannot be undone!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-archive fa-3x text-gray-300 mb-3"></i>
                <p class="text-muted mb-3">No archived categories found</p>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back to Categories</a>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
