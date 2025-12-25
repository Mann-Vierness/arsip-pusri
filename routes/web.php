<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\AdminDocumentController;
use App\Http\Controllers\Admin\LoginLogController;
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
    
    // Export routes BEFORE resource routes to prevent route matching conflicts
    Route::get('sk/export/csv', [SuratKeputusanController::class, 'exportCsv'])->name('user.sk.export');
    Route::get('sp/export/csv', [SuratPerjanjianController::class, 'exportCsv'])->name('user.sp.export');
    Route::get('addendum/export/csv', [SuratAddendumController::class, 'exportCsv'])->name('user.addendum.export');
    
    // Document resource routes
    Route::resource('sk', SuratKeputusanController::class, ['as' => 'user']);
    Route::get('sk/{id}/download', [SuratKeputusanController::class, 'downloadPdf'])->name('sk.download');
    Route::get('sk/{id}/download', [SuratKeputusanController::class, 'downloadPdf'])->name('user.sk.download');
    
    Route::resource('sp', SuratPerjanjianController::class, ['as' => 'user']);
    Route::get('sp/{id}/download', [SuratPerjanjianController::class, 'downloadPdf'])->name('sp.download');
    Route::get('sp/{id}/download', [SuratPerjanjianController::class, 'downloadPdf'])->name('user.sp.download');
    
    Route::resource('addendum', SuratAddendumController::class, ['as' => 'user']);
    Route::get('addendum/{id}/download', [SuratAddendumController::class, 'downloadPdf'])->name('addendum.download');
    Route::get('addendum/{id}/download', [SuratAddendumController::class, 'downloadPdf'])->name('user.addendum.download');
    
    Route::get('/notifications', [UserNotificationController::class, 'index'])->name('user.notifications');
    Route::post('/notifications/{id}/read', [UserNotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/{id}/read', [UserNotificationController::class, 'markAsRead'])->name('user.notifications.read');
    Route::post('/notifications/read-all', [UserNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/read-all', [UserNotificationController::class, 'markAllAsRead'])->name('user.notifications.read-all');
});

// ADMIN ROUTES
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
        // Route detail SK di admin dihandle oleh ApprovalController atau controller lain yang valid
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // APPROVAL
    Route::get('/approval', [ApprovalController::class, 'index'])->name('admin.approval.index');
    Route::get('/approval/{type}/{id}', [ApprovalController::class, 'show'])->name('admin.approval.show');
    Route::post('/approval/{type}/{id}/approve', [ApprovalController::class, 'approve'])->name('admin.approval.approve');
    Route::post('/approval/{type}/{id}/reject', [ApprovalController::class, 'reject'])->name('admin.approval.reject');
    Route::get('/approval/{type}/{id}/download', [ApprovalController::class, 'downloadPdf'])->name('admin.approval.download');
    Route::get('/approval/{type}/{id}/view-pdf', [ApprovalController::class, 'viewPdf'])->name('admin.approval.view-pdf');
    
    // USER MANAGEMENT
    Route::resource('users', UserManagementController::class, ['as' => 'admin']);
    
    // VIEW ALL DOCUMENTS
    Route::get('/documents/sk', [AdminDashboardController::class, 'allSK'])->name('admin.documents.sk');
    Route::get('/documents/sp', [AdminDashboardController::class, 'allSP'])->name('admin.documents.sp');
    Route::get('/documents/addendum', [AdminDashboardController::class, 'allAddendum'])->name('admin.documents.addendum');
    
    // EXPORT DOCUMENTS AS CSV
    Route::get('/documents/sk/export/csv', [AdminDashboardController::class, 'exportCsvSK'])->name('admin.documents.sk.export');
    Route::get('/documents/sp/export/csv', [AdminDashboardController::class, 'exportCsvSP'])->name('admin.documents.sp.export');
    Route::get('/documents/addendum/export/csv', [AdminDashboardController::class, 'exportCsvAddendum'])->name('admin.documents.addendum.export');
    
    // ADMIN INPUT DOCUMENTS (AUTO-APPROVED) - FITUR BARU!
    Route::get('/documents/sk/create', [AdminDocumentController::class, 'createSK'])->name('admin.documents.sk.create');
    Route::post('/documents/sk/store', [AdminDocumentController::class, 'storeSK'])->name('admin.documents.sk.store');
    Route::get('/documents/sp/create', [AdminDocumentController::class, 'createSP'])->name('admin.documents.sp.create');
    Route::post('/documents/sp/store', [AdminDocumentController::class, 'storeSP'])->name('admin.documents.sp.store');
    Route::get('/documents/addendum/create', [AdminDocumentController::class, 'createAddendum'])->name('admin.documents.addendum.create');
    Route::post('/documents/addendum/store', [AdminDocumentController::class, 'storeAddendum'])->name('admin.documents.addendum.store');
    
    // LOGIN LOGS - FITUR BARU!
    Route::get('/logs/login', [LoginLogController::class, 'index'])->name('admin.logs.login');
});
