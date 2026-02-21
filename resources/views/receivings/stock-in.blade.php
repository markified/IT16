@extends('layouts.app')

@section('contents')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <h2>Stock In / Receiving Panel</h2>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>PO #</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Product</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($purchaseOrders as $po)
                    <tr>
                        <td>#{{ $po->id }}</td>
                        <td>{{ $po->supplier->name }}</td>
                        <td>{{ $po->created_at->format('M d, Y') }}</td>
                        <td>
                            <span class="badge bg-{{ 
                                $po->status == 'pending' ? 'warning' : 
                                ($po->status == 'approved' ? 'info' : 
                                ($po->status == 'partial' ? 'primary' : 
                                ($po->status == 'received' ? 'success' : 'danger'))) 
                            }}">
                                {{ ucfirst($po->status) }}
                            </span>
                        </td>
                        <td>
                            <ul class="mb-0 ps-3">
                                @foreach($po->orderDetails as $detail)
                                    {{ $detail->product->name ?? 'Product not found' }} ({{$detail->product->type}}) ({{ $detail->quantity_ordered }})
                                @endforeach
                            </ul>
                        </td>
                        <td>
                        <a href="{{ route('purchase-orders.show', $po->id) }}" class="btn btn-info btn-sm me-1">
                                  <i class="fa fa-eye"></i> View
                                </a>
                            @if($po->status == 'pending')
                                <form action="{{ route('purchase-orders.update-status', $po->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-primary btn-sm">Approve</button>
                                </form>
                            @elseif($po->status == 'approved')
                                <a href="{{ route('receivings.receive-form', $po->id) }}" class="btn btn-success btn-sm">Record Receiving</a>
                            @else
                                <span class="text-muted">No action</span>
                            @endif
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