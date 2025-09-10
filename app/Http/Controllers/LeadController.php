<?php
namespace App\Http\Controllers;

use App\Models\Lead;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LeadController extends Controller
{
      public function crm()
    {
        $userId = Auth::id();
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   // ✅ match by id, not role
                    ->first();

        $role = $userDetails->role;
        return view('leads.crm',compact('role'));
    }

    
    public function index()
    {
        
        $userId = Auth::id();
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   // ✅ match by id, not role
                    ->first();

        $role = $userDetails->role;
        $leads = Lead::latest()->paginate(20);
        // dd($leads);
        return view('leads.index', compact('leads','role'));
    }
    public function show(Lead $lead)
    {
        
        $userId = Auth::id();
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   // ✅ match by id, not role
                    ->first();

        $role = $userDetails->role;
        return view('leads.show', compact('lead','role'));
    }

    public function edit(Lead $lead)
{
    
        $userId = Auth::id();
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   // ✅ match by id, not role
                    ->first();

        $role = $userDetails->role;
    return view('leads.edit', compact('lead','role'));
}
public function destroy(Lead $lead)
{
    $lead->delete();

    return redirect()->route('crm/lead-management')->with('success', 'Lead deleted successfully.');
}


public function update(Request $request, Lead $lead)
{
    $data = $request->validate([
        'full_name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email',
        'source' => 'nullable|string',
        'stage' => 'nullable|string',
        'owner' => 'nullable|string',
        'next_activity_at' => 'nullable|date',
        'notes' => 'nullable|string',
    ]);

    $lead->update($data);

    return redirect()->route('crm/lead-management')->with('success', 'Lead updated successfully.');
}


    public function create()
    {
        
        $userId = Auth::id();
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   // ✅ match by id, not role
                    ->first();

        $role = $userDetails->role;
        return view('leads.create',compact('role')); // optional dedicated page
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'full_name' => ['required','string','max:150'],
            'phone'     => ['nullable','string','max:30'],
            'email'     => ['nullable','email','max:150'],
            'source'    => ['nullable','string','max:50'],
            'stage'     => ['required','string','max:50'],
            'owner'     => ['nullable','string','max:100'],
            'notes'     => ['nullable','string'],
            'next_activity_at' => ['nullable','date'],
        ]);

        Lead::create($data);

        // If it came from the modal, we redirect back to the CRM page; otherwise to leads index.
        // return back()->with('success','Lead saved successfully.');
        return response()->json(['success' => true, 'redirect' => route('crm/lead-management')]);
    }
}
