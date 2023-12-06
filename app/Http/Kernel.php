<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'check_warehouse_permission' => \App\Http\Middleware\CheckWarehousePermission::class,
        'check_user_permission' => \App\Http\Middleware\CheckUserPermission::class,
        'check_role_permission' => \App\Http\Middleware\CheckRolePermission::class,
        'check_customer_permission' => \App\Http\Middleware\CheckCustomerPermission::class,
        'check_supplier_permission' => \App\Http\Middleware\CheckSupplierPermission::class,
        'check_cgt_permission' => \App\Http\Middleware\CheckCGTPermission::class,
        'check_nt_permission' => \App\Http\Middleware\CheckNTPermission::class,
        'check_color_permission' => \App\Http\Middleware\CheckColorPermission::class,
        'check_product_type_permission' => \App\Http\Middleware\CheckProductTypePermission::class,
        'check_scan_in_permission' => \App\Http\Middleware\CheckScanInPermission::class,
        'check_scan_out_permission' => \App\Http\Middleware\CheckScanOutPermission::class,
        'check_inventory_permission' => \App\Http\Middleware\CheckInventoryPermission::class,
        'check_order_permission' => \App\Http\Middleware\CheckOrderPermission::class,
        'check_report_permission' => \App\Http\Middleware\CheckReportPermission::class,
        'CheckInventoryReportPermission' => \App\Http\Middleware\CheckInventoryReportPermission::class,
        'CheckCGTReportPermission' => \App\Http\Middleware\CheckCGTReportPermission::class,
        'CheckNTReportPermission' => \App\Http\Middleware\CheckNTReportPermission::class,
        'CheckColorReportPermission' => \App\Http\Middleware\CheckColorReportPermission::class,
        'CheckCustomerReportPermission' => \App\Http\Middleware\CheckCustomerReportPermission::class,
        'CheckComulativeCGTReportPermission' => \App\Http\Middleware\CheckComulativeCGTReportPermission::class,
        'CheckComulativeNTReportPermission' => \App\Http\Middleware\CheckComulativeNTReportPermission::class,
        'CheckNexpacReportPermission' => \App\Http\Middleware\CheckNexpacReportPermission::class,
        'CheckInternalReportPermission' => \App\Http\Middleware\CheckInternalReportPermission::class,
    ];
}
