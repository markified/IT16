@extends('layouts.app')

@section('contents')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle text-success me-2"></i>Add Stock
        </h1>
        <a href="{{ route('stock-in.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <ul class="mb-0">
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
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-arrow-down me-2"></i>Stock In Details
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('stock-in.store') }}" method="POST">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-microchip me-1"></i>Select PC Part <span class="text-danger">*</span>
                                </label>
                                <select name="product_id" id="product_id" class="form-select form-select-lg @error('product_id') is-invalid @enderror" required>
                                    <option value="">-- Choose a PC Part --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" 
                                                data-stock="{{ $product->quantity }}"
                                                {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} ({{ $product->type }}) - Current Stock: {{ $product->quantity }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-cubes me-1"></i>Quantity <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="quantity" id="quantity" 
                                       class="form-control form-control-lg @error('quantity') is-invalid @enderror" 
                                       value="{{ old('quantity', 1) }}" min="1" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-peso-sign me-1"></i>Unit Cost
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" name="unit_cost" step="0.01" min="0"
                                           class="form-control @error('unit_cost') is-invalid @enderror" 
                                           value="{{ old('unit_cost') }}" placeholder="0.00">
                                </div>
                                @error('unit_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-truck me-1"></i>Supplier Name
                                </label>
                                <input type="text" name="supplier_name" 
                                       class="form-control @error('supplier_name') is-invalid @enderror" 
                                       value="{{ old('supplier_name') }}" placeholder="Enter supplier name">
                                @error('supplier_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-hashtag me-1"></i>Reference Number
                                </label>
                                <input type="text" name="reference_number" 
                                       class="form-control @error('reference_number') is-invalid @enderror" 
                                       value="{{ old('reference_number') }}" placeholder="PO#, Invoice#, etc.">
                                @error('reference_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar me-1"></i>Received Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="received_date" 
                                       class="form-control @error('received_date') is-invalid @enderror" 
                                       value="{{ old('received_date', date('Y-m-d')) }}" required>
                                @error('received_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-sticky-note me-1"></i>Notes
                                </label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                                          rows="3" placeholder="Additional notes or remarks...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('stock-in.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check me-1"></i> Add Stock
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle me-2"></i>Stock Preview
                    </h6>
                </div>
                <div class="card-body">
                    <div id="stockPreview" class="text-center py-4">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Select a PC part to see stock preview</p>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-3">
                <div class="card-header py-3 bg-secondary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-lightbulb me-2"></i>Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3">
                        <li class="mb-2">Enter the quantity of items received</li>
                        <li class="mb-2">Unit cost helps track inventory value</li>
                        <li class="mb-2">Reference number helps trace the source</li>
                        <li>Stock will be added to inventory immediately</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    const stockPreview = document.getElementById('stockPreview');

    function updatePreview() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const quantity = parseInt(quantityInput.value) || 0;

        if (selectedOption.value) {
            const currentStock = parseInt(selectedOption.dataset.stock) || 0;
            const newStock = currentStock + quantity;
            const productName = selectedOption.text.split(' (')[0];

            stockPreview.innerHTML = `
                <h5 class="text-primary mb-3">${productName}</h5>
                <div class="row text-center">
                    <div class="col-4">
                        <div class="border rounded p-2">
                            <div class="text-muted small">Current</div>
                            <div class="h4 mb-0">${currentStock}</div>
                        </div>
                    </div>
                    <div class="col-4 d-flex align-items-center justify-content-center">
                        <div class="text-success h4 mb-0">+ ${quantity}</div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-2 bg-success bg-opacity-10">
                            <div class="text-muted small">New Total</div>
                            <div class="h4 mb-0 text-success">${newStock}</div>
                        </div>
                    </div>
                </div>
            `;
        } else {
            stockPreview.innerHTML = `
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">Select a PC part to see stock preview</p>
            `;
        }
    }

    productSelect.addEventListener('change', updatePreview);
    quantityInput.addEventListener('input', updatePreview);
});
</script>
@endpush
@endsection
