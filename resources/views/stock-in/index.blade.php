@extends('layouts.app')

@section('contents')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-arrow-down text-success me-2"></i>Stock In Records
        </h1>
        <a href="{{ route('stock-in.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> Add Stock
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3 bg-success text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-list me-2"></i>All Stock In Transactions
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="60">#</th>
                            <th>Date</th>
                            <th>PC Part</th>
                            <th class="text-center">Qty Added</th>
                            <th>Supplier</th>
                            <th>Reference</th>
                            <th>Received By</th>
                            <th class="text-center" width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockIns as $stockIn)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration + ($stockIns->currentPage() - 1) * $stockIns->perPage() }}</td>
                            <td class="align-middle">
                                <span class="text-muted">{{ $stockIn->received_date->format('M d, Y') }}</span>
                            </td>
                            <td class="align-middle">
                                <strong>{{ $stockIn->product->name }}</strong>
                                <br><small class="text-muted">{{ $stockIn->product->type }}</small>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge bg-success fs-6">+{{ $stockIn->quantity }}</span>
                            </td>
                            <td class="align-middle">{{ $stockIn->supplier_name ?? '-' }}</td>
                            <td class="align-middle">
                                <code>{{ $stockIn->reference_number ?? '-' }}</code>
                            </td>
                            <td class="align-middle">{{ $stockIn->received_by }}</td>
                            <td class="text-center align-middle">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('stock-in.show', $stockIn->id) }}" class="btn btn-info" title="View Details">
                                        <i class="fas fa-list"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-0">No stock in records found</p>
                                    <a href="{{ route('stock-in.create') }}" class="btn btn-success mt-3">
                                        <i class="fas fa-plus me-1"></i> Add Your First Stock
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $stockIns->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
