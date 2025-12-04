<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HarianController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScanlogController;
use App\Http\Controllers\SubsidiaryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RequestOrderController;
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

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::resource('employees', EmployeeController::class);
    Route::prefix('employee')->controller(EmployeeController::class)->group(function () {
        Route::get('foto_profil/{pp}', 'pp')->name('employee.pp');
        Route::get('KTP/{ktp}', 'ktp')->name('employee.ktp');
        Route::get('NPWP/{npwp}', 'npwp')->name('employee.npwp');
        Route::get('KK/{kk}', 'kk')->name('employee.kk');
        Route::get('BPJS-ket/{bpjs_ket}', 'bpjs_ket')->name('employee.bpjs_ket');
        Route::get('BPJS-kes/{bpjs_kes}', 'bpjs_kes')->name('employee.bpjs_kes');
        Route::get('ttd/{ttd}', 'ttd')->name('employee.ttd');
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
        Route::get('foto/{foto}', 'foto')->name('vehicle.foto');
        Route::get('stnk/{stnk}', 'stnk')->name('vehicle.stnk');
        Route::get('pajak/{pajak}', 'pajak')->name('vehicle.pajak');
        Route::get('kir/{kir}', 'kir')->name('vehicle.kir');
        Route::get('qr/{qr}', 'qr')->name('vehicle.qr');
        Route::get('polis/{polis}', 'polis')->name('vehicle.polis');
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

    Route::resource('request-order', RequestOrderController::class);
    Route::post('request-order/approve/{id}', [RequestOrderController::class, 'approve'])
        ->name('request-order.approve_div_head');
    Route::post('request-order/approve-manager/{id}', [RequestOrderController::class, 'approveManager'])->name('request-order.approve_manager');
    Route::get('request-order/{id}/pdf', [RequestOrderController::class, 'pdf'])
        ->name('request-order.pdf');
});



// Auth::routes([
//     'register' => false
// ]);
Route::redirect('/', '/login');

// e-slip routes
$routes = [
    'ams-malang' => 'e-slip.ams',
    'rmm-malang' => 'e-slip.rmm',
    'eln-malang' => 'e-slip.eln1',
    'eln-bwi'    => 'e-slip.eln2',
    'haka-bwi'   => 'e-slip.haka',
    'bofi-bwi'   => 'e-slip.bofi',
];

foreach ($routes as $uri => $view) {
    Route::view($uri, $view);
}
