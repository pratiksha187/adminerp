<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChallanController;
use App\Http\Controllers\EngineeringController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Admin Dashboard (only after login)
Route::get('/admin', [AdminController::class, 'index'])
    ->middleware('auth')
    ->name('admin');

Route::get('/challans', [ChallanController::class, 'fetch'])->name('challan.list');
Route::get('/challan', [ChallanController::class, 'index'])->name('challan');
Route::post('/save-chalan', [App\Http\Controllers\ChallanController::class, 'store'])->name('challan.save');
Route::get('/challan/{id}', [ChallanController::class, 'show'])->name('challan.show');
Route::get('/challan/datatables', [ChallanController::class, 'getChallanData'])->name('challan.datatables');


// Show the engineering page (GET)
// Route::get('/engineering', [EngineeringController::class, 'engineering'])->name('engineering');

// Save work data (POST)
// Route::post('/work-entry/save', [EngineeringController::class, 'saveworkdata'])->name('work-entry.save');
Route::get('/work-entry', [EngineeringController::class, 'index'])->name('work-entry.index'); // load the view
Route::post('/work-entry/save', [EngineeringController::class, 'saveworkdata'])->name('work-entry.save'); // save form
Route::get('/work-entry/data', [EngineeringController::class, 'data'])->name('work-entry.data'); // return datatable JSON



Route::get('/descriptions/{chapter_id}', [EngineeringController::class, 'getDescriptions']);
