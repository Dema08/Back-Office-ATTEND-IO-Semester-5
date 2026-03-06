<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PertemuanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KehadiranController;
use App\Http\Controllers\MasterDosenController;
use App\Http\Controllers\MasterMahasiswaController;
use App\Http\Controllers\MasterMataKuliahController;
use App\Http\Controllers\MasterKelasController;
use App\Http\Controllers\MasterUserController;   // ⬅️ TAMBAHAN
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RfidController;

/*
|--------------------------------------------------------------------------
| AUTH (LOGIN / REGISTER / LOGOUT)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// halaman login
Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');

// proses login
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest');

// logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');


/*
|--------------------------------------------------------------------------
| LUPA PASSWORD (KIRIM EMAIL KE ADMIN)
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| APP (HARUS LOGIN)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Home & Welcome
    |--------------------------------------------------------------------------
    */
    Route::redirect('/home', '/class-assist')->name('home');
    Route::view('/welcome', 'welcome')->name('welcome');

    /*
    |--------------------------------------------------------------------------
    | Class Assist / Pertemuan
    |--------------------------------------------------------------------------
    */

    // halaman class assist
    Route::get('/class-assist', [PertemuanController::class, 'create'])
        ->name('class-assist');

    Route::post('/class-assist/end', [PertemuanController::class, 'end'])->name('class-assist.end');
    Route::get('/class-assist/kehadiran/{pertemuan}', [PertemuanController::class, 'kehadiranData'])
    ->name('class-assist.kehadiran');

    // alias lama (kalau ada link yang masih pakai pertemuan.create)
    Route::get('/pertemuan/create', [PertemuanController::class, 'create'])
        ->name('pertemuan.create');

    // AJAX untuk dropdown berantai (dosen -> matkul -> kelas)
    Route::get('/pertemuan/options', [PertemuanController::class, 'options'])
        ->name('pertemuan.options');

    // mulai pertemuan
    Route::post('/class-assist/start', [PertemuanController::class, 'start'])
        ->name('class-assist.start');

    /*
    |--------------------------------------------------------------------------
    | Kehadiran
    |--------------------------------------------------------------------------
    */
    Route::post('/pertemuan/{pertemuan}/kehadiran', [KehadiranController::class, 'update'])
        ->name('kehadiran.update');

    /*
    |--------------------------------------------------------------------------
    | Laporan
    |--------------------------------------------------------------------------
    */
    Route::get('/laporan', [LaporanController::class, 'index'])
        ->name('laporan.index');

    Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');

    Route::get('/laporan/{pertemuan}/download', [LaporanController::class, 'download'])
        ->name('laporan.download');

    Route::post('/laporan/kirim-email', [LaporanController::class, 'sendEmail'])
        ->name('laporan.sendEmail');

    Route::get('/laporan/{pertemuan}', [LaporanController::class, 'show'])->name('laporan.show');

        /*
    |--------------------------------------------------------------------------
    | Master Data
    |--------------------------------------------------------------------------
    */
    Route::prefix('master')->name('master.')->group(function () {
        Route::view('/', 'master.index')->name('index');

        Route::resource('dosen', MasterDosenController::class)->names([
            'index'   => 'dosen.index',
            'create'  => 'dosen.create',
            'store'   => 'dosen.store',
            'edit'    => 'dosen.edit',
            'update'  => 'dosen.update',
            'destroy' => 'dosen.destroy',
        ]);

        Route::resource('mahasiswa', MasterMahasiswaController::class)->names([
            'index'   => 'mahasiswa.index',
            'create'  => 'mahasiswa.create',
            'store'   => 'mahasiswa.store',
            'edit'    => 'mahasiswa.edit',
            'update'  => 'mahasiswa.update',
            'destroy' => 'mahasiswa.destroy',
        ]);

        Route::resource('mata-kuliah', MasterMataKuliahController::class)->names([
            'index'   => 'matakuliah.index',
            'create'  => 'matakuliah.create',
            'store'   => 'matakuliah.store',
            'edit'    => 'matakuliah.edit',
            'update'  => 'matakuliah.update',
            'destroy' => 'matakuliah.destroy',
        ]);

        Route::resource('kelas', MasterKelasController::class)
            ->parameters(['kelas' => 'kelas'])
            ->names([
                'index'   => 'kelas.index',
                'create'  => 'kelas.create',
                'store'   => 'kelas.store',
                'edit'    => 'kelas.edit',
                'update'  => 'kelas.update',
                'destroy' => 'kelas.destroy',
            ]);

        // ⬇️ MASTER USER (CRUD akun login)
        Route::resource('user', MasterUserController::class)->names([
            'index'   => 'user.index',
            'create'  => 'user.create',
            'store'   => 'user.store',
            'edit'    => 'user.edit',
            'update'  => 'user.update',
            'destroy' => 'user.destroy',
        ]);
    });

        Route::prefix('rfid')->name('rfid.')->group(function () {
    Route::get('/',        [RfidController::class, 'index'])->name('index');
    Route::get('/create',  [RfidController::class, 'create'])->name('create');
    Route::post('/',       [RfidController::class, 'store'])->name('store');
    Route::delete('/{rfid}', [RfidController::class, 'destroy'])->name('destroy');
});
});
