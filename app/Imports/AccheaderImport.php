<?php

namespace App\Imports;

use App\Models\AccHeader;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

class AccheaderImport implements ToModel, WithHeadingRow, WithValidation
{


    protected $user_id;

    // Menyimpan kode & nama untuk cek duplikat di file
    protected $seen = [
        'no_header' => [],
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

        $no_header = trim($row['no_header']);
        $nama = trim($row['nama']);
        $group_id = trim($row['group']);

        // ================================
        // Cek duplikat di file
        // ================================
        if (in_array($no_header, $this->seen['no_header'])) {
            $this->fail($rowNumber, 'no_header', "Kode $no_header duplikat di file");
        }

        if (in_array($nama, $this->seen['nama'])) {
            $this->fail($rowNumber, 'nama', "Nama $nama duplikat di file");
        }

        $this->seen['no_header'][] = $no_header;
        $this->seen['nama'][] = $nama;

        // ================================
        // Simpan ke database
        // ================================
        return new AccHeader([
            'no_header'    => $no_header,
            'nama'    => $nama,
            'group_id'    => $group_id,
            'user_id' => $this->user_id,
        ]);
    }

    // ================================
    // Rules unik di DB
    // ================================
    public function rules(): array
    {
        return [
            'no_header' => ['required', 'unique:acc_header,no_header'],
            'nama' => ['required', 'string', 'unique:acc_header,nama'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'no_header.unique' => 'No Header sudah ada di database.',
            'nama.unique' => 'Nama sudah ada di database.',
            'no_header.required' => 'Kolom No Header wajib diisi.',
            'nama.required' => 'Kolom nama wajib diisi.',
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
