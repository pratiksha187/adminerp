<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Challan;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ChallanController extends Controller
{
    public function index()
    {
        $users = User::where('role', '2')
                    ->whereNotNull('mobile_no')
                    ->where('mobile_no', '!=', '')
                    ->get();

        return view('accounts.chalan', compact('users'));
    }

   
    // public function store(Request $request)
    // {
    //     // Log request for debugging
    //     \Log::info('Challan POST Data:', $request->all());

    //     // Validate request data
    //     $validated = $request->validate([
    //         'challan_no' => 'required|string|max:50',
    //         'date' => 'required|date',
    //         'party_name' => 'required|string|max:100',
    //         'material' => 'required|array|min:1',
    //         'vehicle_no' => 'nullable|string|max:50',
    //         'measurement' => 'nullable|string|max:50',
    //         'location' => 'nullable|string|max:100',
    //         'time' => 'nullable|string|max:50',
    //         'receiver_sign' => 'nullable|string|max:100',
    //         'driver_sign' => 'nullable|string|max:100',
    //         'driver_name' => 'nullable|string|max:100',
    //         'quantity' => 'nullable', // handled below
    //         'remark' => 'nullable',
    //     ]);

    //     // Convert arrays to comma-separated strings
    //     $validated['material'] = implode(', ', $validated['material']);

    //     if (isset($validated['quantity']) && is_array($validated['quantity'])) {
    //         $validated['quantity'] = implode(', ', $validated['quantity']);
    //     }


    //     try {
    //         // Create Challan record
    //         $challan = Challan::create($validated);

    //         // Generate and save PDF
    //         $pdf = \PDF::loadView('pdf.challan', ['challan' => $challan]);
    //         $pdfFileName = 'challans/challan_' . $challan->id . '.pdf';
    //         Storage::disk('public')->put($pdfFileName, $pdf->output());

    //         // Save PDF path
    //         $challan->update(['pdf_path' => $pdfFileName]);

    //         return response()->json([
    //             'message' => 'Challan saved and PDF generated.',
    //             'challan_id' => $challan->id,
    //             'pdf_url' => Storage::url($pdfFileName)
    //         ]);
    //     } catch (\Exception $e) {
    //         \Log::error('Failed to save challan', ['error' => $e->getMessage()]);
    //         return response()->json(['message' => 'Failed to save challan.'], 500);
    //     }
    // }
    public function store(Request $request)
{
    \Log::info('Challan POST Data:', $request->all());

    $validated = $request->validate([
        'date' => 'required|date',
        'party_name' => 'required|string|max:100',
        'material' => 'required|array|min:1',
        'vehicle_no' => 'nullable|string|max:50',
        'measurement' => 'nullable|string|max:50',
        'location' => 'nullable|string|max:100',
        'time' => 'nullable|string|max:50',
        'receiver_sign' => 'nullable|string|max:100',
        'driver_sign' => 'nullable|string|max:100',
        'driver_name' => 'nullable|string|max:100',
        'quantity' => 'nullable',
        'remark' => 'nullable',
    ]);

    $validated['material'] = implode(', ', $validated['material']);
    if (isset($validated['quantity']) && is_array($validated['quantity'])) {
        $validated['quantity'] = implode(', ', $validated['quantity']);
    }

    try {
        // Step 1: Temporarily insert record without challan_no
        $challan = Challan::create(array_merge($validated, ['challan_no' => '']));

        // Step 2: Generate challan_no
        $year = date('Y', strtotime($validated['date']));
        $challan_no = 'DC/' . $year . '/' . str_pad($challan->id, 2, '0', STR_PAD_LEFT);

        // Step 3: Update challan_no
        $challan->update(['challan_no' => $challan_no]);

        // Step 4: Generate and save PDF
        $pdf = \PDF::loadView('pdf.challan', ['challan' => $challan]);
        $pdfFileName = 'challans/challan_' . $challan->id . '.pdf';
        Storage::disk('public')->put($pdfFileName, $pdf->output());

        // Step 5: Save PDF path
        $challan->update(['pdf_path' => $pdfFileName]);

        return response()->json([
            'message' => 'Challan saved and PDF generated.',
            'challan_id' => $challan->id,
            'challan_no' => $challan->challan_no,
            'pdf_url' => Storage::url($pdfFileName)
        ]);
    } catch (\Exception $e) {
        \Log::error('Failed to save challan', ['error' => $e->getMessage()]);
        return response()->json(['message' => 'Failed to save challan.'], 500);
    }
}



    public function fetch(Request $request)
    {
        $challans = Challan::latest()->paginate(10);
        return response()->json($challans);
    }

    public function show($id)
    {
        $challan = Challan::find($id);

        if (!$challan) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json($challan);
    }

}
