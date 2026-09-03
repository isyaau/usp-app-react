<?php

namespace Database\Seeders;

use App\Models\Anggota;
use Illuminate\Database\Seeder;

class AnggotaSeeder extends Seeder
{
    use ResolvesAdminUser;

    public function run(): void
    {

        Anggota::factory()->count(1000)->create();
        // Contoh data anggota
        Anggota::create([
            'no_anggota'               => 'KP-267001',
            'nama'                     => 'Alfia',
            'alamat'                   => 'Madiun',
            'kelompok_id'              => '1',
            'pin'                      => '123456',
            'provinsi_id'              => 34,
            'kota_id'                  => 3404,
            'kecamatan_id'             => 340402,
            'kelurahan_id'             => 3404022007,
            'email'                    => 'alfia@example.com',
            'tempat_lahir'             => 'Madiun',
            'tgl_lahir'                => '1995-05-10',
            'jenis_kelamin'            => 'Perempuan',
            'agama'                    => 'ISLAM',
            'pekerjaan'                => 'PELAJAR / MAHASISWA',
            'pendidikan'               => 'S1',
            'status_perkawinan'        => 'Belum Kawin',
            'pasangan'                 => null,
            'telepon'                  => '0351-123456',
            'no_hp'                    => '081234567890',
            'jenis_identitas'          => 'KTP',
            'no_identitas'             => '3501011234567890',
            'npwp'                     => null,
            'ibu'                      => 'Siti Aminah',
            'hutang'                   => null,
            'harga_id'                 => null,
            'foto'                     => 'anggota/default.png',
            'pengurus'                 => 0,
            'pengurus_jabatan'         => null,
            'tgl_pengurus_diangkat'    => null,
            'pengurus_berhenti'        => null,
            'tgl_pengurus_berhenti'    => null,
            'pengawas'                 => 0,
            'pengawas_jabatan'         => null,
            'tgl_pengawas_diangkat'    => null,
            'pengawas_berhenti'        => null,
            'tgl_pengawas_berhenti'    => null,
            'waris1'                   => null,
            'hubungan_waris1'          => null,
            'waris2'                   => null,
            'hubungan_waris2'          => null,
            'blokir_pinjaman'          => null,
            'bagian_id'                => null,
            'nomor_rekening'           => null,
            'status'                   => 1,
            'tgl_anggota_berhenti'     => null,
            'anggota_berhenti'         => null,
            'kantor_id'                => '1', // contoh kantor
            'user_id'                  => $this->adminUserId(), // user pembuat
        ]);
    }
}
