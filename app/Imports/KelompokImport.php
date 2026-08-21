<?php

namespace App\Imports;

use App\Models\Kelompok;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

class KelompokImport implements ToModel, WithHeadingRow, WithValidation
{


    protected $user_id;

    // Menyimpan kode & nama untuk cek duplikat di file
    protected $seen = [
        'kode' => [],
        'nama' => [],
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
        $nama = trim($row['nama']);
        $group = trim($row['group']);

        // ================================
        // Cek duplikat di file
        // ================================
        if (in_array($kode, $this->seen['kode'])) {
            $this->fail($rowNumber, 'kode', "Kode $kode duplikat di file");
        }

        if (in_array($nama, $this->seen['nama'])) {
            $this->fail($rowNumber, 'nama', "Nama $nama duplikat di file");
        }

        $this->seen['kode'][] = $kode;
        $this->seen['nama'][] = $nama;

        // ================================
        // Simpan ke database
        // ================================
        return new Kelompok([
            'kode'    => $kode,
            'nama'    => $nama,
            'group_id'    => $group,
            'user_id' => $this->user_id,
        ]);
    }

    // ================================
    // Rules unik di DB
    // ================================
    public function rules(): array
    {
        return [
            'kode' => ['required', 'string', 'unique:kelompok,kode'],
            'nama' => ['required', 'string', 'unique:kelompok,nama'],
            'group_id' => ['required', 'string'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'kode.unique' => 'Kode sudah ada di database.',
            'nama.unique' => 'Nama sudah ada di database.',
            'kode.required' => 'Kolom kode wajib diisi.',
            'nama.required' => 'Kolom nama wajib diisi.',
            'group_id.required' => 'Kolom nama wajib diisi.',
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
