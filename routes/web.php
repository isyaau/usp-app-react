<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Superadmin\AccGroupController;
use App\Http\Controllers\Superadmin\AccHeaderController;
use App\Http\Controllers\Superadmin\AccountController;
use App\Http\Controllers\Superadmin\JaminanController;
use App\Http\Controllers\Superadmin\KodetransaksiController;
use App\Http\Controllers\Superadmin\KantorController;
use App\Http\Controllers\Superadmin\KelompokController;
use App\Http\Controllers\Superadmin\MarketingController;
use App\Http\Controllers\Superadmin\PinjamanProdukController;
use App\Http\Controllers\Superadmin\SimpananProdukController;
use App\Http\Controllers\Superadmin\UserController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

// USER
use App\Livewire\Superadmin\User\Index as UserIndex;
use App\Livewire\Superadmin\User\Create as UserCreate;
use App\Livewire\Superadmin\User\Edit as UserEdit;
use App\Livewire\Superadmin\User\Show as UserShow;

// KELOMPOK
use App\Livewire\Superadmin\Kelompok\Index as KelompokIndex;
use App\Livewire\Superadmin\Kelompok\Create as KelompokCreate;
use App\Livewire\Superadmin\Kelompok\Edit as KelompokEdit;
use App\Livewire\Superadmin\Kelompok\Show as KelompokShow;

// KANTOR
use App\Livewire\Superadmin\Kantor\Index as KantorIndex;
use App\Livewire\Superadmin\Kantor\Create as KantorCreate;
use App\Livewire\Superadmin\Kantor\Edit as KantorEdit;
use App\Livewire\Superadmin\Kantor\Show as KantorShow;

// ANGGOTA
use App\Livewire\Superadmin\Anggota\Index as AnggotaIndex;
use App\Livewire\Superadmin\Anggota\Create as AnggotaCreate;
use App\Livewire\Superadmin\Anggota\Edit as AnggotaEdit;
use App\Livewire\Superadmin\Anggota\Show as AnggotaShow;


// ACCOUNT HEADER
use App\Livewire\Superadmin\Accheader\Index as AccheaderIndex;
use App\Livewire\Superadmin\Accheader\Create as AccheaderCreate;
use App\Livewire\Superadmin\Accheader\Edit as AccheaderEdit;
use App\Livewire\Superadmin\Accheader\Show as AccheaderShow;

// ACCOUNT
use App\Livewire\Superadmin\Account\Index as AccountIndex;
use App\Livewire\Superadmin\Account\Create as AccountCreate;
use App\Livewire\Superadmin\Account\Edit as AccountEdit;
use App\Livewire\Superadmin\Account\Show as AccountShow;

// PINJAMAN PRODUK
use App\Livewire\Superadmin\Pinjamanproduk\Index as PinjamanprodukIndex;
use App\Livewire\Superadmin\Pinjamanproduk\Create as PinjamanprodukCreate;
use App\Livewire\Superadmin\Pinjamanproduk\Edit as PinjamanprodukEdit;
use App\Livewire\Superadmin\Pinjamanproduk\Show as PinjamanprodukShow;

// JAMINAN
use App\Livewire\Superadmin\Jaminan\Index as JaminanIndex;
use App\Livewire\Superadmin\Jaminan\Create as JaminanCreate;
use App\Livewire\Superadmin\Jaminan\Edit as JaminanEdit;
use App\Livewire\Superadmin\Jaminan\Show as JaminanShow;

// PINJAMAN
use App\Livewire\Superadmin\Pinjaman\Index as PinjamanIndex;
use App\Livewire\Superadmin\Pinjaman\Create as PinjamanCreate;
use App\Livewire\Superadmin\Pinjaman\Edit as PinjamanEdit;
use App\Livewire\Superadmin\Pinjaman\Show as PinjamanShow;

// PINJAMAN PRODUK
use App\Livewire\Superadmin\Pinjamanproposal\Index as PinjamanproposalIndex;
use App\Livewire\Superadmin\Pinjamanproposal\Create as PinjamanproposalCreate;
use App\Livewire\Superadmin\Pinjamanproposal\Edit as PinjamanproposalEdit;
use App\Livewire\Superadmin\Pinjamanproposal\Show as PinjamanproposalShow;

// PINJAMAN JADWAL ULANG
use App\Livewire\Superadmin\Pinjamanjadwalulang\Index as PinjamanjadwalulangIndex;
use App\Livewire\Superadmin\Pinjamanjadwalulang\Create as PinjamanjadwalulangCreate;
use App\Livewire\Superadmin\Pinjamanjadwalulang\Edit as PinjamanjadwalulangEdit;
use App\Livewire\Superadmin\Pinjamanjadwalulang\Show as PinjamanjadwalulangShow;

// PINJAMAN TAGIHAN
use App\Livewire\Superadmin\Pinjamantagihan\Index as PinjamantagihanIndex;
use App\Livewire\Superadmin\Pinjamantagihan\Create as PinjamantagihanCreate;
use App\Livewire\Superadmin\Pinjamantagihan\Edit as PinjamantagihanEdit;
use App\Livewire\Superadmin\Pinjamantagihan\Show as PinjamantagihanShow;

// PINJAMAN PENGHAPUSAN
use App\Livewire\Superadmin\Pinjamanpenghapusan\Index as PinjamanpenghapusanIndex;
use App\Livewire\Superadmin\Pinjamanpenghapusan\Create as PinjamanpenghapusanCreate;
use App\Livewire\Superadmin\Pinjamanpenghapusan\Edit as PinjamanpenghapusanEdit;
use App\Livewire\Superadmin\Pinjamanpenghapusan\Show as PinjamanpenghapusanShow;

// PINJAMAN SURAT PERINGATAN
use App\Livewire\Superadmin\Pinjamansuratperingatan\Index as PinjamansuratperingatanIndex;
use App\Livewire\Superadmin\Pinjamansuratperingatan\Create as PinjamansuratperingatanCreate;
use App\Livewire\Superadmin\Pinjamansuratperingatan\Edit as PinjamansuratperingatanEdit;
use App\Livewire\Superadmin\Pinjamansuratperingatan\Show as PinjamansuratperingatanShow;

// PINJAMAN PENGEMBALIAN JAMINAN
use App\Livewire\Superadmin\Pinjamanpengembalianjaminan\Index as PinjamanpengembalianjaminanIndex;
use App\Livewire\Superadmin\Pinjamanpengembalianjaminan\Create as PinjamanpengembalianjaminanCreate;
use App\Livewire\Superadmin\Pinjamanpengembalianjaminan\Edit as PinjamanpengembalianjaminanEdit;
use App\Livewire\Superadmin\Pinjamanpengembalianjaminan\Show as PinjamanpengembalianjaminanShow;

