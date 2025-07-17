<?php

namespace App\Http\Controllers;

use App\Models\WorkEntry;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;


class EngineeringController extends Controller
{
    public function index()
    {
        // $chapters = \App\Models\Chapter::all();
        $chapters = DB::table('chapter')->get();

        $unit  = DB::table('unit')->get();

       $users = DB::table('users')
                ->whereIn('role', [3, 4])
                ->get();


        return view('engg.engineering',compact('chapters', 'unit','users'));
    }

    public function saveworkdata(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'chapter_id' => 'required',
            'description' => 'required|string',
            'unit' => 'required',
            'length' => 'nullable|numeric',
            'breadth' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'total_quantity' => 'nullable|numeric',
            'supervisor_id' => 'required',
            'labour' => 'nullable|array',
            'labour.*' => 'nullable|integer|min:0',
        ]);

        try {
            $entry = WorkEntry::create([
                'date' => $validated['date'],
                'chapter_id' => $validated['chapter_id'],
                'description' => $validated['description'],
                'unit' => $validated['unit'],
                'length' => $validated['length'] ?? 0,
                'breadth' => $validated['breadth'] ?? 0,
                'height' => $validated['height'] ?? 0,
                'total_quantity' => $validated['total_quantity'] ?? 0,
                'supervisor_id' => $validated['supervisor_id'],
                'labour' => $validated['labour'] ?? [],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'entry' => [
                'sr_no' => 0, // DataTables will handle this
                'date' => $entry->date->format('Y-m-d'),
                'chapter' => ['name' => $entry->chapter->name],
                'description' => $entry->description,
                'total_quantity' => $entry->total_quantity,
                'labour_count' => array_sum($validated['labour'] ?? []),
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

}
