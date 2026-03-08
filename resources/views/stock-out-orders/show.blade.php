@extends('layouts.app')

@section('contents')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-file-export text-danger me-2"></i>Stock Out Order #{{ $stockOutOrder->order_number }}
            </h1>
            <span class="badge bg-{{ 
                $stockOutOrder->status == 'pending' ? 'warning' : 
                ($stockOutOrder->status == 'approved' ? 'info' : 
                ($stockOutOrder->status == 'issued' ? 'success' : 'danger')) 
            }} mt-2">
                {{ ucfirst($stockOutOrder->status) }}
            </span>
        </div>
        <a href="{{ route('stock-out-orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Order Details Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle me-2"></i>Order Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Order Number</label>
                            <p class="fw-bold mb-0">{{ $stockOutOrder->order_number }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Issue Date</label>
                            <p class="fw-bold mb-0">{{ $stockOutOrder->issue_date->format('F d, Y') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Recipient</label>
                            <p class="fw-bold mb-0">{{ $stockOutOrder->recipient }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Total Amount</label>
                            <p class="fw-bold mb-0 text-danger">₱{{ number_format($stockOutOrder->total_amount, 2) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Issued By</label>
                            <p class="fw-bold mb-0">{{ $stockOutOrder->issuedByUser?->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Approved By</label>
                            <p class="fw-bold mb-0">{{ $stockOutOrder->approvedByUser?->name ?? 'Pending Approval' }}</p>
                        </div>
                        @if($stockOutOrder->reason)
                        <div class="col-12 mb-3">
                            <label class="text-muted small">Reason</label>
                            <p class="fw-bold mb-0">{{ $stockOutOrder->reason }}</p>
                        </div>
                        @endif
                        @if($stockOutOrder->notes)
                        <div class="col-12">
                            <label class="text-muted small">Notes</label>
                            <p class="mb-0">{{ $stockOutOrder->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Items Card -->
            <div class="card shadow">
                <div class="card-header py-3 bg-secondary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-boxes me-2"></i>Order Items
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Unit Cost</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stockOutOrder->details as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $detail->product_name }}</strong>
                                        @if($detail->product)
                                            <br><small class="text-muted">{{ $detail->product->type ?? '' }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger fs-6">{{ $detail->quantity_issued }}</span>
                                    </td>
                                    <td class="text-end">₱{{ number_format($detail->unit_cost, 2) }}</td>
                                    <td class="text-end fw-bold">₱{{ number_format($detail->quantity_issued * $detail->unit_cost, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total:</td>
                                    <td class="text-end fw-bold text-danger">₱{{ number_format($stockOutOrder->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status Update Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-edit me-2"></i>Update Status
                    </h6>
                </div>
                <div class="card-body">
                    @if($stockOutOrder->status === 'issued')
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Order Issued</strong><br>
                            <small>This order has been issued and cannot be modified. Stock has been deducted.</small>
                        </div>
                    @elseif($stockOutOrder->status === 'cancelled')
                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-ban me-2"></i>
                            <strong>Order Cancelled</strong><br>
                            <small>This order has been cancelled and cannot be modified.</small>
                        </div>
                    @else
                        <form action="{{ route('stock-out-orders.update-status', $stockOutOrder->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label fw-bold">Current Status</label>
                                <p class="mb-0">
                                    <span class="badge bg-{{ 
                                        $stockOutOrder->status == 'pending' ? 'warning' : 
                                        ($stockOutOrder->status == 'approved' ? 'info' : 
                                        ($stockOutOrder->status == 'issued' ? 'success' : 'danger')) 
                                    }} fs-6">
                                        {{ ucfirst($stockOutOrder->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Change Status To</label>
                                <select name="status" class="form-select">
                                    <option value="pending" {{ $stockOutOrder->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ $stockOutOrder->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="issued" {{ $stockOutOrder->status == 'issued' ? 'selected' : '' }}>Issued (Final - Stock Deducted)</option>
                                    <option value="cancelled" {{ $stockOutOrder->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Stock is only deducted when status is set to "Issued"
                                </small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-1"></i> Update Status
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="card shadow">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-history me-2"></i>Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <span class="badge bg-success rounded-circle p-2">
                                        <i class="fas fa-plus"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-bold">Order Created</p>
                                    <small class="text-muted">{{ $stockOutOrder->created_at->format('M d, Y h:i A') }}</small>
                                </div>
                            </div>
                        </li>
                        @if($stockOutOrder->approved_by)
                        <li class="mb-3">
                            <div class="d-flex">
                                <div class="me-3">
                                    <span class="badge bg-info rounded-circle p-2">
                                        <i class="fas fa-check"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-bold">Approved</p>
                                    <small class="text-muted">By {{ $stockOutOrder->approvedByUser?->name }}</small>
                                </div>
                            </div>
                        </li>
                        @endif
                        @if($stockOutOrder->status == 'issued')
                        <li>
                            <div class="d-flex">
                                <div class="me-3">
                                    <span class="badge bg-primary rounded-circle p-2">
                                        <i class="fas fa-truck"></i>
                                    </span>
                                </div>
                                <div>
                                    <p class="mb-0 fw-bold">Items Issued</p>
                                    <small class="text-muted">{{ $stockOutOrder->updated_at->format('M d, Y h:i A') }}</small>
                                </div>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
