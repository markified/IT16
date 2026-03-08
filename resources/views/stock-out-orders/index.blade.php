@extends('layouts.app')

@section('contents')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-export text-danger me-2"></i>Stock Out Orders
        </h1>
        <a href="{{ route('stock-out-orders.create') }}" class="btn btn-danger">
            <i class="fas fa-plus me-1"></i> New Stock Out Order
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header py-3 bg-danger text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-list me-2"></i>All Stock Out Orders
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Recipient</th>
                            <th>Issue Date</th>
                            <th class="text-end">Total Amount</th>
                            <th class="text-center">Status</th>
                            <th>Issued By</th>
                            <th class="text-center" width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockOutOrders as $order)
                        <tr>
                            <td class="align-middle">
                                <strong>{{ $order->order_number }}</strong>
                            </td>
                            <td class="align-middle">{{ $order->recipient }}</td>
                            <td class="align-middle">
                                <span class="text-muted">{{ $order->issue_date->format('M d, Y') }}</span>
                            </td>
                            <td class="text-end align-middle fw-bold">
                                ₱{{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge bg-{{ 
                                    $order->status == 'pending' ? 'warning' : 
                                    ($order->status == 'approved' ? 'info' : 
                                    ($order->status == 'issued' ? 'success' : 'danger')) 
                                }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="align-middle">
                                {{ $order->issuedByUser?->name ?? 'N/A' }}
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group">
                                    <a href="{{ route('stock-out-orders.show', $order->id) }}" class="btn btn-info btn-sm" title="View Details">
                                        <i class="fas fa-list"></i>
                                    </a>
                                    @if(in_array($order->status, ['pending', 'approved']))
                                    <form action="{{ route('stock-out-orders.destroy', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-sm" title="Delete"
                                                data-confirm-delete="Are you sure you want to delete this stock out order?">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-0">No stock out orders found</p>
                                    <a href="{{ route('stock-out-orders.create') }}" class="btn btn-danger mt-3">
                                        <i class="fas fa-plus me-1"></i> Create First Order
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $stockOutOrders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
