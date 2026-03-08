@extends('layouts.app')

@section('contents')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-export text-danger me-2"></i>Create Stock Out Order
        </h1>
        <a href="{{ route('stock-out-orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Error!</strong> Please check the form below for errors.
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-file-alt me-2"></i>Order Details
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('stock-out-orders.store') }}" method="POST" id="stockOutForm">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-user me-1"></i>Recipient <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="recipient" class="form-control @error('recipient') is-invalid @enderror" 
                                       value="{{ old('recipient') }}" placeholder="Enter recipient name or department" required>
                                @error('recipient')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar me-1"></i>Issue Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="issue_date" class="form-control @error('issue_date') is-invalid @enderror" 
                                       value="{{ old('issue_date', date('Y-m-d')) }}" required>
                                @error('issue_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-question-circle me-1"></i>Reason
                                </label>
                                <input type="text" name="reason" class="form-control @error('reason') is-invalid @enderror" 
                                       value="{{ old('reason') }}" placeholder="e.g., Assembly, Replacement, Project">
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-flag me-1"></i>Status <span class="text-danger">*</span>
                                </label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="issued" {{ old('status') == 'issued' ? 'selected' : '' }}>Issued</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Stock is only deducted when status is "Issued"</small>
                            </div>
                            <div class="col-12 mt-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-sticky-note me-1"></i>Notes
                                </label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                          rows="2" placeholder="Additional notes or remarks...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">
                            <i class="fas fa-boxes me-2"></i>Order Items <span class="text-danger">*</span>
                        </h5>

                        <div class="table-responsive mb-3">
                            <table class="table table-bordered" id="products-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>PC Part</th>
                                        <th width="120">Stock</th>
                                        <th width="150">Quantity</th>
                                        <th width="150">Unit Cost</th>
                                        <th width="80">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="product-rows">
                                    <tr class="product-row">
                                        <td>
                                            <select name="products[0][id]" class="form-select product-select" required>
                                                <option value="">-- Select PC Part --</option>
                                                @foreach($products as $product)
                                                <option value="{{ $product->id }}" 
                                                        data-stock="{{ $product->quantity }}"
                                                        data-cost="{{ $product->cost_price ?? 0 }}">
                                                    {{ $product->name }} ({{ $product->type }})
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <span class="stock-display badge bg-secondary">-</span>
                                        </td>
                                        <td>
                                            <input type="number" name="products[0][quantity]" class="form-control quantity-input" 
                                                   value="1" min="1" required>
                                        </td>
                                        <td>
                                            <span class="cost-display text-muted">₱0.00</span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row" disabled>
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-4">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-product">
                                <i class="fas fa-plus me-1"></i> Add Another Item
                            </button>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('stock-out-orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-check me-1"></i> Create Stock Out Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-warning text-dark">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-calculator me-2"></i>Order Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div id="orderSummary">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Items:</span>
                            <strong id="totalItems">0</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Quantity:</span>
                            <strong id="totalQuantity">0</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="h6">Estimated Value:</span>
                            <strong class="h6 text-danger" id="totalAmount">₱0.00</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-lightbulb me-2"></i>Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3 small">
                        <li class="mb-2">Select multiple PC parts to issue in one order</li>
                        <li class="mb-2">Quantity cannot exceed available stock</li>
                        <li class="mb-2"><strong>Pending/Approved:</strong> Stock is reserved but not deducted</li>
                        <li class="mb-2"><strong>Issued:</strong> Stock will be deducted and order becomes final</li>
                        <li><strong>Cancelled:</strong> Stock is never deducted or restored if previously issued</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 1;
    const productsContainer = document.getElementById('product-rows');
    const addProductBtn = document.getElementById('add-product');

    // Products data for validation
    const productsData = {
        @foreach($products as $product)
        {{ $product->id }}: { stock: {{ $product->quantity }}, cost: {{ $product->cost_price ?? 0 }} },
        @endforeach
    };

    function updateRowInfo(row) {
        const select = row.querySelector('.product-select');
        const stockDisplay = row.querySelector('.stock-display');
        const costDisplay = row.querySelector('.cost-display');
        const quantityInput = row.querySelector('.quantity-input');
        
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption.value) {
            const stock = parseInt(selectedOption.dataset.stock) || 0;
            const cost = parseFloat(selectedOption.dataset.cost) || 0;
            
            stockDisplay.textContent = stock;
            stockDisplay.className = 'stock-display badge ' + (stock > 10 ? 'bg-success' : (stock > 0 ? 'bg-warning' : 'bg-danger'));
            costDisplay.textContent = '₱' + cost.toFixed(2);
            quantityInput.max = stock;
            
            // Validate quantity
            if (parseInt(quantityInput.value) > stock) {
                quantityInput.value = stock;
            }
        } else {
            stockDisplay.textContent = '-';
            stockDisplay.className = 'stock-display badge bg-secondary';
            costDisplay.textContent = '₱0.00';
            quantityInput.removeAttribute('max');
        }
        
        updateSummary();
    }

    function updateSummary() {
        let totalItems = 0;
        let totalQuantity = 0;
        let totalAmount = 0;

        document.querySelectorAll('.product-row').forEach(row => {
            const select = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity-input');
            
            if (select.value) {
                totalItems++;
                const qty = parseInt(quantityInput.value) || 0;
                totalQuantity += qty;
                
                const cost = parseFloat(select.options[select.selectedIndex].dataset.cost) || 0;
                totalAmount += qty * cost;
            }
        });

        document.getElementById('totalItems').textContent = totalItems;
        document.getElementById('totalQuantity').textContent = totalQuantity;
        document.getElementById('totalAmount').textContent = '₱' + totalAmount.toFixed(2);
    }

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.product-row');
        rows.forEach(row => {
            row.querySelector('.remove-row').disabled = rows.length <= 1;
        });
    }

    function addProductRow() {
        const newRow = document.createElement('tr');
        newRow.className = 'product-row';
        newRow.innerHTML = `
            <td>
                <select name="products[${rowIndex}][id]" class="form-select product-select" required>
                    <option value="">-- Select PC Part --</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" 
                            data-stock="{{ $product->quantity }}"
                            data-cost="{{ $product->cost_price ?? 0 }}">
                        {{ $product->name }} ({{ $product->type }})
                    </option>
                    @endforeach
                </select>
            </td>
            <td>
                <span class="stock-display badge bg-secondary">-</span>
            </td>
            <td>
                <input type="number" name="products[${rowIndex}][quantity]" class="form-control quantity-input" 
                       value="1" min="1" required>
            </td>
            <td>
                <span class="cost-display text-muted">₱0.00</span>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        
        productsContainer.appendChild(newRow);
        rowIndex++;
        
        // Attach event listeners to new row
        attachRowEvents(newRow);
        updateRemoveButtons();
    }

    function removeProductRow(row) {
        row.remove();
        updateRemoveButtons();
        updateSummary();
    }

    function attachRowEvents(row) {
        row.querySelector('.product-select').addEventListener('change', () => updateRowInfo(row));
        row.querySelector('.quantity-input').addEventListener('input', updateSummary);
        row.querySelector('.remove-row').addEventListener('click', () => removeProductRow(row));
    }

    // Initialize existing rows
    document.querySelectorAll('.product-row').forEach(row => {
        attachRowEvents(row);
        updateRowInfo(row);
    });

    // Add product button
    addProductBtn.addEventListener('click', addProductRow);
});
</script>
@endpush
@endsection
