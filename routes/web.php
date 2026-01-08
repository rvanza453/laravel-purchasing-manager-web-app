<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\PrController;
use App\Http\Controllers\ApprovalController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/employment', [ProfileController::class, 'updateEmployment'])->name('profile.update-employment');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Signature routes
    Route::post('/profile/signature', [ProfileController::class, 'uploadSignature'])->name('profile.signature.upload');
    Route::delete('/profile/signature', [ProfileController::class, 'deleteSignature'])->name('profile.signature.delete');

    // Admin Routes
    // Admin Routes
    Route::resource('departments', \App\Http\Controllers\Admin\DepartmentController::class);
    Route::resource('master-departments', \App\Http\Controllers\Admin\MasterDepartmentController::class);
    Route::resource('sub-departments', \App\Http\Controllers\Admin\SubDepartmentController::class);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('global-approvers', \App\Http\Controllers\Admin\GlobalApproverController::class);
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    
    // Budget Routes
    Route::get('/admin/budgets', [\App\Http\Controllers\Admin\BudgetController::class, 'index'])->name('admin.budgets.index');
    Route::get('/admin/budgets/{subDepartment}/edit', [\App\Http\Controllers\Admin\BudgetController::class, 'edit'])->name('admin.budgets.edit');
    Route::put('/admin/budgets/{subDepartment}', [\App\Http\Controllers\Admin\BudgetController::class, 'update'])->name('admin.budgets.update');

    // PR Routes
    Route::resource('pr', PrController::class);
    Route::get('/pr/{purchaseRequest}/export-pdf', [\App\Http\Controllers\PrPdfController::class, 'export'])->name('pr.export.pdf');
    Route::get('/api/budget/{subDepartment}', [PrController::class, 'getBudgetStatus'])->name('api.budget.status');
    
    // Approval Routes
    Route::get('/approvals', [ApprovalController::class, 'index'])->name('approval.index');
    Route::post('/approvals/{approval}/approve', [ApprovalController::class, 'approve'])->name('approval.approve');
    Route::post('/approvals/{approval}/reject', [ApprovalController::class, 'reject'])->name('approval.reject');
});

require __DIR__.'/auth.php';
