@extends('layouts.app')

@section('contents')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Inventory Issues</h1>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('inventory-issues.create') }}" class="btn btn-primary">New Inventory Issue</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Department</th>
                <th>Employee</th>
                <th>Quantity Issued</th>
                <th>Issue Date</th>
                <th>Issued By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inventoryIssues as $issue)
            <tr>
                <td>{{ $issue->id }}</td>
                <td>{{ $issue->product->name }}</td>
                <td>{{ $issue->department->name }}</td>
                <td>{{ $issue->employee->name }}</td>
                <td>{{ $issue->quantity_issued }}</td>
                <td>{{ $issue->issue_date->format('Y-m-d') }}</td>
                <td>{{ $issue->issued_by ? \App\Models\User::find($issue->issued_by)->name : 'N/A' }}</td>
                <td>
                    <a href="{{ route('inventory-issues.show', $issue->id) }}" class="btn btn-info btn-sm">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $inventoryIssues->links() }}
</div>
@endsection
