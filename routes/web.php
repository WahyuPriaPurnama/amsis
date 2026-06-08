<?php

use App\Http\Controllers\Api\TrackingsController;
use App\Http\Controllers\HRD\EmployeeController;
use App\Http\Controllers\HRD\HarianController;
use App\Http\Controllers\HRD\ScanlogController;
use App\Http\Controllers\HRD\SubsidiaryController;
use App\Http\Controllers\HRD\VehicleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HRD\AssetController;
use App\Http\Controllers\Purchasing\MasterSupplierController;
use App\Http\Controllers\Purchasing\ReceiptController;
use App\Http\Controllers\Purchasing\RequestOrderController;
use App\Http\Controllers\Purchasing\RequestPaymentController;
use App\Http\Controllers\RolePermissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/tracking', [TrackingsController::class, 'index'])->name('tracking.index');
Route::post('/tracking', [TrackingsController::class, 'track'])->name('tracking.process');

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::resource('employees', EmployeeController::class);
    Route::prefix('employee')->controller(EmployeeController::class)->group(function () {
        Route::get('foto_profil/{pp}/{name}', 'pp')->name('employee.pp');
        Route::get('KTP/{ktp}/{name}', 'ktp')->name('employee.ktp');
        Route::get('NPWP/{npwp}/{name}', 'npwp')->name('employee.npwp');
        Route::get('KK/{kk}/{name}', 'kk')->name('employee.kk');
        Route::get('BPJS-ket/{bpjs_ket}/{name}', 'bpjs_ket')->name('employee.bpjs_ket');
        Route::get('BPJS-kes/{bpjs_kes}/{name}', 'bpjs_kes')->name('employee.bpjs_kes');
        Route::get('ttd/{ttd}/{name}', 'ttd')->name('employee.ttd');
        Route::post('import', [EmployeeController::class, 'import'])->name('employees.import');
        Route::get('export-pdf', [EmployeeController::class, 'index_pdf'])->name('employees.pdf');
        Route::get('export-excel', [EmployeeController::class, 'index_excel'])->name('employees.excel');
        Route::get('show-pdf/{employee}', [EmployeeController::class, 'show_pdf'])->name('employee.pdf');
    });

    Route::get('/admin/roles', [RolePermissionController::class, 'index'])->name('roles.index');
    Route::post('/admin/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
    Route::post('/admin/permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
    Route::post('/admin/roles/assign-permission', [RolePermissionController::class, 'assignPermissionToRole'])->name('roles.assign.permission');
    Route::post('/admin/users/assign-role', [RolePermissionController::class, 'assignRoleToUser'])->name('users.assign.role');
    Route::get('/admin/roles/{id}/edit', [RolePermissionController::class, 'edit'])->name('roles.edit');
    Route::put('/admin/roles/{id}', [RolePermissionController::class, 'update'])->name('roles.update');
    Route::delete('/admin/roles/{id}', [RolePermissionController::class, 'destroy'])->name('roles.destroy');
    Route::post('/admin/roles/assign-subsidiary', [RolePermissionController::class, 'assignSubsidiary'])
        ->name('roles.assign.subsidiary');

    Route::get('subsidiaries/transfer', [SubsidiaryController::class, 'transferView'])->name('subsidiaries.transfer');
    Route::post('subsidiaries/transfer', [SubsidiaryController::class, 'transferStore'])->name('subsidiaries.transfer.store');
    Route::resource('subsidiaries', SubsidiaryController::class);

    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    Route::post('/users/password', [UserController::class, 'updatePassword'])->name('password.update2');
    Route::get('/users/password', [UserController::class, 'editPassword'])->name('password.edit');
    Route::resource('users', UserController::class);
    Route::get('log-activity', [HomeController::class, 'logActivity'])->name('log.activity');
    Route::get('log-activity/truncate', [HomeController::class, 'truncate'])->name('log.activity.truncate');
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::resource('vehicles', VehicleController::class);
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

    Route::get('scanlog', [ScanlogController::class, 'index'])->name('scanlog.index');
    Route::prefix('scanlog')->controller(ScanlogController::class)->group(function () {
        Route::post('import', [ScanlogController::class, 'import'])->name('scanlog.import');
        Route::get('export', [ScanlogController::class, 'export'])->name('scanlog.export');
        Route::post('convert', [ScanlogController::class, 'convert'])->name('scanlog.convert');
        Route::get('truncate', [ScanlogController::class, 'truncate'])->name('scanlog.truncate');
        Route::get('proses-gaji', [ScanlogController::class, 'prosesGaji'])->name('scanlog.proses.gaji');
    });

    Route::resource('karyawan-harian', HarianController::class);
    Route::prefix('karyawan-harian')->controller(HarianController::class)->group(function () {
        Route::post('import', [HarianController::class, 'import'])->name('karyawan-harian.import');
        Route::get('export', [HarianController::class, 'export'])->name('karyawan-harian.export');
        Route::get('truncate', [HarianController::class, 'truncate'])->name('karyawan-harian.truncate');
        Route::post('slip/{pin}', [HarianController::class, 'cetakSlip'])->name('karyawan-cetak-slip');
    });

    // Request Order
    Route::prefix('request-order')->name('request-order.')->group(function () {
        Route::resource('/', RequestOrderController::class)->parameters([
            '' => 'request_order'
        ]);
        Route::get('{requestOrder}/receive', [RequestOrderController::class, 'receive'])->name('receive');
        Route::put('{requestOrder}/receive', [RequestOrderController::class, 'updateReceive'])->name('update-receive');
        Route::post('approve/{id}', [RequestOrderController::class, 'approveDivHead'])
            ->name('approve_div_head');
        Route::post('approve-manager/{id}', [RequestOrderController::class, 'approveManager'])
            ->name('approve_manager');
        Route::post('approve-bod/{id}', [RequestOrderController::class, 'approveBod'])
            ->name('approve_bod');
        Route::get('{id}/pdf', [RequestOrderController::class, 'pdf'])
            ->name('pdf');
    });

    // Request Payment
    Route::prefix('request-payment')->name('request-payment.')->group(function () {
        Route::resource('/', RequestPaymentController::class)->parameters([
            '' => 'request_payment'
        ]);
        Route::post('approve/{id}', [RequestPaymentController::class, 'approveManager'])
            ->name('approve_manager');
        Route::post('approve-bod/{id}', [RequestPaymentController::class, 'approveBod'])
            ->name('approve_bod');
        Route::get('{id}/pdf', [RequestPaymentController::class, 'pdf'])
            ->name('pdf');
        Route::get('attachment/{id}', [RequestPaymentController::class, 'attachment'])->name('attachment');
    });

    // Asset
    Route::resource('asset', AssetController::class);
    Route::get('asset/photo/{id}', [AssetController::class, 'photo'])->name('asset.photo');
    Route::get('asset/attachment/{id}', [AssetController::class, 'attachment'])->name('asset.attachment');
    Route::get('asset/delivery-receipt/{id}', [AssetController::class, 'delivery_receipt'])->name('asset.delivery_receipt');
    Route::get('asset/manual-book/{id}', [AssetController::class, 'manual_book'])->name('asset.manual_book');
    Route::get('asset/export/pdf', [AssetController::class, 'export_pdf'])->name('asset.export_pdf');
    Route::get('asset/export/excel', [AssetController::class, 'export_excel'])->name('asset.export_excel');

    //Master Supplier
    Route::resource('master-supplier', MasterSupplierController::class);
    Route::resource('receipts', ReceiptController::class);
});
// routes/web.php
use Illuminate\Support\Facades\Http;

Route::get('/test-wa', function () {
    $response = Http::withHeaders([
        'Authorization' => env('FONNTE_TOKEN'),
    ])->post('https://api.fonnte.com/send', [
        'target' => '085745334330',
        'message' => 'Test pesan dari Browser Lokal',
    ]);

    return $response->json();
});


// Auth::routes([
//     'register' => false
// ]);
Route::redirect('/', '/login');

// e-slip routes
$routes = [
    'ams-malang' => 'hrd.e-slip.ams',
    'rmm-malang' => 'hrd.e-slip.rmm',
    'eln-malang' => 'hrd.e-slip.eln1',
    'eln-bwi'    => 'hrd.e-slip.eln2',
    'haka-bwi'   => 'hrd.e-slip.haka',
    'bofi-bwi'   => 'hrd.e-slip.bofi',
];

foreach ($routes as $uri => $view) {
    Route::view($uri, $view);
}
