<?php

namespace App\Imports;

use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\PinjamanProduk;
use App\Services\LoanCalculationService;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

/**
 * Import data pinjaman sesuai template PinjamanTemplateExport.
 * Kolom "No Anggota" dan "Produk" dicocokkan berdasarkan data yang ada.
 * Nominal angsuran dihitung otomatis bila tidak diisi (via service loan).
 */
class PinjamanImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    protected $user_id;

    protected array $anggotaCache = [];

    protected array $produkCache = [];

    protected array $seenNo = [];

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function model(array $row)
    {
        $rowNumber = $row['__row'] ?? 0;

        $noPinjaman = trim((string) ($row['no_pinjaman'] ?? ''));

        if (in_array($noPinjaman, $this->seenNo)) {
            $this->fail($rowNumber, 'no_pinjaman', "No. pinjaman {$noPinjaman} duplikat di file");
        }
        $this->seenNo[] = $noPinjaman;

        $anggotaId = $this->resolveAnggota(trim((string) ($row['no_anggota'] ?? '')), $rowNumber);
        $produk = $this->resolveProduk(trim((string) ($row['produk'] ?? '')), $rowNumber);

        $tanggal = $this->normalizeDate($row['tanggal'] ?? null);
        $jangka = (int) ($row['jangka_waktu'] ?? 0);
        $satuan = strtolower(trim((string) ($row['satuan'] ?? 'bulan')));
        $aktif = $this->normalizeAktif($row['aktif'] ?? null);

        $hasil = app(LoanCalculationService::class)->calculate([
            'plafon' => (float) ($row['plafon'] ?? 0),
            'bunga' => (float) ($row['bunga'] ?? 0),
            'jangka_waktu' => $jangka,
            'satuan' => $satuan,
            'metode' => $produk?->angsuran,
        ]);

        $nominalAngsuran = trim((string) ($row['nominal_angsuran'] ?? ''));
        if ($nominalAngsuran === '' || (float) $nominalAngsuran <= 0) {
            $nominalAngsuran = (string) $hasil['nominal_angsuran'];
        }

        return new Pinjaman([
            'tanggal' => $tanggal ?? now()->format('Y-m-d'),
            'no_pinjaman' => $noPinjaman,
            'anggota_id' => $anggotaId,
            'jenis_id' => (int) $produk->id,
            'jaminan_id' => 0,
            'marketing_id' => (int) $this->user_id,
            'sektor_id' => 0,
            'plafon' => (string) ($row['plafon'] ?? 0),
            'bunga' => (string) ($row['bunga'] ?? 0),
            'jangka_waktu' => (string) $jangka,
            'satuan' => $satuan,
            'angsuran' => $produk->angsuran ?? 'Flat',
            'nominal_angsuran' => $nominalAngsuran,
            'periode' => (string) $hasil['jumlah_periode'],
            'bayar_pokok_per' => '',
            'pembayaran' => 'manual',
            'manual' => '0',
            'tabungan_id' => 0,
            'kode_id' => 0,
            'kode_koreksi' => '',
            'swp_id' => 0,
            'spp_id' => 0,
            'proposal_id' => 0,
            'angsuranke' => '0',
            'rekening_koran' => '',
            'cair_simpanan' => '',
            'sms' => '',
            'aktif' => $aktif,
            'jatuh_tempo' => $this->hitungJatuhTempo($tanggal ?? now()->format('Y-m-d'), $jangka, $satuan),
            'kantor_id' => (int) ($anggotaId ? Anggota::find($anggotaId)?->kantor_id : 0),
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

    protected function resolveProduk(string $nama, int $rowNumber): PinjamanProduk
    {
        if (! array_key_exists($nama, $this->produkCache)) {
            $found = PinjamanProduk::where('nama', $nama)->first();

            if ($found === null) {
                $this->fail($rowNumber, 'produk', "Produk \"{$nama}\" tidak ditemukan");
            }

            $this->produkCache[$nama] = $found;
        }

        return $this->produkCache[$nama];
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

    /** Normalisasi kolom aktif: 1/0, Aktif/Nonaktif, Ya/Tidak. */
    protected function normalizeAktif($value): string
    {
        $v = strtolower(trim((string) $value));
        return in_array($v, ['0', 'nonaktif', 'tidak', 'no'], true) ? '0' : '1';
    }

    protected function hitungJatuhTempo(string $tanggal, int $jangka, string $satuan): string
    {
        $date = \Carbon\Carbon::parse($tanggal);
        return match ($satuan) {
            'hari' => $date->addDays($jangka)->format('Y-m-d'),
            'minggu' => $date->addWeeks($jangka)->format('Y-m-d'),
            'tahun' => $date->addYears($jangka)->format('Y-m-d'),
            default => $date->addMonths($jangka)->format('Y-m-d'),
        };
    }

    public function rules(): array
    {
        return [
            'no_pinjaman' => ['required', 'unique:pinjaman,no_pinjaman'],
            'no_anggota' => ['required'],
            'produk' => ['required'],
            'plafon' => ['required', 'numeric', 'gt:0'],
            'bunga' => ['required', 'numeric', 'min:0', 'max:100'],
            'jangka_waktu' => ['required', 'integer', 'min:1'],
            'satuan' => ['required', Rule::in(['hari', 'minggu', 'bulan', 'tahun'])],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'no_pinjaman.unique' => 'No. pinjaman sudah ada di database.',
            'no_pinjaman.required' => 'Kolom No Pinjaman wajib diisi.',
            'no_anggota.required' => 'Kolom No Anggota wajib diisi.',
            'produk.required' => 'Kolom Produk wajib diisi.',
            'plafon.required' => 'Kolom Plafon wajib diisi.',
            'bunga.required' => 'Kolom Bunga wajib diisi.',
            'jangka_waktu.required' => 'Kolom Jangka Waktu wajib diisi.',
            'satuan.in' => 'Satuan harus salah satu dari: hari, minggu, bulan, tahun.',
        ];
    }

    protected function fail(int $rowNumber, string $attribute, string $message): void
    {
        $failure = new Failure($rowNumber, $attribute, [$message]);
        throw new ExcelValidationException(null, [$failure]);
    }
}