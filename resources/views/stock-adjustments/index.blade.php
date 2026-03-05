@extends('layouts.app')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Stock Adjustments</h1>
        <p class="mb-0 text-muted">Manual inventory corrections and adjustments</p>
    </div>
    <a href="{{ route('stock-adjustments.create') }}" class="btn btn-warning">
        <i class="fas fa-balance-scale me-1"></i> New Adjustment
    </a>
</div>

<!-- Filters -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('stock-adjustments.index') }}">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <select name="product_id" class="form-control">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <select name="adjustment_type" class="form-control">
                        <option value="">All Types</option>
                        <option value="increase" {{ request('adjustment_type') == 'increase' ? 'selected' : '' }}>Increase</option>
                        <option value="decrease" {{ request('adjustment_type') == 'decrease' ? 'selected' : '' }}>Decrease</option>
                        <option value="correction" {{ request('adjustment_type') == 'correction' ? 'selected' : '' }}>Correction</option>
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <select name="reason" class="form-control">
                        <option value="">All Reasons</option>
                        @foreach($reasons as $key => $label)
                        <option value="{{ $key }}" {{ request('reason') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <input type="date" name="date_from" class="form-control" placeholder="From" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2 mb-2">
                    <input type="date" name="date_to" class="form-control" placeholder="To" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-1 mb-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Adjustments List -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-warning">
                    <tr>
                        <th>Reference</th>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Before → After</th>
                        <th>Reason</th>
                        <th>Adjusted By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adjustments as $adjustment)
                    <tr>
                        <td><code>{{ $adjustment->reference_number }}</code></td>
                        <td>{{ $adjustment->adjustment_date->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('products.show', $adjustment->product_id) }}">
                                {{ $adjustment->product->name }}
                            </a>
                        </td>
                        <td>
                            @if($adjustment->adjustment_type == 'increase')
                            <span class="badge bg-success">Increase</span>
                            @elseif($adjustment->adjustment_type == 'decrease')
                            <span class="badge bg-danger">Decrease</span>
                            @else
                            <span class="badge bg-info">Correction</span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted">{{ $adjustment->quantity_before }}</span>
                            <i class="fas fa-arrow-right mx-2"></i>
                            <strong>{{ $adjustment->quantity_after }}</strong>
                            @php
                                $diff = $adjustment->quantity_after - $adjustment->quantity_before;
                            @endphp
                            @if($diff > 0)
                            <span class="text-success">(+{{ $diff }})</span>
                            @elseif($diff < 0)
                            <span class="text-danger">({{ $diff }})</span>
                            @endif
                        </td>
                        <td>{{ $adjustment->reason_label }}</td>
                        <td>{{ $adjustment->adjustedByUser->name ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('stock-adjustments.show', $adjustment->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ route('stock-adjustments.destroy', $adjustment->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" 
                                    data-confirm-delete="Reverse this adjustment? Stock will be restored to previous level.">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-balance-scale fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted mb-0">No adjustments found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $adjustments->links() }}
    </div>
</div>
@endsection