// SIMPANAN
// KODE TRANSAKSI
use App\Livewire\Superadmin\Kodetransaksi\Index as KodetransaksiIndex;
use App\Livewire\Superadmin\Kodetransaksi\Create as KodetransaksiCreate;
use App\Livewire\Superadmin\Kodetransaksi\Edit as KodetransaksiEdit;
use App\Livewire\Superadmin\Kodetransaksi\Show as KodeTransaksiShow;

// SIMPANAN PRODUK
use App\Livewire\Superadmin\Simpananproduk\Index as SimpananprodukIndex;
use App\Livewire\Superadmin\Simpananproduk\Create as SimpananprodukCreate;
use App\Livewire\Superadmin\Simpananproduk\Edit as SimpananprodukEdit;
use App\Livewire\Superadmin\Simpananproduk\Show as SimpananprodukShow;

// MARKETING
use App\Livewire\Superadmin\Marketing\Index as MarketingIndex;
use App\Livewire\Superadmin\Marketing\Create as MarketingCreate;
use App\Livewire\Superadmin\Marketing\Edit as MarketingEdit;
use App\Livewire\Superadmin\Marketing\Show as MarketingShow;

// SIMPANAN
use App\Livewire\Superadmin\Simpanan\Index as SimpananIndex;
use App\Livewire\Superadmin\Simpanan\Create as SimpananCreate;
use App\Livewire\Superadmin\Simpanan\Edit as SimpananEdit;
use App\Livewire\Superadmin\Simpanan\Show as SimpananShow;

// SIMPANAN RENCANA
use App\Livewire\Superadmin\Simpananrencana\Index as SimpananrencanaIndex;
use App\Livewire\Superadmin\Simpananrencana\Create as SimpananrencanaCreate;
use App\Livewire\Superadmin\Simpananrencana\Edit as SimpananrencanaEdit;
use App\Livewire\Superadmin\Simpananrencana\Show as SimpananrencanaShow;

// SIMPANAN BERJANGKA PRODUK
use App\Livewire\Superadmin\Berjangkaproduk\Index as BerjangkaprodukIndex;
use App\Livewire\Superadmin\Berjangkaproduk\Create as BerjangkaprodukCreate;
use App\Livewire\Superadmin\Berjangkaproduk\Edit as BerjangkaprodukEdit;
use App\Livewire\Superadmin\Berjangkaproduk\Show as BerjangkaprodukShow;

// SIMPANAN BERJANGKA
use App\Livewire\Superadmin\Berjangka\Index as BerjangkaIndex;
use App\Livewire\Superadmin\Berjangka\Create as BerjangkaCreate;
use App\Livewire\Superadmin\Berjangka\Edit as BerjangkaEdit;
use App\Livewire\Superadmin\Berjangka\Show as BerjangkaShow;

// Template
use App\Livewire\Superadmin\Template\Index as TemplateIndex;
use App\Livewire\Superadmin\Template\Create as TemplateCreate;
use App\Livewire\Superadmin\Template\Edit as TemplateEdit;
use App\Livewire\Superadmin\Template\Show as TemplateShow;

// Kas Awal
use App\Livewire\Superadmin\Kasawal\Index as KasawalIndex;
use App\Livewire\Superadmin\Kasawal\Create as KasawalCreate;
use App\Livewire\Superadmin\Kasawal\Edit as KasawalEdit;
use App\Livewire\Superadmin\Kasawal\Show as KasawalShow;

// Kas Keluar
use App\Livewire\Superadmin\Kaskeluar\Index as KaskeluarIndex;
use App\Livewire\Superadmin\Kaskeluar\Create as KaskeluarCreate;
use App\Livewire\Superadmin\Kaskeluar\Edit as KaskeluarEdit;
use App\Livewire\Superadmin\Kaskeluar\Show as KaskeluarShow;

// Kas Masuk
use App\Livewire\Superadmin\Kasmasuk\Index as KasmasukIndex;
use App\Livewire\Superadmin\Kasmasuk\Create as KasmasukCreate;
use App\Livewire\Superadmin\Kasmasuk\Edit as KasmasukEdit;
use App\Livewire\Superadmin\Kasmasuk\Show as KasmasukShow;

// Kas Akhir
use App\Livewire\Superadmin\Kasakhir\Index as KasakhirIndex;
use App\Livewire\Superadmin\Kasakhir\Create as KasakhirCreate;
use App\Livewire\Superadmin\Kasakhir\Edit as KasakhirEdit;
use App\Livewire\Superadmin\Kasakhir\Show as KasakhirShow;

// Pencairan Pinjaman
use App\Livewire\Superadmin\Pencairanpinjaman\Index as PencairanpinjamanIndex;
use App\Livewire\Superadmin\Pencairanpinjaman\Create as PencairanpinjamanCreate;
use App\Livewire\Superadmin\Pencairanpinjaman\Edit as PencairanpinjamanEdit;
use App\Livewire\Superadmin\Pencairanpinjaman\Show as PencairanpinjamanShow;

// Penalti Pinjaman
use App\Livewire\Superadmin\Penaltipinjaman\Index as PenaltipinjamanIndex;
use App\Livewire\Superadmin\Penaltipinjaman\Create as PenaltipinjamanCreate;
use App\Livewire\Superadmin\Penaltipinjaman\Edit as PenaltipinjamanEdit;
use App\Livewire\Superadmin\Penaltipinjaman\Show as PenaltipinjamanShow;

// Penalti Pinjaman Kolektif
use App\Livewire\Superadmin\Penaltipinjamankolektif\Index as PenaltipinjamankolektifIndex;
use App\Livewire\Superadmin\Penaltipinjamankolektif\Create as PenaltipinjamankolektifCreate;
use App\Livewire\Superadmin\Penaltipinjamankolektif\Edit as PenaltipinjamankolektifEdit;
use App\Livewire\Superadmin\Penaltipinjamankolektif\Show as PenaltipinjamankolektifShow;

// Angsuran Pinjaman
use App\Livewire\Superadmin\Angsuranpinjaman\Index as AngsuranpinjamanIndex;
use App\Livewire\Superadmin\Angsuranpinjaman\Create as AngsuranpinjamanCreate;
use App\Livewire\Superadmin\Angsuranpinjaman\Edit as AngsuranpinjamanEdit;
use App\Livewire\Superadmin\Angsuranpinjaman\Show as AngsuranpinjamanShow;

