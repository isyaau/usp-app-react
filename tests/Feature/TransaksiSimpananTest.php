<?php

use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\PemindahbukuanSimpanan;
use App\Models\PenutupanSimpanan;
use App\Models\SetoranSimpanan;
use App\Models\Simpanan;
use App\Models\SimpananJenis;
use App\Models\SimpananKode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Feature test modul Transaksi Simpanan (Inertia).
 *
 * Test berjalan terhadap database aktif (pgsql dev) namun SELF-CONTAINED:
 * setiap test membuat data ujinya sendiri (penanda TEST-) lalu
 * membersihkannya, sehingga tidak mengubah data lain.
 */

function transaksiTestUser(): User
{
    return User::updateOrCreate(
        ['email' => 'smoketest@ksp.test'],
        [
            'nama' => 'Smoke Tester',
            'username' => 'smoketest',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
        ]
    );
}

/** Data induk yang dibuat sekali per test dan dihapus di cleanup. */
function buatDataIndukTransaksi(): array
{
    $uniq = uniqid('t');

    $kantor = Kantor::create([
        'kode' => "KT-{$uniq}",
        'nama_kantor' => "Kantor Uji {$uniq}",
        'alamat_kantor' => 'Jl. Test No. 1',
        'pejabat' => 'Pejabat Uji',
        'jabatan' => 'Kepala Kantor',
        'bendahara' => 'Bendahara Uji',
        'user_id' => transaksiTestUser()->id,
    ]);

    $anggota = Anggota::create([
        'no_anggota' => "TEST-{$uniq}",
        'pin' => '123456',
        'nama' => 'Anggota Transaksi Uji '.$uniq,
        'alamat' => 'Jl. Test No. 1',
        'kelompok_id' => 1,
        'kantor_id' => $kantor->id,
        'provinsi_id' => '11',
        'kota_id' => '1101',
        'kecamatan_id' => '110101',
        'kelurahan_id' => '1101012001',
        'email' => "{$uniq}@ksp.test",
        'tempat_lahir' => 'Jakarta',
        'tgl_lahir' => '1995-01-01',
        'jenis_kelamin' => 'Laki-laki',
        'agama' => 'ISLAM',
        'pekerjaan' => 'BURUH',
        'pendidikan' => 'SMA/SMK',
        'status_perkawinan' => 'Belum Kawin',
        'telepon' => '0211234567',
        'no_hp' => '081234567890',
        'jenis_identitas' => 'KTP',
        'no_identitas' => uniqid('31'),
        'npwp' => '-',
        'ibu' => 'Ibu Uji',
        'foto' => 'anggota/foto-default.jpg',
        'pengurus' => 0,
        'pengawas' => 0,
        'status' => 1,
        'user_id' => transaksiTestUser()->id,
    ]);

    $jenis = SimpananJenis::create([
        'kode' => "JS-{$uniq}",
        'nama' => "Jenis Simpanan Uji {$uniq}",
        'jenis' => 'SUKARELA',
        'user_id' => transaksiTestUser()->id,
    ]);

    $rekening = Simpanan::create([
        'no_rekening' => "REK-{$uniq}",
        'tanggal' => now()->toDateString(),
        'anggota_id' => $anggota->id,
        'jenis_id' => $jenis->id,
        'marketing_id' => transaksiTestUser()->id,
        'aktif' => 1,
        'kantor_id' => $kantor->id,
        'user_id' => transaksiTestUser()->id,
    ]);

    $rekening2 = Simpanan::create([
        'no_rekening' => "REK2-{$uniq}",
        'tanggal' => now()->toDateString(),
        'anggota_id' => $anggota->id,
        'jenis_id' => $jenis->id,
        'marketing_id' => transaksiTestUser()->id,
        'aktif' => 1,
        'kantor_id' => $kantor->id,
        'user_id' => transaksiTestUser()->id,
    ]);

    $kodeSetoran = SimpananKode::create([
        'kode' => "KS-{$uniq}",
        'nama' => "Kode Setoran Uji {$uniq}",
        'account_debet' => 1,
        'account_kredit' => 1,
        'setoran' => true,
        'user_id' => transaksiTestUser()->id,
    ]);

    return compact('uniq', 'kantor', 'anggota', 'jenis', 'rekening', 'rekening2', 'kodeSetoran');
}

