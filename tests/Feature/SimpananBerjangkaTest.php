<?php

use App\Models\Account;
use App\Models\Anggota;
use App\Models\Deposito;
use App\Models\DepositoJenis;
use App\Models\Kantor;
use App\Models\User;

/**
 * Feature test modul Simpanan Berjangka (produk deposito_jenis + rekening deposito).
 * Fixture dibuat & dibersihkan sendiri (self-contained) mengikuti pola TransaksiSimpananTest.
 */

function berjangkaTestUser(): User
{
    return User::firstOrCreate(
        ['email' => 'smoketest@ksp.test'],
        [
            'name' => 'Smoke Test',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ],
    );
}

function berjangkaTestKantor(): Kantor
{
    $uniq = uniqid('t');

    return Kantor::create([
        'kode' => "KT-{$uniq}",
        'nama_kantor' => "Kantor Uji {$uniq}",
        'alamat_kantor' => 'Jl. Test No. 1',
        'pejabat' => 'Pejabat Uji',
        'jabatan' => 'Kepala Kantor',
        'bendahara' => 'Bendahara Uji',
        'user_id' => berjangkaTestUser()->id,
    ]);
}

function berjangkaTestAccount(array $overrides = []): Account
{
    static $seq = 0;
    $uniq = uniqid('t');

    return Account::create([
        ...$overrides,
        'no_account' => ($overrides['no_account'] ?? ('99'.str_pad((string) (++$seq), 4, '0', STR_PAD_LEFT))),
        'nama' => $overrides['nama'] ?? "Account Uji {$uniq}",
        'tipe' => $overrides['tipe'] ?? 'AKTIVA',
        'user_id' => berjangkaTestUser()->id,
    ]);
}

function berjangkaTestProduk(array $overrides = []): DepositoJenis
{
    $uniq = uniqid('t');
    static $kodeSeq = 0;

    return DepositoJenis::create([
        'kode' => $overrides['kode'] ?? ('DJX-'.++$kodeSeq."-{$uniq}"),
        'nama' => $overrides['nama'] ?? "Produk Uji {$uniq}",
        'account_id' => $overrides['account_id'] ?? berjangkaTestAccount()->id,
        'jangka_waktu' => $overrides['jangka_waktu'] ?? '12',
        'bunga' => $overrides['bunga'] ?? '5',
        'account_bunga' => $overrides['account_bunga'] ?? null,
        'rumus_bunga' => $overrides['rumus_bunga'] ?? null,
        'penalti' => $overrides['penalti'] ?? '2',
        'pajak' => $overrides['pajak'] ?? '10',
        'user_id' => berjangkaTestUser()->id,
    ]);
}

function bersihkanBerjangkaUji(): void
{
    // Hapus deposito uji lalu produk & account ujinya.
    Deposito::whereHas('produk', fn ($q) => $q->where('nama', 'LIKE', 'Produk Uji %'))->delete();
    DepositoJenis::where('nama', 'LIKE', 'Produk Uji %')->delete();
    Account::where('nama', 'LIKE', 'Account Uji %')->delete();
}

beforeEach(function () {
    bersihkanBerjangkaUji();
});

afterEach(function () {
    bersihkanBerjangkaUji();
});

/* ============================================================
   Produk Simpanan Berjangka (deposito_jenis)
   ============================================================ */