// Angsuran Pinjaman Kolektif
use App\Livewire\Superadmin\Angsuranpinjamankolektifdebet\Index as AngsuranpinjamankolektifdebetIndex;
use App\Livewire\Superadmin\Angsuranpinjamankolektifdebet\Create as AngsuranpinjamankolektifdebetCreate;
use App\Livewire\Superadmin\Angsuranpinjamankolektifdebet\Edit as AngsuranpinjamankolektifdebetEdit;
use App\Livewire\Superadmin\Angsuranpinjamankolektifdebet\Show as AngsuranpinjamankolektifdebetShow;

// Angsuran Pinjaman Kolektif Tunai
use App\Livewire\Superadmin\Angsuranpinjamankolektiftunai\Index as AngsuranpinjamankolektiftunaiIndex;
use App\Livewire\Superadmin\Angsuranpinjamankolektiftunai\Create as AngsuranpinjamankolektiftunaiCreate;
use App\Livewire\Superadmin\Angsuranpinjamankolektiftunai\Edit as AngsuranpinjamankolektiftunaiEdit;
use App\Livewire\Superadmin\Angsuranpinjamankolektiftunai\Show as AngsuranpinjamankolektiftunaiShow;

// Setoran Kolektif Bank
use App\Livewire\Superadmin\Setorankolektifbank\Index as SetorankolektifbankIndex;
use App\Livewire\Superadmin\Setorankolektifbank\Create as SetorankolektifbankCreate;
use App\Livewire\Superadmin\Setorankolektifbank\Edit as SetorankolektifbankEdit;
use App\Livewire\Superadmin\Setorankolektifbank\Show as SetorankolektifbankShow;

// Setoran Simpanan
use App\Livewire\Superadmin\Setoransimpanan\Index as SetoransimpananIndex;
use App\Livewire\Superadmin\Setoransimpanan\Create as SetoransimpananCreate;
use App\Livewire\Superadmin\Setoransimpanan\Edit as SetoransimpananEdit;
use App\Livewire\Superadmin\Setoransimpanan\Show as SetoransimpananShow;

// Setoran Simpanan Kolektif
use App\Livewire\Superadmin\Setoransimpanankolektif\Index as SetoransimpanankolektifIndex;
use App\Livewire\Superadmin\Setoransimpanankolektif\Create as SetoransimpanankolektifCreate;
use App\Livewire\Superadmin\Setoransimpanankolektif\Edit as SetoransimpanankolektifEdit;
use App\Livewire\Superadmin\Setoransimpanankolektif\Show as SetoransimpanankolektifShow;

// Tarikan Simpanan
use App\Livewire\Superadmin\Tarikansimpanan\Index as TarikansimpananIndex;
use App\Livewire\Superadmin\Tarikansimpanan\Create as TarikansimpananCreate;
use App\Livewire\Superadmin\Tarikansimpanan\Edit as TarikansimpananEdit;
use App\Livewire\Superadmin\Tarikansimpanan\Show as TarikansimpananShow;

// Tarikan Simpana Kolektif
use App\Livewire\Superadmin\Tarikansimpanankolektif\Index as TarikansimpanankolektifIndex;
use App\Livewire\Superadmin\Tarikansimpanankolektif\Create as TarikansimpanankolektifCreate;
use App\Livewire\Superadmin\Tarikansimpanankolektif\Edit as TarikansimpanankolektifEdit;
use App\Livewire\Superadmin\Tarikansimpanankolektif\Show as TarikansimpanankolektifShow;

// Pemindahbukuan Simpanan
use App\Livewire\Superadmin\Pemindahbukuansimpanan\Index as PemindahbukuansimpananIndex;
use App\Livewire\Superadmin\Pemindahbukuansimpanan\Create as PemindahbukuansimpananCreate;
use App\Livewire\Superadmin\Pemindahbukuansimpanan\Edit as PemindahbukuansimpananEdit;
use App\Livewire\Superadmin\Pemindahbukuansimpanan\Show as PemindahbukuansimpananShow;

// Penutupan Simpanan
use App\Livewire\Superadmin\Penutupansimpanan\Index as PenutupansimpananIndex;
use App\Livewire\Superadmin\Penutupansimpanan\Create as PenutupansimpananCreate;
use App\Livewire\Superadmin\Penutupansimpanan\Edit as PenutupansimpananEdit;
use App\Livewire\Superadmin\Penutupansimpanan\Show as PenutupansimpananShow;

// Setoran Simpana Berjangka
use App\Livewire\Superadmin\Setoransimpananberjangka\Index as SetoransimpananberjangkaIndex;
use App\Livewire\Superadmin\Setoransimpananberjangka\Create as SetoransimpananberjangkaCreate;
use App\Livewire\Superadmin\Setoransimpananberjangka\Edit as SetoransimpananberjangkaEdit;
use App\Livewire\Superadmin\Setoransimpananberjangka\Show as SetoransimpananberjangkaShow;

// Pencairan Simpana Berjangka
use App\Livewire\Superadmin\Pencairansimpananberjangka\Index as PencairansimpananberjangkaIndex;
use App\Livewire\Superadmin\Pencairansimpananberjangka\Create as PencairansimpananberjangkaCreate;
use App\Livewire\Superadmin\Pencairansimpananberjangka\Edit as PencairansimpananberjangkaEdit;
use App\Livewire\Superadmin\Pencairansimpananberjangka\Show as PencairansimpananberjangkaShow;

// Penalti Simpanan Berjangka
use App\Livewire\Superadmin\Penaltisimpananberjangka\Index as PenaltisimpananberjangkaIndex;
use App\Livewire\Superadmin\Penaltisimpananberjangka\Create as PenaltisimpananberjangkaCreate;
use App\Livewire\Superadmin\Penaltisimpananberjangka\Edit as PenaltisimpananberjangkaEdit;
use App\Livewire\Superadmin\Penaltisimpananberjangka\Show as PenaltisimpananberjangkaShow;

