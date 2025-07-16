<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChallanController;
use App\Http\Controllers\EngineeringController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisterController;

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::get('Alluser', [RegisterController::class, 'showRegistrationForm'])->name('Alluser');
Route::delete('/employees/{id}', [RegisterController::class, 'destroy'])->name('employees.destroy');

Route::post('register', [RegisterController::class, 'register']);
Route::get('/dashboard', function () {
    return 'Welcome to dashboard!';
})->middleware('auth')->name('dashboard');

Route::get('/', function () {
    return view('auth.login');
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
