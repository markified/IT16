<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Department;
use App\Models\Employee;
use App\Models\InventoryIssue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryIssueController extends Controller
{
    public function index()
    {
        $inventoryIssues = InventoryIssue::with(['product', 'department', 'employee'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('inventory-issues.index', compact('inventoryIssues'));
    }

    public function create()
    {
        $products = Product::where('quantity', '>', 0)->get();
        $departments = Department::all();
        $employees = Employee::all();

        return view('inventory-issues.create', compact('products', 'departments', 'employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'department_id' => 'required|exists:departments,id',
            'employee_id' => 'required|exists:employees,id',
            'quantity_issued' => 'required|integer|min:1',
            'issue_date' => 'required|date',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        if ($product->quantity < $request->quantity_issued) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['quantity_issued' => 'Not enough stock available. Current stock: ' . $product->quantity]);
        }

        DB::beginTransaction();

        try {
            InventoryIssue::create([
                'product_id' => $validated['product_id'],
                'department_id' => $validated['department_id'],
                'employee_id' => $validated['employee_id'],
                'quantity_issued' => $validated['quantity_issued'],
                'issue_date' => $validated['issue_date'],
                'reason' => $validated['reason'],
                'notes' => $validated['notes'],
                'issued_by' => Auth::id(),
            ]);

            $product->quantity -= $validated['quantity_issued'];
            $product->save();

            DB::commit();

            return redirect()->route('inventory-issues.index')
                ->with('success', 'Inventory item issued successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to issue inventory item: ' . $e->getMessage()]);
        }
    }
    public function show($id)
{
    $inventoryIssue = InventoryIssue::with(['product', 'department', 'employee'])->findOrFail($id);
    return view('inventory-issues.show', compact('inventoryIssue'));
}
}