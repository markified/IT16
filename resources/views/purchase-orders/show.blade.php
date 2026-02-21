@extends('layouts.app')

@section('contents')
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h2 class="fw-bold mb-0">Purchase Order #{{ $purchaseOrder->id }}</h2>
        <span class="badge bg-{{
            $purchaseOrder->status == 'pending' ? 'warning' :
            ($purchaseOrder->status == 'received' ? 'success' :
            ($purchaseOrder->status == 'approved' ? 'primary' :
            ($purchaseOrder->status == 'partial' ? 'info' : 'danger')))
        }} ms-2">
            {{ ucfirst($purchaseOrder->status) }}
        </span>
    </div>
    <div class="col-md-4 d-flex justify-content-end align-items-center">
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary me-2">Back to List</a>
    </div>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-md-6">
                <strong>Supplier:</strong> {{ $purchaseOrder->supplier->name }}
            </div>
            <div class="col-md-6">
                <strong>Total Amount:</strong> ${{ number_format($purchaseOrder->total_amount, 2) }}
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-6">
                <strong>Order Date:</strong> {{ $purchaseOrder->created_at->format('M d, Y') }}
            </div>
            <div class="col-md-6">
                <strong>Last Updated:</strong> {{ $purchaseOrder->updated_at->format('M d, Y h:i A') }}
            </div>
        </div>
    </div>
</div>

<h4 class="fw-semibold mb-3">Ordered Item</h4>
<div class="card mb-4 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th>Type</th>
                        <th class="text-end">Quantity Ordered</th>
                        <th class="text-end">Price per Item</th>
                        <th class="text-end">Total Price</th>
                        <th class="text-end">Quantity Received</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->orderDetails as $detail)
                        @php
                            $received = $detail->receivings->sum('quantity_received');
                            $status = 'Pending';
                            if ($received == $detail->quantity_ordered) {
                                $status = 'Received';
                            } elseif ($received > 0) {
                                $status = 'Partial';
                            }
                        @endphp
                        <tr>
                            <td>{{ $detail->product->name ?? 'Product not found' }}</td>
                            <td>{{ $detail->product->type ?? '-' }}</td>
                            <td class="text-end">{{ $detail->quantity_ordered }}</td>
                            <td class="text-end">${{ number_format($detail->price_per_item, 2) }}</td>
                            <td class="text-end">${{ number_format($detail->price_per_item * $detail->quantity_ordered, 2) }}</td>
                            <td class="text-end">{{ $received }}</td>
                            <td>
                                <span class="badge bg-{{
                                    $status == 'Pending' ? 'warning' :
                                    ($status == 'Partial' ? 'info' : 'success')
                                }}">
                                    {{ $status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($purchaseOrder->receivings && $purchaseOrder->receivings->count() > 0)
<h4 class="fw-semibold mb-3">Receiving History</h4>
<div class="card mb-4 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date Received</th>
                        <th>Item</th>
                        <th class="text-end">Quantity Received</th>
                        <th>Received By</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->receivings as $receiving)
                    <tr>
                        <td>{{ $receiving->received_date->format('M d, Y') }}</td>
                        <td>{{ $receiving->orderDetail->product->name ?? 'Product not found' }}</td>
                        <td class="text-end">{{ $receiving->quantity_received }}</td>
                        <td>{{ $receiving->received_by }}</td>
                        <td>{{ $receiving->notes }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
    <p class="text-center text-muted">No receiving history found for this purchase order.</p>
@endif
@endsection
