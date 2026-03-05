@extends('layouts.app')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ $category->name }}</h1>
        <p class="mb-0 text-muted">Category Code: {{ $category->code }}</p>
    </div>
    <div>
        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Category Details</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas {{ $category->icon ?? 'fa-folder' }} fa-4x text-primary"></i>
                </div>
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $category->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Code:</strong></td>
                        <td><span class="badge bg-primary">{{ $category->code }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            @if($category->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Products:</strong></td>
                        <td>{{ $category->products->count() }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Stock:</strong></td>
                        <td>{{ $category->products->sum('quantity') }} units</td>
                    </tr>
                    <tr>
                        <td><strong>Total Value:</strong></td>
                        <td>₱{{ number_format($category->products->sum(fn($p) => $p->quantity * $p->price_per_item), 2) }}</td>
                    </tr>
                </table>
                @if($category->description)
                <hr>
                <p class="mb-0"><strong>Description:</strong></p>
                <p class="text-muted">{{ $category->description }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Products in this Category</h6>
                <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Add Product
                </a>
            </div>
            <div class="card-body">
                @if($category->products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>SKU</th>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->products as $product)
                            <tr>
                                <td><code>{{ $product->sku ?? 'N/A' }}</code></td>
                                <td>
                                    <a href="{{ route('products.show', $product->id) }}">
                                        {{ $product->name }}
                                    </a>
                                </td>
                                <td>
                                    {{ $product->quantity }}
                                    @if($product->isLowStock())
                                    <span class="badge bg-warning">Low</span>
                                    @endif
                                </td>
                                <td>₱{{ number_format($product->price_per_item, 2) }}</td>
                                <td>{!! $product->stock_badge !!}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">No products in this category yet</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
