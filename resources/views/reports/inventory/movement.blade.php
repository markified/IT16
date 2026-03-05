@extends('layouts.app')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Stock Movement Report</h1>
        <p class="mb-0 text-muted">Track all inventory movements</p>
    </div>
    <a href="{{ route('inventory-reports.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total In</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">+{{ number_format($summary['total_in']) }} units</div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Out</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">-{{ number_format($summary['total_out']) }} units</div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Net Change</div>
                <div class="h5 mb-0 font-weight-bold @if($summary['net_change'] >= 0) text-success @else text-danger @endif">
                    {{ $summary['net_change'] >= 0 ? '+' : '' }}{{ number_format($summary['net_change']) }} units
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('inventory-reports.movement') }}">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label class="form-label">Product</label>
                    <select name="product_id" class="form-control">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Apply Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Movement Table -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-success">
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Product</th>
                        <th>Reference</th>
                        <th class="text-end text-success">In</th>
                        <th class="text-end text-danger">Out</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($movement['date'])->format('Y-m-d') }}</td>
                        <td>
                            @if($movement['type'] == 'Stock In')
                            <span class="badge bg-success">Stock In</span>
                            @elseif($movement['type'] == 'Stock Out')
                            <span class="badge bg-danger">Stock Out</span>
                            @else
                            <span class="badge bg-warning">Adjustment</span>
                            @endif
                        </td>
                        <td>{{ $movement['product'] }}</td>
                        <td><code>{{ $movement['reference'] }}</code></td>
                        <td class="text-end">
                            @if($movement['quantity_in'] > 0)
                            <span class="text-success">+{{ $movement['quantity_in'] }}</span>
                            @else
                            -
                            @endif
                        </td>
                        <td class="text-end">
                            @if($movement['quantity_out'] > 0)
                            <span class="text-danger">-{{ $movement['quantity_out'] }}</span>
                            @else
                            -
                            @endif
                        </td>
                        <td><small class="text-muted">{{ $movement['note'] }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="fas fa-exchange-alt fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted mb-0">No movements found for the selected period</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($movements->count() > 0)
                <tfoot class="table-secondary">
                    <tr>
                        <th colspan="4">TOTAL</th>
                        <th class="text-end text-success">+{{ $summary['total_in'] }}</th>
                        <th class="text-end text-danger">-{{ $summary['total_out'] }}</th>
                        <th></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
