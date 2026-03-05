@extends('layouts.app')

@section('title', 'Table: ' . $tableName)

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Table: {{ $tableName }}</h1>
        <p class="mb-0 text-muted">{{ number_format($rowCount) }} rows</p>
    </div>
    <div>
        <a href="{{ route('database.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
        <div class="btn-group">
            <a href="{{ route('database.table.export', ['table' => $tableName, 'format' => 'csv']) }}" class="btn btn-success">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </a>
            <a href="{{ route('database.table.export', ['table' => $tableName, 'format' => 'json']) }}" class="btn btn-info">
                <i class="fas fa-file-code me-1"></i> Export JSON
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Table Structure -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Table Structure</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-primary">
                            <tr>
                                <th>Column</th>
                                <th>Type</th>
                                <th>Null</th>
                                <th>Key</th>
                                <th>Default</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($columns as $column)
                            <tr>
                                <td><code>{{ $column->Field }}</code></td>
                                <td><small>{{ $column->Type }}</small></td>
                                <td>
                                    @if($column->Null === 'YES')
                                    <span class="badge bg-success">Yes</span>
                                    @else
                                    <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($column->Key === 'PRI')
                                    <span class="badge bg-warning">PRI</span>
                                    @elseif($column->Key === 'UNI')
                                    <span class="badge bg-info">UNI</span>
                                    @elseif($column->Key === 'MUL')
                                    <span class="badge bg-secondary">MUL</span>
                                    @endif
                                </td>
                                <td><small>{{ $column->Default ?? 'NULL' }}</small></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Indexes -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Indexes</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-primary">
                            <tr>
                                <th>Name</th>
                                <th>Column</th>
                                <th>Unique</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($indexes as $index)
                            <tr>
                                <td><code>{{ $index->Key_name }}</code></td>
                                <td>{{ $index->Column_name }}</td>
                                <td>
                                    @if($index->Non_unique == 0)
                                    <span class="badge bg-success">Yes</span>
                                    @else
                                    <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td><small>{{ $index->Index_type }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No indexes found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sample Data -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Sample Data (First 10 Rows)</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover">
                <thead class="table-primary">
                    <tr>
                        @if($sampleData->count() > 0)
                            @foreach(array_keys((array) $sampleData->first()) as $header)
                            <th>{{ $header }}</th>
                            @endforeach
                        @else
                            <th>No Data</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($sampleData as $row)
                    <tr>
                        @foreach((array) $row as $value)
                        <td>
                            <small>{{ Str::limit($value, 50) ?? 'NULL' }}</small>
                        </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="text-center">No data in this table</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
