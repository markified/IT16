@if(isset($report->data['stock_out_orders']) && count($report->data['stock_out_orders']) > 0)
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Order Number</th>
                <th>Recipient</th>
                <th>Issue Date</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Reason</th>
                <th>Issued By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->data['stock_out_orders'] as $index => $order)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $order['order_number'] ?? 'N/A' }}</strong></td>
                <td>{{ $order['recipient'] ?? 'N/A' }}</td>
                <td>{{ isset($order['issue_date']) ? \Carbon\Carbon::parse($order['issue_date'])->format('M d, Y') : 'N/A' }}</td>
                <td>₱{{ number_format($order['total_amount'] ?? 0, 2) }}</td>
                <td>
                    @php $status = $order['status'] ?? 'pending'; @endphp
                    <span class="badge
                        @if($status === 'issued') bg-success
                        @elseif($status === 'approved') bg-primary
                        @elseif($status === 'pending') bg-warning text-dark
                        @elseif($status === 'cancelled') bg-danger
                        @else bg-secondary
                        @endif">
                        {{ ucfirst($status) }}
                    </span>
                </td>
                <td>{{ $order['reason'] ?? '—' }}</td>
                <td>{{ $order['issued_by_user']['name'] ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<p class="text-muted">No stock out orders found for the selected date range and filters.</p>
@endif
