<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChallanController;
use App\Http\Controllers\EngineeringController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PaymentController;
use App\Exports\PaymentsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\AttendanceCalendarController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/test', [App\Http\Controllers\AdminController::class, 'test'])->name('test');


Route::middleware(['auth'])->group(function () {

Route::get('/attendance/calendar', [AttendanceCalendarController::class, 'view'])->name('attendance.calendar.view');
Route::get('/attendance/calendar/events', [AttendanceCalendarController::class, 'events'])->name('attendance.calendar.events');


    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    // Attendance
    Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockin');
    Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockout');

    // Register / All Users
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    // Route::get('Alluser', [RegisterController::class, 'Alluser'])->name('Alluser');
    Route::delete('/employees/{id}', [RegisterController::class, 'destroy'])->name('employees.destroy');

Route::post('/employees/{id}/status', [RegisterController::class, 'updateStatus']);

    // Challan
    Route::get('/challans', [ChallanController::class, 'fetch'])->name('challan.list');
    Route::get('/challan', [ChallanController::class, 'index'])->name('challan');
    Route::post('/save-chalan', [ChallanController::class, 'store'])->name('challan.save');
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

    Route::get('/letterhead', [AdminController::class, 'letterhead'])->name('letterhead');

    // Route::get('/letterhead', [LetterHeadController::class, 'index'])->name('letterhead.index');
    Route::post('/letterhead', [AdminController::class, 'storeletterhead'])->name('letterhead.store');

   Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
Route::post('/payments/generate', [PaymentController::class, 'generatePayment'])->name('payments.generate');
Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
// routes/web.php
// Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
Route::get('/payments/export', [PaymentController::class, 'export'])->name('payments.export');

});

Route::get('/test-pass', function () {
    $hash = '$2y$12$ppV0p0fTxwmTAhVidCMjpOVBptoQaXHnJZHD/xehTX.D31naG6gQ2';
    return \Illuminate\Support\Facades\Hash::check('pratiksha@123', $hash) ? 'MATCH' : 'NO MATCH';
});
