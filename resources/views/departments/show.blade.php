@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Department Details</h2>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $department->name }}</h5>
        </div>
    </div>

    <a href="{{ route('departments.index') }}" class="btn btn-secondary mt-3">Back</a>
</div>
@endsection