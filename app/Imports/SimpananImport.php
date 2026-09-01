<?php

namespace App\Imports;

use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\Marketing;
use App\Models\Simpanan;
use App\Models\SimpananJenis;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

/**
 * Import data rekening simpanan sesuai template SimpananTemplateExport.
 * Kolom "No Anggota", "Produk", "Marketing", dan "Kantor" dicocokkan berdasarkan nama/kode.
 */
class SimpananImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected $user_id;

    protected array $anggotaCache = [];

    protected array $produkCache = [];

    protected array $marketingCache = [];

    protected array $kantorCache = [];

    protected array $seenNo = [];

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function model(array $row)
    {
        $rowNumber = $row['__row'] ?? 0;

        $noRekening = trim((string) ($row['no_rekening'] ?? ''));

        if (in_array($noRekening, $this->seenNo)) {
            $this->fail($rowNumber, 'no_rekening', "No. rekening {$noRekening} duplikat di file");
        }
        $this->seenNo[] = $noRekening;

        $anggotaId = $this->resolveAnggota(trim((string) ($row['no_anggota'] ?? '')), $rowNumber);
        $produk = $this->resolveProduk(trim((string) ($row['produk'] ?? '')), $rowNumber);
        $marketingId = $this->resolveMarketing(trim((string) ($row['marketing'] ?? '')), $rowNumber);
        $kantorId = $this->resolveKantor(trim((string) ($row['kantor'] ?? '')), $rowNumber);

        $tanggal = $this->normalizeDate($row['tanggal'] ?? null);
        $aktif = $this->normalizeYaTidak($row['aktif'] ?? null);
        $sms = $this->normalizeYaTidak($row['sms'] ?? null);
        $blokirSimpanan = $this->normalizeYaTidak($row['blokir_simpanan'] ?? null);
        $blokirNominal = $this->normalizeYaTidak($row['blokir_nominal'] ?? null);
        $blokirTgl = $this->normalizeYaTidak($row['blokir_s_d_tanggal'] ?? null);

        return new Simpanan([
            'tanggal' => $tanggal ?? now()->format('Y-m-d'),
            'no_rekening' => $noRekening,
            'anggota_id' => $anggotaId,
            'jenis_id' => $produk->id,
            'marketing_id' => $marketingId ?? (int) $this->user_id,
            'qq' => trim((string) ($row['qq'] ?? '')) ?: null,
            'bunga' => trim((string) ($row['bagi_hasil'] ?? '')) ?: null,
            'nominal_setor' => trim((string) ($row['nominal_setor'] ?? '')) ?: null,
            'aktif' => $aktif,
            'sms' => $sms,
            'blokir_simpanan' => $blokirSimpanan,
            'blokir_nominal' => $blokirNominal,
            'nominal_blokir' => trim((string) ($row['nominal_blokir'] ?? '')) ?: null,
            'blokir_tgl' => $blokirTgl,
            'tgl_blokir' => $this->normalizeDate($row['blokir_s_d_tanggal'] ?? null),
            'kantor_id' => $kantorId,
            'user_id' => $this->user_id,
        ]);
    }

    protected function resolveAnggota(string $noAnggota, int $rowNumber): int
    {
        if (! array_key_exists($noAnggota, $this->anggotaCache)) {
            $found = Anggota::where('no_anggota', $noAnggota)->value('id');

            if ($found === null) {
                $this->fail($rowNumber, 'no_anggota', "Anggota \"{$noAnggota}\" tidak ditemukan");
            }

            $this->anggotaCache[$noAnggota] = (int) $found;
        }

        return $this->anggotaCache[$noAnggota];
    }

    protected function resolveProduk(string $nama, int $rowNumber): SimpananJenis
    {
        if (! array_key_exists($nama, $this->produkCache)) {
            $found = SimpananJenis::where('nama', $nama)
                ->orWhere('kode', $nama)
                ->first();

            if ($found === null) {
                $this->fail($rowNumber, 'produk', "Produk \"{$nama}\" tidak ditemukan");
            }

            $this->produkCache[$nama] = $found;
        }

        return $this->produkCache[$nama];
    }

    protected function resolveMarketing(string $nama, int $rowNumber): ?int
    {
        if ($nama === '') {
            return null;
        }

        if (! array_key_exists($nama, $this->marketingCache)) {
            $found = Marketing::where('nama', $nama)->value('id');

            if ($found === null) {
                $this->fail($rowNumber, 'marketing', "Marketing \"{$nama}\" tidak ditemukan");
            }

            $this->marketingCache[$nama] = (int) $found;
        }

        return $this->marketingCache[$nama];
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

    /** Konversi tanggal Excel (d-m-Y, Y-m-d, atau serial number) ke Y-m-d. */
    protected function normalizeDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)
                ->format('Y-m-d');
        }

        $text = trim((string) $value);
        $parsed = \DateTime::createFromFormat('d-m-Y', $text);
        if ($parsed) {
            return $parsed->format('Y-m-d');
        }
        $parsed = \DateTime::createFromFormat('Y-m-d', $text);

        return $parsed ? $parsed->format('Y-m-d') : null;
    }

    /** Normalisasi kolom Ya/Tidak, Aktif/Nonaktif, 1/0 → '1'/'0'. */
    protected function normalizeYaTidak($value): string
    {
        $v = strtolower(trim((string) $value));
        return in_array($v, ['tidak', 'nonaktif', 'no', '0', ''], true) ? '0' : '1';
    }

    public function rules(): array
    {
        return [
            'no_rekening' => ['required', 'unique:simpanan,no_rekening'],
            'no_anggota' => ['required'],
            'produk' => ['required'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'no_rekening.unique' => 'No. rekening sudah ada di database.',
            'no_rekening.required' => 'Kolom No Rekening wajib diisi.',
            'no_anggota.required' => 'Kolom No Anggota wajib diisi.',
            'produk.required' => 'Kolom Produk wajib diisi.',
        ];
    }

    protected function fail(int $rowNumber, string $attribute, string $message): void
    {
        $failure = new Failure($rowNumber, $attribute, [$message]);
        throw new ExcelValidationException(null, [$failure]);
    }
}
