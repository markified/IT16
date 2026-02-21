<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportViewController extends Controller
{
    /**
     * Display a listing of the reports.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reports = Report::with(['inventoryIssues.department', 'inventoryIssues.employee'])->get();
        return view('reports.view', compact('reports'));
    }

    /**
     * Display the specified report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $report = Report::with(['inventoryIssues.department', 'inventoryIssues.employee', 'suppliers'])->findOrFail($id);
        return view('reports.show', compact('report'));
    }
}
