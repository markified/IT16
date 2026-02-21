@extends('layouts.app')


@section('contents')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Inventory Issue Details</h1>
        <a href="{{ route('inventory-issues') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Issue #{{ $inventoryIssue->id }}</h6>
            <div>
                <a href="#" onclick="window.print()" class="btn btn-sm btn-info shadow-sm">
                    <i class="fas fa-print fa-sm text-white-50"></i> Print
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Product Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Product Name:</div>
                                <div class="col-md-8">{{ $inventoryIssue->product->name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Product ID:</div>
                                <div class="col-md-8">{{ $inventoryIssue->product->id }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Type:</div>
                                <div class="col-md-8">{{ $inventoryIssue->product->type ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Description:</div>
                                <div class="col-md-8">{{ $inventoryIssue->product->description ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Issue Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Quantity Issued:</div>
                                <div class="col-md-8">{{ $inventoryIssue->quantity_issued }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Issue Date:</div>
                                <div class="col-md-8">{{ $inventoryIssue->issue_date->format('Y-m-d') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Reason:</div>
                                <div class="col-md-8">{{ $inventoryIssue->reason }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Issued By:</div>
                                <div class="col-md-8">{{ \App\Models\User::find($inventoryIssue->issued_by)?->name ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Created At:</div>
                                <div class="col-md-8">{{ $inventoryIssue->created_at->format('Y-m-d H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Department Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Department:</div>
                                <div class="col-md-8">{{ $inventoryIssue->department->name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Location:</div>
                                <div class="col-md-8">{{ $inventoryIssue->department->location ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Employee Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Employee Name:</div>
                                <div class="col-md-8">{{ $inventoryIssue->employee->name }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Email:</div>
                                <div class="col-md-8">{{ $inventoryIssue->employee->email ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 font-weight-bold">Phone:</div>
                                <div class="col-md-8">{{ $inventoryIssue->employee->phone ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($inventoryIssue->notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Notes</h6>
                </div>
                <div class="card-body">
                    {{ $inventoryIssue->notes }}
                </div>
            </div>
            @endif
            
      
            <div class="row mt-5 d-none d-print-block">
                <div class="col-md-4">
                    <div class="border-top pt-2 mt-5">
                        <p class="text-center">Issuing Officer Signature</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border-top pt-2 mt-5">
                        <p class="text-center">Employee Signature</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border-top pt-2 mt-5">
                        <p class="text-center">Department Head Signature</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection