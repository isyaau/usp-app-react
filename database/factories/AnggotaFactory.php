<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Kelompok;
use App\Models\Kantor;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Anggota>
 */
class AnggotaFactory extends Factory
{
    public function definition(): array
    {
        // Ambil random kelompok dan kantor
        $kelompokId = Kelompok::inRandomOrder()->first()?->id ?? 1;
        $kantorId = Kantor::inRandomOrder()->first()?->id ?? 1;

        $jenisKelamin = $this->faker->randomElement(['Laki-laki', 'Perempuan']);

        return [
            'no_anggota'           => 'KP-' . $this->faker->unique()->numerify('######'),
            'nama'                 => $this->faker->name($jenisKelamin === 'Laki-laki' ? 'male' : 'female'),
            'alamat'               => $this->faker->address(),
            'kelompok_id'          => $kelompokId,
            'pin'                  => $this->faker->numerify('######'),
            'provinsi_id'          => $this->faker->numberBetween(1, 34),
            'kota_id'              => $this->faker->numberBetween(1, 500),
            'kecamatan_id'         => $this->faker->numberBetween(1000, 9999),
            'kelurahan_id'         => $this->faker->numberBetween(100000, 999999),
            'email'                => $this->faker->unique()->safeEmail(),
            'tempat_lahir'         => $this->faker->city(),
            'tgl_lahir'            => $this->faker->date('Y-m-d', '2005-01-01'),
            'jenis_kelamin'        => $jenisKelamin,
            'agama'                => $this->faker->randomElement(['ISLAM', 'KRISTEN', 'KATOLIK', 'HINDU', 'BUDHA']),
            'pekerjaan'            => $this->faker->jobTitle(),
            'pendidikan'           => $this->faker->randomElement(['SD', 'SMP', 'SMA', 'S1', 'S2', 'S3']),
            'status_perkawinan'    => $this->faker->randomElement(['Belum Kawin', 'Kawin', 'Cerai']),
            'pasangan'             => null,
            'telepon'              => $this->faker->phoneNumber(),
            'no_hp'                => $this->faker->phoneNumber(),
            'jenis_identitas'      => 'KTP',
            'no_identitas'         => $this->faker->numerify('350101##########'),
            'npwp'                 => null,
            'ibu'                  => $this->faker->name('female'),
            'foto'                 => 'anggota/default.png',
            'pengurus'             => 0,
            'pengawas'             => 0,
            'status'               => 1,
            'kantor_id'            => $kantorId,
            'user_id'              => 1,
        ];
    }
}
