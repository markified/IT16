@extends('layouts.app')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Add New PC Part</h1>
        <p class="mb-0 text-muted">Add a new product to inventory</p>
    </div>
    <a href="{{ route('products') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="row">
        <!-- Basic Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Basic Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                                <option value="">-- Select Category --</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Brand</label>
                            <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" value="{{ old('brand') }}" placeholder="e.g., Intel, AMD, NVIDIA">
                            @error('brand')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Model Number</label>
                            <input type="text" name="model_number" class="form-control @error('model_number') is-invalid @enderror" value="{{ old('model_number') }}" placeholder="e.g., i9-13900K">
                            @error('model_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">-- Select Type --</option>
                                <option value="CPU" {{ old('type') == 'CPU' ? 'selected' : '' }}>CPU / Processor</option>
                                <option value="Motherboard" {{ old('type') == 'Motherboard' ? 'selected' : '' }}>Motherboard</option>
                                <option value="RAM" {{ old('type') == 'RAM' ? 'selected' : '' }}>RAM / Memory</option>
                                <option value="Graphics Card" {{ old('type') == 'Graphics Card' ? 'selected' : '' }}>Graphics Card / GPU</option>
                                <option value="Storage" {{ old('type') == 'Storage' ? 'selected' : '' }}>Storage (SSD/HDD)</option>
                                <option value="Power Supply" {{ old('type') == 'Power Supply' ? 'selected' : '' }}>Power Supply (PSU)</option>
                                <option value="Case" {{ old('type') == 'Case' ? 'selected' : '' }}>PC Case</option>
                                <option value="Cooling" {{ old('type') == 'Cooling' ? 'selected' : '' }}>Cooling (Fan/Heatsink)</option>
                                <option value="Monitor" {{ old('type') == 'Monitor' ? 'selected' : '' }}>Monitor</option>
                                <option value="Keyboard" {{ old('type') == 'Keyboard' ? 'selected' : '' }}>Keyboard</option>
                                <option value="Mouse" {{ old('type') == 'Mouse' ? 'selected' : '' }}>Mouse</option>
                                <option value="Network Card" {{ old('type') == 'Network Card' ? 'selected' : '' }}>Network Card</option>
                                <option value="Sound Card" {{ old('type') == 'Sound Card' ? 'selected' : '' }}>Sound Card</option>
                                <option value="Cables" {{ old('type') == 'Cables' ? 'selected' : '' }}>Cables & Connectors</option>
                                <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Specifications</label>
                            <textarea name="specifications" class="form-control @error('specifications') is-invalid @enderror" rows="2" placeholder="Technical specifications...">{{ old('specifications') }}</textarea>
                            @error('specifications')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Storage Location</label>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" placeholder="e.g., Warehouse A, Shelf B3">
                            @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suppliers -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Suppliers</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($suppliers as $supplier)
                        <div class="col-md-4 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="suppliers[]"
                                    id="supplier_{{ $supplier->id }}" value="{{ $supplier->id }}"
                                    {{ is_array(old('suppliers')) && in_array($supplier->id, old('suppliers', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="supplier_{{ $supplier->id }}">
                                    {{ $supplier->name }}
                                </label>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-muted">No suppliers available. <a href="{{ route('suppliers.create') }}">Add one</a></div>
                        @endforelse
                    </div>
                    @error('suppliers')
                    <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Stock & Pricing -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Stock & Pricing</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Initial Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 0) }}" min="0" required>
                        @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Min Stock Level <span class="text-danger">*</span></label>
                        <input type="number" name="min_stock_level" class="form-control @error('min_stock_level') is-invalid @enderror" value="{{ old('min_stock_level', 5) }}" min="0" required>
                        <small class="text-muted">Alert when stock falls below this level</small>
                        @error('min_stock_level')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Cost Price</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" step="0.01" name="cost_price" class="form-control @error('cost_price') is-invalid @enderror" value="{{ old('cost_price', 0) }}" min="0">
                        </div>
                        <small class="text-muted">Purchase cost per unit</small>
                        @error('cost_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Retail Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" step="0.01" name="price_per_item" class="form-control @error('price_per_item') is-invalid @enderror" value="{{ old('price_per_item', 0) }}" min="0" required>
                        </div>
                        @error('price_per_item')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="assigned" {{ old('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="retired" {{ old('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>Add PC Part
                </button>
            </div>
        </div>
    </div>
</form>
@endsection