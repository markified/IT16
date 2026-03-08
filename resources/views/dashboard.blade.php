@extends('layouts.app')

@section('contents')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">PC Parts Inventory Dashboard</h1>
        <p class="mb-0 text-muted">Welcome back! Here's your inventory overview.</p>
    </div>
    @if(!Auth::user()->isRegularAdmin())
    <div>
        <a href="{{ route('stock-in.create') }}" class="btn btn-success me-2">
            <i class="fas fa-plus"></i> Stock In
        </a>
        <a href="{{ route('stock-out-orders.create') }}" class="btn btn-danger">
            <i class="fas fa-minus"></i> Stock Out
        </a>
    </div>
    @endif
</div>

<!-- Stats Row 1 -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total PC Parts</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProducts }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-microchip fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Inventory Value</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱{{ number_format($totalInventoryValue, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-peso-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock Alerts</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lowStockItems }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Out of Stock</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $outOfStockItems }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Row 2 -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Categories</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCategories }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tags fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Suppliers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSuppliers }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-truck fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Stock In Records</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStockIn }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Stock Out (Issued)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStockOutIssued }}</div>
                        <small class="text-muted">{{ $totalStockOut }} total orders</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Stock Movement Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Stock Movement (Last 6 Months)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area" style="height: 300px;">
                    <canvas id="stockMovementChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4 border-left-warning">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-warning">Items Needing Attention</h6>
                @if(!Auth::user()->isRegularAdmin())
                <a href="{{ route('inventory-reports.low-stock') }}" class="btn btn-sm btn-warning">View All</a>
                @endif
            </div>
            <div class="card-body">
                @if($topLowStock->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($topLowStock as $item)
                    @if(!Auth::user()->isRegularAdmin())
                    <a href="{{ route('products.show', $item->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ Str::limit($item->name, 25) }}</strong>
                            <br><small class="text-muted">Min: {{ $item->min_stock_level }}</small>
                        </div>
                        <span class="badge {{ $item->quantity == 0 ? 'bg-danger' : 'bg-warning' }} rounded-pill">
                            {{ $item->quantity }}
                        </span>
                    </a>
                    @else
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ Str::limit($item->name, 25) }}</strong>
                            <br><small class="text-muted">Min: {{ $item->min_stock_level }}</small>
                        </div>
                        <span class="badge {{ $item->quantity == 0 ? 'bg-danger' : 'bg-warning' }} rounded-pill">
                            {{ $item->quantity }}
                        </span>
                    </div>
                    @endif
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <p class="mb-0 text-success">All items adequately stocked!</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Stock In -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-success">Recent Stock In</h6>
                @if(!Auth::user()->isRegularAdmin())
                <a href="{{ route('stock-in.index') }}" class="btn btn-sm btn-success">View All</a>
                @endif
            </div>
            <div class="card-body">
                @if($recentStockIn->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th class="text-end">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentStockIn as $stockIn)
                            <tr>
                                <td><small>{{ $stockIn->received_date }}</small></td>
                                <td>{{ Str::limit($stockIn->product->name, 20) }}</td>
                                <td class="text-end"><span class="text-success">+{{ $stockIn->quantity }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted mb-0">No recent stock in records.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Stock Out -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-danger">Recent Stock Out (Issued)</h6>
                @if(!Auth::user()->isRegularAdmin())
                <a href="{{ route('stock-out-orders.index') }}" class="btn btn-sm btn-danger">View All</a>
                @endif
            </div>
            <div class="card-body">
                @if($recentStockOut->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Recipient</th>
                                <th class="text-center">Items</th>
                                <th class="text-end">Total Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentStockOut as $order)
                            <tr>
                                <td><a href="{{ route('stock-out-orders.show', $order->id) }}"><code>{{ $order->order_number }}</code></a></td>
                                <td><small>{{ $order->issue_date->format('M d, Y') }}</small></td>
                                <td>{{ Str::limit($order->recipient ?? 'N/A', 12) }}</td>
                                <td class="text-center">{{ $order->details->count() }}</td>
                                <td class="text-end"><span class="text-danger">-{{ $order->details->sum('quantity_issued') }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted mb-0">No issued stock out orders yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Category Summary -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Category Overview</h6>
                @if(!Auth::user()->isRegularAdmin())
                <a href="{{ route('inventory-reports.category') }}" class="btn btn-sm btn-primary">Full Report</a>
                @endif
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($stockByCategory as $category)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-6 mb-3">
                        <div class="text-center p-3 border rounded">
                            <div class="h4 mb-0 text-primary">{{ $category['stock'] }}</div>
                            <small class="text-muted">{{ $category['name'] }}</small>
                            <div class="small text-success">₱{{ number_format($category['value'], 0) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center text-muted">
                        @if(!Auth::user()->isRegularAdmin())
                        <p>No categories defined. <a href="{{ route('categories.create') }}">Create one</a></p>
                        @else
                        <p>No categories defined.</p>
                        @endif
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Stock Movement Chart
    const monthlyData = @json($monthlyMovement);
    
    const ctx = document.getElementById('stockMovementChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Stock In',
                data: monthlyData.map(item => item.in),
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1
            }, {
                label: 'Stock Out',
                data: monthlyData.map(item => item.out),
                backgroundColor: 'rgba(220, 53, 69, 0.8)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
});
</script>
@endpush