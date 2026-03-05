@extends('layouts.app')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Inventory Reports</h1>
        <p class="mb-0 text-muted">Comprehensive inventory analysis and reporting</p>
    </div>
</div>

<div class="row">
    <!-- Valuation Report -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Valuation Report
                        </div>
                        <div class="h6 mb-2 font-weight-bold text-gray-800">Inventory Value</div>
                        <p class="small text-muted mb-3">Total value of stock on hand at cost and retail prices</p>
                        <a href="{{ route('inventory-reports.valuation') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-chart-bar me-1"></i> View Report
                        </a>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-peso-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Movement -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Stock Movement
                        </div>
                        <div class="h6 mb-2 font-weight-bold text-gray-800">In/Out History</div>
                        <p class="small text-muted mb-3">Track all stock movements with date range filtering</p>
                        <a href="{{ route('inventory-reports.movement') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-exchange-alt me-1"></i> View Report
                        </a>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Low Stock Alert
                        </div>
                        <div class="h6 mb-2 font-weight-bold text-gray-800">Reorder Items</div>
                        <p class="small text-muted mb-3">Items below minimum stock level requiring reorder</p>
                        <a href="{{ route('inventory-reports.low-stock') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-exclamation-triangle me-1"></i> View Report
                        </a>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Summary -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Category Summary
                        </div>
                        <div class="h6 mb-2 font-weight-bold text-gray-800">By Category</div>
                        <p class="small text-muted mb-3">Stock and value breakdown by product category</p>
                        <a href="{{ route('inventory-reports.category') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-tags me-1"></i> View Report
                        </a>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Export</h6>
            </div>
            <div class="card-body">
                <p class="mb-3">Download reports as CSV files for further analysis:</p>
                <a href="{{ route('inventory-reports.export', 'valuation') }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-download me-1"></i> Export Valuation
                </a>
                <a href="{{ route('inventory-reports.export', 'low-stock') }}" class="btn btn-outline-warning">
                    <i class="fas fa-download me-1"></i> Export Low Stock
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
