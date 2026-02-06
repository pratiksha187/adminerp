<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChallanController;
use App\Http\Controllers\EngineeringController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PaymentController;
use App\Exports\PaymentsExport;
use App\Http\Controllers\HomeController;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\AttendanceCalendarController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\StoreDprController;

use App\Http\Controllers\POController;
Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Route::get('/test', [App\Http\Controllers\AdminController::class, 'test'])->name('test');


Route::middleware(['auth'])->group(function () {

Route::get('/attendance/calendar', [AttendanceCalendarController::class, 'view'])->name('attendance.calendar.view');
Route::get('/attendance/calendar/events', [AttendanceCalendarController::class, 'events'])->name('attendance.calendar.events');


    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    // Attendance
    Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockin');
    Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clockout');

    // Register / All Users
     Route::middleware('role:1,2,17')->group(function () {
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
      });
    Route::get('/employees/{user}', [RegisterController::class, 'show']);          // fetch one (JSON)
    Route::put('/employees/{user}', [RegisterController::class, 'update']);        // update
    // You already have:
    // Route::post('/employees/{user}/status', [RegisterController::class, 'status']); // status toggle
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

    Route::get('/all-engg-work-entry', [EngineeringController::class, 'allenggworkentry'])->name('allenggworkentry');

    Route::post('/work-entry/save', [EngineeringController::class, 'saveworkdata'])->name('work-entry.save');
    Route::get('/work-entry/data', [EngineeringController::class, 'data'])->name('work-entry.data');
    Route::get('work-entry/view/{id}', [EngineeringController::class, 'view'])->name('work-entry.view');


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
    Route::post('import-letterhead', [AdminController::class, 'importLetterhead'])->name('letterhead.import');
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/generate', [PaymentController::class, 'generatePayment'])->name('payments.generate');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
// routes/web.php
// Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::get('/payments/export', [PaymentController::class, 'export'])->name('payments.export');
    Route::get('crm', [LeadController::class, 'crm'])->name('crm');   // Grid
    Route::post('leads/store', [LeadController::class, 'store'])->name('leads.store'); 
    Route::get('crm/lead-management', [LeadController::class, 'index'])->name('crm/lead-management');   // Grid
    Route::get('leads/create', [LeadController::class, 'create'])->name('leads.create');   // Grid
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
    Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
    // Route::patch('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::match(['put', 'patch'], '/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::get('payments/slip/{id}', [PaymentController::class, 'slip'])->name('payments.slip');
    Route::get('/leave', [App\Http\Controllers\LeaveController::class, 'index'])->name('leave.index');
    Route::get('/create', [App\Http\Controllers\LeaveController::class, 'create'])->name('leave.create');
    Route::post('/store', [App\Http\Controllers\LeaveController::class, 'store'])->name('leaves.store');

    // HR portal
    Route::get('hr/leaves', [LeaveController::class, 'hrIndex'])->name('hr.leaves.index');
    Route::post('hr/leaves/{id}/update', [LeaveController::class, 'updateStatus'])->name('hr.leaves.update');
    // Route::get('store-entry', [StoreEntryController ::class, 'storeentry'])->name('store-entry.index');
// Store DPR Routes
// use App\Http\Controllers\StoreDprController;

// Route::get('/store-dpr/create', function() {
//     return view('store-dpr.create'); // Your Blade file
// })->name('store-dpr.create');
    Route::get('/store-dpr/create', [StoreDprController::class, 'storedpr'])->name('store-dpr.create');

    Route::post('/store-dpr/store', [StoreDprController::class, 'store'])->name('store-dpr.store');
    Route::get('/store-dpr/list', [StoreDprController::class, 'index'])->name('store-dpr.list');

    Route::get('/store-requirement', [StoreDprController::class, 'storerequirement'])->name('store-requirement.create');
    Route::post('/store-requirement/save', [StoreDprController::class, 'storeRequirementSave'])
        ->name('store-requirement.save');
    Route::get('/store-requirements-list', [StoreDprController::class, 'storerequirementlist'])
        ->name('store-requirement.list');
    Route::get('/store-requirements-Accepted-list', [StoreDprController::class, 'storerequirementaceptedlist'])
        ->name('store-requirement.accepted.list');
      
    Route::post('/store-requirements/status/{id}', [StoreDprController::class, 'updateRequirementStatus'])->name('store-requirement.status');
    Route::get('/store-requirements/{id}', [StoreDprController::class, 'show'])->name('store-requirement.view');
   
    Route::get('/create-po', [POController::class, 'createpo'])->name('createpo');
    Route::post('/purchase-order/store', [POController::class, 'storepo'])
        ->name('po.store');
    Route::get('/show-po', [POController::class, 'showpo'])->name('showpo');

Route::delete('/purchase-orders/{id}', [POController::class, 'destroy'])
    ->name('po.destroy');

});

Route::get('/test-pass', function () {
    $newHash = Hash::make('secret123');
    // dd($newHash);
    $hash = '$2y$12$R6H5FFOgSxxv1uqm..3Q9OWMH/zbVRZjuyGtJWcpjVeVrwoZyW0mq';
    return \Illuminate\Support\Facades\Hash::check('secret123', $hash) ? 'MATCH' : 'NO MATCH';
});
