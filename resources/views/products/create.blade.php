@extends('layouts.app')

@section('contents')
<h1 class="mb-0">Add New Computer Parts</h1>
<hr />
<form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Type</label>
            <select name="type" class="form-control @error('type') is-invalid @enderror">
                <option value="">-- Select Type --</option>
                <option value="CPU" {{ old('type') == 'CPU' ? 'selected' : '' }}>CPU</option>
                <option value="RAM" {{ old('type') == 'RAM' ? 'selected' : '' }}>RAM</option>
                <option value="Storage" {{ old('type') == 'Storage' ? 'selected' : '' }}>Storage</option>
                <option value="Motherboard" {{ old('type') == 'Motherboard' ? 'selected' : '' }}>Motherboard</option>
                <option value="Graphics Card" {{ old('type') == 'Graphics Card' ? 'selected' : '' }}>Graphics Card</option>
                <option value="Power Supply" {{ old('type') == 'Power Supply' ? 'selected' : '' }}>Power Supply</option>
                <option value="Peripheral" {{ old('type') == 'Peripheral' ? 'selected' : '' }}>Peripheral</option>
                <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 0) }}">
            @error('quantity')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Minimum Stock Level</label>
            <input type="number" name="min_stock_level" class="form-control @error('min_stock_level') is-invalid @enderror" value="{{ old('min_stock_level', 5) }}">
            @error('min_stock_level')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Status</label>
            <select name="status" class="form-control @error('status') is-invalid @enderror">
                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="assigned" {{ old('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="retired" {{ old('status') == 'retired' ? 'selected' : '' }}>Retired</option>
            </select>
            @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6"></div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
            @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Specifications</label>
            <textarea name="specifications" class="form-control @error('specifications') is-invalid @enderror" rows="2">{{ old('specifications') }}</textarea>
            @error('specifications')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Price per Item</label>
            <input type="number" step="0.01" name="price_per_item" class="form-control @error('price_per_item') is-invalid @enderror" value="{{ old('price_per_item', 0) }}">
            @error('price_per_item')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label class="form-label">Suppliers</label>
            <div>
                @foreach($suppliers as $supplier)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input"
                               type="checkbox"
                               name="suppliers[]"
                               id="supplier_{{ $supplier->id }}"
                               value="{{ $supplier->id }}"
                               {{ is_array(old('suppliers')) && in_array($supplier->id, old('suppliers', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="supplier_{{ $supplier->id }}">
                            {{ $supplier->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            @error('suppliers')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="row">
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary">Add</button>
        </div>
    </div>
</form>
@endsection