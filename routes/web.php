<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::post('companies/switch', [CompanyController::class, 'switch'])->name('companies.switch');
Route::resource('companies', CompanyController::class)->except(['show']);

Route::resource('customers', CustomerController::class);
Route::post('customers/{customer}/advance', [CustomerController::class, 'storeAdvance'])->name('customers.advance.store');

Route::resource('products', ProductController::class)->except(['show']);
Route::resource('invoices', InvoiceController::class);
Route::get('invoices/{invoice}/preview', [InvoiceController::class, 'pdfPreview'])->name('invoices.preview');
Route::get('invoices/{invoice}/preview/html', [InvoiceController::class, 'pdfPreviewHtml'])->name('invoices.preview.html');
Route::match(['get', 'post'], 'invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
Route::post('invoices/{invoice}/payments', [PaymentController::class, 'storeForInvoice'])->name('invoices.payments.store');
Route::post('invoices/{invoice}/apply-advance', [PaymentController::class, 'applyAdvance'])->name('invoices.apply-advance');

Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
