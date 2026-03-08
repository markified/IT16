@extends('layouts.app')

@section('contents')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Archived PC Parts</h1>
        <p class="mb-0 text-muted">{{ $products->count() }} archived products</p>
    </div>
    <a href="{{ route('products') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Active Products
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

<!-- Archived Products Table -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-secondary">
                    <tr>
                        <th>SKU</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th class="text-center">Stock</th>
                        <th class="text-end">Price</th>
                        <th class="text-center">Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td><code>{{ $product->sku ?? 'N/A' }}</code></td>
                        <td>
                            <strong>{{ $product->name }}</strong>
                            <br><small class="text-muted">{{ $product->type }}</small>
                        </td>
                        <td>
                            @if($product->category)
                            <span class="badge bg-info">{{ $product->category->name }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $product->brand ?? '-' }}</td>
                        <td class="text-center">
                            <strong>{{ $product->quantity }}</strong>
                        </td>
                        <td class="text-end">₱{{ number_format($product->price_per_item, 2) }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">Archived</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <form action="{{ route('products.restore', $product->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success" title="Restore">
                                        <i class="fas fa-undo me-1"></i> Restore
                                    </button>
                                </form>
                                <form action="{{ route('products.permanent-delete', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to PERMANENTLY delete this product? This action cannot be undone!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" title="Permanent Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-archive fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted mb-0">No archived PC parts found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