function bersihkanDataTransaksi(): void
{
    // Hapus dari tabel anak ke induk (aman terhadap FK).
    PemindahbukuanSimpanan::where('no_transaksi', 'LIKE', 'PMB-%')
        ->whereIn('anggota_id', Anggota::where('no_anggota', 'LIKE', 'TEST-%')->select('id'))
        ->delete();
    PenutupanSimpanan::where('no_transaksi', 'LIKE', 'TNP-%')
        ->whereIn('anggota_id', Anggota::where('no_anggota', 'LIKE', 'TEST-%')->select('id'))
        ->delete();
    SetoranSimpanan::where('no_transaksi', 'LIKE', 'SET-%')
        ->whereIn('anggota_id', Anggota::where('no_anggota', 'LIKE', 'TEST-%')->select('id'))
        ->delete();
    \App\Models\TarikanSimpanan::where('no_transaksi', 'LIKE', 'TRK-%')
        ->whereIn('anggota_id', Anggota::where('no_anggota', 'LIKE', 'TEST-%')->select('id'))
        ->delete();

    Simpanan::whereIn('id', PemindahbukuanSimpanan::where('no_transaksi', 'LIKE', 'PMB-%')->select('simpanan_dari_id'))
        ->orWhereIn('id', PemindahbukuanSimpanan::where('no_transaksi', 'LIKE', 'PMB-%')->select('simpanan_ke_id'))
        ->where('no_rekening', 'LIKE', 'REK%')
        ->delete();
    Simpanan::where('no_rekening', 'LIKE', 'REK-%')->delete();
    SimpananJenis::where('kode', 'LIKE', 'JS-%')->delete();
    Anggota::where('no_anggota', 'LIKE', 'TEST-%')->delete();
    Kantor::where('kode', 'LIKE', 'KT-%')->delete();
    SimpananKode::where('kode', 'LIKE', 'KS-%')->delete();
}

// ================================================================
// SETORAN SIMPANAN
// ================================================================

