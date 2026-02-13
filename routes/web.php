<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\PrController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\InventoryImportController;

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
    
    // Signature routes
    Route::post('/profile/signature', [ProfileController::class, 'uploadSignature'])->name('profile.signature.upload');
    Route::delete('/profile/signature', [ProfileController::class, 'deleteSignature'])->name('profile.signature.delete');

    Route::get('/pr/export', [PrController::class, 'export'])->name('pr.export');
    Route::resource('pr', PrController::class);
    Route::post('/pr/{pr}/reply-hold', [PrController::class, 'replyToHold'])->name('pr.replyHold');
    Route::get('/pr/{purchaseRequest}/export-pdf', [\App\Http\Controllers\PrPdfController::class, 'export'])->name('pr.export.pdf');
    Route::get('/pr/{purchaseRequest}/attachment/download', [PrController::class, 'downloadAttachment'])->name('pr.attachment.download');
    Route::get('/api/budget/{subDepartment}', [PrController::class, 'getBudgetStatus'])->name('api.budget.status');
    Route::get('/api/sub-department/{subDepartment}/jobs', [PrController::class, 'getJobs'])->name('api.jobs');
    Route::get('/api/department/{department}/jobs', [PrController::class, 'getJobsByDepartment'])->name('api.department.jobs');
    Route::get('/api/sites/{site}/departments', [\App\Http\Controllers\Admin\DepartmentController::class, 'getDepartmentsBySite'])->name('api.sites.departments');

    // --- PO READ ROUTES (All Authenticated Users) ---
    Route::get('/po', [\App\Http\Controllers\PoController::class, 'index'])->name('po.index');
    Route::get('/po/{po}', [\App\Http\Controllers\PoController::class, 'show'])
        ->where('po', '[0-9]+')
        ->name('po.show');
    Route::get('/po/{po}/export-pdf', [\App\Http\Controllers\PoPdfController::class, 'export'])->name('po.export.pdf');
    
    // FETCH PRODUCT BY SITES
    Route::get('/api/sites/{site}/products', [\App\Http\Controllers\PrController::class, 'getProductsBySite'])->name('api.site.products');
    
    //leave login as
    Route::post('/users/leave-impersonate', [\App\Http\Controllers\Admin\UserController::class, 'leaveImpersonate'])->name('users.leave-impersonate');

    // --- ADMIN ROUTES (Full Access) ---
    Route::middleware(['role:Admin'])->group(function () {
        Route::resource('departments', \App\Http\Controllers\Admin\DepartmentController::class);
        Route::resource('master-departments', \App\Http\Controllers\Admin\MasterDepartmentController::class);
        Route::resource('sub-departments', \App\Http\Controllers\Admin\SubDepartmentController::class);
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('global-approvers', \App\Http\Controllers\Admin\GlobalApproverController::class);
        
        Route::post('/users/{user}/impersonate', [\App\Http\Controllers\Admin\UserController::class, 'impersonate'])->name('users.impersonate');
        Route::resource('sites', \App\Http\Controllers\Admin\SiteController::class);
        
        // Product & Vendor Write Access (Admin Only)
        Route::get('products/export', [\App\Http\Controllers\Admin\ProductController::class, 'export'])->name('products.export');
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)->except(['index', 'show']);
        Route::resource('vendors', \App\Http\Controllers\Admin\VendorController::class)->except(['index', 'show']);
        
        Route::resource('jobs', \App\Http\Controllers\Admin\JobController::class);
        Route::post('jobs/{job}/mappings', [\App\Http\Controllers\Admin\JobController::class, 'updateMappings'])->name('jobs.mappings.update');
        Route::get('/admin/budgets', [\App\Http\Controllers\Admin\BudgetController::class, 'index'])->name('admin.budgets.index');
        Route::get('/admin/budgets/{subDepartment}/edit', [\App\Http\Controllers\Admin\BudgetController::class, 'edit'])->name('admin.budgets.edit');
        Route::put('/admin/budgets/{subDepartment}', [\App\Http\Controllers\Admin\BudgetController::class, 'update'])->name('admin.budgets.update');
        Route::get('/admin/budgets/department/{department}/edit', [\App\Http\Controllers\Admin\BudgetController::class, 'editDepartment'])->name('admin.budgets.edit-department');
        Route::put('/admin/budgets/department/{department}', [\App\Http\Controllers\Admin\BudgetController::class, 'updateDepartment'])->name('admin.budgets.update-department');
        
        // Capex Verification
        Route::get('/admin/capex-verification', [\App\Http\Controllers\PrController::class, 'verifyCapexIndex'])->name('admin.capex.index');
        Route::post('/admin/capex-verification/{purchaseRequest}', [\App\Http\Controllers\PrController::class, 'verifyCapexStore'])->name('admin.capex.verify');
        Route::post('/admin/capex-verification/{purchaseRequest}/reject', [\App\Http\Controllers\PrController::class, 'verifyCapexReject'])->name('admin.capex.reject');

        // Activity Logs
        Route::get('/admin/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    // --- BUDGET MONITORING (Admin & Approver) ---
    Route::middleware(['role:Admin|Approver'])->group(function () {
        Route::get('/admin/budgets/monitoring', [\App\Http\Controllers\Admin\BudgetController::class, 'monitoring'])->name('admin.budgets.monitoring');
        Route::get('/admin/budgets/{budget}/details', [\App\Http\Controllers\Admin\BudgetController::class, 'usageDetails'])->name('admin.budgets.details');
    });

    // --- PURCHASING ROUTES (PO & Inventory) ---
    // Accessible by: Purchasing, Admin, Warehouse
    Route::middleware(['role:Purchasing|Admin|Warehouse'])->group(function () {
        // PO Management
        Route::get('/pr/{purchaseRequest}/po/select-items', [\App\Http\Controllers\PoController::class, 'selectItems'])->name('po.select-items');
        // PO Creation Flow
        Route::match(['get', 'post'], '/po/create', [\App\Http\Controllers\PoController::class, 'create'])->name('po.create');
        Route::post('/po', [\App\Http\Controllers\PoController::class, 'store'])->name('po.store');

        // PO Cart
        Route::get('/po/cart', [\App\Http\Controllers\PoCartController::class, 'index'])->name('po.cart');
        Route::get('/po/cart/data', [\App\Http\Controllers\PoCartController::class, 'getData'])->name('po.cart.data');
        Route::post('/po/cart/add', [\App\Http\Controllers\PoCartController::class, 'store'])->name('po.cart.add');
        Route::post('/po/cart/remove', [\App\Http\Controllers\PoCartController::class, 'remove'])->name('po.cart.remove');
        Route::post('/po/cart/clear', [\App\Http\Controllers\PoCartController::class, 'clear'])->name('po.cart.clear');

        // Inventory Management
        Route::get('/inventory/create', [\App\Http\Controllers\InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/inventory', [\App\Http\Controllers\InventoryController::class, 'store'])->name('inventory.store');
        Route::get('/inventory-import/kde-script', [\App\Http\Controllers\InventoryImportController::class, 'importKdeInventory'])->name('inventory.import.kde');
        Route::get('/inventory-import/out', [\App\Http\Controllers\InventoryImportController::class, 'formOut'])->name('inventory.import.out');
        Route::post('/inventory-import/out', [\App\Http\Controllers\InventoryImportController::class, 'store'])->name('inventory.import.out.process');
        Route::get('/inventory/{warehouse}/edit', [\App\Http\Controllers\InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('/inventory/{warehouse}', [\App\Http\Controllers\InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('/inventory/{warehouse}', [\App\Http\Controllers\InventoryController::class, 'destroy'])->name('inventory.destroy');
        Route::get('/inventory/{warehouse}/movement/{type}', [\App\Http\Controllers\InventoryController::class, 'createMovement'])->name('inventory.movement');
        Route::post('/inventory/{warehouse}/movement', [\App\Http\Controllers\InventoryController::class, 'storeMovement'])->name('inventory.store-movement');
    });

    // --- PO EDIT/DELETE ROUTES ---
    // Accessible by: Admin, Warehouse (Exclude Purchasing)
    Route::middleware(['role:Admin|Warehouse'])->group(function () {
        Route::get('/po/{po}/edit', [\App\Http\Controllers\PoController::class, 'edit'])->name('po.edit');
        Route::put('/po/{po}', [\App\Http\Controllers\PoController::class, 'update'])->name('po.update');
        Route::delete('/po/{po}', [\App\Http\Controllers\PoController::class, 'destroy'])->name('po.destroy');
    });

    // --- FINANCE ROUTES ---
    // Accessible by: Finance, Purchasing, Admin
    Route::middleware(['role:Finance|Purchasing|Admin|Warehouse'])->group(function () {
    });

    // --- GENERIC READ-ONLY VIEWS (Inventory, Products, Vendors) ---
    // Accessible by: Purchasing, Admin, Warehouse
    Route::middleware(['role:Purchasing|Admin|Warehouse'])->group(function () {
        Route::get('/inventory', [\App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/{warehouse}/history', [\App\Http\Controllers\InventoryController::class, 'history'])->name('inventory.history');
        Route::get('/inventory/{warehouse}', [\App\Http\Controllers\InventoryController::class, 'show'])->name('inventory.show');

        // Products Read-Only
        Route::get('/products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'show'])->name('products.show');

        // Vendors Read-Only
        Route::get('/vendors', [\App\Http\Controllers\Admin\VendorController::class, 'index'])->name('vendors.index');
        Route::get('/vendors/{vendor}', [\App\Http\Controllers\Admin\VendorController::class, 'show'])->name('vendors.show');
    });

    // --- APPROVER ROUTES ---
    // Accessible by: Authenticated Users (Controller enforces ownership)
    Route::middleware(['auth'])->group(function () {
        Route::get('/approvals', [ApprovalController::class, 'index'])->name('approval.index');
        Route::post('/approvals/{approval}/approve', [ApprovalController::class, 'approve'])->name('approval.approve');
        Route::post('/approvals/{approval}/reject', [ApprovalController::class, 'reject'])->name('approval.reject');
        Route::post('/approvals/{approval}/hold', [ApprovalController::class, 'hold'])->name('approval.hold');
    });
});

require __DIR__.'/auth.php';

// Utility route for hosting without SSH
Route::get('/clear-cache', function() {
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        return "Cache cleared successfully!<br>" . nl2br(\Illuminate\Support\Facades\Artisan::output());
    } catch (\Exception $e) {
        return "Error clearing cache: " . $e->getMessage();
    }
});

Route::get('/test-notification', function() {
    if (!auth()->check() || !auth()->user()->hasRole('Admin')) {
        abort(403, 'Unauthorized');
    }
    
    try {
        \Illuminate\Support\Facades\Artisan::call('pr:notify-pending');
        $output = \Illuminate\Support\Facades\Artisan::output();
        return "<h2>Notification Test Completed</h2><pre>" . $output . "</pre><br><a href='/dashboard'>Back to Dashboard</a>";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
})->middleware('auth');



