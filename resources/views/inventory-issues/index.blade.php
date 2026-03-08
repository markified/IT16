@extends('layouts.app')

@section('contents')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-arrow-up text-danger me-2"></i>Stock Out Records
        </h1>
        <a href="{{ route('inventory-issues.create') }}" class="btn btn-danger">
            <i class="fas fa-minus-circle me-1"></i> Issue Stock
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3 bg-danger text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-list me-2"></i>All Stock Out Transactions
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
                            <th class="text-center">Qty Issued</th>
                            <th>Recipient</th>
                            <th>Reason</th>
                            <th>Issued By</th>
                            <th class="text-center" width="100">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventoryIssues as $issue)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration + ($inventoryIssues->currentPage() - 1) * $inventoryIssues->perPage() }}</td>
                            <td class="align-middle">
                                <span class="text-muted">{{ $issue->issue_date->format('M d, Y') }}</span>
                            </td>
                            <td class="align-middle">
                                <strong>{{ $issue->product->name }}</strong>
                                <br><small class="text-muted">{{ $issue->product->type }}</small>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge bg-danger fs-6">-{{ $issue->quantity_issued }}</span>
                            </td>
                            <td class="align-middle">{{ $issue->recipient ?? '-' }}</td>
                            <td class="align-middle">
                                <small>{{ Str::limit($issue->reason, 30) ?? '-' }}</small>
                            </td>
                            <td class="align-middle">
                                {{ $issue->issued_by ? \App\Models\User::find($issue->issued_by)?->name : 'N/A' }}
                            </td>
                            <td class="text-center align-middle">
                                <a href="{{ route('inventory-issues.show', $issue->id) }}" class="btn btn-info btn-sm" title="View Details">
                                    <i class="fas fa-list"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-0">No stock out records found</p>
                                    <a href="{{ route('inventory-issues.create') }}" class="btn btn-danger mt-3">
                                        <i class="fas fa-minus-circle me-1"></i> Issue Your First Stock
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $inventoryIssues->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
