<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\UserManagementController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PelabuhanMerakController;
use App\Http\Controllers\PelabuhanBakauheniController;

use App\Http\Controllers\KinerjaIFCSController;
use App\Http\Controllers\komposisiSegmenController;
use App\Http\Controllers\marketLintasanController;
use App\Http\Controllers\LabaKapalController;

use App\Jobs\RunProphetForecast;

// Route untuk pengguna yang sudah login
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/etl/trigger', [DashboardController::class, 'triggerEtl'])->name('etl.trigger');
    Route::post('/run-forecast', [DashboardController::class, 'runForecast'])->name('runForecast');

    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    Route::get('/user-profile', [InfoUserController::class, 'create']);
    Route::post('/user-profile', [InfoUserController::class, 'store']);

    // ⬅ Tambahkan logout route dengan benar
    Route::post('/logout', [SessionsController::class, 'destroy'])->name('logout');

    // Route khusus admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/user-management', [UserManagementController::class, 'index'])->name('user-management.index');
        Route::post('/user-management/{user}', [UserManagementController::class, 'update'])->name('user-management.update');
        Route::delete('/user_management/{id}', [UserManagementController::class, 'delete'])->name('user-management.delete');
        Route::post('/user-management', [UserManagementController::class, 'store'])->name('user-management.store');

    });

    // Route untuk admin dan karyawan
    Route::middleware('role:admin,karyawan')->group(function () {
        // Pelabuhan Merak
        Route::get('/merak', [PelabuhanMerakController::class, 'index'])->name('pelabuhan.merak.index');
        Route::post('/merak/upload', [PelabuhanMerakController::class, 'uploadcsvPelabuhanMerak'])->name('pelabuhan.merak.upload');
        Route::delete('/merak/{id}', [PelabuhanMerakController::class, 'delete'])->name('pelabuhan.merak.delete');
        Route::get('/edit-merak/edit/{id}', [PelabuhanMerakController::class, 'edit'])->name('pelabuhan-merak.edit');
        Route::post('/pelabuhan-merak/{id}/update', [PelabuhanMerakController::class, 'updatePost'])->name('pelabuhan-merak.updatePost');
        Route::post('/pelabuhan-merak/run-etl/{tahun}', [PelabuhanMerakController::class, 'runEtl'])->name('pelabuhan.merak.run-etl');

        // Pelabuhan Bakauheni
        Route::get('/bakauheni', [PelabuhanBakauheniController::class, 'index'])->name('pelabuhan.bakauheni.index');
        Route::post('/bakauheni/upload', [PelabuhanBakauheniController::class, 'uploadcsvPelabuhanBakauheni'])->name('pelabuhan.bakauheni.upload');
        Route::delete('/pelabuhan-bakauheni/{id}', [PelabuhanBakauheniController::class, 'delete'])->name('pelabuhan.bakauheni.delete');
        Route::get('/edit-bakauheni/edit/{id}', [PelabuhanBakauheniController::class, 'edit'])->name('pelabuhan-bakauheni.edit');
        Route::post('/pelabuhan-bakauheni/{id}/update', [PelabuhanBakauheniController::class, 'updatePost'])->name('pelabuhan-bakauheni.updatePost');
        Route::post('/pelabuhan-bakauheni/run-etl/{tahun}', [PelabuhanBakauheniController::class, 'runEtl'])->name('pelabuhan.bakauheni.run-etl');

        // Kinerja IFCS
        Route::get('/kinerja-ifcs', [kinerjaIfcsController::class, 'index'])->name('kinerja-ifcs.index');
        Route::post('/kinerja-ifcs/upload', [kinerjaIfcsController::class, 'uploadcsvKinerja'])->name('kinerja-ifcs.uploadcsv');
        Route::delete('/kinerja-ifcs/{id}', [kinerjaIfcsController::class, 'delete'])->name('kinerja-ifcs.delete');
        Route::post('/kinerja-ifcs/{id}/update', [kinerjaIfcsController::class, 'updatePost'])->name('kinerja-ifcs.updatePost');

        // Komposisi Segmen
        Route::get('/komposisi-segmen', [komposisiSegmenController::class, 'index'])->name('komposisi.index');

        // Market Lintasan
        Route::get('/market-lintasan', [marketLintasanController::class, 'index'])->name('market-lintasan.index');

        // Laba Kapal
        Route::get('/laba-kapal', [labaKapalController::class, 'index'])->name('laba-kapal.index');
        Route::post('/laba-kapal/upload', [labaKapalController::class, 'uploadcsvKapal'])->name('laba-kapal.uploadcsv');
        Route::delete('/laba-kapal/{id}', [labaKapalController::class, 'delete'])->name('laba-kapal.delete');
        Route::get('/edit-laba/edit/{id}', [labaKapalController::class, 'edit'])->name('laba-kapal.edit');

// Route untuk submit form update (digunakan oleh form inline edit)
Route::post('/laba-kapal/{id}/update', [labaKapalController::class, 'updatePost'])->name('laba-kapal.updatePost');

        Route::get('/', [HomeController::class, 'home']);
    });

    // Halaman statis lainnya
    Route::view('billing', 'billing')->name('billing');
    Route::view('rtl', 'rtl')->name('rtl');
    Route::view('tables', 'tables')->name('tables');
    Route::view('virtual-reality', 'virtual-reality')->name('virtual-reality');
    Route::view('static-sign-in', 'static-sign-in')->name('sign-in');
    Route::view('static-sign-up', 'static-sign-up')->name('sign-up');
    Route::view('/edit-merak', 'edit-merak');
});

// Route untuk tamu (guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/login', [SessionsController::class, 'store']);

    Route::get('/login/forgot-password', [ResetController::class, 'create']);
    Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
    Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
    Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');
});