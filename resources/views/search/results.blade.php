@extends('layouts.app')

@section('contents')
<h1>Search Results for "{{ $query }}"</h1>
<hr />

<a href="{{ url()->previous() }}" class="btn btn-secondary mb-3">Back</a>

<h2>Products</h2>
<table class="table table-hover">
    <thead class="table-primary">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Type</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Supplier</th>
        </tr>
    </thead>
    <tbody>
        @forelse($products as $product)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->type }}</td>
            <td>{{ $product->quantity }}</td>
            <td>{{ $product->status }}</td>
            <td>{{ $product->supplier->name ?? 'N/A' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center">No products found</td>
        </tr>
        @endforelse
    </tbody>
</table>

<h2>Suppliers</h2>
<table class="table table-hover">
    <thead class="table-primary">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Contact Number</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        @forelse($suppliers as $supplier)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $supplier->name }}</td>
            <td>{{ $supplier->contact_number }}</td>
            <td>{{ $supplier->email }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">No suppliers found</td>
        </tr>
        @endforelse
    </tbody>
</table>

<h2>Purchase Orders</h2>
<table class="table table-hover">
    <thead class="table-primary">
        <tr>
            <th>#</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Supplier</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($purchaseOrders as $order)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $order->product_name }}</td>
            <td>{{ $order->quantity }}</td>
            <td>{{ $order->supplier->name ?? 'N/A' }}</td>
            <td>{{ ucfirst($order->status) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">No purchase orders found</td>
        </tr>
        @endforelse
    </tbody>
</table>

<h2>Stock</h2>
<table class="table table-hover">
    <thead class="table-primary">
        <tr>
            <th>#</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Supplier</th>
        </tr>
    </thead>
    <tbody>
        @forelse($stocks as $stock)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $stock->product_name }}</td>
            <td>{{ $stock->quantity }}</td>
            <td>{{ $stock->supplier }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">No stock records found</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection