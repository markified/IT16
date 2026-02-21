@extends('layouts.app')



@section('contents')
<div class="d-flex align-items-center justify-content-between">
    <h1 class="mb-0">Computer Parts</h1>
    <a href="{{ route('products.create') }}" class="btn btn-primary">Add New Item</a>
</div>
<hr />
@if(Session::has('success'))
<div class="alert alert-success" role="alert">
    {{ Session::get('success') }}
</div>
@endif
<table class="table table-hover">
    <thead class="table-primary">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Type</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if($products->count() > 0)
        @foreach($products as $product)
        <tr class="{{ $product->quantity <= $product->min_stock_level ? 'table-danger' : '' }}">
            <td class="align-middle">{{ $loop->iteration }}</td>
            <td class="align-middle">{{ $product->name }}</td>
            <td class="align-middle">{{ $product->type }}</td>
            <td class="align-middle">
                @if($product->status == 'available')
                <span class="badge bg-success">Available</span>
                @elseif($product->status == 'assigned')
                <span class="badge bg-primary">Assigned</span>
                @elseif($product->status == 'maintenance')
                <span class="badge bg-warning">Maintenance</span>
                @else
                <span class="badge bg-danger">Retired</span>
                @endif
            </td>
            
            <td class="align-middle">
                <div class="btn-group" role="group">
                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-secondary">Details</a>
                    <a href="{{ route('products.edit', $product->id)}}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="btn btn-danger p-0" onsubmit="return confirm('Are you sure you want to delete this item?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger m-0">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td class="text-center" colspan="8">No products found</td>
        </tr>
        @endif
    </tbody>
</table>
@endsection