<?php

use Illuminate\Support\Facades\Route;
use Modules\ServiceAgreementSystem\Http\Controllers\DashboardController;
use Modules\ServiceAgreementSystem\Http\Controllers\ContractorController;
use Modules\ServiceAgreementSystem\Http\Controllers\UspkSubmissionController;
use Modules\ServiceAgreementSystem\Http\Controllers\UspkApprovalController;
use Modules\ServiceAgreementSystem\Http\Controllers\UspkApprovalSchemaController;
use Modules\ServiceAgreementSystem\Http\Controllers\UspkBudgetController;
use Modules\ServiceAgreementSystem\Http\Controllers\UspkLegalController;

// Authenticated routes
Route::middleware(['auth', 'assigned.role', 'sas.role'])->prefix('sas')->name('sas.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Kontraktor CRUD
    Route::resource('contractors', ContractorController::class);

    // USPK Submissions
    Route::resource('uspk', UspkSubmissionController::class);
    Route::post('uspk/{uspk}/submit', [UspkSubmissionController::class, 'submit'])->name('uspk.submit');

    // USPK Budgeting
    Route::get('uspk-budgets', [UspkBudgetController::class, 'index'])->name('uspk-budgets.index');
    Route::post('uspk-budgets', [UspkBudgetController::class, 'store'])->name('uspk-budgets.store');

    // USPK Approvals
    Route::get('uspk-approvals', [UspkApprovalController::class, 'index'])->name('uspk-approvals.index');
    Route::post('uspk/{uspk}/approve', [UspkApprovalController::class, 'approve'])->name('uspk.approve');
    Route::post('uspk/{uspk}/hold', [UspkApprovalController::class, 'hold'])->name('uspk.hold');
    Route::post('uspk/{uspk}/reject', [UspkApprovalController::class, 'reject'])->name('uspk.reject');

    // USPK Legal SPK Workflow
    Route::get('uspk-legal', [UspkLegalController::class, 'index'])->name('uspk-legal.index');
    Route::get('uspk/{uspk}/legal/export-spk', [UspkLegalController::class, 'exportDraft'])->name('uspk-legal.export');
    Route::post('uspk/{uspk}/legal/upload-spk', [UspkLegalController::class, 'uploadFinal'])->name('uspk-legal.upload');
    Route::get('uspk/{uspk}/legal/download-spk', [UspkLegalController::class, 'downloadFinal'])->name('uspk-legal.download');
    Route::post('uspk/{uspk}/legal/return-to-selection', [UspkLegalController::class, 'returnToSelection'])->name('uspk-legal.return');

    // USPK Approval Schemas Configuration
    Route::resource('approval-schemas', UspkApprovalSchemaController::class)->except(['show']);

    // API endpoints for cascade dropdowns
    Route::get('api/sub-departments/{departmentId}', [UspkSubmissionController::class, 'getSubDepartments'])->name('api.sub-departments');
    Route::get('api/blocks/{subDepartmentId}', [UspkSubmissionController::class, 'getBlocks'])->name('api.blocks');
    Route::get('api/budget-activities', [UspkSubmissionController::class, 'getBudgetActivities'])->name('api.budget-activities');
});
