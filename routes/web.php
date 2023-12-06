<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CGTGradeController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\NTGradeController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductTypeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SampleRequestController;
use App\Http\Controllers\Admin\ScanInController;
use App\Http\Controllers\Admin\ScanOutController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WarehouseController;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


//Admin Auth Routes
Route::group(['prefix' => 'admin', 'middleware' => ['guest'], 'as' => 'admin:'], function () {
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'login_attempt'])->name('login.action');
    Route::get('/forget-password', [AuthController::class, 'forget_password'])->name('forget_password');
    Route::post('/forgot-password', function (Request $request) {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    })->name('password.email');
});

//Reset Password
Route::get('/reset-password/{token}', function (string $token, Request $request) {
    return view('admin.modules.auth.reset_password', ['token' => $token, 'email' => $request->email]);
})->middleware('guest')->name('password.reset');

Route::post('/password-update', [AuthController::class, 'passwordUpdate'])->middleware('guest')->name('password.update');

Route::group(['prefix' => 'admin', 'middleware' => ['auth'], 'as' => 'admin:'], function () {

    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    //Dashboard Routes
    Route::get('/dashboard/{date?}', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/edit-user-profile/{id}', [DashboardController::class, 'editUserProfile'])->name('edit-user-profile');
    Route::post('/update-profile/{id}', [DashboardController::class, 'userProfile'])->name('update-profile');
    Route::get('/view-user-profile/{id}', [DashboardController::class, 'viewUserProfile'])->name('view-user-profile');
    Route::post('/get-dashboard-inventory-by-filter', [DashboardController::class, 'getNTInventoryByFilter'])->name('dashboard.getNTInventoryByFilter');

    //Users Routes
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/user-create', [UserController::class, 'create'])->name('user.create');
    Route::post('users/add', [UserController::class, 'store'])->name('user.add');
    Route::get('users/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/update/{id}', [UserController::class, 'store'])->name('user.update');
    Route::post('/user-destroy', [UserController::class, 'destroy'])->name('user.destroy');
    Route::post('/user-change-password', [UserController::class, 'change_password'])->name('user.change_password');
    Route::post('/admin-change-password', [UserController::class, 'admin_change_password'])->name('user.admin_change_password');
    //Roles Routes
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles');
        Route::get('/create', [RoleController::class, 'create'])->name('role.create');
        Route::post('/store', [RoleController::class, 'store'])->name('role.store');
        Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('role.edit');
        Route::put('/update/{id}', [RoleController::class, 'update'])->name('role.update');
        Route::post('/destroy', [RoleController::class, 'destroy'])->name('role.destroy');
        Route::post('/show', [RoleController::class, 'show'])->name('role.show');
    });

    # warehouse
    Route::prefix('warehouses')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('warehouses');
        Route::get('/create', [WarehouseController::class, 'create'])->name('warehouses.create');
        Route::post('/store', [WarehouseController::class, 'store'])->name('warehouses.store');
        Route::get('/edit/{id}', [WarehouseController::class, 'edit'])->name('warehouses.edit');
        Route::put('/update/{id}', [WarehouseController::class, 'update'])->name('warehouses.update');
        Route::post('destroy', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');
    });

    # customer
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('customers');
        Route::get('/create', [CustomerController::class, 'create'])->name('customers.create');
        Route::post('/store', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/edit/{id}', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('/update/{id}', [CustomerController::class, 'update'])->name('customers.update');
        Route::post('/destroy', [CustomerController::class, 'destroy'])->name('customers.destroy');
        Route::post('/addSample', [CustomerController::class, 'addSample'])->name('customer.addSample');
        Route::get('/samples/{id}', [CustomerController::class, 'samples'])->name('customers.samples');
        Route::post('/send-email', [CustomerController::class,'sendEmail'])->name('customers.send-email');

    });

    # supplier
    Route::prefix('suppliers')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('suppliers');
        Route::get('/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/store', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/edit/{id}', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/update/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::post('/destroy', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });

    # cgt grade
    Route::prefix('cgt-grades')->group(function () {
        Route::get('/', [CGTGradeController::class, 'index'])->name('cgt_grades');
        Route::get('/create', [CGTGradeController::class, 'create'])->name('cgt_grades.create');
        Route::post('/store', [CGTGradeController::class, 'store'])->name('cgt_grades.store');
        Route::get('/edit/{id}', [CGTGradeController::class, 'edit'])->name('cgt_grades.edit');
        Route::put('/update/{id}', [CGTGradeController::class, 'update'])->name('cgt_grades.update');
        Route::post('/destroy', [CGTGradeController::class, 'destroy'])->name('cgt_grades.destroy');
    });

    # nt grade
    Route::prefix('nt-grades')->group(function () {
        Route::get('/', [NTGradeController::class, 'index'])->name('nt_grades');
        Route::get('/create', [NTGradeController::class, 'create'])->name('nt_grades.create');
        Route::post('/store', [NTGradeController::class, 'store'])->name('nt_grades.store');
        Route::get('/edit/{id}', [NTGradeController::class, 'edit'])->name('nt_grades.edit');
        Route::put('/update/{id}', [NTGradeController::class, 'update'])->name('nt_grades.update');
        Route::post('/destroy', [NTGradeController::class, 'destroy'])->name('nt_grades.destroy');
    });

    # color
    Route::prefix('colors')->group(function () {
        Route::get('/', [ColorController::class, 'index'])->name('colors');
        Route::get('/create', [ColorController::class, 'create'])->name('colors.create');
        Route::post('/store', [ColorController::class, 'store'])->name('colors.store');
        Route::get('/edit/{id}', [ColorController::class, 'edit'])->name('colors.edit');
        Route::put('/update/{id}', [ColorController::class, 'update'])->name('colors.update');
        Route::post('/destroy', [ColorController::class, 'destroy'])->name('colors.destroy');
    });

    # product type
    Route::prefix('product-type')->group(function () {
        Route::get('/', [ProductTypeController::class, 'index'])->name('product.types');
        Route::get('/create', [ProductTypeController::class, 'create'])->name('product.types.create');
        Route::post('/store', [ProductTypeController::class, 'store'])->name('product.types.store');
        Route::get('/edit/{id}', [ProductTypeController::class, 'edit'])->name('product.types.edit');
        Route::put('/update/{id}', [ProductTypeController::class, 'update'])->name('product.types.update');
        Route::delete('/destroy', [ProductTypeController::class, 'destroy'])->name('product.types.destroy');
    });

    # scan
    ## scan in
    Route::prefix('scan-in')->group(function () {
        Route::get('/new-scan-in', [ScanInController::class, 'index'])->name('newScanIn');
        Route::post('/newScanIn-get-values', [ScanInController::class, 'getSkewValues'])->name('newScanIn.getValues');
        Route::post('/newScanIn-add', [ScanInController::class, 'addScanInInventory'])->name('newScanIn.add');
        ## scan in log
        Route::get('/scanInLogs', [ScanInController::class, 'scanInLogs'])->name('scanInLogs');
        Route::post('/getLogsInByfilters', [ScanInController::class, 'getLogsInByfilters'])->name('scanInLog.getLogsInByfilters');
        Route::get('/scanInInventory', [ScanInController::class, 'scanInInventory'])->name('scanInInventory');
        Route::get('/edit-inventory/{id}', [ScanInController::class, 'editInventory'])->name('ScanInInventory.edit');
        Route::put('/update-inventory/{id}', [ScanInController::class, 'updateInventory'])->name('ScanInInventory.updateInventory');
        Route::get('/inventoryHistory/{id}', [ScanInController::class, 'inventoryHistory'])->name('ScanInInventory.inventoryHistory');
        Route::post('/delete-inventory', [ScanInController::class, 'destroy'])->name('ScanInInventory.destroy');
        Route::post('/delete-skew-number', [ScanInController::class, 'skewNumberDelete'])->name('ScanInInventory.skewNumberDelete');
        Route::post('/delete-skew-number-selected', [ScanInController::class, 'deleteSelected'])->name('scanInInventory.deleteSelected');

    });
    ## scan out
    Route::prefix('scan-out')->group(function () {
        Route::get('/new-scan-out', [ScanOutController::class, 'index'])->name('newScanOut');
        Route::post('/newScanOut-get-customers', [ScanOutController::class, 'getCustomers'])->name('newScanOut.getCustomers');
        Route::post('/newScanOut-add', [ScanOutController::class, 'addScanOutInventory'])->name('newScanOut.add');
        Route::post('/newScanOut-get-values', [ScanOutController::class, 'getSkewValues'])->name('newScanOut.getValues');
        Route::get('/newScanOut-getappendCustomers', [ScanOutController::class, 'getappendCustomers'])->name('newScanOut.getappendCustomers');
        ## scan out log
        Route::get('/scanOutLogs', [ScanOutController::class, 'scanOutLogs'])->name('scanOutLogs');
        Route::post('/getLogsByfilters', [ScanOutController::class, 'getLogsByfilters'])->name('scanoutLog.getLogsByfilters');
    });

    //Orders Routes:
    Route::prefix('orders')->group(function () {
        Route::get('/active-orders/{customer_id?}', [OrderController::class, 'index'])->name('orders');
        Route::post('/update-order-scan-status', [OrderController::class, 'updateOrderScanStatus'])->name('orders.updateOrderScanStatus');
        Route::get('/orderHistory/{id}', [OrderController::class, 'orderHistory'])->name('orderHistory');
        Route::get('/edit-order/{id}/{customer_id?}', [OrderController::class, 'editOrder'])->name('orders.edit');
        Route::put('/update-order/{id}/{customer_id?}', [OrderController::class, 'updateOrder'])->name('orders.updateOrder');
        Route::post('/delete-skew-number', [OrderController::class, 'skewNumberDelete'])->name('orders.skewNumberDelete');
        Route::post('/delete-order', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::post('/get-order-skew-values', [OrderController::class, 'getSkewValues'])->name('orders.getValues');
        Route::post('/order-scan-out', [OrderController::class, 'newScanOut'])->name('orders.newScanOut');
        Route::post('/submit-order-document', [OrderController::class, 'updateOrderDocuments'])->name('orders.updateOrderDocuments');
        ## Order Statuses Requirement submission
        Route::post('/submit-status-preload', [OrderController::class, 'preloadStatusSubmission'])->name('orders.preloadStatusSubmission');
        Route::post('/submit-status-shipping-in-process', [OrderController::class, 'shippingInProcessStatusSubmission'])->name('orders.shippingInProcessStatusSubmission');
        Route::post('/submit-status-shipped', [OrderController::class, 'shippedStatusSubmission'])->name('orders.shippedStatusSubmission');
        Route::post('/submit-status-post-loading-documentation', [OrderController::class, 'postLoadingDocumentationStatusSubmission'])->name('orders.postLoadingDocumentationStatusSubmission');
        Route::post('/submit-status-end-stage', [OrderController::class, 'endStageStatusSubmission'])->name('orders.endStageStatusSubmission');
        Route::post('/submit-status-closed', [OrderController::class, 'closedStatusSubmission'])->name('orders.closedStatusSubmission');
        ## Pending Order routes:
        Route::get('/pending-orders', [OrderController::class, 'pendingOrders'])->name('pendingOrders');
        Route::get('/pending-order-queue/{order_id}', [OrderController::class, 'pendingOrderQueue'])->name('pendingOrder.queue');
        Route::post('/queue-data-submit/{id}', [OrderController::class, 'queueDataSubmit'])->name('orders.pendingOrder.queueDataSubmit');
        Route::get('/generate-pdf-of-order-queue/{id}', [OrderController::class, 'generatePDF'])->name('orders.pendingOrder.makeQueuePDF');
        Route::post('/delete-order-document', [OrderController::class, 'deleteDocument'])->name('orders.deleteDocument');
    });

    //Customer Samples:
    Route::prefix('sampleRequests')->group(function () {
        Route::get('/', [SampleRequestController::class, 'index'])->name('sampleRequests');
        Route::get('/create', [SampleRequestController::class, 'create'])->name('sampleRequests.create');
        Route::post('/store', [SampleRequestController::class, 'store'])->name('sampleRequests.store');
        Route::get('/edit/{id}', [SampleRequestController::class, 'edit'])->name('sampleRequests.edit');
        Route::put('/update/{id}', [SampleRequestController::class, 'update'])->name('sampleRequests.update');
        Route::post('/delete-sample', [SampleRequestController::class, 'destroy'])->name('sampleRequests.destroy');
        Route::post('/add-customer', [SampleRequestController::class, 'addCustomer'])->name('sampleRequests.addCustomer');
    });

    Route::post('/import-data', [ImportController::class, 'importData'])->name('import');
    //Reports Routes:
    Route::prefix('reports')->group(function () {
        Route::get('/inventory-summary', [InventoryController::class, 'index'])->name('inventorySummary')->middleware('CheckInventoryReportPermission');
        Route::post('/get-inventory-by-filter', [InventoryController::class, 'getInventoryByFilter'])->name('inventory.getInventoryByFilter');
        Route::get('/cgt-summary', [InventoryController::class, 'cgtSummary'])->name('inventory.cgtSummary')->middleware('CheckCGTReportPermission');
        Route::get('/nt-summary', [InventoryController::class, 'ntSummary'])->name('inventory.ntSummary')->middleware('CheckNTReportPermission');
        Route::get('/color-summary', [InventoryController::class, 'color_summary'])->name('inventory.color_summary')->middleware('CheckColorReportPermission');
        Route::post('/getCgtSummaryByFilterr', [InventoryController::class, 'getCgtSummaryByFilterr'])->name('inventory.getCgtSummaryByFilterr');
        Route::post('/getNtSummaryByFilter', [InventoryController::class, 'getNtSummaryByFilter'])->name('inventory.getNtSummaryByFilter');
        Route::get('/cgt-comulative-summary', [InventoryController::class, 'cgtComulativeSummary'])->name('inventory.cgtComulativeSummary')->middleware('CheckComulativeCGTReportPermission');
        Route::get('/nt-comulative-summary', [InventoryController::class, 'ntComulativeSummary'])->name('inventory.ntComulativeSummary')->middleware('CheckComulativeNTReportPermission');
        Route::post('/get-comulative-cgt-by-filter', [InventoryController::class, 'getComulativeCgtByFilter'])->name('inventory.getComulativeCgtByFilter');
        Route::post('/get-comulative-nt-by-filter', [InventoryController::class, 'getComulativeNtByFilter'])->name('inventory.getComulativeNtByFilter');
        Route::post('/get-ntByColorFilter', [InventoryController::class, 'getNtByColorFilter'])->name('inventory.getNtByColorFilter');
        Route::get('/customer-summary-report', [InventoryController::class, 'customerSummaryReport'])->name('inventory.customerSummaryReport')->middleware('CheckCustomerReportPermission');
        Route::post('/get-customer-report-by-rlsNo', [InventoryController::class, 'getCustomerReportByRlsNo'])->name('inventory.getCustomerReportByRlsNo');
        Route::get('/nexpac-report', [InventoryController::class, 'nexpacReport'])->name('inventory.nexpacReport')->middleware('CheckNexpacReportPermission');
        Route::post('/get-nexpac-report-by-rlsNo', [InventoryController::class, 'getNexpacReportByRlsNo'])->name('inventory.getNexpacReportByRlsNo');
        Route::get('/internal-report', [InventoryController::class, 'internalReport'])->name('inventory.internalReport')->middleware('CheckInternalReportPermission');
        Route::post('/get-internal-report-by-rlsNo', [InventoryController::class, 'getInternalReportByRlsNo'])->name('inventory.getInternalReportByRlsNo');
        Route::get('/billing-report', [InventoryController::class, 'billingReport'])->name('inventory.billingReport');
        Route::post('/get-billing-report-by-refno', [InventoryController::class, 'getBillingReportByRefNo'])->name('inventory.getBillingReportByRefNo');
        Route::get('/pnl-report', [InventoryController::class, 'pnl_report'])->name('inventory.pnl_report');
        Route::post('/get-pnl-report-by-rlsno', [InventoryController::class, 'getPnlReportByRlsNo'])->name('inventory.getPnlReportByRlsNo');

    });
});
