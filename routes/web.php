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
        Route::post('import', [EmployeeController::class, 'import'])->name('employees.import');
        Route::get('export-pdf', [EmployeeController::class, 'index_pdf'])->name('employees.pdf');
        Route::get('export-excel', [EmployeeController::class, 'index_excel'])->name('employees.excel');
        Route::get('show-pdf/{employee}', [EmployeeController::class, 'show_pdf'])->name('employee.pdf');
    });

    Route::resource('subsidiaries', SubsidiaryController::class);
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export');
    Route::post('/users/password', [UserController::class, 'updatePassword'])->name('password.update2');
    Route::get('/users/password', [UserController::class, 'editPassword'])->name('password.edit');
    Route::resource('users', UserController::class);
    Route::get('log-activity', [HomeController::class, 'logActivity'])->name('log.activity');
    Route::get('Log-activity/truncate', [HomeController::class, 'truncate'])->name('log.activity.truncate');
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

    Route::resource('scanlog', ScanlogController::class);
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
});



// Auth::routes([
//     'register' => false
// ]);
Route::redirect('/', '/login');

//e-slip

Route::get('/ams-malang', function () {
    return view('e-slip.ams');
});
Route::get('/rmm-malang', function () {
    return view('e-slip.rmm');
});
Route::get('/eln-malang', function () {
    return view('e-slip.eln1');
});
Route::get('/eln-bwi', function () {
    return view('e-slip.eln2');
});
Route::get('/haka-bwi', function () {
    return view('e-slip.haka');
});
Route::get('/bofi-bwi', function () {
    return view('e-slip.bofi');
});
