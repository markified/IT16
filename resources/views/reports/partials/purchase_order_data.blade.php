@if(isset($report->data['purchase_orders']) && count($report->data['purchase_orders']) > 0)
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>PO Number</th>
                <th>Supplier</th>
                <th>Order Date</th>
                <th>Expected Delivery</th>
                <th>Total Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->data['purchase_orders'] as $index => $order)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $order['po_number'] ?? ('PO-' . str_pad($order['id'] ?? 0, 6, '0', STR_PAD_LEFT)) }}</td>
                <td>{{ $order['supplier']['name'] ?? 'N/A' }}</td>
                <td>{{ isset($order['created_at']) ? \Carbon\Carbon::parse($order['created_at'])->format('M d, Y') : 'N/A' }}</td>
                <td>{{ isset($order['expected_delivery']) && $order['expected_delivery'] ? \Carbon\Carbon::parse($order['expected_delivery'])->format('M d, Y') : 'N/A' }}</td>
                <td>₱{{ number_format($order['total_amount'] ?? 0, 2) }}</td>
                <td>
                    @php $status = $order['status'] ?? 'pending'; @endphp
                    <span class="badge
                        {{ $status === 'pending' ? 'bg-warning text-dark' : '' }}
                        {{ $status === 'approved' ? 'bg-info' : '' }}
                        {{ $status === 'partial' ? 'bg-primary' : '' }}
                        {{ $status === 'received' ? 'bg-success' : '' }}
                        {{ $status === 'completed' ? 'bg-success' : '' }}
                        {{ $status === 'cancelled' ? 'bg-danger' : '' }}
                    ">{{ ucfirst($status) }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="alert alert-info">No purchase order data found for the selected parameters and date range.</div>
@endif
