<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HRD\AssetController;
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


// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', App\Livewire\Auth\Login::class)->name('login');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Employee Management
    Route::get('/employees', App\Livewire\Hrd\Employee\EmployeeIndex::class)->name('employees.index');
    Route::get('/employees/create', App\Livewire\Hrd\Employee\EmployeeCreate::class)->name('employees.create');
    Route::get('/employees/{employee}', App\Livewire\Hrd\Employee\EmployeeShow::class)->name('employees.show');
    Route::get('/employees/{employee}/edit', App\Livewire\Hrd\Employee\EmployeeEdit::class)->name('employees.edit');
    Route::prefix('employee')->controller(EmployeeController::class)->group(function () {
        Route::get('foto_profil/{pp}/{name}', 'pp')->name('employee.pp');
        Route::get('KTP/{ktp}/{name}', 'ktp')->name('employee.ktp');
        Route::get('NPWP/{npwp}/{name}', 'npwp')->name('employee.npwp');
        Route::get('KK/{kk}/{name}', 'kk')->name('employee.kk');
        Route::get('BPJS-ket/{bpjs_ket}/{name}', 'bpjs_ket')->name('employee.bpjs_ket');
        Route::get('BPJS-kes/{bpjs_kes}/{name}', 'bpjs_kes')->name('employee.bpjs_kes');
        Route::get('ttd/{ttd}/{name}', 'ttd')->name('employee.ttd');
        Route::post('import', 'import')->name('employees.import');
        Route::get('export-pdf', 'index_pdf')->name('employees.pdf');
        Route::get('export-excel', 'index_excel')->name('employees.excel');
        Route::get('show-pdf/{employee}', 'show_pdf')->name('employee.pdf');
    });

    // Leave
    // Route::get('/leaves', App\Livewire\Hrd\Leave\LeaveIndex::class)->name('leaves.index');

    // Role & Permission Management
    Route::get('/admin/roles', App\Livewire\Admin\RoleManagement::class)->name('roles.index');
    Route::get('/users', App\Livewire\Admin\UserManagement::class)->name('users.index');
    Route::get('/change-password', App\Livewire\Admin\UserManagement::class)->name('password.change');
    Route::post('/admin/permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
    Route::post('/admin/roles/assign-permission', [RolePermissionController::class, 'assignPermissionToRole'])->name('roles.assign.permission');
    Route::post('/admin/users/assign-role', [RolePermissionController::class, 'assignRoleToUser'])->name('users.assign.role');
    Route::get('/admin/roles/{id}/edit', [RolePermissionController::class, 'edit'])->name('roles.edit');
    Route::put('/admin/roles/{id}', [RolePermissionController::class, 'update'])->name('roles.update');
    Route::delete('/admin/roles/{id}', [RolePermissionController::class, 'destroy'])->name('roles.destroy');
    Route::post('/admin/roles/assign-subsidiary', [RolePermissionController::class, 'assignSubsidiary'])->name('roles.assign.subsidiary');

    // Subsidiaries
    Route::get('/subsidiaries', App\Livewire\Hrd\Subsidiary\SubsidiaryIndex::class)->name('subsidiaries.index');
    Route::get('/subsidiaries/{subsidiary}', App\Livewire\Hrd\Subsidiary\SubsidiaryShow::class)->name('subsidiaries.show');

    // Users & System Dashboard
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    Route::get('log-activity', [HomeController::class, 'logActivity'])->name('log.activity');
    Route::get('log-activity/truncate', [HomeController::class, 'truncate'])->name('log.activity.truncate');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Vehicle Management
    Route::get('/vehicles', App\Livewire\Hrd\Vehicle\VehicleIndex::class)->name('vehicles.index');
    Route::get('/vehicles/create', App\Livewire\Hrd\Vehicle\VehicleCreate::class)->name('vehicles.create');
    Route::get('/vehicles/{vehicle}', App\Livewire\Hrd\Vehicle\VehicleShow::class)->name('vehicles.show');
    Route::get('/vehicles/{vehicle}/edit', App\Livewire\Hrd\Vehicle\VehicleEdit::class)->name('vehicles.edit');
    Route::prefix('vehicle')->controller(VehicleController::class)->group(function () {
        Route::get('foto/{foto}/{jenis}/{nopol}', 'foto')->name('vehicle.foto');
        Route::get('stnk/{stnk}/{jenis}/{nopol}', 'stnk')->name('vehicle.stnk');
        Route::get('pajak/{pajak}/{jenis}/{nopol}', 'pajak')->name('vehicle.pajak');
        Route::get('kir/{kir}/{jenis}/{nopol}', 'kir')->name('vehicle.kir');
        Route::get('qr/{qr}/{jenis}/{nopol}', 'qr')->name('vehicle.qr');
        Route::get('polis/{polis}/{jenis}/{nopol}', 'polis')->name('vehicle.polis');
        Route::get('export-pdf', 'index_pdf')->name('vehicles.pdf');
        Route::get('show-pdf/{id}', 'show_pdf')->name('vehicle.pdf');
    });

    // Request Order
    Route::prefix('request-order')->name('request-order.')->group(function () {
        Route::get('/', App\Livewire\Purchasing\RequestOrder\RequestOrderIndex::class)->name('index');
        Route::get('/create', App\Livewire\Purchasing\RequestOrder\RequestOrderCreate::class)->name('create');
        Route::get('/{requestOrder}', App\Livewire\Purchasing\RequestOrder\RequestOrderShow::class)->name('show');
        Route::get('/{requestOrder}/edit', App\Livewire\Purchasing\RequestOrder\RequestOrderEdit::class)->name('edit');
        Route::get('/{id}/pdf', [RequestOrderController::class, 'pdf'])->name('pdf');
    });

    Route::prefix('request-payment')->name('request-payment.')->group(function () {
        Route::get('/', App\Livewire\Purchasing\RequestPayment\RequestPaymentIndex::class)->name('index');
        Route::get('/create', App\Livewire\Purchasing\RequestPayment\RequestPaymentCreate::class)->name('create');
        Route::get('/{requestPayment}', App\Livewire\Purchasing\RequestPayment\RequestPaymentShow::class)->name('show');
        Route::get('/{requestPayment}/edit', App\Livewire\Purchasing\RequestPayment\RequestPaymentEdit::class)->name('edit');
    });

    Route::prefix('request-payment')->name('request-payment.')->group(function () {
        Route::get('{id}/pdf', [RequestPaymentController::class, 'pdf'])->name('pdf');
        Route::get('attachment/{id}', [RequestPaymentController::class, 'attachment'])->name('attachment');
    });

    // Asset Management
    Route::get('/assets', App\Livewire\Hrd\Asset\AssetIndex::class)->name('asset.index');
    Route::get('/assets/create', App\Livewire\Hrd\Asset\AssetCreate::class)->name('asset.create');
    Route::get('/assets/{asset}', App\Livewire\Hrd\Asset\AssetShow::class)->name('asset.show');
    Route::get('/assets/{asset}/edit', App\Livewire\Hrd\Asset\AssetEdit::class)->name('asset.edit');
    Route::prefix('asset')->controller(AssetController::class)->group(function () {
        Route::get('photo/{id}', 'photo')->name('asset.photo');
        Route::get('attachment/{id}', 'attachment')->name('asset.attachment');
        Route::get('delivery-receipt/{id}', 'delivery_receipt')->name('asset.delivery_receipt');
        Route::get('manual-book/{id}', 'manual_book')->name('asset.manual_book');
        Route::get('export/pdf', 'export_pdf')->name('asset.export_pdf');
        Route::get('export/excel', 'export_excel')->name('asset.export_excel');
    });
});
Route::get('/assets/scan/{code}', App\Livewire\Hrd\Asset\AssetScan::class)->name('asset.scan');
Route::redirect('/', '/login');
Route::get('e-slip/{plant}', App\Livewire\Hrd\ESlipShow::class)->name('e-slip.show');
