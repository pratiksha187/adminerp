<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreDpr;
use App\Models\StoreInward;
use App\Models\StoreOutward;
use App\Models\StoreIssued;
use App\Models\StoreTask;
use App\Models\StoreStock;
use App\Models\StoreRequirement;
use App\Models\StoreRequirementItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreDprController extends Controller
{


public function index(Request $request)
{
    $userId = Auth::id();
    $role = DB::table('users')->where('id', $userId)->value('role');

    // DPRs with site name
    $dprs = DB::table('store_dprs')
        ->join('site_name', 'store_dprs.store_name', '=', 'site_name.id')
        ->select('store_dprs.id', 'site_name.name as site_name')
        ->get();

    // Inwards with available_qty
    $inwards = DB::table('store_inwards')
        ->join('store_requirement_items', 'store_inwards.item', '=', 'store_requirement_items.id')
        ->leftJoin('store_stocks', 'store_inwards.item', '=', 'store_stocks.item')
        ->select(
            'store_inwards.store_dpr_id',
            'store_requirement_items.name as item_name',
            'store_inwards.vendor',
            'store_inwards.rate',
            'store_inwards.qty',
            'store_inwards.type',
            DB::raw('COALESCE(store_stocks.available_qty, 0) as available_qty')
        )
        ->get()
        ->groupBy('store_dpr_id');

    // Outwards with available_qty
    $outwards = DB::table('store_outwards')
        ->join('store_requirement_items', 'store_outwards.item', '=', 'store_requirement_items.id')
        ->leftJoin('store_stocks', 'store_outwards.item', '=', 'store_stocks.item')
        ->select(
            'store_outwards.store_dpr_id',
            'store_requirement_items.name as item_name',
            'store_outwards.qty',
            DB::raw('COALESCE(store_stocks.available_qty, 0) as available_qty')
        )
        ->get()
        ->groupBy('store_dpr_id');

    // Issued with available_qty
    $issued = DB::table('store_issued')
        ->join('store_requirement_items', 'store_issued.item', '=', 'store_requirement_items.id')
        ->leftJoin('store_stocks', 'store_issued.item', '=', 'store_stocks.item')
        ->select(
            'store_issued.store_dpr_id',
            'store_requirement_items.name as item_name',
            'store_issued.qty',
            DB::raw('COALESCE(store_stocks.available_qty, 0) as available_qty')
        )
        ->get()
        ->groupBy('store_dpr_id');
    $stocks = DB::table('store_stocks')
        ->join('store_requirement_items', 'store_stocks.item', '=', 'store_requirement_items.id')
        ->select('store_stocks.*', 'store_requirement_items.name as item_name')
        ->get();
    return view('store_reports.list', compact('dprs', 'inwards', 'outwards', 'issued', 'role','stocks'));
}


    // 📌 Create Page
    public function storedpr()
    {
        $userId = Auth::id();
        $userDetails = DB::table('users')
            ->select('role')
            ->where('id', $userId)
            ->first();

        $role = $userDetails->role;

        $site_name = DB::table('site_name')->get();

        $material_name = DB::table('store_requirement_items')->where('is_approved','1')->get();

        $unit = DB::table('unit')->get();
        // dd($material_name);
        return view('store_reports.create', compact('role','site_name','material_name','unit'));
    }


public function store(Request $request)
{
    // ✅ Validation
    $request->validate([
        'store_name'       => 'required|string|max:255',
        'inward.*.item'    => 'nullable|exists:store_requirement_items,id',
        'inward.*.vendor'  => 'nullable|string|max:255',
        'inward.*.rate'    => 'nullable|numeric|min:0',
        'inward.*.qty'     => 'nullable|numeric|min:0',
        'inward.*.type'    => 'nullable',
        'outward.*.item'   => 'nullable|exists:store_requirement_items,id',
        'outward.*.qty'    => 'nullable|numeric|min:0',
        'issued.*.item'    => 'nullable|exists:store_requirement_items,id',
        'issued.*.qty'     => 'nullable|numeric|min:0',
        'tasks.*.task'     => 'nullable|string|max:1000',
    ]);

    DB::transaction(function() use ($request) {

        // 1️⃣ Save DPR
        $dprId = DB::table('store_dprs')->insertGetId([
            'store_name' => $request->store_name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2️⃣ Save Inwards & Update Stock
        if ($request->has('inward')) {
            foreach ($request->inward as $inward) {
                $itemId = $inward['item'] ?? null;
                $qty    = $inward['qty'] ?? 0;

                // Skip empty rows
                if (!$itemId || $qty <= 0) continue;

                DB::table('store_inwards')->insert([
                    'store_dpr_id' => $dprId,
                    'item'         => $itemId,
                    'vendor'       => $inward['vendor'] ?? null,
                    'rate'         => $inward['rate'] ?? 0,
                    'qty'          => $qty,
                    'type'         => $inward['type'] ?? null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

                // Update or insert stock
                $stock = DB::table('store_stocks')->where('item', $itemId)->first();
                if ($stock) {
                    DB::table('store_stocks')->where('item', $itemId)->increment('available_qty', $qty);
                } else {
                    DB::table('store_stocks')->insert([
                        'item'          => $itemId,
                        'available_qty' => $qty,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }
        }

        // 3️⃣ Save Outwards & Subtract Stock
        if ($request->has('outward')) {
            foreach ($request->outward as $outward) {
                $itemId = $outward['item'] ?? null;
                $qty    = $outward['qty'] ?? 0;

                // Skip empty rows
                if (!$itemId || $qty <= 0) continue;

                DB::table('store_outwards')->insert([
                    'store_dpr_id' => $dprId,
                    'item'         => $itemId,
                    'qty'          => $qty,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

                // Subtract stock safely
                DB::table('store_stocks')->where('item', $itemId)->decrement('available_qty', $qty);
            }
        }

        // 4️⃣ Save Issued & Subtract Stock
        if ($request->has('issued')) {
            foreach ($request->issued as $issued) {
                $itemId = $issued['item'] ?? null;
                $qty    = $issued['qty'] ?? 0;

                // Skip empty rows
                if (!$itemId || $qty <= 0) continue;

                DB::table('store_issued')->insert([
                    'store_dpr_id' => $dprId,
                    'item'         => $itemId,
                    'qty'          => $qty,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

                // Subtract stock safely
                DB::table('store_stocks')->where('item', $itemId)->decrement('available_qty', $qty);
            }
        }

        // 5️⃣ Save Tasks
        if ($request->has('tasks')) {
            foreach ($request->tasks as $task) {
                if (!empty($task['task'])) {
                    DB::table('store_tasks')->insert([
                        'store_dpr_id' => $dprId,
                        'task'         => $task['task'],
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }
        }

    }); // End transaction

    return redirect()->route('store-dpr.list')
                     ->with('success', 'Daily Progress Report saved successfully.');
}

    public function storerequirement(){
        $userId = Auth::id();
        $userDetails = DB::table('users')
            ->select('role')
            ->where('id', $userId)
            ->first();

        $role = $userDetails->role;
        $users = DB::table('users')->select('id','name')->get();

        $unit= DB::table('unit')->get();
        // dd($users);
        return view('store-requirement.create',compact('role','users','unit'));
    }

    public function storerequirementaceptedlist(){
         $userId = Auth::id();
        $userDetails = DB::table('users')
            ->select('role','name')
            ->where('id', $userId)
            ->first();
// dd($userDetails );
        $login_name = $userDetails->name;
        $role = $userDetails->role;
   
$requirements = DB::table('store_requirements as sr')
    ->join('users as u', 'sr.requester_id', '=', 'u.id')
    ->leftJoin('store_requirement_items as sri', 'sr.id', '=', 'sri.store_requirement_id')
    ->select(
        'sri.id',
        'sr.created_at',
        'u.name as requester_name',
        'sri.name',
        'sri.qty',
        'sri.unit',
        'sri.remark',
        'sri.is_approved'
    )
    ->orderBy('sr.created_at', 'desc')
    ->get();

        return view('store-requirement.storerequirementaceptedlist',compact('requirements','role','login_name'));
    }

public function storeRequirementSave(Request $request)
{
    // dd($request);
    $request->validate([
        'requester' => 'required|exists:users,id',
        'materials' => 'required|array|min:1',
        'materials.*.name' => 'required|string|max:255',
        'materials.*.qty'  => 'required|integer|min:1',
        'materials.*.unit' => 'nullable|string|max:50',
        'materials.*.remark' => 'nullable|string|max:255',
    ]);

    // Save main requirement
    $requirement = StoreRequirement::create([
        'requester_id' => $request->requester,
    ]);

    // Save all materials
    foreach ($request->materials as $mat) {
        StoreRequirementItem::create([
            'store_requirement_id' => $requirement->id,
            'name'   => $mat['name'],
            'qty'    => $mat['qty'],
            'unit'   => $mat['unit'] ?? null,
            'remark' => $mat['remark'] ?? null,
        ]);
    }

    return redirect()->route('store-requirement.list')
        ->with('success', 'Material Requirement saved successfully!');
}



public function storerequirementlist()
{
        $userId = Auth::id();
        $userDetails = DB::table('users')
            ->select('role','name')
            ->where('id', $userId)
            ->first();
// dd($userDetails );
        $login_name = $userDetails->name;
        $role = $userDetails->role;
   
$requirements = DB::table('store_requirements as sr')
    ->join('users as u', 'sr.requester_id', '=', 'u.id')
    ->leftJoin('store_requirement_items as sri', 'sr.id', '=', 'sri.store_requirement_id')
    ->select(
        'sri.id',
        'sr.created_at',
        'u.name as requester_name',
        'sri.name',
        'sri.qty',
        'sri.unit',
        'sri.remark',
        'sri.is_approved'
    )
    ->orderBy('sr.created_at', 'desc')
    ->get();

    // dd($requirements);
    return view('store-requirement.index', compact('requirements','role','login_name'));
}
  public function show($id)
    {
        $userId = Auth::id();
        $userDetails = DB::table('users')
            ->select('role')
            ->where('id', $userId)
            ->first();

        $role = $userDetails->role;
        $requirement = StoreRequirement::with('user', 'items')->findOrFail($id);
        return view('store-requirement.show', compact('requirement','role'));
    }

public function updateRequirementStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:0,1,2',
    ]);

    DB::table('store_requirement_items')
        ->where('id', $id)
        ->update(['is_approved' => $request->status]);

    return response()->json(['success' => true, 'message' => 'Status updated']);
}



}
