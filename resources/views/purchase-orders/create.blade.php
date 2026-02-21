@extends('layouts.app')

@section('contents')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Create Purchase Order</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('purchase-orders.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Back to Purchase Orders
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Error!</strong> Please check the form below for errors.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('purchase-orders.store') }}" method="POST" id="poForm">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="supplier_id">Supplier:</label>
                            <select name="supplier_id" id="supplier_id" class="form-control" required disabled>
                                <option value="">Select Supplier</option>
                            </select>
                            <small id="supplier-helper" class="text-muted">Please select a product first.</small>
                        </div>
                    </div>
                </div>

                <h5 class="mt-4 mb-3">Order Items</h5>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered" id="products-table">
                        <thead class="bg-light">
                            <tr>
                                <th>Product</th>
                                <th width="150px">Quantity</th>
                    
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="product-row">
                                <td>
                                    <select name="products[0][id]" class="form-control product-select" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->type }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="products[0][quantity]" class="form-control" value="1" min="1" required>
                                </td>
                               
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Create Purchase Order
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
console.log('Script loaded');
    document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.querySelector('.product-select');
    const supplierSelect = document.getElementById('supplier_id');
    const supplierHelper = document.getElementById('supplier-helper');
                
    // Always start disabled
    supplierSelect.disabled = true;
    supplierHelper.style.display = '';

    productSelect.addEventListener('change', function() {
        console.log('Product changed:', this.value);
        const productId = this.value;
        supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
        if (!productId) {
            supplierSelect.disabled = true;
            supplierHelper.textContent = 'Please select a product first.';
            supplierHelper.style.display = '';
            return;
        }
        fetch('/suppliers/for-product?product_id=' + productId)
            .then(response => response.json())
            .then(suppliers => {
                console.log('Suppliers returned:', suppliers);
                if (suppliers.length === 0) {
                    supplierSelect.innerHTML = '<option value="">No supplier found for this product</option>';
                    supplierSelect.disabled = true;
                    supplierHelper.textContent = 'No supplier found for this product.';
                    supplierHelper.style.display = '';
                } else {
                    supplierSelect.disabled = false;
                    supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
                    suppliers.forEach(function(supplier) {
                        supplierSelect.innerHTML += `<option value="${supplier.id}">${supplier.name}</option>`;
                    });
                    supplierHelper.style.display = 'none';
            }
            });
        });
    });
</script>
@endsection