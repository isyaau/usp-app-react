<?php

use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\Pinjaman;
use App\Models\PinjamanProduk;
use App\Models\User;

/**
 * Feature test modul Data Pinjaman.
 * Self-contained: membuat sendiri seluruh data uji dan membersihkannya di akhir.
 * Catatan: komponen Livewire lama rusak (salinan modul Anggota), jadi modul ini
 * dibangun ulang langsung dari skema tabel `pinjaman` (semua kolom NOT NULL).
 */
beforeEach(function () {
    $this->admin = pinjamanTestUser();
});

afterEach(function () {
    pinjamanBersihkanData();
});

it('index memuat komponen Inertia', function () {
    $this->actingAs($this->admin)
        ->get(route('superadmin.pinjaman.pinjaman'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Superadmin/Pinjaman/Index'));
});

it('create memuat komponen Inertia beserta opsi anggota & produk', function () {
    $f = pinjamanFixtures(['tanpaPinjaman' => true]);

    $this->actingAs($this->admin)
        ->get(route('superadmin.pinjaman.pinjaman.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Superadmin/Pinjaman/Create')
            ->has('anggotaOptions')
            ->has('jenisOptions'));
});

it('store membuat data pinjaman', function () {
    $f = pinjamanFixtures(['tanpaPinjaman' => true]);

    $this->actingAs($this->admin)
        ->post(route('superadmin.pinjaman.pinjaman.store'), [
            'tanggal' => '2026-01-10',
            'no_pinjaman' => "PJ-{$f['uniq']}",
            'anggota_id' => $f['anggota']->id,
            'jenis_id' => $f['jenis']->id,
            'plafon' => '10000000',
            'bunga' => '1.5',
            'jangka_waktu' => '12',
            'satuan' => 'bulan',
        ])
        ->assertRedirect(route('superadmin.pinjaman.pinjaman'));

    $pj = Pinjaman::where('no_pinjaman', "PJ-{$f['uniq']}")->first();

    expect($pj)->not->toBeNull()
        ->and($pj->plafon)->toBe('10000000')
        ->and($pj->bunga)->toBe('1.5')
        ->and($pj->aktif)->toBe('1')
        ->and($pj->kantor_id)->toBe($f['kantor']->id)
        ->and($pj->user_id)->toBe($this->admin->id);
});

it('store menolak no_pinjaman duplikat', function () {
    $f = pinjamanFixtures();

    $this->actingAs($this->admin)
        ->post(route('superadmin.pinjaman.pinjaman.store'), [
            'tanggal' => '2026-01-10',
            'no_pinjaman' => $f['pinjaman']->no_pinjaman,
            'anggota_id' => $f['anggota']->id,
            'jenis_id' => $f['jenis']->id,
            'plafon' => '10000000',
            'jangka_waktu' => '12',
            'satuan' => 'bulan',
        ])
        ->assertSessionHasErrors('no_pinjaman');
});

it('destroy menghapus data pinjaman', function () {
    $f = pinjamanFixtures();

    $this->actingAs($this->admin)
        ->delete(route('superadmin.pinjaman.pinjaman.destroy', $f['pinjaman']))
        ->assertRedirect(route('superadmin.pinjaman.pinjaman'));

    expect(Pinjaman::find($f['pinjaman']->id))->toBeNull();
});

/* ------------------------------------------------------------------ */
/* Helper                                                              */
/* ------------------------------------------------------------------ */

function pinjamanTestUser(): User
{
    return User::updateOrCreate(
        ['email' => 'admin-pinjaman@ksp.test'],
        [
            'nama' => 'Admin Pinjaman Uji',
            'username' => 'adminpinjaman',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]
    );
}

function pinjamanFixtures(array $overrides = []): array
{
    $uniq = uniqid();
    $user = pinjamanTestUser();

    $kantor = Kantor::create([
        'kode' => "KPJ-{$uniq}",
        'nama_kantor' => "Kantor Pinjaman Uji {$uniq}",
        'alamat_kantor' => 'Jl. Test No. 1',
        'pejabat' => 'Pejabat Uji',
        'jabatan' => 'Kepala Kantor',
        'bendahara' => 'Bendahara Uji',
        'user_id' => $user->id,
    ]);

    $anggota = Anggota::create([
        'no_anggota' => "TESTP-{$uniq}",
        'pin' => '123456',
        'nama' => 'Anggota Pinjaman Uji '.$uniq,
        'alamat' => 'Jl. Test No. 1',
        'kelompok_id' => 1,
        'kantor_id' => $kantor->id,
        'provinsi_id' => '11',
        'kota_id' => '1101',
        'kecamatan_id' => '110101',
        'kelurahan_id' => '1101012001',
        'email' => "{$uniq}-pinjaman@ksp.test",
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
        'no_identitas' => uniqid('33'),
        'npwp' => '-',
        'ibu' => 'Ibu Uji',
        'foto' => 'anggota/foto-default.jpg',
        'pengurus' => 0,
        'pengawas' => 0,
        'status' => 1,
        'user_id' => $user->id,
    ]);

    $jenis = PinjamanProduk::create([
        'kode' => "PRJ-{$uniq}",
        'nama' => "Produk Pinjaman Uji {$uniq}",
        'account_id' => 1,
        'bunga' => '1.5',
        'account_bunga' => '',
        'ditangguhkan' => '0',
        'account_ditangguhkan' => '',
        'kas' => 'KAS',
        'insentif' => '0',
        'simpanan' => '0',
        'swp_cair' => '0',
        'swp_angsur' => '0',
        'swp_persen' => '0',
        'nominal_simpanan' => '0',
        'simpanan_pokok' => '0',
        'nominal_simpanan_pokok' => '0',
        'toleransi' => '0',
        'angsuran' => 'ANGSURAN',
        'user_id' => $user->id,
    ]);

    $pinjaman = null;
    if (! ($overrides['tanpaPinjaman'] ?? false)) {
        $pinjaman = Pinjaman::create([
            'tanggal' => '2026-01-10',
            'no_pinjaman' => "PJF-{$uniq}",
            'proposal_id' => 0,
            'anggota_id' => $anggota->id,
            'jaminan_id' => 0,
            'jenis_id' => $jenis->id,
            'marketing_id' => $user->id,
            'sektor_id' => 0,
            'angsuran' => '0',
            'plafon' => '5000000',
            'nominal_angsuran' => '0',
            'bunga' => '1.5',
            'jangka_waktu' => '12',
            'periode' => '1',
            'satuan' => 'bulan',
            'pembayaran' => 'tunai',
            'manual' => '0',
            'tabungan_id' => 0,
            'kode_id' => 0,
            'kode_koreksi' => '',
            'swp_id' => 0,
            'spp_id' => 0,
            'angsuranke' => '0',
            'rekening_koran' => '',
            'cair_simpanan' => '0',
            'sms' => '1',
            'aktif' => '1',
            'kantor_id' => $kantor->id,
            'user_id' => $user->id,
        ]);
    }

    return compact('uniq', 'kantor', 'anggota', 'jenis', 'pinjaman');
}

function pinjamanBersihkanData(): void
{
    Pinjaman::whereIn(
        'kantor_id',
        Kantor::where('kode', 'LIKE', 'KPJ-%')->select('id')
    )->delete();
    PinjamanProduk::where('kode', 'LIKE', 'PRJ-%')->delete();
    Anggota::where('no_anggota', 'LIKE', 'TESTP-%')->delete();
    Kantor::where('kode', 'LIKE', 'KPJ-%')->delete();
}