test('index setoran memuat komponen Inertia dan data terpaginasi', function () {
    $response = $this->actingAs(transaksiTestUser())
        ->get(route('superadmin.transaksi-simpanan.setoran-simpanan'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
            ->component('Superadmin/SetoranSimpanan/Index')
            ->has('transaksi.data')
            ->has('filters.search')
    );
});

test('store setoran membuat transaksi dengan no_transaksi otomatis', function () {
    $d = buatDataIndukTransaksi();

    $response = $this->actingAs(transaksiTestUser())
        ->post(route('superadmin.transaksi-simpanan.setoran-simpanan.store'), [
            'tgl_transaksi' => now()->toDateString(),
            'anggota_id' => $d['anggota']->id,
            'simpanan_id' => $d['rekening']->id,
            'kode_transaksi_id' => $d['kodeSetoran']->id,
            'nominal' => 150000,
            'keterangan' => 'Uji setoran',
            'kantor_id' => $d['kantor']->id,
            'status' => 'draft',
        ]);

    $response->assertRedirect(route('superadmin.transaksi-simpanan.setoran-simpanan'));

    $row = SetoranSimpanan::where('anggota_id', $d['anggota']->id)->first();
    expect($row)->not->toBeNull()
        ->and($row->nominal)->toEqual('150000.00')
        ->and($row->no_transaksi)->toStartWith('SET-');

    bersihkanDataTransaksi();
});

test('store setoran gagal bila nominal negatif', function () {
    $d = buatDataIndukTransaksi();

    $this->actingAs(transaksiTestUser())
        ->from(route('superadmin.transaksi-simpanan.setoran-simpanan.create'))
        ->post(route('superadmin.transaksi-simpanan.setoran-simpanan.store'), [
            'tgl_transaksi' => now()->toDateString(),
            'anggota_id' => $d['anggota']->id,
            'simpanan_id' => $d['rekening']->id,
            'kode_transaksi_id' => $d['kodeSetoran']->id,
            'nominal' => -1000,
            'kantor_id' => $d['kantor']->id,
            'status' => 'draft',
        ])
        ->assertSessionHasErrors('nominal');

    expect(SetoranSimpanan::count())->toBeGreaterThanOrEqual(0);

    bersihkanDataTransaksi();
});

test('update dan destroy setoran bekerja', function () {
    $d = buatDataIndukTransaksi();

    $row = SetoranSimpanan::create([
        'no_transaksi' => 'SET-TESTFIX-0001',
        'tgl_transaksi' => now()->toDateString(),
        'anggota_id' => $d['anggota']->id,
        'simpanan_id' => $d['rekening']->id,
        'kode_transaksi_id' => $d['kodeSetoran']->id,
        'nominal' => 100000,
        'user_id' => transaksiTestUser()->id,
        'kantor_id' => $d['kantor']->id,
        'status' => 'draft',
    ]);

    $this->actingAs(transaksiTestUser())
        ->put(route('superadmin.transaksi-simpanan.setoran-simpanan.update', $row), [
            'tgl_transaksi' => now()->toDateString(),
            'anggota_id' => $d['anggota']->id,
            'simpanan_id' => $d['rekening']->id,
            'kode_transaksi_id' => $d['kodeSetoran']->id,
            'nominal' => 175000,
            'kantor_id' => $d['kantor']->id,
            'status' => 'posted',
        ])
        ->assertRedirect(route('superadmin.transaksi-simpanan.setoran-simpanan'));

    expect($row->fresh()->nominal)->toEqual('175000.00')
        ->and($row->fresh()->status)->toBe('posted');

    $this->actingAs(transaksiTestUser())
        ->delete(route('superadmin.transaksi-simpanan.setoran-simpanan.destroy', $row))
        ->assertRedirect(route('superadmin.transaksi-simpanan.setoran-simpanan'));

    expect(SetoranSimpanan::find($row->id))->toBeNull();

    bersihkanDataTransaksi();
});

test('show setoran memuat komponen detail', function () {
    $d = buatDataIndukTransaksi();

    $row = SetoranSimpanan::create([
        'no_transaksi' => 'SET-TESTSHOW-0001',
        'tgl_transaksi' => now()->toDateString(),
        'anggota_id' => $d['anggota']->id,
        'simpanan_id' => $d['rekening']->id,
        'kode_transaksi_id' => $d['kodeSetoran']->id,
        'nominal' => 90000,
        'user_id' => transaksiTestUser()->id,
        'kantor_id' => $d['kantor']->id,
        'status' => 'draft',
    ]);

    $this->actingAs(transaksiTestUser())
        ->get(route('superadmin.transaksi-simpanan.setoran-simpanan.show', $row))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Superadmin/SetoranSimpanan/Show')
            ->where('transaksiData.no_transaksi', 'SET-TESTSHOW-0001'));

    bersihkanDataTransaksi();
});

// ================================================================
// TARIKAN SIMPANAN
// ================================================================

test('index tarikan memuat komponen Inertia', function () {
    $this->actingAs(transaksiTestUser())
        ->get(route('superadmin.transaksi-simpanan.tarikan-simpanan'))
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Superadmin/TarikanSimpanan/Index'));
});

test('store tarikan membuat transaksi dengan prefix TRK', function () {
    $d = buatDataIndukTransaksi();

    $this->actingAs(transaksiTestUser())
        ->post(route('superadmin.transaksi-simpanan.tarikan-simpanan.store'), [
            'tgl_transaksi' => now()->toDateString(),
            'anggota_id' => $d['anggota']->id,
            'simpanan_id' => $d['rekening']->id,
            'kode_transaksi_id' => $d['kodeSetoran']->id,
            'nominal' => 75000,
            'kantor_id' => $d['kantor']->id,
            'status' => 'posted',
        ])
        ->assertRedirect(route('superadmin.transaksi-simpanan.tarikan-simpanan'));

    $row = \App\Models\TarikanSimpanan::where('anggota_id', $d['anggota']->id)->first();
    expect($row)->not->toBeNull()->and($row->no_transaksi)->toStartWith('TRK-');

    bersihkanDataTransaksi();
});

// ================================================================
// PENUTUPAN SIMPANAN
// ================================================================

