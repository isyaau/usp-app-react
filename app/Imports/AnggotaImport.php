<?php

namespace App\Imports;

use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\Kelompok;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

/**
 * Import data anggota sesuai template AnggotaTemplateExport.
 * Kolom "Kelompok" dan "Kantor" dicocokkan berdasarkan nama.
 */
class AnggotaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected $user_id;

    /** Cache resolusi nama => id agar query tidak berulang. */
    protected array $kelompokCache = [];

    protected array $kantorCache = [];

    protected array $seenNo = [];

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function model(array $row)
    {
        $rowNumber = $row['__row'] ?? 0;

        $no = trim((string) ($row['nomor_anggota'] ?? ''));

        // Cek duplikat di dalam file
        if (in_array($no, $this->seenNo)) {
            $this->fail($rowNumber, 'nomor_anggota', "Nomor anggota {$no} duplikat di file");
        }
        $this->seenNo[] = $no;

        $kelompokId = $this->resolveKelompok(trim((string) ($row['kelompok'] ?? '')), $rowNumber);
        $kantorId = $this->resolveKantor(trim((string) ($row['kantor'] ?? '')), $rowNumber);

        return new Anggota([
            'kelompok_id' => $kelompokId,
            'kantor_id' => $kantorId,
            'no_anggota' => $no,
            'pin' => trim((string) ($row['pin'] ?? '')),
            'nama' => trim((string) ($row['nama'] ?? '')),
            'email' => trim((string) ($row['email'] ?? '')) ?: null,
            'alamat' => trim((string) ($row['alamat'] ?? '')),
            'tempat_lahir' => trim((string) ($row['tempat_lahir'] ?? '')),
            'tgl_lahir' => $this->normalizeDate($row['tanggal_lahir'] ?? null),
            'jenis_kelamin' => trim((string) ($row['jenis_kelamin'] ?? '')),
            'agama' => strtoupper(trim((string) ($row['agama'] ?? ''))),
            'telepon' => trim((string) ($row['telepon'] ?? '')),
            'no_hp' => trim((string) ($row['nomor_hp'] ?? '')),
            'pendidikan' => strtoupper(trim((string) ($row['pendidikan'] ?? ''))),
            'pekerjaan' => strtoupper(trim((string) ($row['pekerjaan'] ?? ''))),
            'status_perkawinan' => trim((string) ($row['status_perkawinan'] ?? '')),
            'pasangan' => trim((string) ($row['nama_pasangan'] ?? '')) ?: null,
            'ibu' => trim((string) ($row['nama_ibu'] ?? '')),
            'jenis_identitas' => strtoupper(trim((string) ($row['jenis_identitas'] ?? ''))) ?: 'KTP',
            'no_identitas' => trim((string) ($row['nomor_identitas'] ?? '')),
            'npwp' => trim((string) ($row['npwp'] ?? '')),
            'status' => 1,
            'foto' => 'anggota/foto-default.jpg',
            'user_id' => $this->user_id,
        ]);
    }

    protected function resolveKelompok(string $nama, int $rowNumber): ?int
    {
        if ($nama === '') {
            return null;
        }

        if (! array_key_exists($nama, $this->kelompokCache)) {
            $found = Kelompok::where('nama', $nama)->value('id');

            if ($found === null) {
                $this->fail($rowNumber, 'kelompok', "Kelompok \"{$nama}\" tidak ditemukan");
            }

            $this->kelompokCache[$nama] = (int) $found;
        }

        return $this->kelompokCache[$nama];
    }

    protected function resolveKantor(string $nama, int $rowNumber): ?int
    {
        if ($nama === '') {
            return null;
        }

        if (! array_key_exists($nama, $this->kantorCache)) {
            $found = Kantor::where('nama_kantor', $nama)->value('id');

            if ($found === null) {
                $this->fail($rowNumber, 'kantor', "Kantor \"{$nama}\" tidak ditemukan");
            }

            $this->kantorCache[$nama] = (int) $found;
        }

        return $this->kantorCache[$nama];
    }

    /** Konversi tanggal Excel (d-m-Y atau serial number) ke Y-m-d. */
    protected function normalizeDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)
                ->format('Y-m-d');
        }

        $parsed = \DateTime::createFromFormat('d-m-Y', trim((string) $value));

        return $parsed ? $parsed->format('Y-m-d') : null;
    }

    public function rules(): array
    {
        return [
            'nomor_anggota' => ['required', 'unique:anggota,no_anggota'],
            'nama' => ['required'],
            'pin' => ['required'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nomor_anggota.unique' => 'Nomor anggota sudah ada di database.',
            'nomor_anggota.required' => 'Kolom Nomor Anggota wajib diisi.',
            'nama.required' => 'Kolom Nama wajib diisi.',
            'pin.required' => 'Kolom PIN wajib diisi.',
        ];
    }

    protected function fail(int $rowNumber, string $attribute, string $message): void
    {
        $failure = new Failure($rowNumber, $attribute, [$message]);
        throw new ExcelValidationException(null, [$failure]);
    }
}
