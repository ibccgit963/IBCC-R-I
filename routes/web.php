<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CenterController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\DispatchLogController;
use App\Http\Controllers\ReceiveLogController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\DepartmentController;




Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/incoming', [App\Http\Controllers\HomeController::class, 'incoming'])->name('incoming');
Route::get('/incoming1', [App\Http\Controllers\HomeController::class, 'incoming1'])->name('incoming1');
Route::get('/incoming2', [App\Http\Controllers\HomeController::class, 'incoming2'])->name('incoming2');
Route::get('/incoming3', [App\Http\Controllers\HomeController::class, 'incoming3'])->name('incoming3');
Route::get('/incoming4', [App\Http\Controllers\HomeController::class, 'incoming4'])->name('incoming4');
Route::get('/outgoing', [App\Http\Controllers\HomeController::class, 'outgoing'])->name('outgoing');
Route::get('/outgoing1', [App\Http\Controllers\HomeController::class, 'outgoing1'])->name('outgoing1');
Route::get('/outgoing2', [App\Http\Controllers\HomeController::class, 'outgoing2'])->name('outgoing2');
Route::get('/outgoing3', [App\Http\Controllers\HomeController::class, 'outgoing3'])->name('outgoing3');
Route::get('/outgoing4', [App\Http\Controllers\HomeController::class, 'outgoing4'])->name('outgoing4');
Route::get('/dashboard/export-csv', [App\Http\Controllers\HomeController::class, 'exportCsv'])->name('dashboard.exportCsv');

// Only super-admin can manage IBCC centers
Route::middleware(['auth', 'role:super-admin'])->group(function () {
    Route::resource('centers', App\Http\Controllers\CenterController::class);
});

// super-admin and center-admin can manage departments and officers (scoped to their own center)
Route::middleware(['auth', 'role:super-admin|center-admin'])->group(function () {
    Route::resource('officers', App\Http\Controllers\OfficerController::class);
    Route::get('departments-by-center/{centerId}', [DepartmentController::class, 'getDepartmentsByCenter'])
        ->name('departments.by-center');
    Route::resource('departments', App\Http\Controllers\DepartmentController::class);
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('search', [SearchController::class, 'index'])->name('search');
    Route::post('attachments', [App\Http\Controllers\AttachmentController::class, 'store'])->name('attachments.store');
    Route::delete('attachments/{attachment}', [App\Http\Controllers\AttachmentController::class, 'destroy'])->name('attachments.destroy');
});

// AJAX helper routes (need session auth, so defined here not in api.php)
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/courier-companies/suggestions', [\App\Http\Controllers\CourierController::class, 'getCompanySuggestions']);
    Route::get('/departments-by-center/{centerId}', [DepartmentController::class, 'getDepartmentsByCenter']);
    Route::get('/users-by-department/{departmentId}', function ($departmentId) {
        return \App\Models\User::where('department_id', $departmentId)
            ->whereHas('role', fn($q) => $q->where('slug', 'officer'))
            ->get(['id', 'name', 'is_focal_person']);
    });
});

Route::middleware(['auth', 'role:super-admin|center-admin|staff-user|officer'])->group(function () {
    Route::post('couriers/bulk-action', [App\Http\Controllers\CourierController::class, 'bulkAction'])->name('couriers.bulkAction');
    Route::resource('couriers', App\Http\Controllers\CourierController::class);
    Route::get('couriers/{courier}/transfer-form', [App\Http\Controllers\CourierController::class, 'transferForm'])->name('couriers.transferForm');
    Route::post('couriers/{courier}/transfer', [App\Http\Controllers\CourierTransferController::class, 'transfer'])->name('couriers.transfer');
    Route::post('couriers/{courier}/revert', [App\Http\Controllers\CourierController::class, 'revert'])->name('couriers.revert');
    Route::post('couriers/{courier}/receive-back', [App\Http\Controllers\CourierController::class, 'receiveBack'])->name('couriers.receiveBack');
    Route::post('couriers/{courier}/return-to-ri', [App\Http\Controllers\CourierController::class, 'returnToRI'])->name('couriers.returnToRI');
    Route::post('couriers/{courier}/mark-received-direct', [App\Http\Controllers\CourierController::class, 'markReceivedDirect'])->name('couriers.markReceivedDirect');
    Route::post('couriers/{courier}/mark-dispatched', [App\Http\Controllers\CourierController::class, 'markDispatched'])->name('couriers.markDispatched');
    Route::post('courier-transfers/{courierTransfer}/mark-received', [App\Http\Controllers\CourierTransferController::class, 'markReceived'])->name('courier-transfers.markReceived');
    Route::resource('dispatches', App\Http\Controllers\DispatchController::class);
    Route::post('dispatches/{dispatch}/mark-received', [App\Http\Controllers\DispatchController::class, 'markReceived'])->name('dispatches.markReceived');
    Route::post('dispatches/{dispatch}/mark-dispatched', [App\Http\Controllers\DispatchController::class, 'markDispatched'])->name('dispatches.markDispatched');
    Route::get('dispatch-logs/export', [DispatchLogController::class, 'export'])->name('dispatch-logs.export');
    Route::resource('dispatch-logs', DispatchLogController::class);
    Route::get('dispatch-others/export', [App\Http\Controllers\DispatchOtherController::class, 'export'])->name('dispatch-others.export');
    Route::resource('dispatch-others', App\Http\Controllers\DispatchOtherController::class);
    Route::get('receive-logs/export', [ReceiveLogController::class, 'export'])->name('receive-logs.export');
    Route::resource('receive-logs', ReceiveLogController::class);
    Route::resource('users', UserController::class);

});
