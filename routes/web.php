<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChallanController;
use App\Http\Controllers\EngineeringController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AttendanceController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {

    // Dashboard
    // Route::get('/dashboard', function () {
    //     return 'Welcome to dashboard!';
    // })->name('dashboard');

    // Admin
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Attendance
    Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockin');
    Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockout');

    // Register / All Users
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    // Route::get('Alluser', [RegisterController::class, 'Alluser'])->name('Alluser');
    Route::delete('/employees/{id}', [RegisterController::class, 'destroy'])->name('employees.destroy');

    // Challan
    Route::get('/challans', [ChallanController::class, 'fetch'])->name('challan.list');
    Route::get('/challan', [ChallanController::class, 'index'])->name('challan');
    Route::post('/save-chalan', [App\Http\Controllers\ChallanController::class, 'store'])->name('challan.save');
    Route::get('/challan/{id}', [ChallanController::class, 'show'])->name('challan.show');
    Route::get('/challan/datatables', [ChallanController::class, 'getChallanData'])->name('challan.datatables');

    // Work Entry
    Route::get('/work-entry', [EngineeringController::class, 'index'])->name('work-entry.index');
    Route::post('/work-entry/save', [EngineeringController::class, 'saveworkdata'])->name('work-entry.save');
    Route::get('/work-entry/data', [EngineeringController::class, 'data'])->name('work-entry.data');

    // Chapter Descriptions
    Route::get('/descriptions/{chapter_id}', [EngineeringController::class, 'getDescriptions']);

    Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    

    Route::get('/attendance/datatables', [AttendanceController::class, 'attendanceDatatable'])->name('attendance.datatables');

    Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');

    Route::get('/manualattendence', [AttendanceController::class, 'manualattendence'])->name('attendance.manualattendence');
    Route::get('/acceptattendence', [AttendanceController::class, 'acceptattendence'])->name('attendance.acceptattendence');

    
    Route::post('/attendance/manual', [AttendanceController::class, 'manualEntry'])->name('attendance.manual');
    Route::get('/attendance/manual/data', [AttendanceController::class, 'getManualData'])->name('attendance.manual.data');

Route::post('/attendance/manual/action', [AttendanceController::class, 'handleManualAction'])->name('attendance.manual.action');




});

