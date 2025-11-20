<?php

namespace App\Http\Controllers;

use App\Models\WorkEntry;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class EngineeringController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   
                    ->first();

        $role = $userDetails->role;
        // $chapters = \App\Models\Chapter::all();
        $chapters = DB::table('chapter')->get();

        $unit  = DB::table('unit')->get();

        $users = DB::table('users')
                ->whereIn('role', [4, 5,15,18])
                ->where('is_active',1)
                ->get();
        // dd($users);

        return view('engg.engineering',compact('chapters', 'unit','users','role'));
    }

    public function allenggworkentry(){
        $userId = Auth::id();
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   
                    ->first();

        $role = $userDetails->role;
        // $chapters = \App\Models\Chapter::all();
        $chapters = DB::table('chapter')->get();

        $unit  = DB::table('unit')->get();

        $users = DB::table('users')
                ->whereIn('role', [4, 5,15,18])
                ->where('is_active',1)
                ->get();

        return view('engg.all_enggdpr_data',compact('chapters', 'unit','users','role'));
    }

    public function saveworkdata(Request $request)
{
    // --------------------------- VALIDATION ---------------------------
    $validated = $request->validate([
        'date' => 'required|date',
        'chapter_id' => 'required|integer',
        'description' => 'required|string',
        'unit' => 'required',              // unit ID
        'length' => 'nullable|numeric',
        'breadth' => 'nullable|numeric',
        'height' => 'nullable|numeric',
        'days' => 'nullable|integer',
        'in_time' => 'nullable',
        'out_time' => 'nullable',
        'tonnage' => 'nullable|numeric',
        'total_quantity' => 'nullable|numeric',
        'supervisor_id' => 'required|integer',
        'labour' => 'nullable|array',
        'labour.*' => 'nullable|integer|min:0',
        'description_of_work_done' => 'nullable|string',
    ]);

    try {

        // --------------------------- INSERT INTO DATABASE ---------------------------
        $entry = WorkEntry::create([
            'date' => $validated['date'],
            'chapter_id' => $validated['chapter_id'],
            'description' => $validated['description'],
            'unit' => $validated['unit'],   // store unit ID (from dropdown)

            // Measurements
            'length' => $validated['length'] ?? 0,
            'breadth' => $validated['breadth'] ?? 0,
            'height' => $validated['height'] ?? 0,
            'days' => $validated['days'] ?? null,
            'in_time' => $validated['in_time'] ?? null,
            'out_time' => $validated['out_time'] ?? null,
            'tonnage' => $validated['tonnage'] ?? null,

            // Qty
            'total_quantity' => $validated['total_quantity'] ?? 0,

            // Supervisor
            'supervisor_id' => $validated['supervisor_id'],

            // Labour (json)
            'labour' => isset($validated['labour']) ? json_encode($validated['labour']) : json_encode([]),

            // Work done
            'description_of_work_done' => $validated['description_of_work_done'] ?? null,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }

    // --------------------------- RESPONSE ---------------------------
    return response()->json([
        'success' => true,
        'entry' => [
            'sr_no' => 0,
            'date' => $entry->date,
            'chapter' => optional($entry->chapter)->chapter_name,
            'description' => $entry->description,
            'total_quantity' => $entry->total_quantity,
            'labour_count' => collect(json_decode($entry->labour, true))->sum(),
        ],
    ]);
}


    public function data(Request $request)
    {
        $query = DB::table('work_entries')
            ->leftJoin('chapter', 'chapter.id', '=', 'work_entries.chapter_id')
            ->select([
                'work_entries.id',
                'work_entries.date',
                'chapter.chapter_name as chapter_name',
                'work_entries.description',
                'work_entries.total_quantity',
                'work_entries.labour'
            ]);

        return DataTables::of($query)
            ->addColumn('labour_count', function ($row) {
                $labour = json_decode($row->labour, true);
                return is_array($labour) ? array_sum($labour) : 0;
            })
            ->make(true);
    }

    
    public function getDescriptions($chapter_id)
    {
        $descriptions = DB::table('description_chapter')
                        ->where('chapter_id', $chapter_id)
                        ->select('id', 'description')
                        ->get();

        return response()->json($descriptions);
    }

//     public function view($id)
// {
//     $entry = DB::table('work_entries')
//         ->leftJoin('chapter', 'chapter.id', '=', 'work_entries.chapter_id')
//         ->leftJoin('users', 'users.id', '=', 'work_entries.supervisor_id')
//         ->select(
//             'work_entries.*',
//             'chapter.chapter_name',
//             'users.name as supervisor'
//         )
//         ->where('work_entries.id', $id)
//         ->first();
// dd($entry);
//     // FIX: Safe decode labour
//     $labour = json_decode($entry->labour, true);
//     $entry->labour = is_array($labour) ? $labour : [];

//     return view('engg.modal-view', compact('entry'));
// }

// public function view($id)
// {
//     $entry = DB::table('work_entries')
//         ->leftJoin('chapter', 'chapter.id', '=', 'work_entries.chapter_id')
//         ->leftJoin('users', 'users.id', '=', 'work_entries.supervisor_id')
//         ->select(
//             'work_entries.*',
//             'chapter.chapter_name',
//             'users.name as supervisor'
//         )
//         ->where('work_entries.id', $id)
//         ->first();

//     // FIX: Remove only the wrapping quotes
//     $cleanJson = trim($entry->labour, '"');

//     // Decode json
//     $decoded = json_decode($cleanJson, true);
//     dd($decoded);
//     // Assign safely
//     $entry->labour = is_array($decoded) ? $decoded : [];

//     return view('engg.modal-view', compact('entry'));
// }

public function view($id)
{
    // Fetch record
    $entry = DB::table('work_entries')
        ->leftJoin('chapter', 'chapter.id', '=', 'work_entries.chapter_id')
        ->leftJoin('users', 'users.id', '=', 'work_entries.supervisor_id')
        ->select(
            'work_entries.*',
            'chapter.chapter_name',
            'users.name as supervisor'
        )
        ->where('work_entries.id', $id)
        ->first();

    if (!$entry) {
        return "<p class='text-danger'>Entry not found.</p>";
    }

    /**
     * ------------------------------------------------------
     *  FIX LABOUR JSON (Your DB stores double-encoded JSON)
     * ------------------------------------------------------
     */

    $decoded = null;

    // Attempt 1: direct decode
    $step1 = json_decode($entry->labour, true);

    if (is_array($step1)) {
        // JSON was NOT double-encoded
        $decoded = $step1;
    } elseif (is_string($step1)) {
        // Attempt 2: decode the resulting string again
        $step2 = json_decode($step1, true);
        if (is_array($step2)) {
            $decoded = $step2;
        }
    } else {
        // Attempt 3: manually remove quotes & decode
        $clean = trim($entry->labour, '"');
        $step3 = json_decode($clean, true);
        if (is_array($step3)) {
            $decoded = $step3;
        }
    }

    // Last fallback
    if (!is_array($decoded)) {
        $decoded = [];
    }

    // Assign corrected labour array
    $entry->labour = $decoded;

    // Return modal view
    return view('engg.modal-view', compact('entry'));
}



}
