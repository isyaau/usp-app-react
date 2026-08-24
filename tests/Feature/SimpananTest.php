<?php

use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\Marketing;
use App\Models\Simpanan;
use App\Models\SimpananJenis;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * Feature test modul Data Simpanan (rekening simpanan anggota).
 * Self-contained: membuat sendiri seluruh data uji dan membersihkannya di akhir.
 */
beforeEach(function () {
    $this->admin = simpananTestUser();
});

afterEach(function () {
    simpananBersihkanData();
});

it('index memuat komponen Inertia', function () {
    $this->actingAs($this->admin)
        ->get(route('superadmin.simpanan'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Superadmin/Simpanan/Index'));
});

it('create memuat komponen Inertia beserta opsi master', function () {
    $this->actingAs($this->admin)
        ->get(route('superadmin.simpanan.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Superadmin/Simpanan/Create')
            ->has('jenisOptions')
            ->has('marketingOptions')
            ->has('kantorOptions')
            ->has('anggotaOptions'));
});

it('store membuat rekening simpanan', function () {
    $f = simpananFixtures();

    $payload = [
        'no_rekening' => 'REK-SIMPANAN-BARU',
        'anggota_id' => $f['anggota']->id,
        'jenis_id' => $f['jenis']->id,
        'marketing_id' => $f['marketing']->id,
        'bunga' => '5',
        'nominal_setor' => '1500000',
        'aktif' => true,
        'sms' => false,
        'blokir_simpanan' => false,
        'blokir_nominal' => false,
        'blokir_tgl' => false,
        'kantor_id' => $f['kantor']->id,
    ];

    $this->actingAs($this->admin)
        ->post(route('superadmin.simpanan.store'), $payload)
        ->assertRedirect(route('superadmin.simpanan'));

    $rek = Simpanan::where('no_rekening', 'REK-SIMPANAN-BARU')->first();

    expect($rek)->not->toBeNull()
        ->and((int) $rek->anggota_id)->toBe($f['anggota']->id)
        ->and((int) $rek->jenis_id)->toBe($f['jenis']->id)
        ->and($rek->nominal_setor)->toBe('1500000')
        ->and($rek->aktif)->toBe('1')
        ->and($rek->sms)->toBe('0')
        ->and($rek->user_id)->toBe($this->admin->id);
});

it('store menolak no_rekening duplikat', function () {
    $f = simpananFixtures();

    $this->actingAs($this->admin)
        ->post(route('superadmin.simpanan.store'), [
            'no_rekening' => $f['rekening']->no_rekening,
            'anggota_id' => $f['anggota']->id,
            'jenis_id' => $f['jenis']->id,
        ])
        ->assertSessionHasErrors('no_rekening');
});

it('store menyimpan tanda tangan mode upload', function () {
    Storage::fake('public');

    $f = simpananFixtures();

    // PNG 1x1 transparan.
    $png = base64_decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
    );

    $this->actingAs($this->admin)
        ->post(route('superadmin.simpanan.store'), [
            'no_rekening' => 'REK-TTD-UPLOAD',
            'anggota_id' => $f['anggota']->id,
            'jenis_id' => $f['jenis']->id,
            'mode' => 'upload',
            'uploaded_signature' => Illuminate\Http\Testing\File::fake()->createWithContent(
                'ttd.png',
                $png
            ),
        ])
        ->assertRedirect(route('superadmin.simpanan'));

    $rek = Simpanan::where('no_rekening', 'REK-TTD-UPLOAD')->first();

    expect($rek->ttd)->not->toBeNull()
        ->and($rek->ttd)->toStartWith('ttd/')
        ->and(Storage::disk('public')->exists($rek->ttd))->toBeTrue();

    Storage::disk('public')->delete($rek->ttd);
});

it('update mengubah data rekening tanpa mengubah TTD lama', function () {
    $f = simpananFixtures();

    $this->actingAs($this->admin)
        ->put(route('superadmin.simpanan.update', $f['rekening']), [
            'no_rekening' => $f['rekening']->no_rekening,
            'anggota_id' => $f['anggota']->id,
            'jenis_id' => $f['jenis']->id,
            'qq' => 'Kuasa Uji',
            'nominal_setor' => '2000000',
            'aktif' => false,
            'sms' => true,
            'blokir_simpanan' => true,
            'blokir_nominal' => false,
            'blokir_tgl' => false,
        ])
        ->assertRedirect(route('superadmin.simpanan'));

    $f['rekening']->refresh();

    expect($f['rekening']->qq)->toBe('Kuasa Uji')
        ->and($f['rekening']->nominal_setor)->toBe('2000000')
        ->and($f['rekening']->aktif)->toBe('0')
        ->and($f['rekening']->sms)->toBe('1')
        ->and($f['rekening']->blokir_simpanan)->toBe('1');
});

it('show memuat komponen Inertia', function () {
    $f = simpananFixtures();

    $this->actingAs($this->admin)
        ->get(route('superadmin.simpanan.show', $f['rekening']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Superadmin/Simpanan/Show')
            ->has('simpananData')
            ->has('signatureUrl'));
});

it('destroy menghapus rekening dan file TTD', function () {
    Storage::fake('public');

    $f = simpananFixtures(['denganTtd' => true]);

    expect(Storage::disk('public')->exists($f['rekening']->ttd))->toBeTrue();

    $this->actingAs($this->admin)
        ->delete(route('superadmin.simpanan.destroy', $f['rekening']))
        ->assertRedirect(route('superadmin.simpanan'));

    expect(Simpanan::find($f['rekening']->id))->toBeNull()
        ->and(Storage::disk('public')->exists($f['rekening']->ttd))->toBeFalse();
});

/* ------------------------------------------------------------------ */
/* Helper                                                              */
/* ------------------------------------------------------------------ */

function simpananTestUser(): User
{
    return User::updateOrCreate(
        ['email' => 'admin-simpanan@ksp.test'],
        [
            'nama' => 'Admin Simpanan Uji',
            'username' => 'adminsimpanan',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]
    );
}

function simpananFixtures(array $overrides = []): array
{
    $uniq = uniqid();
    $user = simpananTestUser();

    $marketing = Marketing::create([
        'kode' => "MKT-{$uniq}",
        'nama' => "Marketing Uji {$uniq}",
        'aktif' => 1,
        'user_id' => $user->id,
    ]);

    $kantor = Kantor::create([
        'kode' => "KT-{$uniq}",
        'nama_kantor' => "Kantor Uji {$uniq}",
        'alamat_kantor' => 'Jl. Test No. 1',
        'pejabat' => 'Pejabat Uji',
        'jabatan' => 'Kepala Kantor',
        'bendahara' => 'Bendahara Uji',
        'user_id' => $user->id,
    ]);

    $anggota = Anggota::create([
        'no_anggota' => "TESTS-{$uniq}",
        'pin' => '123456',
        'nama' => 'Anggota Simpanan Uji '.$uniq,
        'alamat' => 'Jl. Test No. 1',
        'kelompok_id' => 1,
        'kantor_id' => $kantor->id,
        'provinsi_id' => '11',
        'kota_id' => '1101',
        'kecamatan_id' => '110101',
        'kelurahan_id' => '1101012001',
        'email' => "{$uniq}-simpanan@ksp.test",
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
        'no_identitas' => uniqid('32'),
        'npwp' => '-',
        'ibu' => 'Ibu Uji',
        'foto' => 'anggota/foto-default.jpg',
        'pengurus' => 0,
        'pengawas' => 0,
        'status' => 1,
        'user_id' => $user->id,
    ]);

    $jenis = SimpananJenis::create([
        'kode' => "JS-{$uniq}",
        'nama' => "Produk Simpanan Uji {$uniq}",
        'bunga' => '5',
        'jenis' => 'SUKARELA',
        'user_id' => $user->id,
    ]);

    $rekening = null;
    if (! ($overrides['tanpaRekening'] ?? false)) {
        $ttd = null;
        if ($overrides['denganTtd'] ?? false) {
            $ttd = "ttd/uji_{$uniq}.png";
            Storage::disk('public')->put($ttd, 'dummy-png');
        }

        $rekening = Simpanan::create([
            'no_rekening' => "REKS-{$uniq}",
            'tanggal' => now()->toDateString(),
            'anggota_id' => $anggota->id,
            'jenis_id' => $jenis->id,
            'marketing_id' => $marketing->id,
            'ttd' => $ttd,
            'aktif' => 1,
            'kantor_id' => $kantor->id,
            'user_id' => $user->id,
        ]);
    }

    return compact('uniq', 'marketing', 'kantor', 'anggota', 'jenis', 'rekening');
}

function simpananBersihkanData(): void
{
    // Hapus TTD milik rekening uji.
    $ids = Simpanan::whereIn('anggota_id', Anggota::where('no_anggota', 'LIKE', 'TESTS-%')->select('id'))
        ->pluck('ttd')
        ->filter();

    foreach ($ids as $ttd) {
        if (str_starts_with($ttd, 'ttd/')) {
            Storage::disk('public')->delete($ttd);
        }
    }

    Simpanan::whereIn('anggota_id', Anggota::where('no_anggota', 'LIKE', 'TESTS-%')->select('id'))->delete();
    SimpananJenis::where('kode', 'LIKE', 'JS-%')->delete();
    Marketing::where('kode', 'LIKE', 'MKT-%')->delete();
    Anggota::where('no_anggota', 'LIKE', 'TESTS-%')->delete();
    Kantor::where('kode', 'LIKE', 'KT-%')->delete();
}