test('index penutupan memuat komponen Inertia', function () {
    $this->actingAs(transaksiTestUser())
        ->get(route('superadmin.transaksi-simpanan.penutupan-simpanan'))
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Superadmin/PenutupanSimpanan/Index'));
});

test('store penutupan menyimpan nominal bunga', function () {
    $d = buatDataIndukTransaksi();

    $this->actingAs(transaksiTestUser())
        ->post(route('superadmin.transaksi-simpanan.penutupan-simpanan.store'), [
            'tgl_transaksi' => now()->toDateString(),
            'anggota_id' => $d['anggota']->id,
            'simpanan_id' => $d['rekening']->id,
            'kode_transaksi_id' => $d['kodeSetoran']->id,
            'nominal' => 500000,
            'nominal_bunga' => 12500,
            'kantor_id' => $d['kantor']->id,
            'status' => 'draft',
        ])
        ->assertRedirect(route('superadmin.transaksi-simpanan.penutupan-simpanan'));

    $row = PenutupanSimpanan::where('anggota_id', $d['anggota']->id)->first();
    expect($row)->not->toBeNull()
        ->and($row->nominal_bunga)->toEqual('12500.00')
        ->and($row->no_transaksi)->toStartWith('TNP-');

    bersihkanDataTransaksi();
});

// ================================================================
// PEMINDAHBUKUAN SIMPANAN
// ================================================================

test('index pemindahbukuan memuat komponen Inertia', function () {
    $this->actingAs(transaksiTestUser())
        ->get(route('superadmin.transaksi-simpanan.pemindahbukuan-simpanan'))
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Superadmin/PemindahbukuanSimpanan/Index'));
});

test('store pemindahbukuan menolak rekening asal sama dengan tujuan', function () {
    $d = buatDataIndukTransaksi();

    $this->actingAs(transaksiTestUser())
        ->post(route('superadmin.transaksi-simpanan.pemindahbukuan-simpanan.store'), [
            'tgl_transaksi' => now()->toDateString(),
            'anggota_id' => $d['anggota']->id,
            'simpanan_dari_id' => $d['rekening']->id,
            'simpanan_ke_id' => $d['rekening']->id,
            'kode_transaksi_id' => $d['kodeSetoran']->id,
            'nominal' => 10000,
            'kantor_id' => $d['kantor']->id,
            'status' => 'draft',
        ])
        ->assertSessionHasErrors('simpanan_ke_id');

    bersihkanDataTransaksi();
});

test('store pemindahbukuan sukses antar rekening berbeda', function () {
    $d = buatDataIndukTransaksi();

    $this->actingAs(transaksiTestUser())
        ->post(route('superadmin.transaksi-simpanan.pemindahbukuan-simpanan.store'), [
            'tgl_transaksi' => now()->toDateString(),
            'anggota_id' => $d['anggota']->id,
            'simpanan_dari_id' => $d['rekening']->id,
            'simpanan_ke_id' => $d['rekening2']->id,
            'kode_transaksi_id' => $d['kodeSetoran']->id,
            'nominal' => 60000,
            'kantor_id' => $d['kantor']->id,
            'status' => 'posted',
        ])
        ->assertRedirect(route('superadmin.transaksi-simpanan.pemindahbukuan-simpanan'));

    $row = PemindahbukuanSimpanan::where('anggota_id', $d['anggota']->id)->first();
    expect($row)->not->toBeNull()
        ->and($row->simpanan_dari_id)->toBe($d['rekening']->id)
        ->and($row->simpanan_ke_id)->toBe($d['rekening2']->id)
        ->and($row->no_transaksi)->toStartWith('PMB-');

    bersihkanDataTransaksi();
});

// ================================================================
// ENDPOINT JSON REKENING PER ANGGOTA
// ================================================================

test('endpoint rekening per anggota mengembalikan daftar JSON', function () {
    $d = buatDataIndukTransaksi();

    $this->actingAs(transaksiTestUser())
        ->getJson(
            route('superadmin.transaksi-simpanan.simpanan-by-anggota', $d['anggota'])
        )
        ->assertOk()
        ->assertJsonCount(2);

    bersihkanDataTransaksi();
});
