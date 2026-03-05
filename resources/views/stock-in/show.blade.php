@extends('layouts.app')

@section('contents')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-receipt text-success me-2"></i>Stock In Details
        </h1>
        <div>
            <a href="{{ route('stock-in.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
            <button onclick="window.print()" class="btn btn-info">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-arrow-down me-2"></i>Transaction #{{ $stockIn->id }}
                    </h6>
                    <span class="badge bg-light text-dark">
                        {{ $stockIn->received_date->format('F d, Y') }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-microchip me-1"></i>PC Part
                                    </h6>
                                    <h4 class="mb-1">{{ $stockIn->product->name }}</h4>
                                    <span class="badge bg-secondary">{{ $stockIn->product->type }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success bg-opacity-10 border-success">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-cubes me-1"></i>Quantity Added
                                    </h6>
                                    <h2 class="text-success mb-0">+{{ $stockIn->quantity }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-truck text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted">Supplier</small>
                                    <div class="fw-bold">{{ $stockIn->supplier_name ?? 'Not specified' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-hashtag text-info"></i>
                                </div>
                                <div>
                                    <small class="text-muted">Reference Number</small>
                                    <div class="fw-bold">
                                        @if($stockIn->reference_number)
                                            <code>{{ $stockIn->reference_number }}</code>
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-peso-sign text-success"></i>
                                </div>
                                <div>
                                    <small class="text-muted">Unit Cost</small>
                                    <div class="fw-bold">
                                        @if($stockIn->unit_cost)
                                            ₱{{ number_format($stockIn->unit_cost, 2) }}
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-user text-warning"></i>
                                </div>
                                <div>
                                    <small class="text-muted">Received By</small>
                                    <div class="fw-bold">{{ $stockIn->received_by }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($stockIn->notes)
                    <hr>
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-sticky-note me-1"></i>Notes
                        </h6>
                        <div class="bg-light rounded p-3">
                            {{ $stockIn->notes }}
                        </div>
                    </div>
                    @endif

                    <hr>

                    <div class="row text-muted small">
                        <div class="col-md-6">
                            <i class="fas fa-clock me-1"></i>Created: {{ $stockIn->created_at->format('M d, Y h:i A') }}
                        </div>
                        <div class="col-md-6 text-md-end">
                            <i class="fas fa-edit me-1"></i>Updated: {{ $stockIn->updated_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-box me-2"></i>Current Product Status
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h2 class="mb-0">{{ $stockIn->product->quantity }}</h2>
                        <small class="text-muted">Total in Stock</small>
                    </div>
                    <div class="progress mb-3" style="height: 10px;">
                        @php
                            $stockPercent = min(100, ($stockIn->product->quantity / max(1, $stockIn->product->min_stock_level * 3)) * 100);
                            $stockColor = $stockIn->product->quantity <= $stockIn->product->min_stock_level ? 'danger' : 'success';
                        @endphp
                        <div class="progress-bar bg-{{ $stockColor }}" style="width: {{ $stockPercent }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Min Level: {{ $stockIn->product->min_stock_level }}</span>
                        @if($stockIn->product->quantity <= $stockIn->product->min_stock_level)
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Low Stock</span>
                        @else
                            <span class="text-success"><i class="fas fa-check"></i> Good</span>
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('products.show', $stockIn->product->id) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-external-link-alt me-1"></i> View Product Details
                    </a>
                </div>
            </div>

            <div class="card shadow mt-3 d-print-none">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i>Actions
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('stock-in.destroy', $stockIn->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-outline-danger w-100" data-confirm-delete="Are you sure you want to delete this record? This will reverse the stock addition.">
                            <i class="fas fa-trash me-1"></i> Delete This Record
                        </button>
                    </form>
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle me-1"></i>Deleting will reverse the stock addition
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
