<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HRD\EmployeeController;
use App\Http\Controllers\HRD\VehicleController;
use App\Http\Controllers\Purchasing\RequestOrderController;
use App\Http\Controllers\Purchasing\RequestPaymentController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect & Public / Guest Routes
Route::redirect('/', '/login');
Route::get('e-slip/{plant}', App\Livewire\Hrd\ESlipShow::class)->name('e-slip.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', App\Livewire\Auth\Login::class)->name('login');
});

// Vendor Public Portal
Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::get('/register', App\Livewire\Purchasing\Vendor\VendorRegister::class)->name('register');
    Route::get('/portal', App\Livewire\Purchasing\Vendor\VendorPortal::class)->name('portal');
    Route::get('/success', fn() => "Registrasi berhasil! Berkas Anda sedang direview oleh admin.")->name('success');
});

// Public Scan Asset (Akses luar tanpa login)
Route::get('/assets/scan/{asset}', App\Livewire\Hrd\Asset\AssetScan::class)->name('asset.scan');


// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // System Logs
    Route::prefix('log-activity')->name('log.activity')->group(function () {
        Route::get('/', [HomeController::class, 'logActivity']);
        Route::get('/truncate', [HomeController::class, 'truncate'])->name('.truncate');
    });

    // Admin & User Management
    Route::prefix('admin')->group(function () {
        Route::get('/roles', App\Livewire\Admin\RoleManagement::class)->name('roles.index');
        Route::get('/roles/{id}/edit', [RolePermissionController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{id}', [RolePermissionController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{id}', [RolePermissionController::class, 'destroy'])->name('roles.destroy');
        Route::post('/permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
        Route::post('/roles/assign-permission', [RolePermissionController::class, 'assignPermissionToRole'])->name('roles.assign.permission');
        Route::post('/roles/assign-subsidiary', [RolePermissionController::class, 'assignSubsidiary'])->name('roles.assign.subsidiary');
    });

    Route::get('/users', App\Livewire\Admin\UserManagement::class)->name('users.index');
    Route::get('/change-password', App\Livewire\Admin\UserManagement::class)->name('password.change');
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    Route::post('/admin/users/assign-role', [RolePermissionController::class, 'assignRoleToUser'])->name('users.assign.role');

    // Vendor Management (Admin side)
    Route::prefix('vendor')->name('admin.vendors.')->group(function () {
        Route::get('/review', App\Livewire\Purchasing\Vendor\VendorReview::class)->name('review');
        Route::get('/approved', App\Livewire\Purchasing\Vendor\VendorList::class)->name('approved');
    });

    // Subsidiaries
    Route::prefix('subsidiaries')->name('subsidiaries.')->group(function () {
        Route::get('/', App\Livewire\Hrd\Subsidiary\SubsidiaryIndex::class)->name('index');
        Route::get('/{subsidiary}', App\Livewire\Hrd\Subsidiary\SubsidiaryShow::class)->name('show');
    });

    // Employee Management
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', App\Livewire\Hrd\Employee\EmployeeIndex::class)->name('index');
        Route::get('/create', App\Livewire\Hrd\Employee\EmployeeCreate::class)->name('create');
        Route::post('/import', [EmployeeController::class, 'import'])->name('import');
        Route::get('/export-pdf', [EmployeeController::class, 'index_pdf'])->name('pdf');
        Route::get('/export-excel', [EmployeeController::class, 'index_excel'])->name('excel');
        Route::get('/{employee}', App\Livewire\Hrd\Employee\EmployeeShow::class)->name('show');
        Route::get('/{employee}/edit', App\Livewire\Hrd\Employee\EmployeeEdit::class)->name('edit');
    });

    Route::controller(EmployeeController::class)->prefix('employee')->group(function () {
        Route::get('foto_profil/{pp}/{name}', 'pp')->name('employee.pp');
        Route::get('KTP/{ktp}/{name}', 'ktp')->name('employee.ktp');
        Route::get('NPWP/{npwp}/{name}', 'npwp')->name('employee.npwp');
        Route::get('KK/{kk}/{name}', 'kk')->name('employee.kk');
        Route::get('BPJS-ket/{bpjs_ket}/{name}', 'bpjs_ket')->name('employee.bpjs_ket');
        Route::get('BPJS-kes/{bpjs_kes}/{name}', 'bpjs_kes')->name('employee.bpjs_kes');
        Route::get('ttd/{ttd}/{name}', 'ttd')->name('employee.ttd');
        Route::get('show-pdf/{employee}', 'show_pdf')->name('employee.pdf');
    });

    // Vehicle Management
    Route::prefix('vehicles')->name('vehicles.')->group(function () {
        Route::get('/', App\Livewire\Hrd\Vehicle\VehicleIndex::class)->name('index');
        Route::get('/create', App\Livewire\Hrd\Vehicle\VehicleCreate::class)->name('create');
        Route::get('/export-pdf', [VehicleController::class, 'index_pdf'])->name('pdf');
        Route::get('/{vehicle}', App\Livewire\Hrd\Vehicle\VehicleShow::class)->name('show');
        Route::get('/{vehicle}/edit', App\Livewire\Hrd\Vehicle\VehicleEdit::class)->name('edit');
    });

    Route::controller(VehicleController::class)->prefix('vehicle')->group(function () {
        Route::get('foto/{foto}/{jenis}/{nopol}', 'foto')->name('vehicle.foto');
        Route::get('stnk/{stnk}/{jenis}/{nopol}', 'stnk')->name('vehicle.stnk');
        Route::get('pajak/{pajak}/{jenis}/{nopol}', 'pajak')->name('vehicle.pajak');
        Route::get('kir/{kir}/{jenis}/{nopol}', 'kir')->name('vehicle.kir');
        Route::get('qr/{qr}/{jenis}/{nopol}', 'qr')->name('vehicle.qr');
        Route::get('polis/{polis}/{jenis}/{nopol}', 'polis')->name('vehicle.polis');
        Route::get('show-pdf/{id}', 'show_pdf')->name('vehicle.pdf');
    });

    // Asset Management
    Route::prefix('assets')->name('asset.')->group(function () {
        Route::get('/', App\Livewire\Hrd\Asset\AssetIndex::class)->name('index');
        Route::get('/create', App\Livewire\Hrd\Asset\AssetCreate::class)->name('create');
        Route::get('/{asset}', App\Livewire\Hrd\Asset\AssetShow::class)->name('show');
        Route::get('/{asset}/edit', App\Livewire\Hrd\Asset\AssetEdit::class)->name('edit');
    });

    // Purchasing - Request Order
    Route::prefix('request-order')->name('request-order.')->group(function () {
        Route::get('/', App\Livewire\Purchasing\RequestOrder\RequestOrderIndex::class)->name('index');
        Route::get('/create', App\Livewire\Purchasing\RequestOrder\RequestOrderCreate::class)->name('create');
        Route::get('/{requestOrder}', App\Livewire\Purchasing\RequestOrder\RequestOrderShow::class)->name('show');
        Route::get('/{requestOrder}/edit', App\Livewire\Purchasing\RequestOrder\RequestOrderEdit::class)->name('edit');
        Route::get('/{id}/pdf', [RequestOrderController::class, 'pdf'])->name('pdf');
    });

    // Purchasing - Request Payment
    Route::prefix('request-payment')->name('request-payment.')->group(function () {
        Route::get('/', App\Livewire\Purchasing\RequestPayment\RequestPaymentIndex::class)->name('index');
        Route::get('/create', App\Livewire\Purchasing\RequestPayment\RequestPaymentCreate::class)->name('create');
        Route::get('/{requestPayment}', App\Livewire\Purchasing\RequestPayment\RequestPaymentShow::class)->name('show');
        Route::get('/{requestPayment}/edit', App\Livewire\Purchasing\RequestPayment\RequestPaymentEdit::class)->name('edit');
        Route::get('/{id}/pdf', [RequestPaymentController::class, 'pdf'])->name('pdf');
        Route::get('/attachment/{id}', [RequestPaymentController::class, 'attachment'])->name('attachment');
    });
});