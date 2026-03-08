<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Report;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display a listing of the reports.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reports = Report::orderBy('created_at', 'desc')->paginate(10);

        return view('reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new report.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $reportTypes = ['inventory', 'purchase_order', 'issue', 'supplier'];
        $suppliers = Supplier::all();

        return view('reports.create', compact('reportTypes', 'suppliers'));
    }

    /**
     * Store a newly created report in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'report_type' => 'required|in:inventory,purchase_order,issue,supplier',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $reportData = $this->generateReportData(
                $request->report_type,
                $request->start_date,
                $request->end_date,
                $request->all()
            );

            $report = new Report([
                'title' => $request->title,
                'report_type' => $request->report_type,
                'parameters' => $request->except(['_token', 'title', 'report_type', 'start_date', 'end_date']),
                'data' => $reportData,
                'generated_by' => Auth::id(),
                'report_date' => now(),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            $report->save();

            return redirect()->route('reports.show', $report->id)
                ->with('success', 'Report created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Failed to create report. Please try again.');
        }
    }

    /**
     * Display the specified report.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Report $report)
    {
        return view('reports.show', compact('report'));
    }

    /**
     * Show the form for editing the specified report.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Report $report)
    {
        $reportTypes = ['inventory', 'purchase_order', 'issue', 'supplier'];
        $suppliers = Supplier::all();

        return view('reports.edit', compact('report', 'reportTypes', 'suppliers'));
    }

    /**
     * Update the specified report in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Report $report)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'report_type' => 'required|in:inventory,purchase_order,issue,supplier',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $reportData = $this->generateReportData(
                $request->report_type,
                $request->start_date,
                $request->end_date,
                $request->all()
            );

            $report->update([
                'title' => $request->title,
                'report_type' => $request->report_type,
                'parameters' => $request->except(['_token', '_method', 'title', 'report_type', 'start_date', 'end_date']),
                'data' => $reportData,
                'report_date' => now(),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            return redirect()->route('reports.show', $report->id)
                ->with('success', 'Report updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Failed to update report. Please try again.');
        }
    }

    /**
     * Remove the specified report from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Report $report)
    {
        try {
            $report->delete();

            return redirect()->route('reports.index')
                ->with('success', 'Report deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('reports.index')
                ->with('error', 'Failed to delete report. Please try again.');
        }
    }

    /**
     * Generate report data based on type, date range, and optional parameters.
     *
     * @param  string  $reportType
     * @param  string  $startDate
     * @param  string  $endDate
     * @param  array  $parameters
     * @return array
     */
    private function generateReportData($reportType, $startDate, $endDate, $parameters)
    {
        $data = [];

        switch ($reportType) {
            case 'inventory':
                $query = Product::query();

                if (! empty($parameters['type'])) {
                    $query->where('type', $parameters['type']);
                }

                if (isset($parameters['low_stock']) && $parameters['low_stock']) {
                    $query->whereRaw('quantity <= min_stock_level');
                }

                $data['products'] = $query->get()->toArray();
                $data['summary'] = [
                    'total_products' => Product::count(),
                    'low_stock_count' => Product::whereRaw('quantity <= min_stock_level')->count(),
                    'out_of_stock_count' => Product::where('quantity', 0)->count(),
                    'total_value' => Product::sum(DB::raw('quantity * price_per_item')),
                ];
                break;

            case 'purchase_order':
                $query = PurchaseOrder::with(['supplier', 'orderDetails.product'])
                    ->whereBetween('created_at', [$startDate, $endDate]);

                if (! empty($parameters['status'])) {
                    $query->where('status', $parameters['status']);
                }

                if (! empty($parameters['supplier_id'])) {
                    $query->where('supplier_id', $parameters['supplier_id']);
                }

                $purchaseOrders = $query->get();
                $data['purchase_orders'] = $purchaseOrders->toArray();
                $data['summary'] = [
                    'total_orders' => $purchaseOrders->count(),
                    'total_amount' => $purchaseOrders->sum('total_amount'),
                    'pending_orders' => $purchaseOrders->where('status', 'pending')->count(),
                    'completed_orders' => $purchaseOrders->whereIn('status', ['received', 'completed'])->count(),
                ];
                break;

            case 'issue':
                $query = DB::table('inventory_issues')
                    ->join('products', 'inventory_issues.product_id', '=', 'products.id')
                    ->join('employees', 'inventory_issues.employee_id', '=', 'employees.id')
                    ->select(
                        'inventory_issues.*',
                        'products.name as product_name',
                        'employees.name as employee_name'
                    )
                    ->whereBetween('issue_date', [$startDate, $endDate]);

                $issues = $query->get();
                $data['issues'] = $issues->toArray();
                $data['summary'] = [
                    'total_issues' => $issues->count(),
                    'total_items_issued' => $issues->sum('quantity_issued'),
                ];
                break;

            case 'supplier':
                $query = Supplier::with(['purchaseOrders' => function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('created_at', [$startDate, $endDate]);
                }]);

                if (! empty($parameters['supplier_id'])) {
                    $query->where('id', $parameters['supplier_id']);
                }

                $suppliers = $query->get();
                $data['suppliers'] = $suppliers->toArray();
                $data['summary'] = [
                    'total_suppliers' => $suppliers->count(),
                    'total_orders' => $suppliers->flatMap(function ($supplier) {
                        return $supplier->purchaseOrders;
                    })->count(),
                    'total_amount' => $suppliers->flatMap(function ($supplier) {
                        return $supplier->purchaseOrders;
                    })->sum('total_amount'),
                ];
                break;
        }

        return $data;
    }

    /**
     * Download report as PDF
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(Report $report)
    {
        // This is a placeholder for PDF generation
        // In a real application, you would implement PDF generation here
        return redirect()->back()->with('info', 'PDF download functionality to be implemented.');
    }
}
