<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LetterHead;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function letterhead(){
        $letterHeads = LetterHead::latest()->get();
        return view('admin.letterhead', compact('letterHeads'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'date' => 'required|date',
    //         'name' => 'required|string|max:255',
    //         'description' => 'required|string|max:255',
    //     ]);

    //     LetterHead::create($request->only('date','name', 'ref_no', 'description'));

    //     return redirect()->back()->with('success', 'Letter Head added successfully.');
    // }
//     public function storeletterhead(Request $request)
// {
//     $request->validate([
//         'date' => 'required|date',
//         'name' => 'required|string|max:255',
//         'description' => 'required|string|max:255',
//         'assigned_to' => 'required|string|max:255', // Validate the 'assigned_to' field
//     ]);

//     // Get the selected "assigned_to" value
//     $assignedTo = $request->assigned_to;
//     $currentYear = date('Y'); // Current year (2025, 2026, etc.)
//     $userInitials = '';

//     // Set initials for the user
//     switch ($assignedTo) {
//         case 'Pirlpl':
//             $userInitials = 'pi';
//             break;
//         case 'Shreeyash':
//             $userInitials = 'sc';
//             break;
//         case 'Apurva':
//             $userInitials = 'ap';
//             break;
//         case 'Swaraj':
//             $userInitials = 'sw';
//             break;
//         default:
//             // Fallback initials
//             $userInitials = 'xx';
//             break;
//     }

//     // Generate the base ref_no without the incrementing ID
//     $baseRefNo = $userInitials . '/%/'. $currentYear;

//     // Check if the ref_no exists in the database
//     $existingRef = LetterHead::where('ref_no', 'like', $baseRefNo)->orderBy('ref_no', 'desc')->first();

//     // If a reference already exists, we need to increment the ID part (e.g., pi/02/2025)
//     if ($existingRef) {
//         // Extract the numeric part from the last ref_no, increment it
//         preg_match('/(\d{2})\/' . $currentYear . '$/', $existingRef->ref_no, $matches);
//         $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
//         $newId = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT); // Increment and pad with leading zero
//     } else {
//         // If no previous ref_no exists, start with "01"
//         $newId = '01';
//     }

//     // Generate the final ref_no
//     $refNo = str_replace('%', $newId, $baseRefNo);

//     // Store the data, including the dynamically generated ref_no
//     LetterHead::create([
//         'date' => $request->date,
//         'name' => $request->name,
//         'ref_no' => $refNo, // Save the generated ref_no
//         'description' => $request->description,
//     ]);

//     return redirect()->back()->with('success', 'Letter Head added successfully.');
// }
// public function storeletterhead(Request $request)
// {
//     $request->validate([
//         'date' => 'required|date',
//         'name' => 'required|string|max:255',
//         'description' => 'required|string|max:255',
//         'assigned_to' => 'required|string|max:255', // Validate the 'assigned_to' field
//     ]);

//     // Get the selected "assigned_to" value
//     $assignedTo = $request->assigned_to;
    
//     // Get the current year and calculate the fiscal year range (2025-26)
//     $currentYear = date('Y'); // Current year (e.g., 2025)
//     $nextYear = $currentYear + 1; // Next year (e.g., 2026)
//     $fiscalYear = $currentYear . '-' . substr($nextYear, -2); // Generate fiscal year range (e.g., 2025-26)

//     $userInitials = '';

//     // Set initials for the user
//     switch ($assignedTo) {
//         case 'Pirlpl':
//             $userInitials = 'pi';
//             break;
//         case 'Shreeyash':
//             $userInitials = 'sc';
//             break;
//         case 'Apurva':
//             $userInitials = 'ap';
//             break;
//         case 'Swaraj':
//             $userInitials = 'sw';
//             break;
//         default:
//             // Fallback initials
//             $userInitials = 'xx';
//             break;
//     }

//     // Generate the base ref_no without the incrementing ID
//     $baseRefNo = $userInitials . '/%/' . $fiscalYear; // e.g., pi/%/2025-26

//     // Check if the ref_no exists in the database
//     $existingRef = LetterHead::where('ref_no', 'like', $baseRefNo)->orderBy('ref_no', 'desc')->first();

//     // If a reference already exists, we need to increment the ID part (e.g., pi/02/2025-26)
//     if ($existingRef) {
//         // Extract the numeric part from the last ref_no, increment it
//         preg_match('/(\d{2})\/' . $fiscalYear . '$/', $existingRef->ref_no, $matches);
//         $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
//         $newId = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT); // Increment and pad with leading zero
//     } else {
//         // If no previous ref_no exists, start with "01"
//         $newId = '01';
//     }

//     // Generate the final ref_no
//     $refNo = str_replace('%',$baseRefNo,$newId);

//     // Store the data, including the dynamically generated ref_no
//     LetterHead::create([
//         'date' => $request->date,
//         'name' => $request->name,
//         'ref_no' => $refNo, // Save the generated ref_no
//         'description' => $request->description,
//     ]);

//     return redirect()->back()->with('success', 'Letter Head added successfully.');
// }

// public function storeletterhead(Request $request)
// {
//     $request->validate([
//         'date' => 'required|date',
//         'name' => 'required|string|max:255',
//         'description' => 'required|string|max:255',
//         'assigned_to' => 'required|string|max:255', // Validate the 'assigned_to' field
//     ]);

//     // Get the selected "assigned_to" value
//     $assignedTo = $request->assigned_to;
    
//     // Get the current year and calculate the fiscal year range (2025-26)
//     $currentYear = date('Y'); // Current year (e.g., 2025)
//     $nextYear = $currentYear + 1; // Next year (e.g., 2026)
//     $fiscalYear = $currentYear . '-' . substr($nextYear, -2); // Generate fiscal year range (e.g., 2025-26)

//     $userInitials = '';

//     // Set initials for the user
//     switch ($assignedTo) {
//         case 'Pirlpl':
//             $userInitials = 'pi';
//             break;
//         case 'Shreeyash':
//             $userInitials = 'sc';
//             break;
//         case 'Apurva':
//             $userInitials = 'ap';
//             break;
//         case 'Swaraj':
//             $userInitials = 'sw';
//             break;
//         default:
//             // Fallback initials
//             $userInitials = 'xx';
//             break;
//     }

//     // Generate the base ref_no without the incrementing ID
//     $baseRefNo = $userInitials . '/' . $fiscalYear . '/%'; // e.g., pi/2025-26/%

//     // Check if the ref_no exists in the database for the selected fiscal year and assigned user
//     $existingRef = LetterHead::where('ref_no', 'like', $baseRefNo)->orderBy('ref_no', 'desc')->first();

//     // If a reference already exists, we need to increment the ID part (e.g., pi/2025-26/002)
//     if ($existingRef) {
//         // Extract the numeric part from the last ref_no, increment it
//         preg_match('/(\d{3})\/' . $fiscalYear . '$/', $existingRef->ref_no, $matches);
//         $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
//         $newId = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT); // Increment and pad with leading zeros (e.g., 001, 002, 003)
//     } else {
//         // If no previous ref_no exists, start with "001"
//         $newId = '001';
//     }

//     // Generate the final ref_no with the incremented ID
//     $refNo = $userInitials . '/' . $fiscalYear . '/' . $newId; // e.g., pi/2025-26/001

//     // Store the data, including the dynamically generated ref_no
//     LetterHead::create([
//         'date' => $request->date,
//         'name' => $request->name,
//         'ref_no' => $refNo, // Save the generated ref_no
//         'description' => $request->description,
//     ]);

//     return redirect()->back()->with('success', 'Letter Head added successfully.');
// }
// public function storeletterhead(Request $request)
// {
//     $request->validate([
//         'date' => 'required|date',
//         'name' => 'required|string|max:255',
//         'description' => 'required|string|max:255',
//         'assigned_to' => 'required|string|max:255', // Validate the 'assigned_to' field
//     ]);

//     // Get the selected "assigned_to" value
//     $assignedTo = $request->assigned_to;
    
//     // Get the current year and calculate the fiscal year range (2025-26)
//     $currentYear = date('Y'); // Current year (e.g., 2025)
//     $nextYear = $currentYear + 1; // Next year (e.g., 2026)
//     $fiscalYear = $currentYear . '-' . substr($nextYear, -2); // Generate fiscal year range (e.g., 2025-26)

//     $userInitials = '';

//     // Set initials for the user
//     switch ($assignedTo) {
//         case 'Pirlpl':
//             $userInitials = 'pi';
//             break;
//         case 'Shreeyash':
//             $userInitials = 'sc';
//             break;
//         case 'Apurva':
//             $userInitials = 'ap';
//             break;
//         case 'Swaraj':
//             $userInitials = 'sw';
//             break;
//         default:
//             // Fallback initials
//             $userInitials = 'xx';
//             break;
//     }

//     // Generate the base ref_no without the incrementing ID
//     $baseRefNo = $userInitials . '/%/' . $fiscalYear; // e.g., pi/%/2025-26

//     // Check if the ref_no exists in the database
//     $existingRef = LetterHead::where('ref_no', 'like', $baseRefNo)->orderBy('ref_no', 'desc')->first();

//     // If a reference already exists, we need to increment the ID part (e.g., pi/02/2025-26)
//     if ($existingRef) {
//         // Extract the numeric part from the last ref_no, increment it
//         preg_match('/(\d{2})\/' . $fiscalYear . '$/', $existingRef->ref_no, $matches);
//         $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
//         $newId = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT); // Increment and pad with leading zero
//     } else {
//         // If no previous ref_no exists, start with "01"
//         $newId = '01';
//     }

//     // Generate the final ref_no by concatenating the baseRefNo with the incremented ID
//     $refNo = str_replace('%', $newId, $baseRefNo); // Replace % with the incremented ID

//     // Store the data, including the dynamically generated ref_no
//     LetterHead::create([
//         'date' => $request->date,
//         'name' => $request->name,
//         'ref_no' => $refNo, // Save the generated ref_no
//         'description' => $request->description,
//     ]);

//     return redirect()->back()->with('success', 'Letter Head added successfully.');
// }
public function storeletterhead(Request $request)
{
    $request->validate([
        'date' => 'required|date',
        'name' => 'required|string|max:255',
        'description' => 'required|string|max:255',
        'assigned_to' => 'required|string|max:255', // Validate the 'assigned_to' field
    ]);

    // Get the selected "assigned_to" value
    $assignedTo = $request->assigned_to;
    
    // Get the current year and calculate the fiscal year range (2025-26)
    $currentYear = date('Y'); // Current year (e.g., 2025)
    $nextYear = $currentYear + 1; // Next year (e.g., 2026)
    $fiscalYear = $currentYear . '-' . substr($nextYear, -2); // Generate fiscal year range (e.g., 2025-26)

    $userInitials = '';

    // Set initials for the user
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
            // Fallback initials
            $userInitials = 'xx';
            break;
    }

    // Generate the base ref_no with the fiscal year (without the incrementing ID yet)
    $baseRefNo = $userInitials . '/' . $fiscalYear . '/%'; // e.g., pi/2025-26/% 

    // Check if the ref_no exists in the database
    $existingRef = LetterHead::where('ref_no', 'like', $baseRefNo)->orderBy('ref_no', 'desc')->first();

    // If a reference already exists, we need to increment the ID part (e.g., pi/2025-26/02)
    if ($existingRef) {
        // Extract the numeric part from the last ref_no, increment it
        preg_match('/\/' . $fiscalYear . '\/(\d{2})$/', $existingRef->ref_no, $matches);
        $lastNumber = isset($matches[1]) ? (int)$matches[1] : 0;
        $newId = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT); // Increment and pad with leading zero
    } else {
        // If no previous ref_no exists, start with "01"
        $newId = '01';
    }

    // Generate the final ref_no by replacing the '%' with the incremented ID
    $refNo = str_replace('%', $newId, $baseRefNo); // e.g., pi/2025-26/02

    // Store the data, including the dynamically generated ref_no
    LetterHead::create([
        'date' => $request->date,
        'name' => $request->name,
        'ref_no' => $refNo, // Save the generated ref_no
        'description' => $request->description,
    ]);

    return redirect()->back()->with('success', 'Letter Head added successfully.');
}




     public function test(){
        return view('test');
    }
}
