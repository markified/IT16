@extends('layouts.app')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Edit Category</h1>
        <p class="mb-0 text-muted">Update category: {{ $category->name }}</p>
    </div>
    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $category->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Category Code <span class="text-danger">*</span></label>
                    <select name="code" class="form-control @error('code') is-invalid @enderror" required>
                        <option value="">Select a category code</option>
                        <option value="CPU" {{ old('code', $category->code) == 'CPU' ? 'selected' : '' }}>CPU - Processors</option>
                        <option value="GPU" {{ old('code', $category->code) == 'GPU' ? 'selected' : '' }}>GPU - Graphics Cards</option>
                        <option value="RAM" {{ old('code', $category->code) == 'RAM' ? 'selected' : '' }}>RAM - Memory</option>
                        <option value="SSD" {{ old('code', $category->code) == 'SSD' ? 'selected' : '' }}>SSD - Solid State Drives</option>
                        <option value="HDD" {{ old('code', $category->code) == 'HDD' ? 'selected' : '' }}>HDD - Hard Disk Drives</option>
                        <option value="MOBO" {{ old('code', $category->code) == 'MOBO' ? 'selected' : '' }}>MOBO - Motherboards</option>
                        <option value="PSU" {{ old('code', $category->code) == 'PSU' ? 'selected' : '' }}>PSU - Power Supplies</option>
                        <option value="CASE" {{ old('code', $category->code) == 'CASE' ? 'selected' : '' }}>CASE - PC Cases</option>
                        <option value="COOL" {{ old('code', $category->code) == 'COOL' ? 'selected' : '' }}>COOL - Cooling Systems</option>
                        <option value="MON" {{ old('code', $category->code) == 'MON' ? 'selected' : '' }}>MON - Monitors</option>
                        <option value="KB" {{ old('code', $category->code) == 'KB' ? 'selected' : '' }}>KB - Keyboards</option>
                        <option value="MOUSE" {{ old('code', $category->code) == 'MOUSE' ? 'selected' : '' }}>MOUSE - Mice</option>
                        <option value="AUDIO" {{ old('code', $category->code) == 'AUDIO' ? 'selected' : '' }}>AUDIO - Audio Devices</option>
                        <option value="NET" {{ old('code', $category->code) == 'NET' ? 'selected' : '' }}>NET - Networking</option>
                        <option value="CABLE" {{ old('code', $category->code) == 'CABLE' ? 'selected' : '' }}>CABLE - Cables & Adapters</option>
                        <option value="ACC" {{ old('code', $category->code) == 'ACC' ? 'selected' : '' }}>ACC - Accessories</option>
                        <option value="OTHER" {{ old('code', $category->code) == 'OTHER' ? 'selected' : '' }}>OTHER - Other Components</option>
                    </select>
                    @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Icon</label>
                    <select name="icon" class="form-control">
                        <option value="">Select an icon</option>
                        <option value="fa-microchip" {{ old('icon', $category->icon) == 'fa-microchip' ? 'selected' : '' }}>💾 Microchip (CPU)</option>
                        <option value="fa-desktop" {{ old('icon', $category->icon) == 'fa-desktop' ? 'selected' : '' }}>🖥️ Desktop (Monitor)</option>
                        <option value="fa-memory" {{ old('icon', $category->icon) == 'fa-memory' ? 'selected' : '' }}>🧠 Memory (RAM)</option>
                        <option value="fa-hdd" {{ old('icon', $category->icon) == 'fa-hdd' ? 'selected' : '' }}>💿 HDD (Storage)</option>
                        <option value="fa-keyboard" {{ old('icon', $category->icon) == 'fa-keyboard' ? 'selected' : '' }}>⌨️ Keyboard</option>
                        <option value="fa-mouse" {{ old('icon', $category->icon) == 'fa-mouse' ? 'selected' : '' }}>🖱️ Mouse</option>
                        <option value="fa-plug" {{ old('icon', $category->icon) == 'fa-plug' ? 'selected' : '' }}>🔌 Power Supply</option>
                        <option value="fa-server" {{ old('icon', $category->icon) == 'fa-server' ? 'selected' : '' }}>🖧 Server (Motherboard)</option>
                        <option value="fa-fan" {{ old('icon', $category->icon) == 'fa-fan' ? 'selected' : '' }}>🌀 Fan (Cooling)</option>
                        <option value="fa-box" {{ old('icon', $category->icon) == 'fa-box' ? 'selected' : '' }}>📦 Box (Case)</option>
                        <option value="fa-ethernet" {{ old('icon', $category->icon) == 'fa-ethernet' ? 'selected' : '' }}>🔗 Network</option>
                        <option value="fa-headphones" {{ old('icon', $category->icon) == 'fa-headphones' ? 'selected' : '' }}>🎧 Audio</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                            role="switch" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            <span id="statusLabel">{{ old('is_active', $category->is_active) ? 'Active' : 'Inactive' }}</span>
                        </label>
                    </div>
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $category->description) }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Update Category
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSwitch = document.getElementById('is_active');
    const statusLabel = document.getElementById('statusLabel');
    
    statusSwitch.addEventListener('change', function() {
        statusLabel.textContent = this.checked ? 'Active' : 'Inactive';
    });
});
</script>
@endpush
@endsection
