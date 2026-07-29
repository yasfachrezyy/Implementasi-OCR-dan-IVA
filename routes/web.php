<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IvaController;
use App\Http\Controllers\OcrController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $role = Auth::user()->role;
        if ($role === 'klien') return redirect()->route('klien.dashboard');
        if ($role === 'staff') return redirect()->route('staff.dashboard');
        if ($role === 'notaris') return redirect()->route('notaris.dashboard');
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:klien'])->prefix('klien')->name('klien.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Klien\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('permohonan', \App\Http\Controllers\Klien\PermohonanKlienController::class);
    Route::get('permohonan/{permohonan}/lengkapi-berkas', [\App\Http\Controllers\Klien\PermohonanKlienController::class, 'showReuploadForm'])->name('permohonan.reupload.form');
    Route::post('permohonan/{permohonan}/lengkapi-berkas', [\App\Http\Controllers\Klien\PermohonanKlienController::class, 'handleReupload'])->name('permohonan.reupload.handle');
    Route::get('/syarat-informasi', [\App\Http\Controllers\Klien\PermohonanKlienController::class, 'showSyaratInformasi'])->name('syarat-informasi');
});

Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Staff\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('permohonan', \App\Http\Controllers\Staff\PermohonanController::class)->except(['create', 'store', 'destroy']);
    Route::resource('layanan', \App\Http\Controllers\Staff\LayananController::class);
    Route::get('arsip', [\App\Http\Controllers\Staff\ArsipController::class, 'index'])->name('arsip.index');
    Route::get('arsip/{permohonan}/edit', [\App\Http\Controllers\Staff\ArsipController::class, 'edit'])->name('arsip.edit');
    Route::put('arsip/{permohonan}', [\App\Http\Controllers\Staff\ArsipController::class, 'update'])->name('arsip.update');
    Route::get('arsip/{permohonan}/download', [\App\Http\Controllers\Staff\ArsipController::class, 'download'])->name('arsip.download');
    Route::get('laporan', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/excel', [\App\Http\Controllers\LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
    Route::get('laporan/pdf', [\App\Http\Controllers\LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
});

Route::middleware(['auth', 'role:notaris'])->prefix('notaris')->name('notaris.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('permohonan', \App\Http\Controllers\Staff\PermohonanController::class)->except(['create', 'store', 'destroy']);
    Route::resource('layanan', \App\Http\Controllers\Staff\LayananController::class);
    Route::resource('arsip', \App\Http\Controllers\Staff\ArsipController::class)->only(['index', 'edit', 'update']);
    Route::get('staff', [\App\Http\Controllers\Admin\UserController::class, 'indexStaff'])->name('staff.index');
    Route::get('klien', [\App\Http\Controllers\Admin\UserController::class, 'indexKlien'])->name('klien.index');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['index']);
    Route::get('laporan', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/excel', [\App\Http\Controllers\LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
    Route::get('laporan/pdf', [\App\Http\Controllers\LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
});

Route::get('/iva', [IvaController::class, 'index'])->name('iva.index');
Route::post('/iva/send-message', [IvaController::class, 'sendMessage'])->name('iva.sendMessage');

Route::post('/ocr/hitung-halaman', [OcrController::class, 'countPages'])->name('ocr.count');
Route::post('/ocr/proses-halaman', [OcrController::class, 'processSinglePage'])->name('ocr.process');
Route::post('/ocr/proses-dokumen', [OcrController::class, 'processDocument'])->name('ocr.processDocument');
Route::post('/ocr/simpan-verifikasi', [OcrController::class, 'saveVerifiedData'])->name('ocr.save');
Route::get('/view-pdf', [App\Http\Controllers\OcrController::class, 'viewPdf'])->name('view.pdf');

require __DIR__.'/auth.php';