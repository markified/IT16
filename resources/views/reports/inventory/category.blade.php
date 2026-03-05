@extends('layouts.app')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Category Summary Report</h1>
        <p class="mb-0 text-muted">Inventory breakdown by category</p>
    </div>
    <a href="{{ route('inventory-reports.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Products</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totals['products']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Stock</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totals['stock']) }} units</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Value</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totals['value'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock Items</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totals['low_stock']) }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Category Cards -->
<div class="row">
    @forelse($categories as $category)
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas {{ $category->icon ?? 'fa-folder' }} me-2"></i>{{ $category->name }}
                </h6>
                <span class="badge bg-primary">{{ $category->code }}</span>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="h4 mb-0">{{ $category->products_count }}</div>
                        <small class="text-muted">Products</small>
                    </div>
                    <div class="col-4">
                        <div class="h4 mb-0">{{ number_format($category->total_stock) }}</div>
                        <small class="text-muted">Units</small>
                    </div>
                    <div class="col-4">
                        <div class="h4 mb-0">₱{{ number_format($category->total_value, 0) }}</div>
                        <small class="text-muted">Value</small>
                    </div>
                </div>
                @if($category->low_stock_count > 0)
                <hr>
                <div class="text-center">
                    <span class="badge bg-warning">
                        <i class="fas fa-exclamation-triangle"></i> {{ $category->low_stock_count }} low stock
                    </span>
                </div>
                @endif
            </div>
            <div class="card-footer bg-light">
                <a href="{{ route('categories.show', $category->id) }}" class="btn btn-sm btn-outline-primary w-100">
                    View Products
                </a>
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

<!-- Summary Table -->
<div class="card shadow mt-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Category Summary Table</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-info">
                    <tr>
                        <th>Category</th>
                        <th class="text-center">Products</th>
                        <th class="text-center">Total Stock</th>
                        <th class="text-end">Total Value</th>
                        <th class="text-center">Low Stock</th>
                        <th class="text-center">% of Total Value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td>
                            <i class="fas {{ $category->icon ?? 'fa-folder' }} text-primary me-2"></i>
                            {{ $category->name }}
                        </td>
                        <td class="text-center">{{ $category->products_count }}</td>
                        <td class="text-center">{{ number_format($category->total_stock) }}</td>
                        <td class="text-end">₱{{ number_format($category->total_value, 2) }}</td>
                        <td class="text-center">
                            @if($category->low_stock_count > 0)
                            <span class="badge bg-warning">{{ $category->low_stock_count }}</span>
                            @else
                            <span class="badge bg-success">0</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($totals['value'] > 0)
                            {{ number_format(($category->total_value / $totals['value']) * 100, 1) }}%
                            @else
                            0%
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th>TOTAL</th>
                        <th class="text-center">{{ $totals['products'] }}</th>
                        <th class="text-center">{{ number_format($totals['stock']) }}</th>
                        <th class="text-end">₱{{ number_format($totals['value'], 2) }}</th>
                        <th class="text-center">{{ $totals['low_stock'] }}</th>
                        <th class="text-center">100%</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
