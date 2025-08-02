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
     public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        LetterHead::create($request->only('date','name', 'ref_no', 'description'));

        return redirect()->back()->with('success', 'Letter Head added successfully.');
    }

     public function test(){
        return view('test');
    }
}
