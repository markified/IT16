@extends('layouts.app')

@section('contents')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Create New Report</h2>
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Reports
                    </a>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('reports.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="title">Report Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="report_type">Report Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('report_type') is-invalid @enderror" id="report_type" name="report_type" required>
                                        <option value="" selected disabled>Select Report Type</option>
                                        @foreach($reportTypes as $type)
                                        <option value="{{ $type }}" {{ old('report_type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                        @endforeach
                                    </select>
                                    @error('report_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') ?? now()->subMonth()->format('Y-m-d') }}" required>
                                            @error('start_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="end_date">End Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') ?? now()->format('Y-m-d') }}" required>
                                            @error('end_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Conditional parameters based on report type -->
                                <div id="inventory-params" class="report-params">
                                    <h4>Inventory Report Parameters</h4>
                                    <div class="form-group mb-3">
                                        <label for="type">Product Type</label>
                                        <select class="form-control" id="type" name="type">
                                            <option value="">All Types</option>
                                            <option value="hardware">Hardware</option>
                                            <option value="software">Software</option>
                                            <option value="peripheral">Peripheral</option>
                                            <option value="component">Component</option>
                                        </select>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="low_stock" name="low_stock" value="1" {{ old('low_stock') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="low_stock">
                                            Show only low stock items
                                        </label>
                                    </div>
                                </div>

                                <div id="purchase-order-params" class="report-params">
                                    <h4>Purchase Order Report Parameters</h4>
                                    <div class="form-group mb-3">
                                        <label for="status">Order Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="">All Statuses</option>
                                            <option value="pending">Pending</option>
                                            <option value="approved">Approved</option>
                                            <option value="partial">Partial</option>
                                            <option value="received">Received</option>
                                            <option value="completed">Completed</option>
                                            <option value="cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="supplier_id">Supplier</label>
                                        <select class="form-control" id="supplier_id" name="supplier_id">
                                            <option value="">All Suppliers</option>
                                            @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div id="issue-params" class="report-params">
                                    <h4>Issue Report Parameters</h4>
                                    <div class="form-group mb-3">
                                        <label for="department_id">Department</label>
                                        <select class="form-control" id="department_id" name="department_id">
                                            <option value="">All Departments</option>
                                            @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div id="supplier-params" class="report-params">
                                    <h4>Supplier Report Parameters</h4>
                                    <div class="form-group mb-3">
                                        <label for="supplier_id_filter">Specific Supplier</label>
                                        <select class="form-control" id="supplier_id_filter" name="supplier_id">
                                            <option value="">All Suppliers</option>
                                            @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div id="department-params" class="report-params">
                                    <h4>Department Report Parameters</h4>
                                    <div class="form-group mb-3">
                                        <label for="department_id_filter">Specific Department</label>
                                        <select class="form-control" id="department_id_filter" name="department_id">
                                            <option value="">All Departments</option>
                                            @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-alt"></i> Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hide all parameter sections initially
        document.querySelectorAll('.report-params').forEach(function(element) {
            element.style.display = 'none';
        });

        // Show the relevant section based on the selected report type
        const reportTypeSelect = document.getElementById('report_type');

        function updateParamSections() {
            const reportType = reportTypeSelect.value;
            document.querySelectorAll('.report-params').forEach(function(element) {
                element.style.display = 'none';
            });

            if (reportType) {
                const sectionId = reportType === 'purchase_order' ? 'purchase-order-params' : reportType + '-params';
                const section = document.getElementById(sectionId);
                if (section) {
                    section.style.display = 'block';
                }
            }
        }

        reportTypeSelect.addEventListener('change', updateParamSections);

        // Update on page load for pre-selected values
        updateParamSections();
    });
</script>
@endsection