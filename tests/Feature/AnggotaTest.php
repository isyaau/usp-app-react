<?php

use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\User;
use Illuminate\Http\UploadedFile;

/**
 * Feature test modul Anggota (Inertia).
 *
 * Test berjalan terhadap database aktif (pgsql dev) namun SELF-CONTAINED:
 * semua test yang membutuhkan record membuat barisnya sendiri dengan
 * penanda khusus lalu membersihkannya, sehingga tidak mengubah data lain.
 */

function anggotaTestUser(): User
{
    return User::updateOrCreate(
        ['email' => 'smoketest@ksp.test'],
        [
            'nama' => 'Smoke Tester',
            'username' => 'smoketest',
            'password' => Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'superadmin',
        ]
    );
}

function buatAnggotaUji(array $overrides = []): Anggota
{
    $uniq = uniqid('t');

    // Kanton uji dibuat sendiri agar test tidak bergantung pada data existing.
    $kantor = Kantor::create([
        'kode' => "KT-{$uniq}",
        'nama_kantor' => "Kantor Uji {$uniq}",
        'alamat_kantor' => 'Jl. Test No. 1',
        'pejabat' => 'Pejabat Uji',
        'jabatan' => 'Kepala Kantor',
        'bendahara' => 'Bendahara Uji',
        'user_id' => anggotaTestUser()->id,
    ]);

    return Anggota::create([
        'no_anggota' => "TEST-{$uniq}",
        'pin' => '123456',
        'nama' => 'Anggota Uji '.$uniq,
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
        'no_identitas' => '3170000000000001',
        'npwp' => '-',
        'ibu' => 'Ibu Test',
        'pengurus' => 0,
        'pengawas' => 0,
        'status' => 1,
        'foto' => 'anggota/foto-default.jpg',
        'user_id' => anggotaTestUser()->id,
        ...$overrides,
    ]);
}

function hapusAnggotaUji(): void
{
    Anggota::where('no_anggota', 'LIKE', 'TEST-%')->delete();

    // Bersihkan kantor uji yang dibuat oleh buatAnggotaUji.
    Kantor::where('kode', 'LIKE', 'KT-%')
        ->where('nama_kantor', 'LIKE', 'Kantor Uji %')
        ->delete();
}

test('index memuat komponen Inertia dan data terpaginasi', function () {
    $response = $this->actingAs(anggotaTestUser())
        ->get(route('superadmin.anggota'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
            ->component('Superadmin/Anggota/Index')
            ->has('anggota.data')
            ->has('filters.search')
    );
});

test('create memuat komponen beserta opsi kelompok dan kantor', function () {
    $response = $this->actingAs(anggotaTestUser())
        ->get(route('superadmin.anggota.create'));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
            ->component('Superadmin/Anggota/Create')
            ->has('kelompoks')
            ->has('kantors')
    );
});

test('show memuat komponen dengan data yang tepat', function () {
    $anggota = buatAnggotaUji();

    $response = $this->actingAs(anggotaTestUser())
        ->get(route('superadmin.anggota.show', $anggota));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
            ->component('Superadmin/Anggota/Show')
            ->where('anggotaData.no_anggota', $anggota->no_anggota)
            ->etc()
    );

    hapusAnggotaUji();
});

test('edit memuat komponen dengan data awal', function () {
    $anggota = buatAnggotaUji();

    $response = $this->actingAs(anggotaTestUser())
        ->get(route('superadmin.anggota.edit', $anggota));

    $response->assertStatus(200);
    $response->assertInertia(
        fn ($page) => $page
            ->component('Superadmin/Anggota/Edit')
            ->where('anggotaData.no_anggota', $anggota->no_anggota)
    );

    hapusAnggotaUji();
});

test('store gagal validasi ketika payload kosong', function () {
    $response = $this->actingAs(anggotaTestUser())
        ->from(route('superadmin.anggota.create'))
        ->post(route('superadmin.anggota.store'), []);

    $response->assertStatus(302);
    $response->assertSessionHasErrors([
        'no_anggota',
        'nama',
        'provinsi_id',
        'email',
        'foto',
    ]);
});

test('update bisa mengubah nama anggota uji', function () {
    $anggota = buatAnggotaUji();

    $payload = collect($anggota->getAttributes())
        ->except(['id', 'created_at', 'updated_at'])
        ->all();

    // Foto tidak dikirim saat update — validasi nullable, jadi unset
    unset($payload['foto']);

    // Field lokasi wajib ada di validasi — isi kode contoh hanya jika kolomnya null
    $payload['provinsi_id'] ??= '11';
    $payload['kota_id'] ??= '11.01';
    $payload['kecamatan_id'] ??= '11.01.01';
    $payload['kelurahan_id'] ??= '11.01.01.1001';

    $payload['nama'] = $anggota->nama.' UPDATED';

    $response = $this->actingAs(anggotaTestUser())
        ->put(route('superadmin.anggota.update', $anggota), $payload);

    $response->assertSessionHasNoErrors();
    $response->assertStatus(302);

    expect(Anggota::find($anggota->id)->nama)->toBe($payload['nama']);

    hapusAnggotaUji();
});

test('destroy menghapus anggota uji', function () {
    $anggota = buatAnggotaUji(['foto' => 'anggota/foto-default.jpg']);

    $response = $this->actingAs(anggotaTestUser())
        ->delete(route('superadmin.anggota.destroy', $anggota));

    $response->assertStatus(302);
    expect(Anggota::find($anggota->id))->toBeNull();

    hapusAnggotaUji();
});

test('template export dapat diunduh sebagai xlsx', function () {
    $response = $this->actingAs(anggotaTestUser())
        ->get(route('superadmin.anggota.template'));

    $response->assertStatus(200);
});

test('import menolak file bukan excel', function () {
    $response = $this->actingAs(anggotaTestUser())
        ->post(route('superadmin.anggota.import'), [
            'file' => UploadedFile::fake()->createWithContent('data.txt', 'bukan excel'),
        ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('file');
});