// Penarikan Titipan Anggota
use App\Livewire\Superadmin\Penarikandanatitipan\Index as PenarikandanatitipanIndex;
use App\Livewire\Superadmin\Penarikandanatitipan\Create as PenarikandanatitipanCreate;
use App\Livewire\Superadmin\Penarikandanatitipan\Edit as PenarikandanatitipanEdit;
use App\Livewire\Superadmin\Penarikandanatitipan\Show as PenarikandanatitipanShow;

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

    //Anggota
    Route::get('/anggota', AnggotaIndex::class)->name('anggota');
    Route::get('/anggota/create', AnggotaCreate::class)->name('anggota.create');
    Route::get('/anggota/export', AnggotaIndex::class)->name('anggota.export-pdf');
    Route::get('/anggota/{id}/edit', AnggotaEdit::class)->name('anggota.edit');
    Route::get('/anggota/{id}', AnggotaShow::class)->name('anggota.show');
    Route::get('export-anggota', [AnggotaIndex::class, 'export']);

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

    // Pinjaman Pinjaman
    Route::get('/pinjaman/pinjaman', PinjamanIndex::class)->name('pinjaman.pinjaman');
    Route::get('/pinjaman/pinjaman/create', PinjamanCreate::class)->name('pinjaman.pinjaman.create');
    Route::get('/pinjaman/pinjaman/export', PinjamanIndex::class)->name('pinjaman.pinjaman.export-pdf');
    Route::get('/pinjaman/pinjaman/{id}/edit', PinjamanEdit::class)->name('pinjaman.pinjaman.edit');
    Route::get('/pinjaman/pinjaman/{id}', PinjamanShow::class)->name('pinjaman.pinjaman.show');
    Route::get('export-pinjaman-pinjaman', [PinjamanIndex::class, 'export']);

    // Pinjaman Proposal
    Route::get('/pinjaman/proposal', PinjamanproposalIndex::class)->name('pinjaman.proposal');
    Route::get('/pinjaman/proposal/create', PinjamanproposalCreate::class)->name('pinjaman.proposal.create');
    Route::get('/pinjaman/proposal/export', PinjamanproposalIndex::class)->name('pinjaman.proposal.export-pdf');
    Route::get('/pinjaman/proposal/{id}/edit', PinjamanproposalEdit::class)->name('pinjaman.proposal.edit');
    Route::get('/pinjaman/proposal/{id}', PinjamanproposalShow::class)->name('pinjaman.proposal.show');
    Route::get('export-pinjaman-proposal', [PinjamanproposalIndex::class, 'export']);

    // Pinjaman Jadwal Ulang
    Route::get('/pinjaman/jadwal-ulang', PinjamanjadwalulangIndex::class)->name('pinjaman.jadwal-ulang');
    Route::get('/pinjaman/jadwal-ulang/create', PinjamanjadwalulangCreate::class)->name('pinjaman.jadwal-ulang.create');
    Route::get('/pinjaman/jadwal-ulang/export', PinjamanjadwalulangIndex::class)->name('pinjaman.jadwal-ulang.export-pdf');
    Route::get('/pinjaman/jadwal-ulang/{id}/edit', PinjamanjadwalulangEdit::class)->name('pinjaman.jadwal-ulang.edit');
    Route::get('/pinjaman/jadwal-ulang/{id}', PinjamanjadwalulangShow::class)->name('pinjaman.jadwal-ulang.show');
    Route::get('export-pinjaman-jadwal-ulang', [PinjamanjadwalulangIndex::class, 'export']);

    // Pinjaman Tagihan
    Route::get('/pinjaman/tagihan', PinjamantagihanIndex::class)->name('pinjaman.tagihan');
    Route::get('/pinjaman/tagihan/create', PinjamantagihanCreate::class)->name('pinjaman.tagihan.create');
    Route::get('/pinjaman/tagihan/export', PinjamantagihanIndex::class)->name('pinjaman.tagihan.export-pdf');
    Route::get('/pinjaman/tagihan/{id}/edit', PinjamantagihanEdit::class)->name('pinjaman.tagihan.edit');
    Route::get('/pinjaman/tagihan/{id}', PinjamantagihanShow::class)->name('pinjaman.tagihan.show');
    Route::get('export-pinjaman-tagihan', [PinjamantagihanIndex::class, 'export']);

    // Pinjaman Penghapusan
    Route::get('/pinjaman/penghapusan', PinjamanpenghapusanIndex::class)->name('pinjaman.penghapusan');
    Route::get('/pinjaman/penghapusan/create', PinjamanpenghapusanCreate::class)->name('pinjaman.penghapusan.create');
    Route::get('/pinjaman/penghapusan/export', PinjamanpenghapusanIndex::class)->name('pinjaman.penghapusan.export-pdf');
    Route::get('/pinjaman/penghapusan/{id}/edit', PinjamanpenghapusanEdit::class)->name('pinjaman.penghapusan.edit');
    Route::get('/pinjaman/penghapusan/{id}', PinjamanpenghapusanShow::class)->name('pinjaman.penghapusan.show');
    Route::get('export-pinjaman-penghapusan', [PinjamanpenghapusanIndex::class, 'export']);

    // Pinjaman Surat Peringatan
    Route::get('/pinjaman/surat-peringatan', PinjamansuratperingatanIndex::class)->name('pinjaman.surat-peringatan');
    Route::get('/pinjaman/surat-peringatan/create', PinjamansuratperingatanCreate::class)->name('pinjaman.surat-peringatan.create');
    Route::get('/pinjaman/surat-peringatan/export', PinjamansuratperingatanIndex::class)->name('pinjaman.surat-peringatan.export-pdf');
    Route::get('/pinjaman/surat-peringatan/{id}/edit', PinjamansuratperingatanEdit::class)->name('pinjaman.surat-peringatan.edit');
    Route::get('/pinjaman/surat-peringatan/{id}', PinjamansuratperingatanShow::class)->name('pinjaman.surat-peringatan.show');
    Route::get('export-pinjaman-surat-peringatan', [PinjamansuratperingatanIndex::class, 'export']);

    // Pinjaman Pengembalian Jaminan
    Route::get('/pinjaman/pengembalian-jaminan', PinjamanpengembalianjaminanIndex::class)->name('pinjaman.pengembalian-jaminan');
    Route::get('/pinjaman/pengembalian-jaminan/create', PinjamanpengembalianjaminanCreate::class)->name('pinjaman.pengembalian-jaminan.create');
    Route::get('/pinjaman/pengembalian-jaminan/export', PinjamanpengembalianjaminanIndex::class)->name('pinjaman.pengembalian-jaminan.export-pdf');
    Route::get('/pinjaman/pengembalian-jaminan/{id}/edit', PinjamanpengembalianjaminanEdit::class)->name('pinjaman.pengembalian-jaminan.edit');
    Route::get('/pinjaman/pengembalian-jaminan/{id}', PinjamanpengembalianjaminanShow::class)->name('pinjaman.pengembalian-jaminan.show');
    Route::get('export-pinjaman-pengembalian-jaminan', [PinjamanpengembalianjaminanIndex::class, 'export']);

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
    Route::get('/simpanan/rencana', SimpananrencanaIndex::class)->name('simpanan.rencana');
    Route::get('/simpanan/rencana/create', SimpananrencanaCreate::class)->name('simpanan.rencana.create');
    Route::get('/simpanan/rencana/export', SimpananrencanaIndex::class)->name('simpanan.rencana.export-pdf');
    Route::get('/simpanan/rencana/{id}/edit', SimpananrencanaEdit::class)->name('simpanan.rencana.edit');
    Route::get('/simpanan/rencana/{id}', SimpananrencanaShow::class)->name('simpanan.rencana.show');
    Route::get('export-simpanan-rencana', [SimpananrencanaIndex::class, 'export']);

    // Simpanan
    Route::get('/simpanan', SimpananIndex::class)->name('simpanan');
    Route::get('/simpanan/create', SimpananCreate::class)->name('simpanan.create');
    Route::get('/simpanan/export', SimpananIndex::class)->name('simpanan.export-pdf');
    Route::get('/simpanan/{id}/edit', SimpananEdit::class)->name('simpanan.edit');
    Route::get('/simpanan/{id}', SimpananShow::class)->name('simpanan.show');
    Route::get('export-simpanan', [SimpananIndex::class, 'export']);


    // Berjangka
    // Produk Berjangka
    Route::get('/simpanan-berjangka/produk', BerjangkaprodukIndex::class)->name('simpanan-berjangka.produk');
    Route::get('/simpanan-berjangka/produk/create', BerjangkaprodukCreate::class)->name('simpanan-berjangka.produk.create');
    Route::get('/simpanan-berjangka/produk/export', BerjangkaprodukIndex::class)->name('simpanan-berjangka.produk.export-pdf');
    Route::get('/simpanan-berjangka/produk/{id}/edit', BerjangkaprodukEdit::class)->name('simpanan-berjangka.produk.edit');
    Route::get('/simpanan-berjangka/produk/{id}', BerjangkaprodukShow::class)->name('simpanan-berjangka.produk.show');
    Route::get('export-simpanan-berjangka-produk', [BerjangkaprodukIndex::class, 'export']);

    // Simpanan Berjangka
    Route::get('/simpanan-berjangka', BerjangkaIndex::class)->name('simpanan-berjangka');
    Route::get('/simpanan-berjangka/create', BerjangkaCreate::class)->name('simpanan-berjangka.create');
    Route::get('/simpanan-berjangka/export', BerjangkaIndex::class)->name('simpanan-berjangka.export-pdf');
    Route::get('/simpanan-berjangka/{id}/edit', BerjangkaEdit::class)->name('simpanan-berjangka.edit');
    Route::get('/simpanan-berjangka/{id}', BerjangkaShow::class)->name('simpanan-berjangka.show');
    Route::get('export-simpanan-berjangka', [BerjangkaIndex::class, 'export']);

    // Template
    Route::get('/template', TemplateIndex::class)->name('template');
    Route::get('/template/create', TemplateCreate::class)->name('template.create');
    Route::get('/template/export', TemplateIndex::class)->name('template.export-pdf');
    Route::get('/template/{id}/edit', TemplateEdit::class)->name('template.edit');
    Route::get('/template/{id}', TemplateShow::class)->name('template.show');
    Route::get('export-template', [TemplateIndex::class, 'export']);

    // KAS Harian
    // Kas Awal
    Route::get('/kas-harian/kas-awal', KasawalIndex::class)->name('kas-harian.kas-awal');
    Route::get('/kas-harian/kas-awal/create', KasawalCreate::class)->name('kas-harian.kas-awal.create');
    Route::get('/kas-harian/kas-awal/export', KasawalIndex::class)->name('kas-awal.export-pdf');
    Route::get('/kas-harian/kas-awal/{id}/edit', KasawalEdit::class)->name('kas-awal.edit');
    Route::get('/kas-harian/kas-awal/{id}', KasawalShow::class)->name('kas-awal.show');
    Route::get('export-kas-awal', [KasawalIndex::class, 'export']);

    // Kas Keluar
    Route::get('/kas-harian/kas-keluar', KaskeluarIndex::class)->name('kas-harian.kas-keluar');
    Route::get('/kas-harian/kas-keluar/create', KaskeluarCreate::class)->name('kas-harian.kas-keluar.create');
    Route::get('/kas-harian/kas-keluar/export', KaskeluarIndex::class)->name('kas-harian.kas-keluar.export-pdf');
    Route::get('/kas-harian/kas-keluar/{id}/edit', KaskeluarEdit::class)->name('kas-harian.kas-keluar.edit');
    Route::get('/kas-harian/kas-keluar/{id}', KaskeluarShow::class)->name('kas-harian.kas-keluar.show');
    Route::get('export-kas-keluar', [KaskeluarIndex::class, 'export']);

    // Kas Masuk
    Route::get('/kas-harian/kas-masuk', KasmasukIndex::class)->name('kas-harian.kas-masuk');
    Route::get('/kas-harian/kas-masuk/create', KasmasukCreate::class)->name('kas-harian.kas-masuk.create');
    Route::get('/kas-harian/kas-masuk/export', KasmasukIndex::class)->name('kas-harian.kas-masuk.export-pdf');
    Route::get('/kas-harian/kas-masuk/{id}/edit', KasmasukEdit::class)->name('kas-harian.kas-masuk.edit');
    Route::get('/kas-harian/kas-masuk/{id}', KasmasukShow::class)->name('kas-harian.kas-masuk.show');
    Route::get('export-kas-masuk', [KasmasukIndex::class, 'export']);

    // Kas Akhir
    Route::get('/kas-harian/kas-akhir', KasakhirIndex::class)->name('kas-harian.kas-akhir');
    Route::get('/kas-harian/kas-akhir/create', KasakhirCreate::class)->name('kas-harian.kas-akhir.create');
    Route::get('/kas-harian/kas-akhir/export', KasakhirIndex::class)->name('kas-harian.kas-akhir.export-pdf');
    Route::get('/kas-harian/kas-akhir/{id}/edit', KasakhirEdit::class)->name('kas-harian.kas-akhir.edit');
    Route::get('/kas-harian/kas-akhir/{id}', KasakhirShow::class)->name('kas-harian.kas-akhir.show');
    Route::get('export-kas-akhir', [KasakhirIndex::class, 'export']);

    // Transaksi Pinjaman
    // Pencairan Pinjaman
    Route::get('/transaksi-pinjaman/pencairan-pinjaman', PencairanpinjamanIndex::class)->name('transaksi-pinjaman.pencairan-pinjaman');
    Route::get('/transaksi-pinjaman/pencairan-pinjaman/create', PencairanpinjamanCreate::class)->name('transaksi-pinjaman.pencairan-pinjaman.create');
    Route::get('/transaksi-pinjaman/pencairan-pinjaman/export', PencairanpinjamanIndex::class)->name('transaksi-pinjaman.pencairan-pinjaman.export-pdf');
    Route::get('/transaksi-pinjaman/pencairan-pinjaman/{id}/edit', PencairanpinjamanEdit::class)->name('transaksi-pinjaman.pencairan-pinjaman.edit');
    Route::get('/transaksi-pinjaman/pencairan-pinjaman/{id}', PencairanpinjamanShow::class)->name('transaksi-pinjaman.pencairan-pinjaman.show');
    Route::get('export-pencairan-pinjaman', [PencairanpinjamanIndex::class, 'export']);

    // Penalti Pinjaman
    Route::get('/transaksi-pinjaman/penalti-pinjaman', PenaltipinjamanIndex::class)->name('transaksi-pinjaman.penalti-pinjaman');
    Route::get('/transaksi-pinjaman/penalti-pinjaman/create', PenaltipinjamanCreate::class)->name('transaksi-pinjaman.penalti-pinjaman.create');
    Route::get('/transaksi-pinjaman/penalti-pinjaman/export', PenaltipinjamanIndex::class)->name('transaksi-pinjaman.penalti-pinjaman.export-pdf');
    Route::get('/transaksi-pinjaman/penalti-pinjaman/{id}/edit', PenaltipinjamanEdit::class)->name('transaksi-pinjaman.penalti-pinjaman.edit');
    Route::get('/transaksi-pinjaman/penalti-pinjaman/{id}', PenaltipinjamanShow::class)->name('transaksi-pinjaman.penalti-pinjaman.show');
    Route::get('export-penalti-pinjaman', [PenaltipinjamanIndex::class, 'export']);

    // Penalti Pinjaman Koletif Tunai
    Route::get('/transaksi-pinjaman/penalti-pinjaman-kolektif-tunai', PenaltipinjamankolektifIndex::class)->name('transaksi-pinjaman.penalti-pinjaman-kolektif-tunai');
    Route::get('/transaksi-pinjaman/penalti-pinjaman-kolektif-tunai/create', PenaltipinjamankolektifCreate::class)->name('transaksi-pinjaman.penalti-pinjaman-kolektif-tunai.create');
    Route::get('/transaksi-pinjaman/penalti-pinjaman-kolektif-tunai/export', PenaltipinjamankolektifIndex::class)->name('transaksi-pinjaman.penalti-pinjaman-kolektif-tunai.export-pdf');
    Route::get('/transaksi-pinjaman/penalti-pinjaman-kolektif-tunai/{id}/edit', PenaltipinjamankolektifEdit::class)->name('transaksi-pinjaman.penalti-pinjaman-kolektif-tunai.edit');
    Route::get('/transaksi-pinjaman/penalti-pinjaman-kolektif-tunai/{id}', PenaltipinjamankolektifShow::class)->name('transaksi-pinjaman.penalti-pinjaman-kolektif-tunai.show');
    Route::get('export-penalti-pinjaman-kolektif-tunai', [PenaltipinjamankolektifIndex::class, 'export']);

    // Angsuran Pinjaman
    Route::get('/transaksi-pinjaman/angsuran-pinjaman', AngsuranpinjamanIndex::class)->name('transaksi-pinjaman.angsuran-pinjaman');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman/create', AngsuranpinjamanCreate::class)->name('transaksi-pinjaman.angsuran-pinjaman.create');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman/export', AngsuranpinjamanIndex::class)->name('transaksi-pinjaman.angsuran-pinjaman.export-pdf');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman/{id}/edit', AngsuranpinjamanEdit::class)->name('transaksi-pinjaman.angsuran-pinjaman.edit');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman/{id}', AngsuranpinjamanShow::class)->name('transaksi-pinjaman.angsuran-pinjaman.show');
    Route::get('export-angsuran-pinjaman', [AngsuranpinjamanIndex::class, 'export']);

    // Angsuran Pinjaman
    Route::get('/transaksi-pinjaman/angsuran-pinjaman-kolektif-debet', AngsuranpinjamankolektifdebetIndex::class)->name('transaksi-pinjaman.angsuran-pinjaman-kolektif-debet');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman-kolektif-debet/create', AngsuranpinjamankolektifdebetCreate::class)->name('transaksi-pinjaman.angsuran-pinjaman-kolektif-debet.create');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman-kolektif-debet/export', AngsuranpinjamankolektifdebetIndex::class)->name('transaksi-pinjaman.angsuran-pinjaman-kolektif-debet.export-pdf');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman-kolektif-debet/{id}/edit', AngsuranpinjamankolektifdebetEdit::class)->name('transaksi-pinjaman.angsuran-pinjaman-kolektif-debet.edit');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman-kolektif-debet/{id}', AngsuranpinjamankolektifdebetShow::class)->name('transaksi-pinjaman.angsuran-pinjaman-kolektif-debet.show');
    Route::get('export-angsuran-pinjaman-kolektif-debet', [AngsuranpinjamankolektifdebetIndex::class, 'export']);

    // Angsuran Pinjaman Kolektif Tunai
    Route::get('/transaksi-pinjaman/angsuran-pinjaman-kolektif-tunai', AngsuranpinjamankolektiftunaiIndex::class)->name('transaksi-pinjaman.angsuran-pinjaman-kolektif-tunai');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman-kolektif-tunai/create', AngsuranpinjamankolektiftunaiCreate::class)->name('transaksi-pinjaman.angsuran-pinjaman-kolektif-tunai.create');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman-kolektif-tunai/export', AngsuranpinjamankolektiftunaiIndex::class)->name('transaksi-pinjaman.angsuran-pinjaman-kolektif-tunai.export-pdf');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman-kolektif-tunai/{id}/edit', AngsuranpinjamankolektiftunaiEdit::class)->name('transaksi-pinjaman.angsuran-pinjaman-kolektif-tunai.edit');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman-kolektif-tunai/{id}', AngsuranpinjamankolektiftunaiShow::class)->name('transaksi-pinjaman.angsuran-pinjaman-kolektif-tunai.show');
    Route::get('export-angsuran-pinjaman-kolektif-tunai', [AngsuranpinjamankolektiftunaiIndex::class, 'export']);

    // Angsuran Pinjaman Kolektif Tunai
    Route::get('/transaksi-pinjaman/angsuran-pinjaman-kolektif-tunai', AngsuranpinjamankolektiftunaiIndex::class)->name('transaksi-pinjaman.angsuran-pinjaman-kolektif-tunai');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman-kolektif-tunai/create', AngsuranpinjamankolektiftunaiCreate::class)->name('transaksi-pinjaman.angsuran-pinjaman-kolektif-tunai.create');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman-kolektif-tunai/export', AngsuranpinjamankolektiftunaiIndex::class)->name('transaksi-pinjaman.angsuran-pinjaman-kolektif-tunai.export-pdf');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman-kolektif-tunai/{id}/edit', AngsuranpinjamankolektiftunaiEdit::class)->name('transaksi-pinjaman.angsuran-pinjaman-kolektif-tunai.edit');
    Route::get('/transaksi-pinjaman/angsuran-pinjaman-kolektif-tunai/{id}', AngsuranpinjamankolektiftunaiShow::class)->name('transaksi-pinjaman.angsuran-pinjaman-kolektif-tunai.show');
    Route::get('export-angsuran-pinjaman-kolektif-tunai', [AngsuranpinjamankolektiftunaiIndex::class, 'export']);

    // Setoran Pinjaman Kolektif Bank
    Route::get('/transaksi-pinjaman/setoran-kolektif-bank', SetorankolektifbankIndex::class)->name('transaksi-pinjaman.setoran-kolektif-bank');
    Route::get('/transaksi-pinjaman/setoran-kolektif-bank/create', SetorankolektifbankCreate::class)->name('transaksi-pinjaman.setoran-kolektif-bank.create');
    Route::get('/transaksi-pinjaman/setoran-kolektif-bank/export', SetorankolektifbankIndex::class)->name('transaksi-pinjaman.setoran-kolektif-bank.export-pdf');
    Route::get('/transaksi-pinjaman/setoran-kolektif-bank/{id}/edit', SetorankolektifbankEdit::class)->name('transaksi-pinjaman.setoran-kolektif-bank.edit');
    Route::get('/transaksi-pinjaman/setoran-kolektif-bank/{id}', SetorankolektifbankShow::class)->name('transaksi-pinjaman.setoran-kolektif-bank.show');
    Route::get('export-setoran-kolektif-bank', [SetorankolektifbankIndex::class, 'export']);

    // Transaksi Simpanan
    // Setoran Simpanan
    Route::get('/transaksi-simpanan/setoran-simpanan', SetoransimpananIndex::class)->name('transaksi-simpanan.setoran-simpanan');
    Route::get('/transaksi-simpanan/setoran-simpanan/create', SetoransimpananCreate::class)->name('transaksi-simpanan.setoran-simpanan.create');
    Route::get('/transaksi-simpanan/setoran-simpanan/export', SetoransimpananIndex::class)->name('transaksi-simpanan.setoran-simpanan.export-pdf');
    Route::get('/transaksi-simpanan/setoran-simpanan/{id}/edit', SetoransimpananEdit::class)->name('transaksi-simpanan.setoran-simpanan.edit');
    Route::get('/transaksi-simpanan/setoran-simpanan/{id}', SetoransimpananShow::class)->name('transaksi-simpanan.setoran-simpanan.show');
    Route::get('export-setoran-simpanan', [SetoransimpananIndex::class, 'export']);

    // Setoran Simpanan Kolektif
    Route::get('/transaksi-simpanan/setoran-simpanan-kolektif', SetoransimpanankolektifIndex::class)->name('transaksi-simpanan.setoran-simpanan-kolektif');
    Route::get('/transaksi-simpanan/setoran-simpanan-kolektif/create', SetoransimpanankolektifCreate::class)->name('transaksi-simpanan.setoran-simpanan-kolektif.create');
    Route::get('/transaksi-simpanan/setoran-simpanan-kolektif/export', SetoransimpanankolektifIndex::class)->name('transaksi-simpanan.setoran-simpanan-kolektif.export-pdf');
    Route::get('/transaksi-simpanan/setoran-simpanan-kolektif/{id}/edit', SetoransimpanankolektifEdit::class)->name('transaksi-simpanan.setoran-simpanan-kolektif.edit');
    Route::get('/transaksi-simpanan/setoran-simpanan-kolektif/{id}', SetoransimpanankolektifShow::class)->name('transaksi-simpanan.setoran-simpanan-kolektif.show');
    Route::get('export-setoran-simpanan-kolektif', [SetoransimpanankolektifIndex::class, 'export']);

    // Tarikan Simpanan
    Route::get('/transaksi-simpanan/tarikan-simpanan', TarikansimpananIndex::class)->name('transaksi-simpanan.tarikan-simpanan');
    Route::get('/transaksi-simpanan/tarikan-simpanan/create', TarikansimpananCreate::class)->name('transaksi-simpanan.tarikan-simpanan.create');
    Route::get('/transaksi-simpanan/tarikan-simpanan/export', TarikansimpananIndex::class)->name('transaksi-simpanan.tarikan-simpanan.export-pdf');
    Route::get('/transaksi-simpanan/tarikan-simpanan/{id}/edit', TarikansimpananEdit::class)->name('transaksi-simpanan.tarikan-simpanan.edit');
    Route::get('/transaksi-simpanan/tarikan-simpanan/{id}', TarikansimpananShow::class)->name('transaksi-simpanan.tarikan-simpanan.show');
    Route::get('export-tarikan-simpanan', [TarikansimpananIndex::class, 'export']);

    // Tarikan Simpanan Kolektif
    Route::get('/transaksi-simpanan/tarikan-simpanan-kolektif', TarikansimpanankolektifIndex::class)->name('transaksi-simpanan.tarikan-simpanan-kolektif');
    Route::get('/transaksi-simpanan/tarikan-simpanan-kolektif/create', TarikansimpanankolektifCreate::class)->name('transaksi-simpanan.tarikan-simpanan-kolektif.create');
    Route::get('/transaksi-simpanan/tarikan-simpanan-kolektif/export', TarikansimpanankolektifIndex::class)->name('transaksi-simpanan.tarikan-simpanan-kolektif.export-pdf');
    Route::get('/transaksi-simpanan/tarikan-simpanan-kolektif/{id}/edit', TarikansimpanankolektifEdit::class)->name('transaksi-simpanan.tarikan-simpanan-kolektif.edit');
    Route::get('/transaksi-simpanan/tarikan-simpanan-kolektif/{id}', TarikansimpanankolektifShow::class)->name('transaksi-simpanan.tarikan-simpanan-kolektif.show');
    Route::get('export-tarikan-simpanan-kolektif', [TarikansimpanankolektifIndex::class, 'export']);

    // Pemindahbukuan Simpanan
    Route::get('/transaksi-simpanan/pemindahbukuan-simpanan', PemindahbukuansimpananIndex::class)->name('transaksi-simpanan.pemindahbukuan-simpanan');
    Route::get('/transaksi-simpanan/pemindahbukuan-simpanan/create', PemindahbukuansimpananCreate::class)->name('transaksi-simpanan.pemindahbukuan-simpanan.create');
    Route::get('/transaksi-simpanan/pemindahbukuan-simpanan/export', PemindahbukuansimpananIndex::class)->name('transaksi-simpanan.pemindahbukuan-simpanan.export-pdf');
    Route::get('/transaksi-simpanan/pemindahbukuan-simpanan/{id}/edit', PemindahbukuansimpananEdit::class)->name('transaksi-simpanan.pemindahbukuan-simpanan.edit');
    Route::get('/transaksi-simpanan/pemindahbukuan-simpanan/{id}', PemindahbukuansimpananShow::class)->name('transaksi-simpanan.pemindahbukuan-simpanan.show');
    Route::get('export-pemindahbukuan-simpanan', [PemindahbukuansimpananIndex::class, 'export']);

    // Penutupan Simpanan
    Route::get('/transaksi-simpanan/penutupan-simpanan', PenutupansimpananIndex::class)->name('transaksi-simpanan.penutupan-simpanan');
    Route::get('/transaksi-simpanan/penutupan-simpanan/create', PenutupansimpananCreate::class)->name('transaksi-simpanan.penutupan-simpanan.create');
    Route::get('/transaksi-simpanan/penutupan-simpanan/export', PenutupansimpananIndex::class)->name('transaksi-simpanan.penutupan-simpanan.export-pdf');
    Route::get('/transaksi-simpanan/penutupan-simpanan/{id}/edit', PenutupansimpananEdit::class)->name('transaksi-simpanan.penutupan-simpanan.edit');
    Route::get('/transaksi-simpanan/penutupan-simpanan/{id}', PenutupansimpananShow::class)->name('transaksi-simpanan.penutupan-simpanan.show');
    Route::get('export-penutupan-simpanan', [PenutupansimpananIndex::class, 'export']);

    // Transaksi Simpanan Berjangka
    // Setoran Simpanan Berjangka
    Route::get('/transaksi-simpanan-berjangka/setoran-simpanan-berjangka', SetoransimpananberjangkaIndex::class)->name('transaksi-simpanan-berjangka.setoran-simpanan-berjangka');
    Route::get('/transaksi-simpanan-berjangka/setoran-simpanan-berjangka/create', SetoransimpananberjangkaCreate::class)->name('transaksi-simpanan-berjangka.setoran-simpanan-berjangka.create');
    Route::get('/transaksi-simpanan-berjangka/setoran-simpanan-berjangka/export', SetoransimpananberjangkaIndex::class)->name('transaksi-simpanan-berjangka.setoran-simpanan-berjangka.export-pdf');
    Route::get('/transaksi-simpanan-berjangka/setoran-simpanan-berjangka/{id}/edit', SetoransimpananberjangkaEdit::class)->name('transaksi-simpanan-berjangka.setoran-simpanan-berjangka.edit');
    Route::get('/transaksi-simpanan-berjangka/setoran-simpanan-berjangka/{id}', SetoransimpananberjangkaShow::class)->name('transaksi-simpanan-berjangka.setoran-simpanan-berjangka.show');
    Route::get('export-setoran-simpanan-berjangka', [SetoransimpananberjangkaIndex::class, 'export']);

    // Pencairan Simpanan Berjangka
    Route::get('/transaksi-simpanan-berjangka/pencairan-simpanan-berjangka', PencairansimpananberjangkaIndex::class)->name('transaksi-simpanan-berjangka.pencairan-simpanan-berjangka');
    Route::get('/transaksi-simpanan-berjangka/pencairan-simpanan-berjangka/create', PencairansimpananberjangkaCreate::class)->name('transaksi-simpanan-berjangka.pencairan-simpanan-berjangka.create');
    Route::get('/transaksi-simpanan-berjangka/pencairan-simpanan-berjangka/export', PencairansimpananberjangkaIndex::class)->name('transaksi-simpanan-berjangka.pencairan-simpanan-berjangka.export-pdf');
    Route::get('/transaksi-simpanan-berjangka/pencairan-simpanan-berjangka/{id}/edit', PencairansimpananberjangkaEdit::class)->name('transaksi-simpanan-berjangka.pencairan-simpanan-berjangka.edit');
    Route::get('/transaksi-simpanan-berjangka/pencairan-simpanan-berjangka/{id}', PencairansimpananberjangkaShow::class)->name('transaksi-simpanan-berjangka.pencairan-simpanan-berjangka.show');
    Route::get('export-pencairan-simpanan-berjangka', [PencairansimpananberjangkaIndex::class, 'export']);

    // Penalti Simpanan Berjangka
    Route::get('/transaksi-simpanan-berjangka/penalti-simpanan-berjangka', PenaltisimpananberjangkaIndex::class)->name('transaksi-simpanan-berjangka.penalti-simpanan-berjangka');
    Route::get('/transaksi-simpanan-berjangka/penalti-simpanan-berjangka/create', PenaltisimpananberjangkaCreate::class)->name('transaksi-simpanan-berjangka.penalti-simpanan-berjangka.create');
    Route::get('/transaksi-simpanan-berjangka/penalti-simpanan-berjangka/export', PenaltisimpananberjangkaIndex::class)->name('transaksi-simpanan-berjangka.penalti-simpanan-berjangka.export-pdf');
    Route::get('/transaksi-simpanan-berjangka/penalti-simpanan-berjangka/{id}/edit', PenaltisimpananberjangkaEdit::class)->name('transaksi-simpanan-berjangka.penalti-simpanan-berjangka.edit');
    Route::get('/transaksi-simpanan-berjangka/penalti-simpanan-berjangka/{id}', PenaltisimpananberjangkaShow::class)->name('transaksi-simpanan-berjangka.penalti-simpanan-berjangka.show');
    Route::get('export-penalti-simpanan-berjangka', [PenaltisimpananberjangkaIndex::class, 'export']);

    // Penarikan Dana titipan Anggota
    Route::get('/penarikan-dana-titipan', PenarikandanatitipanIndex::class)->name('penarikan-dana-titipan');
    Route::get('/penarikan-dana-titipan/create', PenarikandanatitipanCreate::class)->name('penarikan-dana-titipan.create');
    Route::get('/penarikan-dana-titipan/export', PenarikandanatitipanIndex::class)->name('penarikan-dana-titipan.export-pdf');
    Route::get('/penarikan-dana-titipan/{id}/edit', PenarikandanatitipanEdit::class)->name('penarikan-dana-titipan.edit');
    Route::get('/penarikan-dana-titipan/{id}', PenarikandanatitipanShow::class)->name('penarikan-dana-titipan.show');
    Route::get('export-penarikan-dana-titipan', [PenarikandanatitipanIndex::class, 'export']);
});

// routes/web.php
//  Route::get('/dashboard', Dashboard::class)->name('dashboard')->middleware('auth');

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');







Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.attempt');
Route::get('/', fn () => redirect()->route('login'))->name('home');
