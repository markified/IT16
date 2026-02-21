<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\Department;
use App\Models\InventoryIssue;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get total counts
        $totalProducts = Product::count();
        $totalSuppliers = Supplier::count();
        $totalEmployees = Employee::count();
        $totalDepartments = Department::count();
        $totalPurchaseOrders = PurchaseOrder::count(); // Add this line

        // Get low stock items
        $lowStockItems = Product::whereRaw('quantity <= min_stock_level')->count();

        // Get items by status
        $itemsByStatus = Product::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        // Get recent inventory activities
        $recentActivities = InventoryIssue::with(['product', 'employee'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get pending purchase orders
        $pendingOrders = PurchaseOrder::where('status', 'pending')
            ->with('supplier')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalSuppliers',
            'totalEmployees',
            'totalDepartments',
            'lowStockItems',
            'itemsByStatus',
            'recentActivities',
            'pendingOrders',
            'totalPurchaseOrders'
        ));
    }

    public function employeeDashboard()
    {
        // Get total counts
        $totalProducts = Product::count();
        $totalSuppliers = Supplier::count();
        $totalEmployees = Employee::count();
        $totalDepartments = Department::count();
        $totalPurchaseOrders = PurchaseOrder::count();

        // Get low stock items
        $lowStockItems = Product::where('quantity', '<', 10)->count();

        // Get items by status
        $itemsByStatus = Product::selectRaw('status, count(*) as total')->groupBy('status')->get();

        // Get recent inventory activities
        $recentActivities = InventoryIssue::with(['product', 'employee'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get pending purchase orders (add this)
        $pendingOrders = PurchaseOrder::where('status', 'pending')
            ->with('supplier')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('employees.dashboard', compact(
            'totalProducts',
            'totalSuppliers',
            'totalEmployees',
            'totalDepartments',
            'lowStockItems',
            'itemsByStatus',
            'recentActivities',
            'totalPurchaseOrders',
            'pendingOrders' // Add this to compact
        ));
    }
}
