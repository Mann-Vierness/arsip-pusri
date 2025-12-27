<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AdminDocumentController;
use App\Http\Controllers\Admin\LoginLogController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\SuratKeputusanController;
use App\Http\Controllers\User\SuratPerjanjianController;
use App\Http\Controllers\User\SuratAddendumController;
use App\Http\Controllers\User\NotificationController as UserNotificationController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// USER ROUTES
Route::prefix('user')->middleware(['auth', 'role:user'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    
    // Export routes BEFORE resource routes
    Route::get('sk/export/csv', [SuratKeputusanController::class, 'exportCsv'])->name('user.sk.export');
    Route::get('sp/export/csv', [SuratPerjanjianController::class, 'exportCsv'])->name('user.sp.export');
    Route::get('addendum/export/csv', [SuratAddendumController::class, 'exportCsv'])->name('user.addendum.export');
    
    // Document resource routes dengan parameter 'id'
    Route::resource('sk', SuratKeputusanController::class, ['as' => 'user'])
        ->parameters(['sk' => 'id']);
    Route::get('sk/{id}/download', [SuratKeputusanController::class, 'downloadPdf'])->name('user.sk.download');
    
    Route::resource('sp', SuratPerjanjianController::class, ['as' => 'user'])
        ->parameters(['sp' => 'id']);
    Route::get('sp/{id}/download', [SuratPerjanjianController::class, 'downloadPdf'])->name('user.sp.download');
    
    Route::resource('addendum', SuratAddendumController::class, ['as' => 'user'])
        ->parameters(['addendum' => 'id']);
    Route::get('addendum/{id}/download', [SuratAddendumController::class, 'downloadPdf'])->name('user.addendum.download');
    
    // Notifications
    Route::get('/notifications', [UserNotificationController::class, 'index'])->name('user.notifications');
    Route::post('/notifications/{id}/read', [UserNotificationController::class, 'markAsRead'])->name('user.notifications.read');
    Route::post('/notifications/read-all', [UserNotificationController::class, 'markAllAsRead'])->name('user.notifications.read-all');
});

// ADMIN ROUTES
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // SETTINGS - Pengaturan Batas Input User
    Route::get('settings/input-limit', [SettingsController::class, 'inputLimitForm'])->name('settings.input-limit');
    Route::post('settings/input-limit', [SettingsController::class, 'updateInputLimit'])->name('settings.input-limit.update');
    
    // APPROVAL
    Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');
    Route::get('/approval/{type}/{id}', [ApprovalController::class, 'show'])->name('approval.show');
    Route::post('/approval/{type}/{id}/approve', [ApprovalController::class, 'approve'])->name('approval.approve');
    Route::post('/approval/{type}/{id}/reject', [ApprovalController::class, 'reject'])->name('approval.reject');
    Route::get('/approval/{type}/{id}/download', [ApprovalController::class, 'downloadPdf'])->name('approval.download');
    Route::get('/approval/{type}/{id}/view-pdf', [ApprovalController::class, 'viewPdf'])->name('approval.view-pdf');
    
    // USER MANAGEMENT
    Route::resource('users', UserManagementController::class);
    
    // VIEW ALL DOCUMENTS
    Route::get('/documents/sk', [AdminDashboardController::class, 'allSK'])->name('documents.sk');
    Route::get('/documents/sk/{id}/download', [AdminDashboardController::class, 'downloadSK'])->name('documents.sk.download');
    Route::get('/documents/sp', [AdminDashboardController::class, 'allSP'])->name('documents.sp');
    Route::get('/documents/sp/{id}/download', [AdminDashboardController::class, 'downloadSP'])->name('documents.sp.download');
    Route::get('/documents/sp/{id}', [AdminDashboardController::class, 'showSP'])->name('documents.sp.show');
    Route::get('/documents/addendum', [AdminDashboardController::class, 'allAddendum'])->name('documents.addendum');
    
    // EXPORT DOCUMENTS AS CSV
    Route::get('/documents/sk/export/csv', [AdminDashboardController::class, 'exportCsvSK'])->name('documents.sk.export');
    Route::get('/documents/sp/export/csv', [AdminDashboardController::class, 'exportCsvSP'])->name('documents.sp.export');
    Route::get('/documents/addendum/export/csv', [AdminDashboardController::class, 'exportCsvAddendum'])->name('documents.addendum.export');
    
    // ADMIN INPUT DOCUMENTS (AUTO-APPROVED)
    Route::get('/documents/sk/create', [AdminDocumentController::class, 'createSK'])->name('documents.sk.create');
    Route::post('/documents/sk/store', [AdminDocumentController::class, 'storeSK'])->name('documents.sk.store');
    Route::get('/documents/surat-perjanjian/create', [AdminDocumentController::class, 'createSP'])->name('documents.sp.create');
    Route::post('/documents/surat-perjanjian/store', [AdminDocumentController::class, 'storeSP'])->name('documents.sp.store');
    Route::get('/documents/addendum/create', [AdminDocumentController::class, 'createAddendum'])->name('documents.addendum.create');
    Route::post('/documents/addendum/store', [AdminDocumentController::class, 'storeAddendum'])->name('documents.addendum.store');
    
    // LOGIN LOGS
    Route::get('/logs/login', [LoginLogController::class, 'index'])->name('logs.login');
});