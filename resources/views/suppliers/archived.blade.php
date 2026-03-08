@extends('layouts.app')

@section('contents')
<div class="d-flex align-items-center justify-content-between">
    <div>
        <h1 class="mb-0">Archived Suppliers</h1>
        <p class="mb-0 text-muted">{{ $suppliers->count() }} archived suppliers</p>
    </div>
    <a href="{{ route('suppliers') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Active Suppliers
    </a>
</div>
<hr />

@if(Session::has('success'))
<div class="alert alert-success" role="alert">
    {{ Session::get('success') }}
</div>
@endif

@if(Session::has('error'))
<div class="alert alert-danger" role="alert">
    {{ Session::get('error') }}
</div>
@endif

<table class="table table-hover">
    <thead class="table-secondary">
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Contact Number</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($suppliers as $supplier)
        <tr>
            <td class="align-middle">{{ $loop->iteration }}</td>
            <td class="align-middle">{{ $supplier->name }}</td>
            <td class="align-middle">{{ $supplier->contact_number }}</td>
            <td class="align-middle">{{ $supplier->email }}</td>
            <td class="align-middle">
                <div class="btn-group btn-group-sm">
                    <form action="{{ route('suppliers.restore', $supplier->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success" title="Restore">
                            <i class="fas fa-undo me-1"></i> Restore
                        </button>
                    </form>
                    <form action="{{ route('suppliers.permanent-delete', $supplier->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to PERMANENTLY delete this supplier? This action cannot be undone!')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" title="Permanent Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center py-4">
                <i class="fas fa-archive fa-3x text-gray-300 mb-3"></i>
                <p class="text-muted mb-0">No archived suppliers found</p>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
