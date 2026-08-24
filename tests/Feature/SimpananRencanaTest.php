<?php

use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\Simpanan;
use App\Models\SimpananJenis;
use App\Models\SimpananRencana;
use App\Models\SimpananRencanaDetail;
use App\Models\User;

/**
 * Feature test modul Simpanan Rencana.
 * Self-contained: membuat sendiri seluruh data uji dan membersihkannya di akhir.
 * Catatan: aplikasi lama hanya punya Create & Delete (Edit/Show stub kosong).
 */
beforeEach(function () {
    $this->admin = rencanaTestUser();
});

afterEach(function () {
    rencanaBersihkanData();
});

it('index memuat komponen Inertia', function () {
    $this->actingAs($this->admin)
        ->get(route('superadmin.simpanan.rencana'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Superadmin/SimpananRencana/Index'));
});

it('create memuat komponen Inertia beserta opsi kantor & rekening', function () {
    $f = rencanaFixtures(['tanpaRencana' => true]);

    $this->actingAs($this->admin)
        ->get(route('superadmin.simpanan.rencana.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Superadmin/SimpananRencana/Create')
            ->has('kantorOptions')
            ->has('rekeningOptions'));
});

it('store membuat rencana beserta detail rekening terpilih', function () {
    $f = rencanaFixtures(['tanpaRencana' => true]);

    $payload = [
        'tanggal_mulai' => '2026-01-01',
        'tanggal_jatuhtempo' => '2026-07-01',
        'no_bukti' => "RNC-{$f['uniq']}",
        'jangka_waktu' => '6',
        'satuan' => 'bulan',
        'nominal' => '500000',
        'bunga' => '4',
        'keterangan' => 'Rencana uji',
        'kantor_id' => $f['kantor']->id,
        'simpanan_ids' => [$f['rekening']->id],
    ];

    $this->actingAs($this->admin)
        ->post(route('superadmin.simpanan.rencana.store'), $payload)
        ->assertRedirect(route('superadmin.simpanan.rencana'));

    $rencana = SimpananRencana::where('no_bukti', "RNC-{$f['uniq']}")->first();

    expect($rencana)->not->toBeNull()
        ->and($rencana->nominal)->toBe('500000')
        ->and($rencana->bunga)->toBe('4')
        ->and($rencana->user_id)->toBe($this->admin->id)
        ->and($rencana->details()->where('simpanan_id', $f['rekening']->id)->exists())->toBeTrue();
});

it('store menolak tanpa pemilihan rekening', function () {
    $f = rencanaFixtures(['tanpaRencana' => true]);

    $this->actingAs($this->admin)
        ->post(route('superadmin.simpanan.rencana.store'), [
            'tanggal_mulai' => '2026-01-01',
            'tanggal_jatuhtempo' => '2026-07-01',
            'no_bukti' => "RNC-{$f['uniq']}",
            'jangka_waktu' => '6',
            'satuan' => 'bulan',
            'nominal' => '500000',
            'kantor_id' => $f['kantor']->id,
            'simpanan_ids' => [],
        ])
        ->assertSessionHasErrors('simpanan_ids');

    expect(SimpananRencana::where('no_bukti', "RNC-{$f['uniq']}")->exists())->toBeFalse();
});

it('store menolak no_bukti duplikat dan jatuhtempo sebelum mulai', function () {
    $f = rencanaFixtures();

    $this->actingAs($this->admin)
        ->post(route('superadmin.simpanan.rencana.store'), [
            'tanggal_mulai' => '2026-01-01',
            'tanggal_jatuhtempo' => '2025-01-01',
            'no_bukti' => $f['rencana']->no_bukti,
            'jangka_waktu' => '6',
            'satuan' => 'bulan',
            'nominal' => '500000',
            'kantor_id' => $f['kantor']->id,
            'simpanan_ids' => [$f['rekening']->id],
        ])
        ->assertSessionHasErrors(['no_bukti', 'tanggal_jatuhtempo']);
});

it('destroy menghapus rencana dan detailnya', function () {
    $f = rencanaFixtures();

    $detailId = $f['rencana']->details()->create([
        'simpanan_id' => $f['rekening']->id,
        'user_id' => $this->admin->id,
    ])->id;

    $this->actingAs($this->admin)
        ->delete(route('superadmin.simpanan.rencana.destroy', $f['rencana']))
        ->assertRedirect(route('superadmin.simpanan.rencana'));

    expect(SimpananRencana::find($f['rencana']->id))->toBeNull()
        ->and(SimpananRencanaDetail::find($detailId))->toBeNull();
});

/* ------------------------------------------------------------------ */
/* Helper                                                              */
/* ------------------------------------------------------------------ */

function rencanaTestUser(): User
{
    return User::updateOrCreate(
        ['email' => 'admin-rencana@ksp.test'],
        [
            'nama' => 'Admin Rencana Uji',
            'username' => 'adminrencana',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]
    );
}

function rencanaFixtures(array $overrides = []): array
{
    $uniq = uniqid();
    $user = rencanaTestUser();

    $kantor = Kantor::create([
        'kode' => "KR-{$uniq}",
        'nama_kantor' => "Kantor Rencana Uji {$uniq}",
        'alamat_kantor' => 'Jl. Test No. 1',
        'pejabat' => 'Pejabat Uji',
        'jabatan' => 'Kepala Kantor',
        'bendahara' => 'Bendahara Uji',
        'user_id' => $user->id,
    ]);

    $anggota = Anggota::create([
        'no_anggota' => "TESTR-{$uniq}",
        'pin' => '123456',
        'nama' => 'Anggota Rencana Uji '.$uniq,
        'alamat' => 'Jl. Test No. 1',
        'kelompok_id' => 1,
        'kantor_id' => $kantor->id,
        'provinsi_id' => '11',
        'kota_id' => '1101',
        'kecamatan_id' => '110101',
        'kelurahan_id' => '1101012001',
        'email' => "{$uniq}-rencana@ksp.test",
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

    $jenis = SimpananJenis::create([
        'kode' => "JR-{$uniq}",
        'nama' => "Jenis Rencana Uji {$uniq}",
        'jenis' => 'SUKARELA',
        'user_id' => $user->id,
    ]);

    $rekening = Simpanan::create([
        'no_rekening' => "REKR-{$uniq}",
        'tanggal' => now()->toDateString(),
        'anggota_id' => $anggota->id,
        'jenis_id' => $jenis->id,
        'marketing_id' => $user->id,
        'aktif' => 1,
        'kantor_id' => $kantor->id,
        'user_id' => $user->id,
    ]);

    $rencana = null;
    if (! ($overrides['tanpaRencana'] ?? false)) {
        $rencana = SimpananRencana::create([
            'tanggal_mulai' => '2026-01-01',
            'tanggal_jatuhtempo' => '2026-07-01',
            'no_bukti' => "RNCF-{$uniq}",
            'jangka_waktu' => '6',
            'satuan' => 'bulan',
            'nominal' => '250000',
            'bunga' => '3',
            'keterangan' => '',
            'kantor_id' => $kantor->id,
            'user_id' => $user->id,
        ]);
    }

    return compact('uniq', 'kantor', 'anggota', 'jenis', 'rekening', 'rencana');
}

function rencanaBersihkanData(): void
{
    $rencanaIds = SimpananRencana::whereIn(
        'kantor_id',
        Kantor::where('kode', 'LIKE', 'KR-%')->select('id')
    )->pluck('id');

    SimpananRencanaDetail::whereIn('rencana_id', $rencanaIds)->delete();
    SimpananRencana::whereIn('id', $rencanaIds)->delete();

    Simpanan::where('no_rekening', 'LIKE', 'REKR-%')->delete();
    SimpananJenis::where('kode', 'LIKE', 'JR-%')->delete();
    Anggota::where('no_anggota', 'LIKE', 'TESTR-%')->delete();
    Kantor::where('kode', 'LIKE', 'KR-%')->delete();
}
