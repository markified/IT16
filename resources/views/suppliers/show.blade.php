@extends('layouts.app')



@section('contents')
<div class="d-flex align-items-center justify-content-between">
    <h1 class="mb-0">Supplier Details</h1>
    <div>
        <a href="{{ route('suppliers') }}" class="btn btn-secondary">Back</a>
     
    </div>
</div>
<hr />

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Supplier Information
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Name:</div>
                    <div class="col-md-8">{{ $supplier->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Contact Number:</div>
                    <div class="col-md-8">{{ $supplier->contact_number }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Email:</div>
                    <div class="col-md-8">{{ $supplier->email }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Created At:</div>
                    <div class="col-md-8">{{ $supplier->created_at->format('Y-m-d H:i:s') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 fw-bold">Last Updated:</div>
                    <div class="col-md-8">{{ $supplier->updated_at->format('Y-m-d H:i:s') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Supplied Products
            </div>
            <div class="card-body">
                @if($supplier->suppliedProducts->count() > 0)
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supplier->suppliedProducts as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->type }}</td>
                           
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p>No products associated with this supplier.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection