<?php

namespace App\Imports;

use App\Models\Kantor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

class KantorImport implements ToModel, WithHeadingRow, WithValidation
{


    protected $user_id;

    // Menyimpan kode & nama untuk cek duplikat di file
    protected $seen = [
        'kode' => [],
    ];

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function model(array $row)
    {
        // Ambil nomor baris (Excel otomatis)
        $rowNumber = $row['__row'] ?? 0;

        $kode = trim($row['kode']);
        $nama_kantor = trim($row['nama_kantor']);
        $alamat_kantor = trim($row['alamat_kantor']);
        $pejabat = trim($row['pejabat']);
        $jabatan = trim($row['jabatan']);
        $bendahara = trim($row['bendahara']);

        // ================================
        // Cek duplikat di file
        // ================================
        if (in_array($kode, $this->seen['kode'])) {
            $this->fail($rowNumber, 'kode', "Kode $kode duplikat di file");
        }

        $this->seen['kode'][] = $kode;
        $this->seen['nama_kantor'][] = $nama_kantor;
        $this->seen['alamat_kantor'][] = $alamat_kantor;
        $this->seen['pejabat'][] = $pejabat;
        $this->seen['jabatan'][] = $jabatan;
        $this->seen['bendahara'][] = $bendahara;

        // ================================
        // Simpan ke database
        // ================================
        return new Kantor([
            'kode'    => $kode,
            'nama_kantor'    => $nama_kantor,
            'alamat_kantor'    => $alamat_kantor,
            'pejabat'    => $pejabat,
            'jabatan'    => $jabatan,
            'bendahara'    => $bendahara,
            'user_id' => $this->user_id,
        ]);
    }

    // ================================
    // Rules unik di DB
    // ================================
    public function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'unique:kantor,kode'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'kode.unique' => 'Kode sudah ada di database.',
            'kode.required' => 'Kolom kode wajib diisi.',
            'nama_kantor.required' => 'Kolom nama kantor wajib diisi.',
            'alamat_kantor.required' => 'Kolom alamat kantor wajib diisi.',
            'pejabat.required' => 'Kolom pejabat wajib diisi.',
            'jabatan.required' => 'Kolom jabatan wajib diisi.',
            'bendahara.required' => 'Kolom bendahara wajib diisi.',
        ];
    }

    // ================================
    // Helper untuk throw ValidationException Excel
    // ================================
    protected function fail($rowNumber, $attribute, $message)
    {
        $failure = new Failure($rowNumber, $attribute, [$message]);
        throw new ExcelValidationException(null, [$failure]);
    }
}
