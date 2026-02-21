@extends('layouts.app')



@section('contents')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <h2>Purchase Orders</h2>
    <div class="d-flex align-items-center">
        <div class="dropdown me-2">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="poMenuButton" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-cog"></i> Options
            </button>
            <ul class="dropdown-menu" aria-labelledby="poMenuButton">
                <li><a class="dropdown-item" href="{{ route('purchase-orders.generate-low-stock') }}">Generate for Low Stock</a></li>
            </ul>
        </div>
        <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> New Purchase Order
        </a>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>PO #</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th class="text-end">Total Amount</th>
                        <th>Status</th>
                        <th width="200px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchaseOrders as $po)
                    <tr>
                        <td>{{ $po->id }}</td>
                        <td>{{ $po->supplier->name }}</td>
                        <td>{{ $po->created_at->format('M d, Y') }}</td>
                        <td class="text-end fw-bold">${{ number_format($po->total_amount, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ 
                                $po->status == 'pending' ? 'warning' : 
                                ($po->status == 'approved' ? 'info' : 
                                ($po->status == 'received' ? 'success' : 
                                ($po->status == 'partial' ? 'info' : 'danger'))) 
                            }}">
                                {{ ucfirst($po->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex">
                                <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn btn-info btn-sm me-1">
                                    <i class="fa fa-eye"></i> View
                                </a>
                                @if ($po->status == 'pending')
                                <form action="{{ route('purchase-orders.destroy', $po->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this purchase order?')">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No purchase orders found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {!! $purchaseOrders->links() !!}
    </div>
</div>
@endsection
