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
use App\Http\Controllers\Superadmin\ProposalController;
use App\Http\Controllers\Superadmin\JadwalUlangController;
use App\Http\Controllers\Superadmin\TagihanPinjamanController;
use App\Http\Controllers\Superadmin\SimpananProdukController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\Superadmin\AngsuranPinjamanController;
use App\Http\Controllers\Superadmin\PenaltiPinjamanController;
use App\Http\Controllers\Superadmin\AngsuranKolektifController;
use App\Http\Controllers\Superadmin\TransaksiSimpananBerjangkaController;
use App\Http\Controllers\Superadmin\PenarikanDanaTitipanController;
use App\Http\Controllers\Superadmin\LaporanController;
use App\Http\Controllers\Superadmin\LaporanCSController;
use App\Http\Controllers\Superadmin\LaporanPinjamanController;
use App\Http\Controllers\Superadmin\LaporanSimpananController;
use App\Http\Controllers\Superadmin\LaporanSimpananBerjangkaController;
use App\Http\Controllers\Superadmin\LaporanMarketingController;

// Route Livewire lama modul Anggota dihapus — sudah dimigrasikan ke Inertia.
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/pinjaman/pinjaman/{pinjaman}/edit', [PinjamanController::class, 'edit'])->name('pinjaman.pinjaman.edit');
    Route::put('/pinjaman/pinjaman/{pinjaman}', [PinjamanController::class, 'update'])->name('pinjaman.pinjaman.update');
    Route::delete('/pinjaman/pinjaman/{pinjaman}', [PinjamanController::class, 'destroy'])->name('pinjaman.pinjaman.destroy');
    Route::get('/pinjaman/proposal', [ProposalController::class, 'index'])->name('pinjaman.proposal');
    Route::get('/pinjaman/proposal/create', [ProposalController::class, 'create'])->name('pinjaman.proposal.create');
    Route::post('/pinjaman/proposal', [ProposalController::class, 'store'])->name('pinjaman.proposal.store');
    Route::get('/pinjaman/proposal/{proposal}/edit', [ProposalController::class, 'edit'])->name('pinjaman.proposal.edit');
    Route::put('/pinjaman/proposal/{proposal}', [ProposalController::class, 'update'])->name('pinjaman.proposal.update');
    Route::delete('/pinjaman/proposal/{proposal}', [ProposalController::class, 'destroy'])->name('pinjaman.proposal.destroy');
    Route::get('/pinjaman/jadwal-ulang', [JadwalUlangController::class, 'index'])->name('pinjaman.jadwal-ulang');
    Route::get('/pinjaman/jadwal-ulang/create', [JadwalUlangController::class, 'create'])->name('pinjaman.jadwal-ulang.create');
    Route::get('/pinjaman/jadwal-ulang/anggota/{anggota}', [JadwalUlangController::class, 'pinjamanByAnggota'])->name('pinjaman.jadwal-ulang.pinjaman-by-anggota');
    Route::get('/pinjaman/jadwal-ulang/pinjaman-asal/{pinjaman}', [JadwalUlangController::class, 'pinjamanAsal'])->name('pinjaman.jadwal-ulang.pinjaman-asal');
    Route::post('/pinjaman/jadwal-ulang', [JadwalUlangController::class, 'store'])->name('pinjaman.jadwal-ulang.store');
    Route::get('/pinjaman/jadwal-ulang/{jadwalUlang}', [JadwalUlangController::class, 'show'])->name('pinjaman.jadwal-ulang.show');
    Route::get('/pinjaman/jadwal-ulang/{jadwalUlang}/edit', [JadwalUlangController::class, 'edit'])->name('pinjaman.jadwal-ulang.edit');
    Route::put('/pinjaman/jadwal-ulang/{jadwalUlang}', [JadwalUlangController::class, 'update'])->name('pinjaman.jadwal-ulang.update');
    Route::delete('/pinjaman/jadwal-ulang/{jadwalUlang}', [JadwalUlangController::class, 'destroy'])->name('pinjaman.jadwal-ulang.destroy');

    // Tagihan Pinjaman (Inertia + React)
    Route::get('/pinjaman/tagihan', [TagihanPinjamanController::class, 'index'])->name('pinjaman.tagihan');

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

    // ==================== LAPORAN CS — ANGGOTA ====================
    Route::prefix('laporan-cs/anggota')->name('laporan-cs.anggota.')->group(function () {
        Route::get('/daftar-anggota', [LaporanCSController::class, 'daftarAnggota'])->name('daftar-anggota');
        Route::get('/daftar-anggota/cetak', [LaporanCSController::class, 'cetakDaftarAnggota'])->name('daftar-anggota.cetak');
        Route::get('/non-anggota', [LaporanCSController::class, 'daftarNonAnggota'])->name('non-anggota');
        Route::get('/non-anggota/cetak', [LaporanCSController::class, 'cetakDaftarNonAnggota'])->name('non-anggota.cetak');
        Route::get('/pengurus', [LaporanCSController::class, 'daftarPengurus'])->name('pengurus');
        Route::get('/pengurus/cetak', [LaporanCSController::class, 'cetakDaftarPengurus'])->name('pengurus.cetak');
        Route::get('/pengawas', [LaporanCSController::class, 'daftarPengawas'])->name('pengawas');
        Route::get('/pengawas/cetak', [LaporanCSController::class, 'cetakDaftarPengawas'])->name('pengawas.cetak');
        Route::get('/per-kelompok', [LaporanCSController::class, 'anggotaPerKelompok'])->name('per-kelompok');
        Route::get('/per-kelompok/cetak', [LaporanCSController::class, 'cetakAnggotaPerKelompok'])->name('per-kelompok.cetak');
        Route::get('/kartu', [LaporanCSController::class, 'kartuAnggota'])->name('kartu');
        Route::get('/kartu/{anggota}/cetak', [LaporanCSController::class, 'cetakKartuAnggota'])->name('kartu.cetak');
        Route::get('/laporan', [LaporanCSController::class, 'laporanAnggota'])->name('laporan');
        Route::get('/laporan/cetak', [LaporanCSController::class, 'cetakLaporanAnggota'])->name('laporan.cetak');
        Route::get('/penarikan', [LaporanCSController::class, 'penarikanAnggota'])->name('penarikan');
        Route::get('/penarikan/cetak', [LaporanCSController::class, 'cetakPenarikanAnggota'])->name('penarikan.cetak');
        Route::get('/sisa-penarikan', [LaporanCSController::class, 'sisaPenarikanDana'])->name('sisa-penarikan');
        Route::get('/sisa-penarikan/cetak', [LaporanCSController::class, 'cetakSisaPenarikanDana'])->name('sisa-penarikan.cetak');
        Route::get('/simpanan-pinjaman', [LaporanCSController::class, 'simpananPinjaman'])->name('simpanan-pinjaman');
        Route::get('/simpanan-pinjaman/cetak', [LaporanCSController::class, 'cetakSimpananPinjaman'])->name('simpanan-pinjaman.cetak');
        Route::get('/simpan-pinjam-detail', [LaporanCSController::class, 'simpanPinjamDetail'])->name('simpan-pinjam-detail');
        Route::get('/simpan-pinjam-detail/cetak', [LaporanCSController::class, 'cetakSimpanPinjamDetail'])->name('simpan-pinjam-detail.cetak');
        Route::get('/hutang-kewajiban', [LaporanCSController::class, 'hutangKewajiban'])->name('hutang-kewajiban');
        Route::get('/hutang-kewajiban/cetak', [LaporanCSController::class, 'cetakHutangKewajiban'])->name('hutang-kewajiban.cetak');
    });

    // ==================== LAPORAN CS — PINJAMAN ====================
    Route::prefix('laporan-cs/pinjaman')->name('laporan-cs.pinjaman.')->group(function () {
        Route::get('/daftar-pinjaman', [LaporanPinjamanController::class, 'daftarPinjaman'])->name('daftar-pinjaman');
        Route::get('/daftar-pinjaman/cetak', [LaporanPinjamanController::class, 'cetakDaftarPinjaman'])->name('daftar-pinjaman.cetak');
        Route::get('/daftar-nama-peminjam', [LaporanPinjamanController::class, 'daftarNamaPeminjam'])->name('daftar-nama-peminjam');
        Route::get('/daftar-nama-peminjam/cetak', [LaporanPinjamanController::class, 'cetakDaftarNamaPeminjam'])->name('daftar-nama-peminjam.cetak');
        Route::get('/kartu', [LaporanPinjamanController::class, 'kartuPinjaman'])->name('kartu');
        Route::get('/kartu/{anggota}/cetak', [LaporanPinjamanController::class, 'cetakKartuPinjaman'])->name('kartu.cetak');
        Route::get('/kartu-data', [LaporanPinjamanController::class, 'kartuPinjamanData'])->name('kartu-data');
        Route::get('/kartu-data/{anggota}/cetak', [LaporanPinjamanController::class, 'cetakKartuPinjamanData'])->name('kartu-data.cetak');
        Route::get('/angsuran', [LaporanPinjamanController::class, 'laporanAngsuranPinjaman'])->name('angsuran');
        Route::get('/angsuran/cetak', [LaporanPinjamanController::class, 'cetakLaporanAngsuranPinjaman'])->name('angsuran.cetak');
        Route::get('/angsuran-detail', [LaporanPinjamanController::class, 'laporanAngsuranPinjamanDetail'])->name('angsuran-detail');
        Route::get('/angsuran-detail/cetak', [LaporanPinjamanController::class, 'cetakLaporanAngsuranPinjamanDetail'])->name('angsuran-detail.cetak');
        Route::get('/kolektibilitas', [LaporanPinjamanController::class, 'laporanKolektibilitas'])->name('kolektibilitas');
        Route::get('/kolektibilitas/cetak', [LaporanPinjamanController::class, 'cetakLaporanKolektibilitas'])->name('kolektibilitas.cetak');
        Route::get('/mutasi', [LaporanPinjamanController::class, 'laporanMutasiPinjaman'])->name('mutasi');
        Route::get('/mutasi/cetak', [LaporanPinjamanController::class, 'cetakLaporanMutasiPinjaman'])->name('mutasi.cetak');
        Route::get('/nominatif-sisa', [LaporanPinjamanController::class, 'laporanNominatifSisa'])->name('nominatif-sisa');
        Route::get('/nominatif-sisa/cetak', [LaporanPinjamanController::class, 'cetakLaporanNominatifSisa'])->name('nominatif-sisa.cetak');
        Route::get('/nominatif-angsuran', [LaporanPinjamanController::class, 'laporanNominatifAngsuran'])->name('nominatif-angsuran');
        Route::get('/nominatif-angsuran/cetak', [LaporanPinjamanController::class, 'cetakLaporanNominatifAngsuran'])->name('nominatif-angsuran.cetak');
        Route::get('/nominatif-jaminan', [LaporanPinjamanController::class, 'laporanNominatifJaminan'])->name('nominatif-jaminan');
        Route::get('/nominatif-jaminan/cetak', [LaporanPinjamanController::class, 'cetakLaporanNominatifJaminan'])->name('nominatif-jaminan.cetak');
        Route::get('/nominatif-denda', [LaporanPinjamanController::class, 'laporanNominatifDenda'])->name('nominatif-denda');
        Route::get('/nominatif-denda/cetak', [LaporanPinjamanController::class, 'cetakLaporanNominatifDenda'])->name('nominatif-denda.cetak');
        Route::get('/custom', [LaporanPinjamanController::class, 'laporanPinjamanCustom'])->name('custom');
        Route::get('/custom/cetak', [LaporanPinjamanController::class, 'cetakLaporanPinjamanCustom'])->name('custom.cetak');
        Route::get('/jatuh-tempo', [LaporanPinjamanController::class, 'laporanPinjamanJatuhTempo'])->name('jatuh-tempo');
        Route::get('/jatuh-tempo/cetak', [LaporanPinjamanController::class, 'cetakLaporanPinjamanJatuhTempo'])->name('jatuh-tempo.cetak');
        Route::get('/lunas', [LaporanPinjamanController::class, 'laporanPinjamanLunas'])->name('lunas');
        Route::get('/lunas/cetak', [LaporanPinjamanController::class, 'cetakLaporanPinjamanLunas'])->name('lunas.cetak');
        Route::get('/bagi-hasil', [LaporanPinjamanController::class, 'laporanPendapatanBagiHasil'])->name('bagi-hasil');
        Route::get('/bagi-hasil/cetak', [LaporanPinjamanController::class, 'cetakLaporanPendapatanBagiHasil'])->name('bagi-hasil.cetak');
        Route::get('/pengembalian-jaminan', [LaporanPinjamanController::class, 'laporanPengembalianJaminan'])->name('pengembalian-jaminan');
        Route::get('/pengembalian-jaminan/cetak', [LaporanPinjamanController::class, 'cetakLaporanPengembalianJaminan'])->name('pengembalian-jaminan.cetak');
        Route::get('/proposal', [LaporanPinjamanController::class, 'proposalPinjaman'])->name('proposal');
        Route::get('/proposal/cetak', [LaporanPinjamanController::class, 'cetakProposalPinjaman'])->name('proposal.cetak');
        Route::get('/proposal-penalti', [LaporanPinjamanController::class, 'proposalPenaltiPinjaman'])->name('proposal-penalti');
        Route::get('/proposal-penalti/cetak', [LaporanPinjamanController::class, 'cetakProposalPenaltiPinjaman'])->name('proposal-penalti.cetak');
        Route::get('/proposal-mobile', [LaporanPinjamanController::class, 'laporanProposalPinjamanMobile'])->name('proposal-mobile');
        Route::get('/proposal-mobile/cetak', [LaporanPinjamanController::class, 'cetakLaporanProposalPinjamanMobile'])->name('proposal-mobile.cetak');
        Route::get('/tabel-angsuran', [LaporanPinjamanController::class, 'tabelAngsuranPinjaman'])->name('tabel-angsuran');
        Route::get('/tabel-angsuran/cetak', [LaporanPinjamanController::class, 'cetakTabelAngsuranPinjaman'])->name('tabel-angsuran.cetak');
        Route::get('/tabel-angsuran-kosong', [LaporanPinjamanController::class, 'tabelAngsuranPinjamanKosong'])->name('tabel-angsuran-kosong');
        Route::get('/tabel-angsuran-kosong/cetak', [LaporanPinjamanController::class, 'cetakTabelAngsuranPinjamanKosong'])->name('tabel-angsuran-kosong.cetak');
        Route::get('/transaksi', [LaporanPinjamanController::class, 'transaksiPinjaman'])->name('transaksi');
        Route::get('/transaksi/cetak', [LaporanPinjamanController::class, 'cetakTransaksiPinjaman'])->name('transaksi.cetak');
        Route::get('/tunggakan', [LaporanPinjamanController::class, 'laporanTunggakanPinjaman'])->name('tunggakan');
        Route::get('/tunggakan/cetak', [LaporanPinjamanController::class, 'cetakLaporanTunggakanPinjaman'])->name('tunggakan.cetak');
        Route::get('/tunggakan-per-kota', [LaporanPinjamanController::class, 'laporanTunggakanPinjamanPerKota'])->name('tunggakan-per-kota');
        Route::get('/tunggakan-per-kota/cetak', [LaporanPinjamanController::class, 'cetakLaporanTunggakanPinjamanPerKota'])->name('tunggakan-per-kota.cetak');
        Route::get('/pencairan', [LaporanPinjamanController::class, 'laporanPencairanPinjaman'])->name('pencairan');
        Route::get('/pencairan/cetak', [LaporanPinjamanController::class, 'cetakLaporanPencairanPinjaman'])->name('pencairan.cetak');
        Route::get('/harian', [LaporanPinjamanController::class, 'laporanTransaksiHarianPinjaman'])->name('harian');
        Route::get('/harian/cetak', [LaporanPinjamanController::class, 'cetakLaporanTransaksiHarianPinjaman'])->name('harian.cetak');
        Route::get('/kontrol-angsuran', [LaporanPinjamanController::class, 'laporanKontrolAngsuran'])->name('kontrol-angsuran');
        Route::get('/kontrol-angsuran/cetak', [LaporanPinjamanController::class, 'cetakLaporanKontrolAngsuran'])->name('kontrol-angsuran.cetak');
        Route::get('/rekapitulasi', [LaporanPinjamanController::class, 'rekapitulasiPinjaman'])->name('rekapitulasi');
        Route::get('/rekapitulasi/cetak', [LaporanPinjamanController::class, 'cetakRekapitulasiPinjaman'])->name('rekapitulasi.cetak');
        Route::get('/rekapitulasi-sektor', [LaporanPinjamanController::class, 'rekapitulasiPinjamanSektor'])->name('rekapitulasi-sektor');
        Route::get('/rekapitulasi-sektor/cetak', [LaporanPinjamanController::class, 'cetakRekapitulasiPinjamanSektor'])->name('rekapitulasi-sektor.cetak');
        Route::get('/rekap-bagi-hasil', [LaporanPinjamanController::class, 'rekapitulasiPendapatanBagiHasil'])->name('rekap-bagi-hasil');
        Route::get('/rekap-bagi-hasil/cetak', [LaporanPinjamanController::class, 'cetakRekapitulasiPendapatanBagiHasil'])->name('rekap-bagi-hasil.cetak');
        Route::get('/rekap-produk', [LaporanPinjamanController::class, 'laporanRekapNominatifProduk'])->name('rekap-produk');
        Route::get('/rekap-produk/cetak', [LaporanPinjamanController::class, 'cetakLaporanRekapNominatifProduk'])->name('rekap-produk.cetak');
        Route::get('/rekap-marketing', [LaporanPinjamanController::class, 'laporanRekapNominatifMarketing'])->name('rekap-marketing');
        Route::get('/rekap-marketing/cetak', [LaporanPinjamanController::class, 'cetakLaporanRekapNominatifMarketing'])->name('rekap-marketing.cetak');
        Route::get('/pasal-perjanjian', [LaporanPinjamanController::class, 'pasalSuratPerjanjianPinjaman'])->name('pasal-perjanjian');
        Route::get('/pasal-perjanjian/{pinjaman}/cetak', [LaporanPinjamanController::class, 'cetakPasalSuratPerjanjianPinjaman'])->name('pasal-perjanjian.cetak');
        Route::get('/surat-perjanjian', [LaporanPinjamanController::class, 'suratPerjanjianPinjaman'])->name('surat-perjanjian');
        Route::get('/surat-perjanjian/{pinjaman}/cetak', [LaporanPinjamanController::class, 'cetakSuratPerjanjianPinjaman'])->name('surat-perjanjian.cetak');
        Route::get('/surat-kuasa', [LaporanPinjamanController::class, 'suratKuasa'])->name('surat-kuasa');
        Route::get('/surat-kuasa/{pinjaman}/cetak', [LaporanPinjamanController::class, 'cetakSuratKuasa'])->name('surat-kuasa.cetak');
        Route::get('/surat-pernyataan', [LaporanPinjamanController::class, 'suratPernyataan'])->name('surat-pernyataan');
        Route::get('/surat-pernyataan/{pinjaman}/cetak', [LaporanPinjamanController::class, 'cetakSuratPernyataan'])->name('surat-pernyataan.cetak');
        Route::get('/tanda-terima-jaminan', [LaporanPinjamanController::class, 'tandaTerimaJaminan'])->name('tanda-terima-jaminan');
        Route::get('/tanda-terima-jaminan/{pinjaman}/cetak', [LaporanPinjamanController::class, 'cetakTandaTerimaJaminan'])->name('tanda-terima-jaminan.cetak');
        Route::get('/simulasi-tagihan', [LaporanPinjamanController::class, 'simulasiTagihanPinjaman'])->name('simulasi-tagihan');
        Route::get('/simulasi-tagihan/cetak', [LaporanPinjamanController::class, 'cetakSimulasiTagihanPinjaman'])->name('simulasi-tagihan.cetak');
        Route::get('/angsuran-per-anggota', [LaporanPinjamanController::class, 'laporanAngsuranPerAnggota'])->name('angsuran-per-anggota');
        Route::get('/angsuran-per-anggota/cetak', [LaporanPinjamanController::class, 'cetakLaporanAngsuranPerAnggota'])->name('angsuran-per-anggota.cetak');
        Route::get('/rekapan-pemasukan', [LaporanPinjamanController::class, 'laporanRekapanPemasukanDetail'])->name('rekapan-pemasukan');
        Route::get('/rekapan-pemasukan/cetak', [LaporanPinjamanController::class, 'cetakLaporanRekapanPemasukanDetail'])->name('rekapan-pemasukan.cetak');
        Route::get('/jatuh-tempo-angsuran', [LaporanPinjamanController::class, 'laporanJatuhTempoAngsuran'])->name('jatuh-tempo-angsuran');
        Route::get('/jatuh-tempo-angsuran/cetak', [LaporanPinjamanController::class, 'cetakLaporanJatuhTempoAngsuran'])->name('jatuh-tempo-angsuran.cetak');
        Route::get('/penilaian-terlambat', [LaporanPinjamanController::class, 'laporanPenilaianAnggotaTerlambat'])->name('penilaian-terlambat');
        Route::get('/penilaian-terlambat/cetak', [LaporanPinjamanController::class, 'cetakLaporanPenilaianAnggotaTerlambat'])->name('penilaian-terlambat.cetak');
    });

    // ==================== LAPORAN CS — SIMPANAN ====================
    Route::prefix('laporan-cs/simpanan')->name('laporan-cs.simpanan.')->group(function () {
        Route::get('/kartu-depan', [LaporanSimpananController::class, 'kartuSimpananDepan'])->name('kartu-depan');
        Route::get('/kartu-depan/cetak', [LaporanSimpananController::class, 'cetakKartuSimpananDepan'])->name('kartu-depan.cetak');
        Route::get('/kartu-belakang', [LaporanSimpananController::class, 'kartuSimpananBelakang'])->name('kartu-belakang');
        Route::get('/kartu-belakang/cetak', [LaporanSimpananController::class, 'cetakKartuSimpananBelakang'])->name('kartu-belakang.cetak');
        Route::get('/kartu-belakang-data', [LaporanSimpananController::class, 'kartuSimpananBelakangData'])->name('kartu-belakang-data');
        Route::get('/kartu-belakang-data/cetak', [LaporanSimpananController::class, 'cetakKartuSimpananBelakangData'])->name('kartu-belakang-data.cetak');
        Route::get('/pasal-kartu', [LaporanSimpananController::class, 'pasalKartuSimpanan'])->name('pasal-kartu');
        Route::get('/pasal-kartu/cetak', [LaporanSimpananController::class, 'cetakPasalKartuSimpanan'])->name('pasal-kartu.cetak');
        Route::get('/rekening-koran', [LaporanSimpananController::class, 'rekeningKoran'])->name('rekening-koran');
        Route::get('/rekening-koran/cetak', [LaporanSimpananController::class, 'cetakRekeningKoran'])->name('rekening-koran.cetak');
        Route::get('/rekening-koran-kolektif', [LaporanSimpananController::class, 'rekeningKoranKolektif'])->name('rekening-koran-kolektif');
        Route::get('/rekening-koran-kolektif/cetak', [LaporanSimpananController::class, 'cetakRekeningKoranKolektif'])->name('rekening-koran-kolektif.cetak');
        Route::get('/transaksi-simpanan', [LaporanSimpananController::class, 'transaksiSimpanan'])->name('transaksi-simpanan');
        Route::get('/transaksi-simpanan/cetak', [LaporanSimpananController::class, 'cetakTransaksiSimpanan'])->name('transaksi-simpanan.cetak');
        Route::get('/daftar-simpanan', [LaporanSimpananController::class, 'daftarSimpanan'])->name('daftar-simpanan');
        Route::get('/daftar-simpanan/cetak', [LaporanSimpananController::class, 'cetakDaftarSimpanan'])->name('daftar-simpanan.cetak');
        Route::get('/mutasi-simpanan', [LaporanSimpananController::class, 'mutasiSimpanan'])->name('mutasi-simpanan');
        Route::get('/mutasi-simpanan/cetak', [LaporanSimpananController::class, 'cetakMutasiSimpanan'])->name('mutasi-simpanan.cetak');
        Route::get('/mutasi-harian-simpanan', [LaporanSimpananController::class, 'mutasiHarianSimpanan'])->name('mutasi-harian-simpanan');
        Route::get('/mutasi-harian-simpanan/cetak', [LaporanSimpananController::class, 'cetakMutasiHarianSimpanan'])->name('mutasi-harian-simpanan.cetak');
        Route::get('/bagi-hasil-simpanan', [LaporanSimpananController::class, 'bagiHasilSimpanan'])->name('bagi-hasil-simpanan');
        Route::get('/bagi-hasil-simpanan/cetak', [LaporanSimpananController::class, 'cetakBagiHasilSimpanan'])->name('bagi-hasil-simpanan.cetak');
        Route::get('/bagi-hasil-simpanan-2', [LaporanSimpananController::class, 'bagiHasilSimpanan2'])->name('bagi-hasil-simpanan-2');
        Route::get('/bagi-hasil-simpanan-2/cetak', [LaporanSimpananController::class, 'cetakBagiHasilSimpanan2'])->name('bagi-hasil-simpanan-2.cetak');
        Route::get('/nominatif-simpanan', [LaporanSimpananController::class, 'nominatifSimpanan'])->name('nominatif-simpanan');
        Route::get('/nominatif-simpanan/cetak', [LaporanSimpananController::class, 'cetakNominatifSimpanan'])->name('nominatif-simpanan.cetak');
        Route::get('/nominatif-simpanan-detail', [LaporanSimpananController::class, 'nominatifSimpananDetail'])->name('nominatif-simpanan-detail');
        Route::get('/nominatif-simpanan-detail/cetak', [LaporanSimpananController::class, 'cetakNominatifSimpananDetail'])->name('nominatif-simpanan-detail.cetak');
        Route::get('/saldo-simpanan', [LaporanSimpananController::class, 'saldoSimpanan'])->name('saldo-simpanan');
        Route::get('/saldo-simpanan/cetak', [LaporanSimpananController::class, 'cetakSaldoSimpanan'])->name('saldo-simpanan.cetak');
        Route::get('/simpanan-baru', [LaporanSimpananController::class, 'simpananBaru'])->name('simpanan-baru');
        Route::get('/simpanan-baru/cetak', [LaporanSimpananController::class, 'cetakSimpananBaru'])->name('simpanan-baru.cetak');
        Route::get('/penutupan-simpanan', [LaporanSimpananController::class, 'penutupanSimpanan'])->name('penutupan-simpanan');
        Route::get('/penutupan-simpanan/cetak', [LaporanSimpananController::class, 'cetakPenutupanSimpanan'])->name('penutupan-simpanan.cetak');
        Route::get('/tunggakan-setoran-wajib', [LaporanSimpananController::class, 'tunggakanSetoranWajib'])->name('tunggakan-setoran-wajib');
        Route::get('/tunggakan-setoran-wajib/cetak', [LaporanSimpananController::class, 'cetakTunggakanSetoranWajib'])->name('tunggakan-setoran-wajib.cetak');
        Route::get('/tidak-aktif', [LaporanSimpananController::class, 'simpananTidakAktif'])->name('tidak-aktif');
        Route::get('/tidak-aktif/cetak', [LaporanSimpananController::class, 'cetakSimpananTidakAktif'])->name('tidak-aktif.cetak');
        Route::get('/jatuh-tempo-simpanan', [LaporanSimpananController::class, 'simpananJatuhTempo'])->name('jatuh-tempo-simpanan');
        Route::get('/jatuh-tempo-simpanan/cetak', [LaporanSimpananController::class, 'cetakSimpananJatuhTempo'])->name('jatuh-tempo-simpanan.cetak');
        Route::get('/rekapitulasi-produk', [LaporanSimpananController::class, 'rekapitulasiProdukSimpanan'])->name('rekapitulasi-produk');
        Route::get('/rekapitulasi-produk/cetak', [LaporanSimpananController::class, 'cetakRekapitulasiProdukSimpanan'])->name('rekapitulasi-produk.cetak');
        Route::get('/rekapitulasi-grafik', [LaporanSimpananController::class, 'rekapitulasiSimpananGrafik'])->name('rekapitulasi-grafik');
        Route::get('/rekapitulasi-grafik/cetak', [LaporanSimpananController::class, 'cetakRekapitulasiSimpananGrafik'])->name('rekapitulasi-grafik.cetak');
        Route::get('/rekapitulasi-bagi-hasil', [LaporanSimpananController::class, 'rekapitulasiBagiHasilSimpanan'])->name('rekapitulasi-bagi-hasil');
        Route::get('/rekapitulasi-bagi-hasil/cetak', [LaporanSimpananController::class, 'cetakRekapitulasiBagiHasilSimpanan'])->name('rekapitulasi-bagi-hasil.cetak');
        Route::get('/buku-tabungan/cetak', [LaporanSimpananController::class, 'cetakBukuTabungan'])->name('buku-tabungan.cetak');
        Route::get('/buku-tabungan/data', [LaporanSimpananController::class, 'bukuTabunganData'])->name('buku-tabungan.data');
    });

    Route::prefix('laporan-cs/simpanan-berjangka')->name('laporan-cs.simpanan-berjangka.')->group(function () {
        Route::get('/daftar-berjangka', [LaporanSimpananBerjangkaController::class, 'daftarBerjangka'])->name('daftar-berjangka');
        Route::get('/daftar-berjangka/cetak', [LaporanSimpananBerjangkaController::class, 'cetakDaftarBerjangka'])->name('daftar-berjangka.cetak');
        Route::get('/bilyet-berjangka', [LaporanSimpananBerjangkaController::class, 'bilyetBerjangka'])->name('bilyet-berjangka');
        Route::get('/bilyet-berjangka/cetak', [LaporanSimpananBerjangkaController::class, 'cetakBilyetBerjangka'])->name('bilyet-berjangka.cetak');
        Route::get('/kartu-berjangka', [LaporanSimpananBerjangkaController::class, 'kartuBerjangka'])->name('kartu-berjangka');
        Route::get('/kartu-berjangka/cetak', [LaporanSimpananBerjangkaController::class, 'cetakKartuBerjangka'])->name('kartu-berjangka.cetak');
        Route::get('/konfirmasi-perubahan-bagi-hasil', [LaporanSimpananBerjangkaController::class, 'konfirmasiPerubahanBagiHasil'])->name('konfirmasi-perubahan-bagi-hasil');
        Route::get('/konfirmasi-perubahan-bagi-hasil/cetak', [LaporanSimpananBerjangkaController::class, 'cetakKonfirmasiPerubahanBagiHasil'])->name('konfirmasi-perubahan-bagi-hasil.cetak');
        Route::get('/simpanan-berjangka-baru', [LaporanSimpananBerjangkaController::class, 'simpananBerjangkaBaru'])->name('simpanan-berjangka-baru');
        Route::get('/simpanan-berjangka-baru/cetak', [LaporanSimpananBerjangkaController::class, 'cetakSimpananBerjangkaBaru'])->name('simpanan-berjangka-baru.cetak');
        Route::get('/pencairan-berjangka', [LaporanSimpananBerjangkaController::class, 'pencairanBerjangka'])->name('pencairan-berjangka');
        Route::get('/pencairan-berjangka/cetak', [LaporanSimpananBerjangkaController::class, 'cetakPencairanBerjangka'])->name('pencairan-berjangka.cetak');
        Route::get('/bagi-hasil-berjangka', [LaporanSimpananBerjangkaController::class, 'bagiHasilBerjangka'])->name('bagi-hasil-berjangka');
        Route::get('/bagi-hasil-berjangka/cetak', [LaporanSimpananBerjangkaController::class, 'cetakBagiHasilBerjangka'])->name('bagi-hasil-berjangka.cetak');
        Route::get('/bagi-hasil-berjangka-2', [LaporanSimpananBerjangkaController::class, 'bagiHasilBerjangka2'])->name('bagi-hasil-berjangka-2');
        Route::get('/bagi-hasil-berjangka-2/cetak', [LaporanSimpananBerjangkaController::class, 'cetakBagiHasilBerjangka2'])->name('bagi-hasil-berjangka-2.cetak');
        Route::get('/posting-bagi-hasil', [LaporanSimpananBerjangkaController::class, 'postingBagiHasil'])->name('posting-bagi-hasil');
        Route::get('/posting-bagi-hasil/cetak', [LaporanSimpananBerjangkaController::class, 'cetakPostingBagiHasil'])->name('posting-bagi-hasil.cetak');
        Route::get('/nominatif-berjangka', [LaporanSimpananBerjangkaController::class, 'nominatifBerjangka'])->name('nominatif-berjangka');
        Route::get('/nominatif-berjangka/cetak', [LaporanSimpananBerjangkaController::class, 'cetakNominatifBerjangka'])->name('nominatif-berjangka.cetak');
        Route::get('/jatuh-tempo-berjangka', [LaporanSimpananBerjangkaController::class, 'jatuhTempoBerjangka'])->name('jatuh-tempo-berjangka');
        Route::get('/jatuh-tempo-berjangka/cetak', [LaporanSimpananBerjangkaController::class, 'cetakJatuhTempoBerjangka'])->name('jatuh-tempo-berjangka.cetak');
        Route::get('/rekapitulasi-bagi-hasil', [LaporanSimpananBerjangkaController::class, 'rekapitulasiBagiHasil'])->name('rekapitulasi-bagi-hasil');
        Route::get('/rekapitulasi-bagi-hasil/cetak', [LaporanSimpananBerjangkaController::class, 'cetakRekapitulasiBagiHasil'])->name('rekapitulasi-bagi-hasil.cetak');
    });

    Route::prefix('laporan-cs/marketing')->name('laporan-cs.marketing.')->group(function () {
        Route::get('/daftar-marketing', [LaporanMarketingController::class, 'daftarMarketing'])->name('daftar-marketing');
        Route::get('/daftar-marketing/cetak', [LaporanMarketingController::class, 'cetakDaftarMarketing'])->name('daftar-marketing.cetak');
        Route::get('/angsuran-pinjaman-marketing', [LaporanMarketingController::class, 'angsuranPinjamanMarketing'])->name('angsuran-pinjaman-marketing');
        Route::get('/angsuran-pinjaman-marketing/cetak', [LaporanMarketingController::class, 'cetakAngsuranPinjamanMarketing'])->name('angsuran-pinjaman-marketing.cetak');
        Route::get('/angsuran-pinjaman-marketing-detail', [LaporanMarketingController::class, 'angsuranPinjamanMarketingDetail'])->name('angsuran-pinjaman-marketing-detail');
        Route::get('/angsuran-pinjaman-marketing-detail/cetak', [LaporanMarketingController::class, 'cetakAngsuranPinjamanMarketingDetail'])->name('angsuran-pinjaman-marketing-detail.cetak');
        Route::get('/insentif-marketing', [LaporanMarketingController::class, 'insentifMarketing'])->name('insentif-marketing');
        Route::get('/insentif-marketing/cetak', [LaporanMarketingController::class, 'cetakInsentifMarketing'])->name('insentif-marketing.cetak');
        Route::get('/insentif-marketing-angsuran-pinjaman', [LaporanMarketingController::class, 'insentifMarketingAngsuranPinjaman'])->name('insentif-marketing-angsuran-pinjaman');
        Route::get('/insentif-marketing-angsuran-pinjaman/cetak', [LaporanMarketingController::class, 'cetakInsentifMarketingAngsuranPinjaman'])->name('insentif-marketing-angsuran-pinjaman.cetak');
        Route::get('/pinjaman-marketing', [LaporanMarketingController::class, 'pinjamanMarketing'])->name('pinjaman-marketing');
        Route::get('/pinjaman-marketing/cetak', [LaporanMarketingController::class, 'cetakPinjamanMarketing'])->name('pinjaman-marketing.cetak');
        Route::get('/tagihan-marketing', [LaporanMarketingController::class, 'tagihanMarketing'])->name('tagihan-marketing');
        Route::get('/tagihan-marketing/cetak', [LaporanMarketingController::class, 'cetakTagihanMarketing'])->name('tagihan-marketing.cetak');
        Route::get('/tagihan-marketing-detail', [LaporanMarketingController::class, 'tagihanMarketingDetail'])->name('tagihan-marketing-detail');
        Route::get('/tagihan-marketing-detail/cetak', [LaporanMarketingController::class, 'cetakTagihanMarketingDetail'])->name('tagihan-marketing-detail.cetak');
        Route::get('/tagihan-marketing-status', [LaporanMarketingController::class, 'tagihanMarketingStatus'])->name('tagihan-marketing-status');
        Route::get('/tagihan-marketing-status/cetak', [LaporanMarketingController::class, 'cetakTagihanMarketingStatus'])->name('tagihan-marketing-status.cetak');
        Route::get('/simpanan-marketing', [LaporanMarketingController::class, 'simpananMarketing'])->name('simpanan-marketing');
        Route::get('/simpanan-marketing/cetak', [LaporanMarketingController::class, 'cetakSimpananMarketing'])->name('simpanan-marketing.cetak');
        Route::get('/transaksi-simpanan-marketing', [LaporanMarketingController::class, 'transaksiSimpananMarketing'])->name('transaksi-simpanan-marketing');
        Route::get('/transaksi-simpanan-marketing/cetak', [LaporanMarketingController::class, 'cetakTransaksiSimpananMarketing'])->name('transaksi-simpanan-marketing.cetak');
        Route::get('/transaksi-simpanan-marketing-detail', [LaporanMarketingController::class, 'transaksiSimpananMarketingDetail'])->name('transaksi-simpanan-marketing-detail');
        Route::get('/transaksi-simpanan-marketing-detail/cetak', [LaporanMarketingController::class, 'cetakTransaksiSimpananMarketingDetail'])->name('transaksi-simpanan-marketing-detail.cetak');
        Route::get('/npl-marketing', [LaporanMarketingController::class, 'nplMarketing'])->name('npl-marketing');
        Route::get('/npl-marketing/cetak', [LaporanMarketingController::class, 'cetakNplMarketing'])->name('npl-marketing.cetak');
        Route::get('/pencapaian-angsuran-harian', [LaporanMarketingController::class, 'pencapaianAngsuranHarian'])->name('pencapaian-angsuran-harian');
        Route::get('/pencapaian-angsuran-harian/cetak', [LaporanMarketingController::class, 'cetakPencapaianAngsuranHarian'])->name('pencapaian-angsuran-harian.cetak');
        Route::get('/pencapaian-angsuran-mingguan', [LaporanMarketingController::class, 'pencapaianAngsuranMingguan'])->name('pencapaian-angsuran-mingguan');
        Route::get('/pencapaian-angsuran-mingguan/cetak', [LaporanMarketingController::class, 'cetakPencapaianAngsuranMingguan'])->name('pencapaian-angsuran-mingguan.cetak');
        Route::get('/pencapaian-angsuran-bulanan', [LaporanMarketingController::class, 'pencapaianAngsuranBulanan'])->name('pencapaian-angsuran-bulanan');
        Route::get('/pencapaian-angsuran-bulanan/cetak', [LaporanMarketingController::class, 'cetakPencapaianAngsuranBulanan'])->name('pencapaian-angsuran-bulanan.cetak');
        Route::get('/rekapitulasi-pinjaman-marketing', [LaporanMarketingController::class, 'rekapitulasiPinjamanMarketing'])->name('rekapitulasi-pinjaman-marketing');
        Route::get('/rekapitulasi-pinjaman-marketing/cetak', [LaporanMarketingController::class, 'cetakRekapitulasiPinjamanMarketing'])->name('rekapitulasi-pinjaman-marketing.cetak');
    });

});

// routes/web.php

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');


Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.attempt');
Route::get('/', fn () => redirect()->route('login'))->name('home');
