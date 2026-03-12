@if(isset($report->data['suppliers']) && count($report->data['suppliers']) > 0)
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Supplier Name</th>
                <th>Contact Person</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Total Orders (Period)</th>
                <th>Total Amount (Period)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->data['suppliers'] as $index => $supplier)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $supplier['name'] ?? 'N/A' }}</td>
                <td>{{ $supplier['contact_person'] ?? '—' }}</td>
                <td>{{ $supplier['email'] ?? '—' }}</td>
                <td>{{ $supplier['phone'] ?? $supplier['contact_number'] ?? '—' }}</td>
                <td>{{ isset($supplier['purchase_orders']) ? count($supplier['purchase_orders']) : 0 }}</td>
                <td>₱{{ number_format(collect($supplier['purchase_orders'] ?? [])->sum('total_amount'), 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="alert alert-info">No supplier data found for the selected parameters and date range.</div>
@endif
