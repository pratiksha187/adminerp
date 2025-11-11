<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;


class LeaveController extends Controller
{

    public function index() {
        $userId = Auth::id();

        $userDetails = DB::table('users')
                    ->select('role','join_date')
                    ->where('id', $userId)   
                    ->first();

        $role = $userDetails->role;
        $join_date = $userDetails->join_date;

        // check probation period (less than 3 months)
        $disableType = false;
        if ($join_date) {
            $join = \Carbon\Carbon::parse($join_date);
            $monthsDiff = $join->diffInMonths(now());
            $disableType = $monthsDiff < 3; // true if less than 3 months
        }

        $leaves = Leave::where('user_id', $userId)->latest()->get();

        return view('leaves.index', compact('leaves','role','disableType'));
    }


    public function create() {
        return view('leaves.create');
    }

    public function store(Request $request) {
        $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'type' => 'required|string',
            'reason' => 'nullable|string',
            'hod_name'=> 'required'
        ]);

        $user = Auth::user();

        $leaveColumn = match ($request->type) {
            'Sick'   => 'sl',
            'Casual' => 'cl',
            'Paid'   => 'el',
            default  => null,
        };

        if ((int)$user->$leaveColumn <= 0) {
            return redirect()->back()->with('error', 'You don’t have enough ' . $request->type . ' leaves left.');
        }

        Leave::create([
            'user_id' => Auth::id(),
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'type' => $request->type,
            'reason' => $request->reason,
            'hod_name' => $request->hod_name,
            'status' => 'Pending',
        ]);

        $user->decrement($leaveColumn, 1);
        return redirect()->route('leave.index')
        ->with('success', 'Leave applied successfully. Remaining ' . $request->type . ' leaves: ' . ($user->$leaveColumn - 1));
        // return redirect()->route('leave.index')->with('success', 'Leave applied successfully.');
    }

    public function hrIndex()
    {
        $userId = Auth::id();
        // dd($userId);
        $userDetails = DB::table('users')
                    ->select('role')
                    ->where('id', $userId)   
                    ->first();

        $role = $userDetails->role;
        // Show all leave applications for HR
        $leaves = Leave::with('user')->latest()->get();
        return view('leaves.leaves_responce', compact('leaves','role'));
    }

    // public function updateStatus(Request $request, $id)
    // {
    //     $request->validate([
    //         'status' => 'required|in:Approved,Rejected',
    //         'hr_reason' => 'nullable|string|max:500',
    //     ]);

    //     $leave = Leave::findOrFail($id);
    //     $leave->status = $request->status;
    //     $leave->hr_reason = $request->hr_reason;
    //     $leave->save();
    //     if ($request->status === 'Rejected') {
    //             $user = $leave->user; // relationship: Leave belongsTo User

    //             // Map type back to users table column
    //             $leaveColumn = match ($leave->type) {
    //                 'Sick'   => 'sl',
    //                 'Casual' => 'cl',
    //                 'Paid'   => 'el',
    //                 default  => null,
    //             };

    //             if ($leaveColumn) {
    //                 // Give back 1 leave balance
    //                 $user->increment($leaveColumn, 1);
    //             }
    //     }

    //     return back()->with('success', 'Leave status updated successfully!');
    // }
    public function updateStatus(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:Approved,Rejected',
        'hr_reason' => 'nullable|string|max:500',
    ]);

    $leave = Leave::findOrFail($id);
    $leave->status = $request->status;
    $leave->hr_reason = $request->hr_reason;
    $leave->save();

    if ($request->status === 'Rejected') {
        $user = $leave->user;

        $leaveColumn = match ($leave->type) {
            'Sick'   => 'sl',
            'Casual' => 'cl',
            'Paid'   => 'el',
            default  => null,
        };

        if ($leaveColumn) {
            $user->increment($leaveColumn, 1);
        }
    }

    // ✅ Generate PDF after status update
    $pdf = Pdf::loadView('leaves.pdf_leave', compact('leave'));

    $fileName = 'leave_'.$leave->id.'_'.time().'.pdf';
    $path = 'leave_pdfs/'.$fileName;

    Storage::disk('public')->put($path, $pdf->output());

    $leave->pdf_path = $path;
    $leave->save();

    return back()->with('success', 'Leave status updated and PDF generated successfully!');
}

}


