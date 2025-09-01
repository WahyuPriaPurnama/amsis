<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HarianController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScanlogController;
use App\Http\Controllers\SubsidiaryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

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

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
route::middleware('auth')->group(function () {
    route::resource('employees', EmployeeController::class);
    route::prefix('employee')->controller(EmployeeController::class)->group(function () {
        Route::get('foto_profil/{pp}', 'pp')->name('employee.pp');
        Route::get('KTP/{ktp}', 'ktp')->name('employee.ktp');
        Route::get('NPWP/{npwp}', 'npwp')->name('employee.npwp');
        Route::get('KK/{kk}', 'kk')->name('employee.kk');
        Route::get('BPJS-ket/{bpjs_ket}', 'bpjs_ket')->name('employee.bpjs_ket');
        Route::get('BPJS-kes/{bpjs_kes}', 'bpjs_kes')->name('employee.bpjs_kes');
        route::get('export-pdf', [EmployeeController::class, 'index_pdf'])->name('employees.pdf');
        route::get('export-excel', [EmployeeController::class, 'index_excel'])->name('employees.excel');
        route::get('show-pdf/{employee}', [EmployeeController::class, 'show_pdf'])->name('employee.pdf');
    });

    route::resource('subsidiaries', SubsidiaryController::class);
    route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    route::post('/users/password', [UserController::class, 'updatePassword'])->name('password.update2');
    route::get('/users/password', [UserController::class, 'editPassword'])->name('password.edit');
    route::resource('users', UserController::class);
    route::get('log-activity', [HomeController::class, 'logActivity'])->name('log.activity');
    route::get('Log-activity/truncate', [HomeController::class, 'truncate'])->name('log.activity.truncate');
    route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    route::resource('vehicles', VehicleController::class);
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

    route::resource('scanlog', ScanlogController::class);
    route::prefix('scanlog')->controller(ScanlogController::class)->group(function () {
        route::post('import', [ScanlogController::class, 'import'])->name('scanlog.import');
        route::get('export', [ScanlogController::class, 'export'])->name('scanlog.export');
        route::post('convert', [ScanlogController::class, 'convert'])->name('scanlog.convert');
        route::get('truncate', [ScanlogController::class, 'truncate'])->name('scanlog.truncate');
        route::get('proses-gaji', [ScanlogController::class, 'prosesGaji'])->name('scanlog.proses.gaji');
    });

    route::resource('karyawan-harian', HarianController::class);
    route::prefix('karyawan-harian')->controller(HarianController::class)->group(function () {
        route::post('import', [HarianController::class, 'import'])->name('karyawan-harian.import');
        route::get('export', [HarianController::class, 'export'])->name('karyawan-harian.export');
        route::get('truncate', [HarianController::class, 'truncate'])->name('karyawan-harian.truncate');
        route::post('slip/{pin}', [HarianController::class, 'cetakSlip'])->name('karyawan-cetak-slip');
    });
});



// Auth::routes([
//     'register' => false
// ]);
route::redirect('/', '/login');

//e-slip

route::get('/ams-malang', function () {
    return view('e-slip.ams');
});
route::get('/rmm-malang', function () {
    return view('e-slip.rmm');
});
route::get('/eln-malang', function () {
    return view('e-slip.eln1');
});
route::get('/eln-bwi', function () {
    return view('e-slip.eln2');
});
route::get('/haka-bwi', function () {
    return view('e-slip.haka');
});
route::get('/bofi-bwi', function () {
    return view('e-slip.bofi');
});
