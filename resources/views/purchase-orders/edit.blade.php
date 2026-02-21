@extends('layouts.app')

@section('contents')
<div class="d-flex align-items-center justify-content-between">
    <h1 class="mb-0">Edit Purchase Order</h1>
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">Back</a>
</div>
<hr />
<form action="{{ route('purchase-orders.update', $purchaseOrder->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label for="product_name" class="form-label">Product Name</label>
        <input type="text" class="form-control" id="product_name" name="product_name" value="{{ old('product_name', $purchaseOrder->product_name) }}" required>
    </div>
    <div class="mb-3">
        <label for="quantity" class="form-label">Quantity</label>
        <input type="number" class="form-control" id="quantity" name="quantity" value="{{ old('quantity', $purchaseOrder->quantity) }}" required>
    </div>
    <div class="mb-3">
        <label for="supplier_id" class="form-label">Supplier</label>
        <select class="form-control" id="supplier_id" name="supplier_id" required>
            @foreach($suppliers as $supplier)
            <option value="{{ $supplier->id }}" {{ $supplier->id == $purchaseOrder->supplier_id ? 'selected' : '' }}>
                {{ $supplier->name }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select class="form-control" id="status" name="status" required>
            <option value="pending" {{ $purchaseOrder->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="completed" {{ $purchaseOrder->status == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ $purchaseOrder->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
    </div>
    <button type="submit" class="btn btn-warning">Update</button>
</form>
@endsection