<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PublicPaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TrackedPluginController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Payment Link Routes (No Auth Required for Clients)
Route::get('/pay/invoice/{token}', [PublicPaymentController::class, 'showCheckout'])->name('public.payment.checkout');
Route::post('/pay/invoice/{token}', [PublicPaymentController::class, 'processPayment'])->name('public.payment.process');
Route::get('/payment/success/{token}', [PublicPaymentController::class, 'success'])->name('public.payment.success');
Route::get('/payment/failed/{token}', [PublicPaymentController::class, 'failed'])->name('public.payment.failed');
Route::get('/pay/invoice/{token}/pdf', [PublicPaymentController::class, 'downloadPdf'])->name('public.payment.pdf');

// Webhook Endpoints
Route::post('/webhooks/{gateway}', [WebhookController::class, 'handle'])->name('webhooks.handle');

// Authenticated Admin / Staff Application Routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clients
    Route::resource('clients', ClientController::class);
    Route::get('/clients/{client}/statement', [ClientController::class, 'statement'])->name('clients.statement');
    Route::get('/clients/{client}/statement/pdf', [ClientController::class, 'downloadStatementPdf'])->name('clients.statement_pdf');

    // Projects
    Route::resource('projects', ProjectController::class);

    // Services Catalog
    Route::resource('services', ServiceController::class);

    // Invoices
    Route::post('/invoices/calculate-ajax', [InvoiceController::class, 'calculateAjax'])->name('invoices.calculate_ajax');
    Route::resource('invoices', InvoiceController::class);
    Route::post('/invoices/{invoice}/duplicate', [InvoiceController::class, 'duplicate'])->name('invoices.duplicate');
    Route::post('/invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
    Route::get('/invoices/{invoice}/download-pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.download_pdf');
    Route::post('/invoices/{invoice}/send-email', [InvoiceController::class, 'sendEmail'])->name('invoices.send_email');

    // Payments
    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store']);
    Route::post('/invoices/{invoice}/generate-payment-link', [PaymentController::class, 'generateLink'])->name('payments.generate_link');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/invoices', [ReportController::class, 'invoices'])->name('reports.invoices');
    Route::get('/reports/payments', [ReportController::class, 'payments'])->name('reports.payments');
    Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/tax', [ReportController::class, 'tax'])->name('reports.tax');

    // Track Plugins Telemetry Dashboard
    Route::get('/tracked-plugins', [TrackedPluginController::class, 'index'])->name('tracked-plugins.index');
    Route::delete('/tracked-plugins/{trackedPlugin}', [TrackedPluginController::class, 'destroy'])->name('tracked-plugins.destroy');

    // Settings (Protected by Admin Role)
    Route::prefix('settings')->name('settings.')->middleware(['role:admin'])->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/company', [SettingController::class, 'updateCompany'])->name('update_company');
        Route::post('/invoice', [SettingController::class, 'updateInvoice'])->name('update_invoice');
        Route::post('/tax', [SettingController::class, 'storeTax'])->name('store_tax');
        Route::delete('/tax/{tax}', [SettingController::class, 'deleteTax'])->name('delete_tax');
        Route::post('/currency', [SettingController::class, 'storeCurrency'])->name('store_currency');
        Route::post('/gateways', [SettingController::class, 'updateGateways'])->name('update_gateways');
        Route::post('/users', [SettingController::class, 'storeUser'])->name('store_user');
    });
});
