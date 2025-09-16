<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\StoreReport;
class StoreReportController extends Controller
{
    // Display all store reports
    public function index()
    {   $userId = Auth::id();
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   
                    ->first();

        $role = $userDetails->role;

        $reports = StoreReport::all();
        return view('store_reports.index', compact('reports','role'));
    }

    // Show the form to create a new report
    public function create()
    {
           $userId = Auth::id();
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   
                    ->first();

        $role = $userDetails->role;
        return view('store_reports.create',compact('role'));
    }

    // Store a new report
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'store_name' => 'required|string',
            'inward_material' => 'required|json',
            'outward_material' => 'required|json',
            'tasks_completed' => 'required|json',
        ]);

        StoreReport::create($validated);

        return redirect()->route('store_reports.index');
    }

    // Show a specific report
    public function show(StoreReport $storeReport)
    {
        return view('store_reports.show', compact('storeReport'));
    }

    // Show the form to edit a report
    public function edit(StoreReport $storeReport)
    {
        return view('store_reports.edit', compact('storeReport'));
    }

    // Update the report
    public function update(Request $request, StoreReport $storeReport)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'store_name' => 'required|string',
            'inward_material' => 'required|json',
            'outward_material' => 'required|json',
            'tasks_completed' => 'required|json',
        ]);

        $storeReport->update($validated);

        return redirect()->route('store_reports.index');
    }

    // Delete a report
    public function destroy(StoreReport $storeReport)
    {
        $storeReport->delete();

        return redirect()->route('store_reports.index');
    }
}

