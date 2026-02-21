@extends('layouts.app')

@section('contents')
<h2>Record Receiving for Purchase Order #{{ $purchaseOrder->id }}</h2>
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form action="{{ route('purchase-orders.process-receive', $purchaseOrder->id) }}" method="POST">
    @csrf
    <div class="table-responsive mb-3">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Type</th>
                    <th>Quantity Ordered</th>
                    <th>Quantity to Receive</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->orderDetails as $detail)
                    @php
                        $previouslyReceived = $detail->receivings->sum('quantity_received');
                        $remaining = $detail->quantity_ordered - $previouslyReceived;
                    @endphp
                    @if($remaining > 0)
                    <tr>
                        <td>{{ $detail->product->name ?? 'Product not found' }}</td>
                        <td>{{ $detail->product->type ?? '-' }}</td>
                        <td>{{ $detail->quantity_ordered }}</td>
                        <td>
                            <input type="hidden" name="receivings[{{ $detail->id }}][order_detail_id]" value="{{ $detail->id }}">
                            <input type="number" class="form-control" name="receivings[{{ $detail->id }}][quantity_received]" value="0" min="0" max="{{ $remaining }}">
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mb-3">
        <label for="received_by" class="form-label">Received By</label>
        <input type="text" class="form-control" id="received_by" name="received_by" required>
    </div>
    <div class="mb-3">
        <label for="notes" class="form-label">Notes</label>
        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
    </div>
    <button type="submit" class="btn btn-success">Confirm</button>
    <a href="{{ route('purchase-orders.show', $purchaseOrder->id) }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection 