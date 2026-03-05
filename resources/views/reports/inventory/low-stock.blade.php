@extends('layouts.app')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Low Stock Alert Report</h1>
        <p class="mb-0 text-muted">Items requiring reorder</p>
    </div>
    <div>
        <a href="{{ route('inventory-reports.export', 'low-stock') }}" class="btn btn-success">
            <i class="fas fa-download me-1"></i> Export CSV
        </a>
        <a href="{{ route('inventory-reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Out of Stock</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $outOfStock }} items</div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lowStock }} items</div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Alerts</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $outOfStock + $lowStock }} items</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter by Category</h6>
    </div>
    <div class="card-body">
        <form method="GET" class="row">
            <div class="col-md-4">
                <select name="category_id" class="form-control" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Alert Table -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-warning">
                    <tr>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th class="text-center">Current Stock</th>
                        <th class="text-center">Min Level</th>
                        <th class="text-center">Shortage</th>
                        <th class="text-center">Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="{{ $product->quantity == 0 ? 'table-danger' : 'table-warning' }}">
                        <td><code>{{ $product->sku ?? 'N/A' }}</code></td>
                        <td>
                            <a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                        </td>
                        <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                        <td class="text-center">
                            <strong class="{{ $product->quantity == 0 ? 'text-danger' : 'text-warning' }}">
                                {{ $product->quantity }}
                            </strong>
                        </td>
                        <td class="text-center">{{ $product->min_stock_level }}</td>
                        <td class="text-center">
                            <span class="badge bg-danger">
                                {{ max(0, $product->min_stock_level - $product->quantity) }} needed
                            </span>
                        </td>
                        <td class="text-center">
                            @if($product->quantity == 0)
                            <span class="badge bg-danger">OUT OF STOCK</span>
                            @else
                            <span class="badge bg-warning">LOW</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('stock-in.create') }}?product_id={{ $product->id }}" class="btn btn-sm btn-success">
                                <i class="fas fa-plus"></i> Restock
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-success mb-0">All items are adequately stocked!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