test('index produk memuat komponen Inertia dan data terpaginasi', function () {
    berjangkaTestProduk();

    $response = $this->actingAs(berjangkaTestUser())
        ->get(route('superadmin.simpanan-berjangka.produk'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Superadmin/Berjangkaproduk/Index')
            ->has('produk.data'));
});

test('store produk membuat data baru', function () {
    $account = berjangkaTestAccount();

    $response = $this->actingAs(berjangkaTestUser())
        ->post(route('superadmin.simpanan-berjangka.produk.store'), [
            'kode' => 'DJX-NEW-'.uniqid(),
            'nama' => 'Produk Baru Uji '.uniqid(),
            'account_id' => $account->id,
            'jangka_waktu' => '12',
            'bunga' => '5',
            'penalti' => '2',
            'pajak' => '10',
        ]);

    $response->assertRedirect(route('superadmin.simpanan-berjangka.produk'));
    expect(DepositoJenis::where('nama', 'LIKE', 'Produk Baru Uji %')->exists())->toBeTrue();
});

test('update produk bisa mengubah nama', function () {
    $produk = berjangkaTestProduk();

    $response = $this->actingAs(berjangkaTestUser())
        ->put(route('superadmin.simpanan-berjangka.produk.update', $produk), [
            ...$produk->only(['kode']),
            'nama' => 'Produk Revisi '.uniqid(),
            'account_id' => $produk->account_id,
            'bunga' => '6',
        ]);

    $response->assertRedirect(route('superadmin.simpanan-berjangka.produk'));
    expect($produk->fresh()->bunga)->toBe('6');
});

test('destroy produk menghapus data', function () {
    $produk = berjangkaTestProduk();
    $id = $produk->id;

    $response = $this->actingAs(berjangkaTestUser())
        ->delete(route('superadmin.simpanan-berjangka.produk.destroy', $id));

    $response->assertRedirect(route('superadmin.simpanan-berjangka.produk'));
    expect(DepositoJenis::find($id))->toBeNull();
});

/* ============================================================
   Simpanan Berjangka / Deposito
   ============================================================ */

function payloadDepositoUji(array $overrides = []): array
{
    $produk = berjangkaTestProduk();
    $kantor = berjangkaTestKantor();
    $anggota = Anggota::create([
        'no_anggota' => 'TESTD-'.uniqid(),
        'pin' => '123456',
        'nama' => 'Anggota Dep Uji '.uniqid(),
        'alamat' => 'Jl. Test No. 1',
        'kelompok_id' => $overrides['kelompok_id'] ?? 1,
        'kantor_id' => $kantor->id,
        'provinsi_id' => 11,
        'kota_id' => 1101,
        'kecamatan_id' => 110101,
        'kelurahan_id' => 1101012001,
        'email' => uniqid('dep').'@ksp.test',
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
        'status' => 1,
        'foto' => 'anggota/foto-default.jpg',
        'user_id' => berjangkaTestUser()->id,
    ]);

    return [
        ...$overrides,
        '_produk' => $produk,
        '_anggota' => $anggota,
        '_kantor' => $kantor,
    ];
}

test('index simpanan berjangka memuat komponen Inertia', function () {
    $ctx = payloadDepositoUji();
    Deposito::create([
        'tanggal' => now()->toDateString(),
        'no_deposito' => '55.'.now()->format('ym').'.9001',
        'anggota_id' => $ctx['_anggota']->id,
        'jenis_id' => $ctx['_produk']->id,
        'qq' => '-',
        'jangka_waktu' => '12',
        'bunga' => '5',
        'nominal' => '10000000',
        'otomatis' => '0',
        'bayar_bunga' => '1',
        'diawal' => '1',
        'bunga_accrual' => '0',
        'bayar_jatuhtempo' => '1',
        'blokir' => '0',
        'kantor_id' => $ctx['_kantor']->id,
        'user_id' => berjangkaTestUser()->id,
    ]);

    $response = $this->actingAs(berjangkaTestUser())
        ->get(route('superadmin.simpanan-berjangka'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Superadmin/Berjangka/Index')
            ->has('berjangka.data'));

    Anggota::where('no_anggota', 'LIKE', 'TESTD-%')->delete();
});

test('store simpanan berjangka membuat no_deposito otomatis', function () {
    $ctx = payloadDepositoUji();

    $response = $this->actingAs(berjangkaTestUser())
        ->post(route('superadmin.simpanan-berjangka.store'), [
            'tanggal' => now()->toDateString(),
            'anggota_id' => $ctx['_anggota']->id,
            'jenis_id' => $ctx['_produk']->id,
            'jangka_waktu' => '12',
            'bunga' => '5',
            'nominal' => '10000000',
            'otomatis' => true,
            'bayar_bunga' => '1',
            'diawal' => '1',
            'bunga_accrual' => false,
            'bayar_jatuhtempo' => '1',
            'blokir' => false,
            'kantor_id' => $ctx['_kantor']->id,
        ]);

    $response->assertRedirect(route('superadmin.simpanan-berjangka'));

    $deposito = Deposito::where('anggota_id', $ctx['_anggota']->id)->first();
    expect($deposito)->not->toBeNull()
        ->and($deposito->no_deposito)->toMatch('/^55\.\d{8}$/');

    Anggota::where('no_anggota', 'LIKE', 'TESTD-%')->delete();
});

test('store simpanan berjangka gagal bila nominal kosong', function () {
    $ctx = payloadDepositoUji();

    $response = $this->actingAs(berjangkaTestUser())
        ->from(route('superadmin.simpanan-berjangka.create'))
        ->post(route('superadmin.simpanan-berjangka.store'), [
            'tanggal' => now()->toDateString(),
            'anggota_id' => $ctx['_anggota']->id,
            'jenis_id' => $ctx['_produk']->id,
            'jangka_waktu' => '12',
            'bunga' => '5',
            'nominal' => '',
            'bayar_bunga' => '1',
            'diawal' => '1',
            'bayar_jatuhtempo' => '1',
            'kantor_id' => $ctx['_kantor']->id,
        ]);

    $response->assertSessionHasErrors('nominal');

    Anggota::where('no_anggota', 'LIKE', 'TESTD-%')->delete();
});

test('show simpanan berjangka memuat detail', function () {
    $ctx = payloadDepositoUji();
    $deposito = Deposito::create([
        'tanggal' => now()->toDateString(),
        'no_deposito' => '55.'.now()->format('ym').'.9002',
        'anggota_id' => $ctx['_anggota']->id,
        'jenis_id' => $ctx['_produk']->id,
        'qq' => '-',
        'jangka_waktu' => '6',
        'bunga' => '5',
        'nominal' => '5000000',
        'otomatis' => '0',
        'bayar_bunga' => '2',
        'diawal' => '2',
        'bunga_accrual' => '0',
        'bayar_jatuhtempo' => '2',
        'blokir' => '0',
        'kantor_id' => $ctx['_kantor']->id,
        'user_id' => berjangkaTestUser()->id,
    ]);

    $response = $this->actingAs(berjangkaTestUser())
        ->get(route('superadmin.simpanan-berjangka.show', $deposito));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Superadmin/Berjangka/Show')
            ->where('berjangkaData.no_deposito', $deposito->no_deposito));

    Anggota::where('no_anggota', 'LIKE', 'TESTD-%')->delete();
});

test('destroy simpanan berjangka menghapus data', function () {
    $ctx = payloadDepositoUji();
    $deposito = Deposito::create([
        'tanggal' => now()->toDateString(),
        'no_deposito' => '55.'.now()->format('ym').'.9003',
        'anggota_id' => $ctx['_anggota']->id,
        'jenis_id' => $ctx['_produk']->id,
        'qq' => '-',
        'jangka_waktu' => '6',
        'bunga' => '5',
        'nominal' => '5000000',
        'otomatis' => '0',
        'bayar_bunga' => '1',
        'diawal' => '1',
        'bunga_accrual' => '0',
        'bayar_jatuhtempo' => '1',
        'blokir' => '0',
        'kantor_id' => $ctx['_kantor']->id,
        'user_id' => berjangkaTestUser()->id,
    ]);
    $id = $deposito->id;

    $response = $this->actingAs(berjangkaTestUser())
        ->delete(route('superadmin.simpanan-berjangka.destroy', $id));

    $response->assertRedirect(route('superadmin.simpanan-berjangka'));
    expect(Deposito::find($id))->toBeNull();

    Anggota::where('no_anggota', 'LIKE', 'TESTD-%')->delete();
});
