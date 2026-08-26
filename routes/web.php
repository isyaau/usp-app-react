<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Superadmin\AccGroupController;
use App\Http\Controllers\Superadmin\AccHeaderController;
use App\Http\Controllers\Superadmin\AccountController;
use App\Http\Controllers\Superadmin\AnggotaController;
use App\Http\Controllers\Superadmin\JaminanController;
use App\Http\Controllers\Superadmin\KasHarianController;
use App\Http\Controllers\Superadmin\KodetransaksiController;
use App\Http\Controllers\Superadmin\KantorController;
use App\Http\Controllers\Superadmin\KelompokController;
use App\Http\Controllers\Superadmin\MarketingController;
use App\Http\Controllers\Superadmin\PemindahbukuanSimpananController;
use App\Http\Controllers\Superadmin\PenutupanSimpananController;
use App\Http\Controllers\Superadmin\SetoranSimpananController;
use App\Http\Controllers\Superadmin\TarikanSimpananController;
use App\Http\Controllers\Superadmin\BerjangkaprodukController;
use App\Http\Controllers\Superadmin\BerjangkaController;
use App\Http\Controllers\Superadmin\SimpananController;
use App\Http\Controllers\Superadmin\SimpananRencanaController;
use App\Http\Controllers\Superadmin\PencairanPinjamanController;
use App\Http\Controllers\Superadmin\PinjamanController;
use App\Http\Controllers\Superadmin\PinjamanProdukController;
use App\Http\Controllers\Superadmin\SimpananProdukController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\Superadmin\AngsuranPinjamanController;
use App\Http\Controllers\Superadmin\PenaltiPinjamanController;
use App\Http\Controllers\Superadmin\AngsuranKolektifController;
use App\Http\Controllers\Superadmin\TransaksiSimpananBerjangkaController;
use App\Http\Controllers\Superadmin\PenarikanDanaTitipanController;
use App\Http\Controllers\Superadmin\LaporanController;

// Route Livewire lama modul Anggota dihapus — sudah dimigrasikan ke Inertia.
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

// USER

// KELOMPOK

// KANTOR


// ACCOUNT HEADER

// ACCOUNT

// PINJAMAN PRODUK

// JAMINAN

// PINJAMAN
// Data Pinjaman sudah dimigrasikan ke Inertia (PinjamanController).

// PINJAMAN PRODUK

// PINJAMAN JADWAL ULANG

// PINJAMAN TAGIHAN

// PINJAMAN PENGHAPUSAN

// PINJAMAN SURAT PERINGATAN

// PINJAMAN PENGEMBALIAN JAMINAN

// SIMPANAN
// KODE TRANSAKSI

// SIMPANAN PRODUK

// MARKETING

// SIMPANAN
// Data Simpanan sudah dimigrasikan ke Inertia (SimpananController).

// SIMPANAN RENCANA
// Simpanan Rencana sudah dimigrasikan ke Inertia (SimpananRencanaController).

// SIMPANAN BERJANGKA PRODUK

// SIMPANAN BERJANGKA


// Penalti Pinjaman Kolektif


// Angsuran Pinjaman Kolektif


// Setoran Kolektif Bank

// Setoran Simpanan sudah dimigrasikan ke Inertia (SetoranSimpananController).


// Tarikan Simpanan sudah dimigrasikan ke Inertia (TarikanSimpananController).

// Tarikan Simpana Kolektif

// Pemindahbukuan Simpanan sudah dimigrasikan ke Inertia (PemindahbukuanSimpananController).

// Penutupan Simpanan sudah dimigrasikan ke Inertia (PenutupanSimpananController).

// Setoran Simpana Berjangka

// Simpanan Berjangka & Produknya sudah dimigrasikan ke Inertia (BerjangkaprodukController & BerjangkaController).

// Pencairan Simpana Berjangka


// Penarikan Titipan Anggota

use Illuminate\Support\Facades\Auth;

use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/reset-swal', function () {
    session()->forget('swal');
    return response()->noContent();
});
// Route::get('/', function () {
//     return view('welcome');
// });


// Wilayah Indonesia (dropdown berantai Provinsi → Kota → Kecamatan → Kelurahan)
Route::middleware(['auth'])->prefix('wilayah')->name('wilayah.')->group(function () {
    Route::get('/provinsi', [WilayahController::class, 'provinces'])->name('provinces');
    Route::get('/kota/{province}', [WilayahController::class, 'cities'])->name('cities');
    Route::get('/kecamatan/{city}', [WilayahController::class, 'districts'])->name('districts');
    Route::get('/kelurahan/{district}', [WilayahController::class, 'villages'])->name('villages');
});

