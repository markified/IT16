@extends('layouts.app')

@section('contents')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Create Inventory Issue</h1>

    <form action="{{ route('inventory-issues.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="product_id">Product</label>
            <select name="product_id" id="product_id" class="form-control" required>
                @foreach ($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->quantity }})</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="quantity_issued">Quantity to Issue</label>
            <input type="number" name="quantity_issued" id="quantity_issued" class="form-control" min="1" required>
        </div>
        <div class="form-group">
            <label for="department_id">Department</label>
            <select name="department_id" id="department_id" class="form-control" required>
                @foreach ($departments as $department)
                <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="employee_id">Employee</label>
            <select name="employee_id" id="employee_id" class="form-control" required>
                @foreach ($employees as $employee)
                <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="issue_date">Issue Date</label>
            <input type="date" name="issue_date" id="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="form-group">
            <label for="reason">Reason</label>
            <input type="text" name="reason" id="reason" class="form-control">
        </div>
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea name="notes" id="notes" class="form-control"></textarea>
        </div>
        <div class="d-flex justify-content-between">
    <a href="{{ route('inventory-issues.index') }}" class="btn btn-secondary">Close</a>
    <button type="submit" class="btn btn-primary">Submit</button>
</div>
    </form>
</div>
@endsection