<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LetterHead;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LetterHeadImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
class AdminController extends Controller
{

     public function index()
    {
        // dd($request);
      $userId = Auth::id();
    //   dd($userId);
      $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)
                    ->first();

       $role = $userDetails->role;
    // $role = 2
        return view('admin.dashboard',compact('role'));
    }
    public function letterhead(Request $request)
    {
        $userId = Auth::id();
        $userDetails = DB::table('users')
                        ->select('role')
                        ->where('id', $userId)
                        ->first();

        $role = $userDetails->role;
        $letterHeads = LetterHead::latest(); // Fetch the latest letter heads

        if ($request->ajax()) {
            return DataTables::of($letterHeads)
                ->addIndexColumn() // Add an auto-incrementing index column
                ->addColumn('date', function($row) {
                    return \Carbon\Carbon::parse($row->date)->format('Y-m-d');
                })
                ->make(true);
        }

        return view('admin.letterhead',compact('letterHeads', 'role'));
    }


    public function storeletterhead(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'assigned_to' => 'required|string|max:255',
        ]);

        $assignedTo = $request->assigned_to;

        // Generate fiscal year logic
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        $fiscalYear = $currentYear . '-' . substr($nextYear, -2);

        // Assign initials based on the assigned person
        $userInitials = '';
        switch ($assignedTo) {
            case 'Pirlpl':
                $userInitials = 'pi';
                break;
            case 'Shreeyash':
                $userInitials = 'sc';
                break;
            case 'Apurva':
                $userInitials = 'ap';
                break;
            case 'Swaraj':
                $userInitials = 'sw';
                break;
            default:
                $userInitials = 'xx';
                break;
        }

        // Generate ref_no based on fiscal year
        $baseRefNo = $userInitials . '/' . $fiscalYear . '/%';

        $existingRef = LetterHead::where('ref_no', 'like', $baseRefNo)
                                 ->orderBy('ref_no', 'desc')
                                 ->first();

        if ($existingRef) {
            preg_match('/\/' . $fiscalYear . '\/(\d{2})$/', $existingRef->ref_no, $matches);
            $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
            $newId = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newId = '01';
        }

        $refNo = str_replace('%', $newId, $baseRefNo);

        // Store the new letter head
        LetterHead::create([
            'date' => $request->date,
            'name' => $request->name,
            'ref_no' => $refNo,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Letter Head added successfully.');
    }

    public function importLetterhead(Request $request)
    {
        
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048',
        ]);

        // Check if the file is uploaded
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Import the file data using the LetterHeadImport class
            Excel::import(new LetterHeadImport, $file);

            return redirect()->back()->with('success', 'Letter Heads imported successfully.');
        }

        return redirect()->back()->with('error', 'No file uploaded.');
    }
}
