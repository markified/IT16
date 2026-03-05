@extends('layouts.app')

@section('contents')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-receipt text-danger me-2"></i>Stock Out Details
        </h1>
        <div>
            <a href="{{ route('inventory-issues') }}" class="btn btn-secondary">
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
                <div class="card-header py-3 bg-danger text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-arrow-up me-2"></i>Transaction #{{ $inventoryIssue->id }}
                    </h6>
                    <span class="badge bg-light text-dark">
                        {{ $inventoryIssue->issue_date->format('F d, Y') }}
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
                                    <h4 class="mb-1">{{ $inventoryIssue->product->name }}</h4>
                                    <span class="badge bg-secondary">{{ $inventoryIssue->product->type }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-danger bg-opacity-10 border-danger">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-cubes me-1"></i>Quantity Issued
                                    </h6>
                                    <h2 class="text-danger mb-0">-{{ $inventoryIssue->quantity_issued }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                                <div>
                                    <small class="text-muted">Recipient</small>
                                    <div class="fw-bold">{{ $inventoryIssue->recipient ?? 'Not specified' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-question-circle text-info"></i>
                                </div>
                                <div>
                                    <small class="text-muted">Reason</small>
                                    <div class="fw-bold">{{ $inventoryIssue->reason ?? 'Not specified' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-user-check text-warning"></i>
                                </div>
                                <div>
                                    <small class="text-muted">Issued By</small>
                                    <div class="fw-bold">{{ \App\Models\User::find($inventoryIssue->issued_by)?->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-secondary bg-opacity-10 p-3 me-3">
                                    <i class="fas fa-calendar text-secondary"></i>
                                </div>
                                <div>
                                    <small class="text-muted">Issue Date</small>
                                    <div class="fw-bold">{{ $inventoryIssue->issue_date->format('F d, Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($inventoryIssue->notes)
                    <hr>
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-sticky-note me-1"></i>Notes
                        </h6>
                        <div class="bg-light rounded p-3">
                            {{ $inventoryIssue->notes }}
                        </div>
                    </div>
                    @endif

                    <hr>

                    <div class="row text-muted small">
                        <div class="col-md-6">
                            <i class="fas fa-clock me-1"></i>Created: {{ $inventoryIssue->created_at->format('M d, Y h:i A') }}
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
                        <h2 class="mb-0">{{ $inventoryIssue->product->quantity }}</h2>
                        <small class="text-muted">Total in Stock</small>
                    </div>
                    <div class="progress mb-3" style="height: 10px;">
                        @php
                            $stockPercent = min(100, ($inventoryIssue->product->quantity / max(1, $inventoryIssue->product->min_stock_level * 3)) * 100);
                            $stockColor = $inventoryIssue->product->quantity <= $inventoryIssue->product->min_stock_level ? 'danger' : 'success';
                        @endphp
                        <div class="progress-bar bg-{{ $stockColor }}" style="width: {{ $stockPercent }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Min Level: {{ $inventoryIssue->product->min_stock_level }}</span>
                        @if($inventoryIssue->product->quantity <= $inventoryIssue->product->min_stock_level)
                            <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Low Stock</span>
                        @else
                            <span class="text-success"><i class="fas fa-check"></i> Good</span>
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('products.show', $inventoryIssue->product->id) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-external-link-alt me-1"></i> View Product Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
@media print {
    .btn, .card-footer, .d-print-none { display: none !important; }
    .card { border: 1px solid #ddd !important; }
    .card-header { background-color: #f8f9fa !important; color: #000 !important; }
}
</style>
@endpush
@endsection