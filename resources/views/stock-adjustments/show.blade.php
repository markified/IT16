@extends('layouts.app')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Adjustment Details</h1>
        <p class="mb-0 text-muted">Reference: {{ $adjustment->reference_number }}</p>
    </div>
    <a href="{{ route('stock-adjustments.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-warning">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-balance-scale me-2"></i>Adjustment Information
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="40%"><strong>Reference Number:</strong></td>
                        <td><code>{{ $adjustment->reference_number }}</code></td>
                    </tr>
                    <tr>
                        <td><strong>Product:</strong></td>
                        <td>
                            <a href="{{ route('products.show', $adjustment->product_id) }}">
                                {{ $adjustment->product->name }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Adjustment Date:</strong></td>
                        <td>{{ $adjustment->adjustment_date->format('F d, Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Adjustment Type:</strong></td>
                        <td>
                            @if($adjustment->adjustment_type == 'increase')
                            <span class="badge bg-success">Increase</span>
                            @elseif($adjustment->adjustment_type == 'decrease')
                            <span class="badge bg-danger">Decrease</span>
                            @else
                            <span class="badge bg-info">Correction</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Reason:</strong></td>
                        <td>{{ $adjustment->reason_label }}</td>
                    </tr>
                    <tr>
                        <td><strong>Adjusted By:</strong></td>
                        <td>{{ $adjustment->adjustedByUser->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Created At:</strong></td>
                        <td>{{ $adjustment->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line me-2"></i>Stock Change
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="h2 text-muted">{{ $adjustment->quantity_before }}</div>
                        <small class="text-muted">Before</small>
                    </div>
                    <div class="col-4">
                        <div class="h2">
                            <i class="fas fa-arrow-right text-warning"></i>
                        </div>
                        @php
                            $diff = $adjustment->quantity_after - $adjustment->quantity_before;
                        @endphp
                        <small class="@if($diff > 0) text-success @elseif($diff < 0) text-danger @else text-muted @endif">
                            {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                        </small>
                    </div>
                    <div class="col-4">
                        <div class="h2 text-primary">{{ $adjustment->quantity_after }}</div>
                        <small class="text-muted">After</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="text-center">
                    <p class="mb-1"><strong>Current Stock:</strong></p>
                    <div class="h3">{{ $adjustment->product->quantity }}</div>
                    {!! $adjustment->product->stock_badge !!}
                </div>
            </div>
        </div>

        @if($adjustment->notes)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">
                    <i class="fas fa-sticky-note me-2"></i>Notes
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $adjustment->notes }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
