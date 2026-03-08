@extends('layouts.app')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">New Stock Adjustment</h1>
        <p class="mb-0 text-muted">Correct inventory discrepancies</p>
    </div>
    <a href="{{ route('stock-adjustments.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-warning">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-balance-scale me-2"></i>Adjustment Details
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('stock-adjustments.store') }}" method="POST" id="adjustmentForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select Product <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-control @error('product_id') is-invalid @enderror" required>
                                <option value="">-- Select PC Part --</option>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                    data-quantity="{{ $product->quantity }}"
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} (Current: {{ $product->quantity }})
                                </option>
                                @endforeach
                            </select>
                            @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Adjustment Date <span class="text-danger">*</span></label>
                            <input type="date" name="adjustment_date" class="form-control @error('adjustment_date') is-invalid @enderror"
                                value="{{ old('adjustment_date', date('Y-m-d')) }}" required>
                            @error('adjustment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Adjustment Type <span class="text-danger">*</span></label>
                            <select name="adjustment_type" id="adjustment_type" class="form-control @error('adjustment_type') is-invalid @enderror" required>
                                <option value="">-- Select Type --</option>
                                <option value="add" {{ old('adjustment_type') == 'add' ? 'selected' : '' }}>
                                    Add Stock (+)
                                </option>
                                <option value="remove" {{ old('adjustment_type') == 'remove' ? 'selected' : '' }}>
                                    Remove Stock (-)
                                </option>
                            </select>
                            @error('adjustment_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" id="quantity_label">Quantity to Adjust <span class="text-danger">*</span></label>
                            <input type="number" name="quantity_adjusted" id="quantity_adjusted" 
                                class="form-control @error('quantity_adjusted') is-invalid @enderror"
                                value="{{ old('quantity_adjusted') }}" min="1" required>
                            <small id="quantity_help" class="text-muted">Enter the quantity to add or remove</small>
                            @error('quantity_adjusted')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reason <span class="text-danger">*</span></label>
                            <select name="reason" class="form-control @error('reason') is-invalid @enderror" required>
                                <option value="">-- Select Reason --</option>
                                @foreach($reasons as $key => $label)
                                <option value="{{ $key }}" {{ old('reason') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                            @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                rows="3" placeholder="Additional details about this adjustment...">{{ old('notes') }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-check me-1"></i> Record Adjustment
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Preview Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calculator me-2"></i>Adjustment Preview
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="h1 text-gray-300" id="preview_current">--</div>
                    <small class="text-muted">Current Stock</small>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Adjustment:</span>
                    <span id="preview_adjustment" class="font-weight-bold">--</span>
                </div>
                <hr>
                <div class="text-center">
                    <div class="h2 text-primary" id="preview_new">--</div>
                    <small class="text-muted">New Stock Level</small>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="card shadow mb-4 border-left-info">
            <div class="card-body">
                <h6 class="font-weight-bold text-info">Adjustment Types</h6>
                <p class="small mb-2"><strong>Add Stock:</strong> Increase inventory (found items, returns, corrections)</p>
                <p class="small mb-0"><strong>Remove Stock:</strong> Decrease inventory (damage, theft, loss, corrections)</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const typeSelect = document.getElementById('adjustment_type');
    const quantityInput = document.getElementById('quantity_adjusted');
    const quantityLabel = document.getElementById('quantity_label');
    const quantityHelp = document.getElementById('quantity_help');
    
    function updatePreview() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const currentStock = selectedOption.value ? parseInt(selectedOption.dataset.quantity) : 0;
        const type = typeSelect.value;
        const quantity = parseInt(quantityInput.value) || 0;
        
        document.getElementById('preview_current').textContent = currentStock;
        
        let newStock = currentStock;
        let adjustmentText = '--';
        
        if (type === 'add') {
            newStock = currentStock + quantity;
            adjustmentText = quantity > 0 ? '+' + quantity : '--';
            quantityLabel.innerHTML = 'Quantity to Add <span class="text-danger">*</span>';
            quantityHelp.textContent = 'Units to add to stock';
        } else if (type === 'remove') {
            newStock = Math.max(0, currentStock - quantity);
            adjustmentText = quantity > 0 ? '-' + quantity : '--';
            quantityLabel.innerHTML = 'Quantity to Remove <span class="text-danger">*</span>';
            quantityHelp.textContent = 'Units to remove from stock';
        }
        
        document.getElementById('preview_adjustment').textContent = adjustmentText;
        document.getElementById('preview_new').textContent = newStock;
    }
    
    productSelect.addEventListener('change', updatePreview);
    typeSelect.addEventListener('change', updatePreview);
    quantityInput.addEventListener('input', updatePreview);
    
    updatePreview();
});
</script>
@endpush
@endsection
