<?php

use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\Marketing;
use App\Models\PinjamanProduk;
use App\Models\Proposal;
use App\Models\ProposalBiaya;
use App\Models\User;
use Database\Seeders\ProposalSeeder;

/**
 * Feature test modul Proposal Pinjaman.
 * Self-contained: membuat sendiri seluruh data uji dan membersihkannya di akhir.
 */
beforeEach(function () {
    $this->admin = proposalTestUser();
    $this->uniq = uniqid();
});

afterEach(function () {
    proposalBersihkanData();
});

it('index memuat komponen Inertia', function () {
    $this->actingAs($this->admin)
        ->get(route('superadmin.pinjaman.proposal'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Superadmin/Proposal/Index'));
});

it('show memuat detail proposal beserta biaya & debitur', function () {
    $f = proposalTestFixtures($this->uniq);

    $this->actingAs($this->admin)
        ->get(route('superadmin.pinjaman.proposal.show', $f['proposal']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Superadmin/Proposal/Show')
            ->has('proposal.biaya')
            ->has('proposal.anggota')
            ->where('proposal.jenis_pinjaman.nama', $f['produk']->nama));
});

it('cetak proposal menghasilkan pdf', function () {
    $f = proposalTestFixtures($this->uniq);

    $res = $this->actingAs($this->admin)
        ->get(route('superadmin.pinjaman.proposal.cetak', $f['proposal']));

    $res->assertOk();
    expect($res->headers->get('Content-Type'))->toContain('application/pdf')
        ->and($res->streamedContent())->toStartWith('%PDF');
});

it('seeder membuat 100 data proposal', function () {
    $this->seed(ProposalSeeder::class);

    $proposal = Proposal::where('no_bukti', 'LIKE', 'PROP-SEED-%');

    expect($proposal->count())->toBe(100);

    $contoh = $proposal->first();
    expect($contoh)->not->toBeNull()
        ->and(ProposalBiaya::where('proposal_id', $contoh->id)->count())->toBeGreaterThan(0)
        ->and((int) $contoh->total_terima)->toBeLessThanOrEqual((int) $contoh->plafon);
});

/* ------------------------------------------------------------------ */
/* Helper                                                              */
/* ------------------------------------------------------------------ */

function proposalTestUser(): User
{
    return User::updateOrCreate(
        ['email' => 'admin-proposal@ksp.test'],
        [
            'nama' => 'Admin Proposal Uji',
            'username' => 'adminproposal',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]
    );
}

function proposalTestFixtures(string $uniq): array
{
    $user = proposalTestUser();

    $kantor = Kantor::create([
        'kode' => "KP2-{$uniq}",
        'nama_kantor' => "Kantor Proposal Uji {$uniq}",
        'alamat_kantor' => 'Jl. Test No. 1',
        'pejabat' => 'Pejabat Uji',
        'jabatan' => 'Kepala Kantor',
        'bendahara' => 'Bendahara Uji',
        'user_id' => $user->id,
    ]);

    $anggota = Anggota::create([
        'no_anggota' => "TESTPR-{$uniq}",
        'pin' => '123456',
        'nama' => 'Anggota Proposal Uji '.$uniq,
        'alamat' => 'Jl. Test No. 1',
        'kelompok_id' => 1,
        'kantor_id' => $kantor->id,
        'provinsi_id' => '11',
        'kota_id' => '1101',
        'kecamatan_id' => '110101',
        'kelurahan_id' => '1101012001',
        'email' => "{$uniq}-proposal@ksp.test",
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

    $produk = PinjamanProduk::create([
        'kode' => "PRP2-{$uniq}",
        'nama' => "Produk Proposal Uji {$uniq}",
        'account_id' => 1,
        'bunga' => '12',
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
        'angsuran' => 'Anuitas',
        'user_id' => $user->id,
    ]);

    $marketing = Marketing::create([
        'kode' => "MKTP-{$uniq}",
        'nama' => 'Marketing Proposal Uji',
        'alamat' => 'Jl. Test No. 1',
        'no_ktp' => '3501010000000000',
        'telepon' => '081000000000',
        'no_hp' => '081000000000',
        'aktif' => '1',
        'kantor_id' => $kantor->id,
        'user_id' => $user->id,
    ]);

    $proposal = Proposal::create([
        'tanggal' => '2026-01-10',
        'no_bukti' => "PRP-{$uniq}",
        'anggota_id' => $anggota->id,
        'jenis_id' => $produk->id,
        'marketing_id' => $marketing->id,
        'plafon' => '10000000',
        'bunga' => '12',
        'jangka_waktu' => '12',
        'satuan' => 'bulan',
        'bayar_pokok_per' => '',
        'pembayaran' => 'per-jangka',
        'setiap_saat' => '0',
        'jenis_angsuran' => 'Anuitas',
        'nominal_angsuran' => '884799',
        'penggunaan_kredit' => 'Modal Usaha',
        'jaminan' => 'BPKB Kendaraan',
        'total_biaya' => '25000',
        'total_terima' => '9900000',
        'status' => '1',
        'kantor_id' => $kantor->id,
        'user_id' => $user->id,
    ]);

    ProposalBiaya::create([
        'proposal_id' => $proposal->id,
        'component_id' => '0',
        'nama' => 'Biaya Administrasi',
        'nominal' => '25000',
        'persen' => '0',
        'account_id' => '0',
        'is_deducted_from_disbursement' => '0',
        'user_id' => $user->id,
    ]);

    return compact('kantor', 'anggota', 'produk', 'marketing', 'proposal');
}

function proposalBersihkanData(): void
{
    $proposalIds = Proposal::where('no_bukti', 'LIKE', 'PRP-%')
        ->orWhere('no_bukti', 'LIKE', 'PROP-SEED-%')
        ->pluck('id');

    if ($proposalIds->isNotEmpty()) {
        ProposalBiaya::whereIn('proposal_id', $proposalIds)->delete();
        Proposal::whereIn('id', $proposalIds)->delete();
    }

    Marketing::where('kode', 'LIKE', 'MKTP-%')->delete();
    PinjamanProduk::where('kode', 'LIKE', 'PRP2-%')->delete();
    Anggota::where('no_anggota', 'LIKE', 'TESTPR-%')->delete();
    Kantor::where('kode', 'LIKE', 'KP2-%')->delete();
}