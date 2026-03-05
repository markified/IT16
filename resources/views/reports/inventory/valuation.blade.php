@extends('layouts.app')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Inventory Valuation Report</h1>
        <p class="mb-0 text-muted">Total value of stock on hand</p>
    </div>
    <div>
        <a href="{{ route('inventory-reports.export', 'valuation') }}" class="btn btn-success">
            <i class="fas fa-download me-1"></i> Export CSV
        </a>
        <a href="{{ route('inventory-reports.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Items</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($summary['total_items']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Retail Value</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($summary['total_retail_value'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Cost Value</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($summary['total_cost_value'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Potential Profit</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($summary['potential_profit'], 2) }}</div>
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

<!-- Detail Table -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th class="text-end">Qty</th>
                        <th class="text-end">Cost Price</th>
                        <th class="text-end">Retail Price</th>
                        <th class="text-end">Cost Value</th>
                        <th class="text-end">Retail Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td><code>{{ $product->sku ?? 'N/A' }}</code></td>
                        <td>
                            <a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                        </td>
                        <td>{{ $product->category->name ?? 'Uncategorized' }}</td>
                        <td class="text-end">{{ $product->quantity }}</td>
                        <td class="text-end">₱{{ number_format($product->cost_price, 2) }}</td>
                        <td class="text-end">₱{{ number_format($product->price_per_item, 2) }}</td>
                        <td class="text-end">₱{{ number_format($product->quantity * $product->cost_price, 2) }}</td>
                        <td class="text-end">₱{{ number_format($product->quantity * $product->price_per_item, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">No products found</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th colspan="3">TOTAL</th>
                        <th class="text-end">{{ $products->sum('quantity') }}</th>
                        <th colspan="2"></th>
                        <th class="text-end">₱{{ number_format($summary['total_cost_value'], 2) }}</th>
                        <th class="text-end">₱{{ number_format($summary['total_retail_value'], 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
