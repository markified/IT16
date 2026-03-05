@extends('layouts.app')

@section('contents')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-minus-circle text-danger me-2"></i>Issue Stock
        </h1>
        <a href="{{ route('inventory-issues.index') }}" class="btn btn-secondary">
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
                <div class="card-header py-3 bg-danger text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-arrow-up me-2"></i>Stock Out Details
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('inventory-issues.store') }}" method="POST">
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
                                            {{ $product->name }} ({{ $product->type }}) - Stock: {{ $product->quantity }}
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
                                <input type="number" name="quantity_issued" id="quantity_issued" 
                                       class="form-control form-control-lg @error('quantity_issued') is-invalid @enderror" 
                                       value="{{ old('quantity_issued', 1) }}" min="1" required>
                                @error('quantity_issued')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-user me-1"></i>Recipient <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="recipient" 
                                       class="form-control @error('recipient') is-invalid @enderror" 
                                       value="{{ old('recipient') }}" placeholder="Enter recipient name" required>
                                @error('recipient')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-calendar me-1"></i>Issue Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="issue_date" 
                                       class="form-control @error('issue_date') is-invalid @enderror" 
                                       value="{{ old('issue_date', date('Y-m-d')) }}" required>
                                @error('issue_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-question-circle me-1"></i>Reason
                                </label>
                                <input type="text" name="reason" 
                                       class="form-control @error('reason') is-invalid @enderror" 
                                       value="{{ old('reason') }}" placeholder="e.g., Assembly, Replacement, Testing">
                                @error('reason')
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
                            <a href="{{ route('inventory-issues.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-check me-1"></i> Issue Stock
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
                        <i class="fas fa-calculator me-2"></i>Stock Preview
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
                        <i class="fas fa-exclamation-circle me-2"></i>Important
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3">
                        <li class="mb-2 text-danger">Stock will be deducted immediately</li>
                        <li class="mb-2">Ensure quantity doesn't exceed current stock</li>
                        <li class="mb-2">Recipient name is required for tracking</li>
                        <li>This action cannot be undone</li>
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
    const quantityInput = document.getElementById('quantity_issued');
    const stockPreview = document.getElementById('stockPreview');

    function updatePreview() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const quantity = parseInt(quantityInput.value) || 0;

        if (selectedOption.value) {
            const currentStock = parseInt(selectedOption.dataset.stock) || 0;
            const newStock = currentStock - quantity;
            const productName = selectedOption.text.split(' (')[0];
            const isInvalid = newStock < 0;

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
                        <div class="text-danger h4 mb-0">- ${quantity}</div>
                    </div>
                    <div class="col-4">
                        <div class="border rounded p-2 ${isInvalid ? 'bg-danger bg-opacity-10 border-danger' : 'bg-warning bg-opacity-10'}">
                            <div class="text-muted small">Remaining</div>
                            <div class="h4 mb-0 ${isInvalid ? 'text-danger' : 'text-warning'}">${newStock}</div>
                        </div>
                    </div>
                </div>
                ${isInvalid ? '<div class="alert alert-danger mt-3 mb-0 py-2"><i class="fas fa-exclamation-triangle me-1"></i> Insufficient stock!</div>' : ''}
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