@if(isset($report->data['issues']) && count($report->data['issues']) > 0)
<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Issue Date</th>
                <th>Product</th>
                <th>Employee / Recipient</th>
                <th>Quantity Issued</th>
                <th>Purpose / Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->data['issues'] as $index => $issue)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ isset($issue['issue_date']) ? \Carbon\Carbon::parse($issue['issue_date'])->format('M d, Y') : 'N/A' }}</td>
                <td>{{ $issue['product_name'] ?? $issue['product']['name'] ?? 'N/A' }}</td>
                <td>{{ $issue['employee_name'] ?? $issue['recipient'] ?? 'N/A' }}</td>
                <td>{{ $issue['quantity_issued'] ?? 0 }}</td>
                <td>{{ $issue['notes'] ?? $issue['purpose'] ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="alert alert-info">No inventory issue data found for the selected date range.</div>
@endif
