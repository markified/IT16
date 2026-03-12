@if(isset($report->data['products']) && count($report->data['products']) > 0)
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Type</th>
                <th>Serial Number</th>
                <th>Quantity</th>
                <th>Price Per Item</th>
                <th>Total Value</th>
                <th>Min Stock Level</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->data['products'] as $index => $product)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $product['name'] ?? 'N/A' }}</td>
                <td>{{ ucfirst($product['type'] ?? 'N/A') }}</td>
                <td>{{ $product['serial_number'] ?? 'N/A' }}</td>
                <td>
                    @if(($product['quantity'] ?? 0) <= ($product['min_stock_level'] ?? 5))
                        <span class="badge bg-danger">{{ $product['quantity'] ?? 0 }}</span>
                    @else
                        {{ $product['quantity'] ?? 0 }}
                    @endif
                </td>
                <td>₱{{ number_format($product['price_per_item'] ?? 0, 2) }}</td>
                <td>₱{{ number_format(($product['quantity'] ?? 0) * ($product['price_per_item'] ?? 0), 2) }}</td>
                <td>{{ $product['min_stock_level'] ?? 5 }}</td>
                <td>
                    @php $status = $product['status'] ?? 'available'; @endphp
                    <span class="badge 
                        {{ $status === 'available' ? 'bg-success' : '' }}
                        {{ $status === 'assigned' ? 'bg-primary' : '' }}
                        {{ $status === 'maintenance' ? 'bg-warning' : '' }}
                        {{ $status === 'retired' ? 'bg-secondary' : '' }}
                    ">{{ ucfirst($status) }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="alert alert-info">No inventory data found for the selected parameters and date range.</div>
@endif