Route::prefix('superadmin')->name('superadmin.')->middleware(['auth', 'role:superadmin'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //User (Inertia + React)
    Route::get('/user', [UserController::class, 'index'])->name('user');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::get('/user/{user}', [UserController::class, 'show'])->name('user.show');

    //Kelompok (Inertia + React)
    Route::get('/kelompok', [KelompokController::class, 'index'])->name('kelompok');
    Route::get('/kelompok/create', [KelompokController::class, 'create'])->name('kelompok.create');
    Route::post('/kelompok', [KelompokController::class, 'store'])->name('kelompok.store');
    Route::get('/kelompok/search-users', [KelompokController::class, 'searchUsers'])
        ->name('kelompok.search-users');
    Route::get('/kelompok/{kelompok}/edit', [KelompokController::class, 'edit'])->name('kelompok.edit');
    Route::put('/kelompok/{kelompok}', [KelompokController::class, 'update'])->name('kelompok.update');
    Route::delete('/kelompok/{kelompok}', [KelompokController::class, 'destroy'])->name('kelompok.destroy');
    Route::get('/kelompok/{kelompok}', [KelompokController::class, 'show'])->name('kelompok.show');

    //Anggota (Inertia + React)
    Route::get('/anggota', [AnggotaController::class, 'index'])->name('anggota');
    Route::get('/anggota/create', [AnggotaController::class, 'create'])->name('anggota.create');
    Route::post('/anggota', [AnggotaController::class, 'store'])->name('anggota.store');
    Route::get('/anggota/export/template', [AnggotaController::class, 'downloadTemplate'])->name('anggota.template');
    Route::post('/anggota/import', [AnggotaController::class, 'import'])->name('anggota.import');
    Route::get('/anggota/export/pdf', [AnggotaController::class, 'exportPdf'])->name('anggota.export-pdf');
    Route::get('/anggota/export/excel', [AnggotaController::class, 'exportExcel'])->name('anggota.export-excel');
    Route::get('/anggota/{anggota}/edit', [AnggotaController::class, 'edit'])->name('anggota.edit');
    Route::put('/anggota/{anggota}', [AnggotaController::class, 'update'])->name('anggota.update');
    Route::delete('/anggota/{anggota}', [AnggotaController::class, 'destroy'])->name('anggota.destroy');
    Route::get('/anggota/{anggota}', [AnggotaController::class, 'show'])->name('anggota.show');

    // Kas Harian (Inertia + React)
    Route::get('/kas-harian', [KasHarianController::class, 'index'])->name('kas-harian');
    Route::get('/kas-harian/create', [KasHarianController::class, 'create'])->name('kas-harian.create');
    Route::post('/kas-harian', [KasHarianController::class, 'store'])->name('kas-harian.store');
    Route::get('/kas-harian/{kasHarian}/edit', [KasHarianController::class, 'edit'])->name('kas-harian.edit');
    Route::put('/kas-harian/{kasHarian}', [KasHarianController::class, 'update'])->name('kas-harian.update');
    Route::delete('/kas-harian/{kasHarian}', [KasHarianController::class, 'destroy'])->name('kas-harian.destroy');
    Route::get('/kas-harian/{kasHarian}', [KasHarianController::class, 'show'])->name('kas-harian.show');

    //Kantor (Inertia + React)
    Route::get('/kantor', [KantorController::class, 'index'])->name('kantor');
    Route::get('/kantor/create', [KantorController::class, 'create'])->name('kantor.create');
    Route::post('/kantor', [KantorController::class, 'store'])->name('kantor.store');
    Route::get('/kantor/{kantor}/edit', [KantorController::class, 'edit'])->name('kantor.edit');
    Route::put('/kantor/{kantor}', [KantorController::class, 'update'])->name('kantor.update');
    Route::delete('/kantor/{kantor}', [KantorController::class, 'destroy'])->name('kantor.destroy');
    Route::get('/kantor/{kantor}', [KantorController::class, 'show'])->name('kantor.show');

    // Account Group (dialog Kelola Grup di halaman Account Header)
    Route::post('/acc-group', [AccGroupController::class, 'store'])->name('acc-group.store');

    // Account Header (Inertia + React)
    Route::get('/account-header', [AccHeaderController::class, 'index'])->name('account-header');
    Route::post('/account-header', [AccHeaderController::class, 'store'])->name('account-header.store');
    Route::get('/account-header/create', [AccHeaderController::class, 'create'])->name('account-header.create');
    Route::get('/account-header/{header}/edit', [AccHeaderController::class, 'edit'])->name('account-header.edit');
    Route::put('/account-header/{header}', [AccHeaderController::class, 'update'])->name('account-header.update');
    Route::delete('/account-header/{header}', [AccHeaderController::class, 'destroy'])->name('account-header.destroy');
    Route::get('/account-header/{header}', [AccHeaderController::class, 'show'])->name('account-header.show');

    // Account (Inertia + React)
    Route::get('/account', [AccountController::class, 'index'])->name('account');
    Route::get('/account/create', [AccountController::class, 'create'])->name('account.create');
    Route::post('/account', [AccountController::class, 'store'])->name('account.store');
    Route::get('/account/{account}/edit', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/account/{account}', [AccountController::class, 'update'])->name('account.update');
    Route::delete('/account/{account}', [AccountController::class, 'destroy'])->name('account.destroy');
    Route::get('/account/{account}', [AccountController::class, 'show'])->name('account.show');

    // Pinjaman Produk (Inertia + React)
    Route::get('/pinjaman/produk', [PinjamanProdukController::class, 'index'])->name('pinjaman.produk');
    Route::get('/pinjaman/produk/create', [PinjamanProdukController::class, 'create'])->name('pinjaman.produk.create');
    Route::post('/pinjaman/produk', [PinjamanProdukController::class, 'store'])->name('pinjaman.produk.store');
    Route::get('/pinjaman/produk/{produk}/edit', [PinjamanProdukController::class, 'edit'])->name('pinjaman.produk.edit');
    Route::put('/pinjaman/produk/{produk}', [PinjamanProdukController::class, 'update'])->name('pinjaman.produk.update');
    Route::delete('/pinjaman/produk/{produk}', [PinjamanProdukController::class, 'destroy'])->name('pinjaman.produk.destroy');
    Route::get('/pinjaman/produk/{produk}', [PinjamanProdukController::class, 'show'])->name('pinjaman.produk.show');

    // Pinjaman Jaminan (Inertia + React)
    Route::get('/pinjaman/jaminan', [JaminanController::class, 'index'])->name('pinjaman.jaminan');
    Route::get('/pinjaman/jaminan/create', [JaminanController::class, 'create'])->name('pinjaman.jaminan.create');
    Route::post('/pinjaman/jaminan', [JaminanController::class, 'store'])->name('pinjaman.jaminan.store');
    Route::get('/pinjaman/jaminan/{jaminan}/edit', [JaminanController::class, 'edit'])->name('pinjaman.jaminan.edit');
    Route::put('/pinjaman/jaminan/{jaminan}', [JaminanController::class, 'update'])->name('pinjaman.jaminan.update');
    Route::delete('/pinjaman/jaminan/{jaminan}', [JaminanController::class, 'destroy'])->name('pinjaman.jaminan.destroy');
    Route::get('/pinjaman/jaminan/{jaminan}', [JaminanController::class, 'show'])->name('pinjaman.jaminan.show');

    // Data Pinjaman (Inertia + React)
    Route::get('/pinjaman/pinjaman', [PinjamanController::class, 'index'])->name('pinjaman.pinjaman');
    Route::get('/pinjaman/pinjaman/create', [PinjamanController::class, 'create'])->name('pinjaman.pinjaman.create');
    Route::post('/pinjaman/pinjaman', [PinjamanController::class, 'store'])->name('pinjaman.pinjaman.store');
    Route::delete('/pinjaman/pinjaman/{pinjaman}', [PinjamanController::class, 'destroy'])->name('pinjaman.pinjaman.destroy');

    // Pencairan Pinjaman (Inertia + React)
    Route::get('/pencairan-pinjaman', [PencairanPinjamanController::class, 'index'])->name('pencairan-pinjaman');
    Route::get('/pencairan-pinjaman/create', [PencairanPinjamanController::class, 'create'])->name('pencairan-pinjaman.create');
    Route::post('/pencairan-pinjaman', [PencairanPinjamanController::class, 'store'])->name('pencairan-pinjaman.store');
    Route::get('/pencairan-pinjaman/{pencairan}', [PencairanPinjamanController::class, 'show'])->name('pencairan-pinjaman.show');
    Route::get('/pencairan-pinjaman/{pencairan}/edit', [PencairanPinjamanController::class, 'edit'])->name('pencairan-pinjaman.edit');
    Route::put('/pencairan-pinjaman/{pencairan}', [PencairanPinjamanController::class, 'update'])->name('pencairan-pinjaman.update');
    Route::delete('/pencairan-pinjaman/{pencairan}', [PencairanPinjamanController::class, 'destroy'])->name('pencairan-pinjaman.destroy');
    Route::post('/pencairan-pinjaman/{pencairan}/approve', [PencairanPinjamanController::class, 'approve'])->name('pencairan-pinjaman.approve');
    Route::post('/pencairan-pinjaman/{pencairan}/reject', [PencairanPinjamanController::class, 'reject'])->name('pencairan-pinjaman.reject');
    Route::post('/pencairan-pinjaman/{pencairan}/cairkan', [PencairanPinjamanController::class, 'cairkan'])->name('pencairan-pinjaman.cairkan');

    // Transaksi Pinjaman — JSON helpers
    Route::get('/transaksi-pinjaman/pinjaman-by-anggota/{anggota}', [AngsuranPinjamanController::class, 'pinjamanByAnggota'])
        ->name('transaksi-pinjaman.pinjaman-by-anggota');
    Route::get('/transaksi-pinjaman/pinjaman-by-kelompok/{kelompok}', [AngsuranKolektifController::class, 'pinjamanByKelompok'])
        ->name('transaksi-pinjaman.pinjaman-by-kelompok');

    // Angsuran Pinjaman (Inertia + React)
    Route::get('/transaksi-pinjaman/angsuran-pinjaman', [AngsuranPinjamanController::class, 'index'])->name('transaksi-pinjaman.angsuran-pinjaman');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman/create', [AngsuranPinjamanController::class, 'create'])->name('transaksi-pinjaman.angsuran-pinjaman.create');
    Route::post('/transaksi-pinjaman/angsuran-pinjaman', [AngsuranPinjamanController::class, 'store'])->name('transaksi-pinjaman.angsuran-pinjaman.store');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman/{angsuranPinjaman}', [AngsuranPinjamanController::class, 'show'])->name('transaksi-pinjaman.angsuran-pinjaman.show');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman/{angsuranPinjaman}/edit', [AngsuranPinjamanController::class, 'edit'])->name('transaksi-pinjaman.angsuran-pinjaman.edit');
    Route::put('/transaksi-pinjaman/angsuran-pinjaman/{angsuranPinjaman}', [AngsuranPinjamanController::class, 'update'])->name('transaksi-pinjaman.angsuran-pinjaman.update');
    Route::delete('/transaksi-pinjaman/angsuran-pinjaman/{angsuranPinjaman}', [AngsuranPinjamanController::class, 'destroy'])->name('transaksi-pinjaman.angsuran-pinjaman.destroy');

    // Penalti Pinjaman (Inertia + React)
    Route::get('/transaksi-pinjaman/penalti-pinjaman', [PenaltiPinjamanController::class, 'index'])->name('transaksi-pinjaman.penalti-pinjaman');
    Route::get('/transaksi-pinjaman/penalti-pinjaman/create', [PenaltiPinjamanController::class, 'create'])->name('transaksi-pinjaman.penalti-pinjaman.create');
    Route::post('/transaksi-pinjaman/penalti-pinjaman', [PenaltiPinjamanController::class, 'store'])->name('transaksi-pinjaman.penalti-pinjaman.store');
    Route::get('/transaksi-pinjaman/penalti-pinjaman/{penaltiPinjaman}', [PenaltiPinjamanController::class, 'show'])->name('transaksi-pinjaman.penalti-pinjaman.show');
    Route::get('/transaksi-pinjaman/penalti-pinjaman/{penaltiPinjaman}/edit', [PenaltiPinjamanController::class, 'edit'])->name('transaksi-pinjaman.penalti-pinjaman.edit');
    Route::put('/transaksi-pinjaman/penalti-pinjaman/{penaltiPinjaman}', [PenaltiPinjamanController::class, 'update'])->name('transaksi-pinjaman.penalti-pinjaman.update');
    Route::delete('/transaksi-pinjaman/penalti-pinjaman/{penaltiPinjaman}', [PenaltiPinjamanController::class, 'destroy'])->name('transaksi-pinjaman.penalti-pinjaman.destroy');

    // Angsuran Kolektif (Inertia + React) — shared for all kolektif variants
    Route::get('/transaksi-pinjaman/angsuran-kolektif', [AngsuranKolektifController::class, 'index'])->name('transaksi-pinjaman.angsuran-kolektif');
    Route::get('/transaksi-pinjaman/angsuran-kolektif/create', [AngsuranKolektifController::class, 'create'])->name('transaksi-pinjaman.angsuran-kolektif.create');
    Route::post('/transaksi-pinjaman/angsuran-kolektif', [AngsuranKolektifController::class, 'store'])->name('transaksi-pinjaman.angsuran-kolektif.store');
    Route::get('/transaksi-pinjaman/angsuran-kolektif/{angsuranKolektif}', [AngsuranKolektifController::class, 'show'])->name('transaksi-pinjaman.angsuran-kolektif.show');
    Route::get('/transaksi-pinjaman/angsuran-kolektif/{angsuranKolektif}/edit', [AngsuranKolektifController::class, 'edit'])->name('transaksi-pinjaman.angsuran-kolektif.edit');
    Route::put('/transaksi-pinjaman/angsuran-kolektif/{angsuranKolektif}', [AngsuranKolektifController::class, 'update'])->name('transaksi-pinjaman.angsuran-kolektif.update');
    Route::delete('/transaksi-pinjaman/angsuran-kolektif/{angsuranKolektif}', [AngsuranKolektifController::class, 'destroy'])->name('transaksi-pinjaman.angsuran-kolektif.destroy');

    // Transaksi Simpanan Berjangka — JSON helpers
    Route::get('/transaksi-simpanan-berjangka/deposito-by-anggota/{anggota}', [TransaksiSimpananBerjangkaController::class, 'depositoByAnggota'])
        ->name('transaksi-simpanan-berjangka.deposito-by-anggota');

    // Variant routes for Transaksi Simpanan Berjangka
    $berjangkaVariants = [
        'setoran-simpanan-berjangka',
        'pencairan-simpanan-berjangka',
        'penalti-simpanan-berjangka',
    ];
    $berjangkaMethods = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'];
    foreach ($berjangkaVariants as $variant) {
        $param = 'transaksiBerjangka';
        Route::get("/transaksi-simpanan-berjangka/{$variant}", [TransaksiSimpananBerjangkaController::class, 'index'])->name("transaksi-simpanan-berjangka.{$variant}");
        Route::get("/transaksi-simpanan-berjangka/{$variant}/create", [TransaksiSimpananBerjangkaController::class, 'create'])->name("transaksi-simpanan-berjangka.{$variant}.create");
        Route::post("/transaksi-simpanan-berjangka/{$variant}", [TransaksiSimpananBerjangkaController::class, 'store'])->name("transaksi-simpanan-berjangka.{$variant}.store");
        Route::get("/transaksi-simpanan-berjangka/{$variant}/{{$param}}", [TransaksiSimpananBerjangkaController::class, 'show'])->name("transaksi-simpanan-berjangka.{$variant}.show");
        Route::get("/transaksi-simpanan-berjangka/{$variant}/{{$param}}/edit", [TransaksiSimpananBerjangkaController::class, 'edit'])->name("transaksi-simpanan-berjangka.{$variant}.edit");
        Route::put("/transaksi-simpanan-berjangka/{$variant}/{{$param}}", [TransaksiSimpananBerjangkaController::class, 'update'])->name("transaksi-simpanan-berjangka.{$variant}.update");
        Route::delete("/transaksi-simpanan-berjangka/{$variant}/{{$param}}", [TransaksiSimpananBerjangkaController::class, 'destroy'])->name("transaksi-simpanan-berjangka.{$variant}.destroy");
    }

    // Transaksi Titipan — Penarikan Dana Titipan Anggota
    $titipanPrefix = 'transaksi-titipan.penarikan-dana-titipan';
    Route::get('/transaksi-titipan/penarikan-dana-titipan', [PenarikanDanaTitipanController::class, 'index'])->name($titipanPrefix);
    Route::get('/transaksi-titipan/penarikan-dana-titipan/create', [PenarikanDanaTitipanController::class, 'create'])->name("{$titipanPrefix}.create");
    Route::post('/transaksi-titipan/penarikan-dana-titipan', [PenarikanDanaTitipanController::class, 'store'])->name("{$titipanPrefix}.store");
    Route::get('/transaksi-titipan/penarikan-dana-titipan/{penarikanDanaTitipan}', [PenarikanDanaTitipanController::class, 'show'])->name("{$titipanPrefix}.show");
    Route::get('/transaksi-titipan/penarikan-dana-titipan/{penarikanDanaTitipan}/edit', [PenarikanDanaTitipanController::class, 'edit'])->name("{$titipanPrefix}.edit");
    Route::put('/transaksi-titipan/penarikan-dana-titipan/{penarikanDanaTitipan}', [PenarikanDanaTitipanController::class, 'update'])->name("{$titipanPrefix}.update");
    Route::delete('/transaksi-titipan/penarikan-dana-titipan/{penarikanDanaTitipan}', [PenarikanDanaTitipanController::class, 'destroy'])->name("{$titipanPrefix}.destroy");

    // Laporan routes — variant-based
    $laporanVariants = ['laporan-kas-harian', 'laporan-transaksi-simpanan', 'laporan-transaksi-pinjaman'];
    foreach ($laporanVariants as $lv) {
        $param = 'laporanItem';
        Route::get("/laporan/{$lv}", [LaporanController::class, 'index'])->name("laporan.{$lv}");
        Route::get("/laporan/{$lv}/create", [LaporanController::class, 'create'])->name("laporan.{$lv}.create");
        Route::post("/laporan/{$lv}", [LaporanController::class, 'store'])->name("laporan.{$lv}.store");
        Route::get("/laporan/{$lv}/{{$param}}", [LaporanController::class, 'show'])->name("laporan.{$lv}.show");
        Route::get("/laporan/{$lv}/{{$param}}/edit", [LaporanController::class, 'edit'])->name("laporan.{$lv}.edit");
        Route::put("/laporan/{$lv}/{{$param}}", [LaporanController::class, 'update'])->name("laporan.{$lv}.update");
        Route::delete("/laporan/{$lv}/{{$param}}", [LaporanController::class, 'destroy'])->name("laporan.{$lv}.destroy");
    }

    // Variant routes for Angsuran Kolektif — reuse same controller
    $kolektifVariants = [
        ['slug' => 'penalti-kolektif-tunai', 'path' => 'penalti-kolektif-tunai'],
        ['slug' => 'setoran-angsuran-bank', 'path' => 'setoran-angsuran-bank'],
        ['slug' => 'setoran-angsuran-custom', 'path' => 'setoran-angsuran-custom'],
        ['slug' => 'angsuran-kolektif-tunai', 'path' => 'angsuran-kolektif-tunai'],
        ['slug' => 'angsuran-kolektif-debet-simpanan', 'path' => 'angsuran-kolektif-debet-simpanan'],
    ];

    foreach ($kolektifVariants as $kv) {
        $prefix = "transaksi-pinjaman.{$kv['slug']}";
        Route::get("/transaksi-pinjaman/{$kv['path']}", [AngsuranKolektifController::class, 'index'])->name($prefix);
        Route::get("/transaksi-pinjaman/{$kv['path']}/create", [AngsuranKolektifController::class, 'create'])->name("{$prefix}.create");
        Route::post("/transaksi-pinjaman/{$kv['path']}", [AngsuranKolektifController::class, 'store'])->name("{$prefix}.store");
        Route::get("/transaksi-pinjaman/{$kv['path']}/{angsuranKolektif}", [AngsuranKolektifController::class, 'show'])->name("{$prefix}.show");
        Route::get("/transaksi-pinjaman/{$kv['path']}/{angsuranKolektif}/edit", [AngsuranKolektifController::class, 'edit'])->name("{$prefix}.edit");
        Route::put("/transaksi-pinjaman/{$kv['path']}/{angsuranKolektif}", [AngsuranKolektifController::class, 'update'])->name("{$prefix}.update");
        Route::delete("/transaksi-pinjaman/{$kv['path']}/{angsuranKolektif}", [AngsuranKolektifController::class, 'destroy'])->name("{$prefix}.destroy");
    }

    // Simpanan
    // Kode Transaksi
        // Kode Transaksi (Inertia + React)
    Route::get('/simpanan/kode-transaksi', [KodetransaksiController::class, 'index'])->name('simpanan.kode-transaksi');
    Route::get('/simpanan/kode-transaksi/create', [KodetransaksiController::class, 'create'])->name('simpanan.kode-transaksi.create');
    Route::post('/simpanan/kode-transaksi', [KodetransaksiController::class, 'store'])->name('simpanan.kode-transaksi.store');
    Route::get('/simpanan/kode-transaksi/{kodetransaksi}/edit', [KodetransaksiController::class, 'edit'])->name('simpanan.kode-transaksi.edit');
    Route::put('/simpanan/kode-transaksi/{kodetransaksi}', [KodetransaksiController::class, 'update'])->name('simpanan.kode-transaksi.update');
    Route::delete('/simpanan/kode-transaksi/{kodetransaksi}', [KodetransaksiController::class, 'destroy'])->name('simpanan.kode-transaksi.destroy');
    Route::get('/simpanan/kode-transaksi/{kodetransaksi}', [KodetransaksiController::class, 'show'])->name('simpanan.kode-transaksi.show');


    // Simpanan Produk (Inertia + React)
    Route::get('/simpanan/produk-simpanan', [SimpananProdukController::class, 'index'])->name('simpanan.produk-simpanan');
    Route::get('/simpanan/produk-simpanan/create', [SimpananProdukController::class, 'create'])->name('simpanan.produk-simpanan.create');
    Route::post('/simpanan/produk-simpanan', [SimpananProdukController::class, 'store'])->name('simpanan.produk-simpanan.store');
    Route::get('/simpanan/produk-simpanan/{produk}/edit', [SimpananProdukController::class, 'edit'])->name('simpanan.produk-simpanan.edit');
    Route::put('/simpanan/produk-simpanan/{produk}', [SimpananProdukController::class, 'update'])->name('simpanan.produk-simpanan.update');
    Route::delete('/simpanan/produk-simpanan/{produk}', [SimpananProdukController::class, 'destroy'])->name('simpanan.produk-simpanan.destroy');
    Route::get('/simpanan/produk-simpanan/{produk}', [SimpananProdukController::class, 'show'])->name('simpanan.produk-simpanan.show');

    // Marketing (Inertia + React)
    Route::get('/marketing', [MarketingController::class, 'index'])->name('marketing');
    Route::get('/marketing/create', [MarketingController::class, 'create'])->name('marketing.create');
    Route::post('/marketing', [MarketingController::class, 'store'])->name('marketing.store');
    Route::get('/marketing/{marketing}/edit', [MarketingController::class, 'edit'])->name('marketing.edit');
    Route::put('/marketing/{marketing}', [MarketingController::class, 'update'])->name('marketing.update');
    Route::delete('/marketing/{marketing}', [MarketingController::class, 'destroy'])->name('marketing.destroy');
    Route::get('/marketing/{marketing}', [MarketingController::class, 'show'])->name('marketing.show');

    // Simpanan
    // Simpanan Rencana (Inertia + React)
    Route::get('/simpanan/rencana', [SimpananRencanaController::class, 'index'])->name('simpanan.rencana');
    Route::get('/simpanan/rencana/create', [SimpananRencanaController::class, 'create'])->name('simpanan.rencana.create');
    Route::post('/simpanan/rencana', [SimpananRencanaController::class, 'store'])->name('simpanan.rencana.store');
    Route::delete('/simpanan/rencana/{rencana}', [SimpananRencanaController::class, 'destroy'])->name('simpanan.rencana.destroy');

    // Simpanan
    // Data Simpanan (Inertia + React)
    Route::get('/simpanan', [SimpananController::class, 'index'])->name('simpanan');
    Route::get('/simpanan/create', [SimpananController::class, 'create'])->name('simpanan.create');
    Route::post('/simpanan', [SimpananController::class, 'store'])->name('simpanan.store');
    Route::get('/simpanan/{simpanan}/edit', [SimpananController::class, 'edit'])->name('simpanan.edit');
    Route::put('/simpanan/{simpanan}', [SimpananController::class, 'update'])->name('simpanan.update');
    Route::delete('/simpanan/{simpanan}', [SimpananController::class, 'destroy'])->name('simpanan.destroy');
    Route::get('/simpanan/{simpanan}', [SimpananController::class, 'show'])->name('simpanan.show');


    // Berjangka
    // Produk Berjangka (Inertia + React)
    Route::get('/simpanan-berjangka/produk', [BerjangkaprodukController::class, 'index'])->name('simpanan-berjangka.produk');
    Route::get('/simpanan-berjangka/produk/create', [BerjangkaprodukController::class, 'create'])->name('simpanan-berjangka.produk.create');
    Route::post('/simpanan-berjangka/produk', [BerjangkaprodukController::class, 'store'])->name('simpanan-berjangka.produk.store');
    Route::get('/simpanan-berjangka/produk/{produkBerjangka}/edit', [BerjangkaprodukController::class, 'edit'])->name('simpanan-berjangka.produk.edit');
    Route::put('/simpanan-berjangka/produk/{produkBerjangka}', [BerjangkaprodukController::class, 'update'])->name('simpanan-berjangka.produk.update');
    Route::delete('/simpanan-berjangka/produk/{produkBerjangka}', [BerjangkaprodukController::class, 'destroy'])->name('simpanan-berjangka.produk.destroy');
    Route::get('/simpanan-berjangka/produk/{produkBerjangka}', [BerjangkaprodukController::class, 'show'])->name('simpanan-berjangka.produk.show');

    // Simpanan Berjangka (Inertia + React)
    Route::get('/simpanan-berjangka', [BerjangkaController::class, 'index'])->name('simpanan-berjangka');
    Route::get('/simpanan-berjangka/create', [BerjangkaController::class, 'create'])->name('simpanan-berjangka.create');
    Route::post('/simpanan-berjangka', [BerjangkaController::class, 'store'])->name('simpanan-berjangka.store');
    Route::get('/simpanan-berjangka/{berjangka}/edit', [BerjangkaController::class, 'edit'])->name('simpanan-berjangka.edit');
    Route::put('/simpanan-berjangka/{berjangka}', [BerjangkaController::class, 'update'])->name('simpanan-berjangka.update');
    Route::delete('/simpanan-berjangka/{berjangka}', [BerjangkaController::class, 'destroy'])->name('simpanan-berjangka.destroy');
    Route::get('/simpanan-berjangka/{berjangka}', [BerjangkaController::class, 'show'])->name('simpanan-berjangka.show');


    // Transaksi Simpanan
    // Endpoint JSON rekening simpanan per anggota (dropdown bertingkat form transaksi)
    Route::get('/transaksi-simpanan/simpanan-by-anggota/{anggota}', [SetoranSimpananController::class, 'simpananByAnggota'])
        ->name('transaksi-simpanan.simpanan-by-anggota');

    // Setoran Simpanan (Inertia + React)
    Route::get('/transaksi-simpanan/setoran-simpanan', [SetoranSimpananController::class, 'index'])->name('transaksi-simpanan.setoran-simpanan');
    Route::get('/transaksi-simpanan/setoran-simpanan/create', [SetoranSimpananController::class, 'create'])->name('transaksi-simpanan.setoran-simpanan.create');
    Route::post('/transaksi-simpanan/setoran-simpanan', [SetoranSimpananController::class, 'store'])->name('transaksi-simpanan.setoran-simpanan.store');
    Route::get('/transaksi-simpanan/setoran-simpanan/{setoranSimpanan}/edit', [SetoranSimpananController::class, 'edit'])->name('transaksi-simpanan.setoran-simpanan.edit');
    Route::put('/transaksi-simpanan/setoran-simpanan/{setoranSimpanan}', [SetoranSimpananController::class, 'update'])->name('transaksi-simpanan.setoran-simpanan.update');
    Route::delete('/transaksi-simpanan/setoran-simpanan/{setoranSimpanan}', [SetoranSimpananController::class, 'destroy'])->name('transaksi-simpanan.setoran-simpanan.destroy');
    Route::get('/transaksi-simpanan/setoran-simpanan/{setoranSimpanan}', [SetoranSimpananController::class, 'show'])->name('transaksi-simpanan.setoran-simpanan.show');


    // Tarikan Simpanan (Inertia + React)
    Route::get('/transaksi-simpanan/tarikan-simpanan', [TarikanSimpananController::class, 'index'])->name('transaksi-simpanan.tarikan-simpanan');
    Route::get('/transaksi-simpanan/tarikan-simpanan/create', [TarikanSimpananController::class, 'create'])->name('transaksi-simpanan.tarikan-simpanan.create');
    Route::post('/transaksi-simpanan/tarikan-simpanan', [TarikanSimpananController::class, 'store'])->name('transaksi-simpanan.tarikan-simpanan.store');
    Route::get('/transaksi-simpanan/tarikan-simpanan/{tarikanSimpanan}/edit', [TarikanSimpananController::class, 'edit'])->name('transaksi-simpanan.tarikan-simpanan.edit');
    Route::put('/transaksi-simpanan/tarikan-simpanan/{tarikanSimpanan}', [TarikanSimpananController::class, 'update'])->name('transaksi-simpanan.tarikan-simpanan.update');
    Route::delete('/transaksi-simpanan/tarikan-simpanan/{tarikanSimpanan}', [TarikanSimpananController::class, 'destroy'])->name('transaksi-simpanan.tarikan-simpanan.destroy');
    Route::get('/transaksi-simpanan/tarikan-simpanan/{tarikanSimpanan}', [TarikanSimpananController::class, 'show'])->name('transaksi-simpanan.tarikan-simpanan.show');


    // Pemindahbukuan Simpanan (Inertia + React)
    Route::get('/transaksi-simpanan/pemindahbukuan-simpanan', [PemindahbukuanSimpananController::class, 'index'])->name('transaksi-simpanan.pemindahbukuan-simpanan');
    Route::get('/transaksi-simpanan/pemindahbukuan-simpanan/create', [PemindahbukuanSimpananController::class, 'create'])->name('transaksi-simpanan.pemindahbukuan-simpanan.create');
    Route::post('/transaksi-simpanan/pemindahbukuan-simpanan', [PemindahbukuanSimpananController::class, 'store'])->name('transaksi-simpanan.pemindahbukuan-simpanan.store');
    Route::get('/transaksi-simpanan/pemindahbukuan-simpanan/{pemindahbukuanSimpanan}/edit', [PemindahbukuanSimpananController::class, 'edit'])->name('transaksi-simpanan.pemindahbukuan-simpanan.edit');
    Route::put('/transaksi-simpanan/pemindahbukuan-simpanan/{pemindahbukuanSimpanan}', [PemindahbukuanSimpananController::class, 'update'])->name('transaksi-simpanan.pemindahbukuan-simpanan.update');
    Route::delete('/transaksi-simpanan/pemindahbukuan-simpanan/{pemindahbukuanSimpanan}', [PemindahbukuanSimpananController::class, 'destroy'])->name('transaksi-simpanan.pemindahbukuan-simpanan.destroy');
    Route::get('/transaksi-simpanan/pemindahbukuan-simpanan/{pemindahbukuanSimpanan}', [PemindahbukuanSimpananController::class, 'show'])->name('transaksi-simpanan.pemindahbukuan-simpanan.show');

    // Penutupan Simpanan (Inertia + React)
    Route::get('/transaksi-simpanan/penutupan-simpanan', [PenutupanSimpananController::class, 'index'])->name('transaksi-simpanan.penutupan-simpanan');
    Route::get('/transaksi-simpanan/penutupan-simpanan/create', [PenutupanSimpananController::class, 'create'])->name('transaksi-simpanan.penutupan-simpanan.create');
    Route::post('/transaksi-simpanan/penutupan-simpanan', [PenutupanSimpananController::class, 'store'])->name('transaksi-simpanan.penutupan-simpanan.store');
    Route::get('/transaksi-simpanan/penutupan-simpanan/{penutupanSimpanan}/edit', [PenutupanSimpananController::class, 'edit'])->name('transaksi-simpanan.penutupan-simpanan.edit');
    Route::put('/transaksi-simpanan/penutupan-simpanan/{penutupanSimpanan}', [PenutupanSimpananController::class, 'update'])->name('transaksi-simpanan.penutupan-simpanan.update');
    Route::delete('/transaksi-simpanan/penutupan-simpanan/{penutupanSimpanan}', [PenutupanSimpananController::class, 'destroy'])->name('transaksi-simpanan.penutupan-simpanan.destroy');
    Route::get('/transaksi-simpanan/penutupan-simpanan/{penutupanSimpanan}', [PenutupanSimpananController::class, 'show'])->name('transaksi-simpanan.penutupan-simpanan.show');


});

// routes/web.php

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');


Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.attempt');
Route::get('/', fn () => redirect()->route('login'))->name('home');
