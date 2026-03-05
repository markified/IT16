@extends('layouts.app')

@section('contents')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">PC Parts Inventory</h1>
        <p class="mb-0 text-muted">{{ $products->count() }} products in database</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Add New PC Part
    </a>
</div>

@if(Session::has('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ Session::get('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Search & Filters -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Search & Filter</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('products') }}">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, SKU, barcode, brand..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3 mb-2">
                    <select name="category_id" class="form-control">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <select name="stock_status" class="form-control">
                        <option value="">All Stock Status</option>
                        <option value="in" {{ request('stock_status') == 'in' ? 'selected' : '' }}>In Stock</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-primary">
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
                    <tr class="{{ $product->quantity == 0 ? 'table-danger' : ($product->isLowStock() ? 'table-warning' : '') }}">
                        <td><code>{{ $product->sku ?? 'N/A' }}</code></td>
                        <td>
                            <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none">
                                <strong>{{ $product->name }}</strong>
                            </a>
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
                            @if($product->quantity == 0)
                            <br><span class="badge bg-danger">Out</span>
                            @elseif($product->isLowStock())
                            <br><span class="badge bg-warning">Low</span>
                            @endif
                        </td>
                        <td class="text-end">₱{{ number_format($product->price_per_item, 2) }}</td>
                        <td class="text-center">
                            @if($product->status == 'available')
                            <span class="badge bg-success">Available</span>
                            @elseif($product->status == 'assigned')
                            <span class="badge bg-primary">Assigned</span>
                            @elseif($product->status == 'maintenance')
                            <span class="badge bg-warning">Maintenance</span>
                            @else
                            <span class="badge bg-secondary">Retired</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('products.show', $product->id) }}" class="btn btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger" title="Delete" data-confirm-delete="Are you sure you want to delete this PC part? This action cannot be undone.">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted mb-0">No PC parts found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection