<?php

namespace App\Livewire\Superadmin\Jaminan;

use App\Models\Jaminan;
use App\Models\JaminanDetail;
use Livewire\Component;

class Edit extends Component
{
    public $jaminanId; // Menyimpan ID Jaminan yang sedang diedit
    public $userLogin, $nama;
    public int $aktif = 1;

    // Array untuk menampung baris detail jaminan
    public $jaminan = [];

    /* =======================
     * LIFECYCLE HOOKS
     * ======================= */
    public function mount($id) // Menerima parameter ID dari Route
    {
        $this->userLogin = auth()->user();
        $this->jaminanId = $id;

        // 1. Ambil data parent
        $dataJaminan = Jaminan::findOrFail($id);
        $this->nama = $dataJaminan->nama;

        // 2. Ambil data child (detail jaminan)
        $details = JaminanDetail::where('jaminan_id', $id)
            ->pluck('detail')
            ->toArray();

        // 3. Masukkan ke array form, dan tambahkan 1 baris kosong di akhir untuk memancing input baru
        if (count($details) > 0) {
            $this->jaminan = $details;
            $this->jaminan[] = ''; // Baris kosong ekstra
        } else {
            $this->jaminan = [''];
        }
    }

    public function updated($propertyName, $value)
    {
        if (str_starts_with($propertyName, 'jaminan.')) {
            $index = explode('.', $propertyName)[1];

            // Tambahkan baris baru secara otomatis jika baris terakhir mulai diisi
            if ($index == count($this->jaminan) - 1 && !empty(trim($value))) {
                $this->jaminan[] = '';
            }
        }
    }

    /* =======================
     * DYNAMIC FIELDS METHODS
     * ======================= */
    public function removeField($index)
    {
        unset($this->jaminan[$index]);
        $this->jaminan = array_values($this->jaminan); // Reset index array

        if (empty($this->jaminan)) {
            $this->jaminan = [''];
        }
    }

    /* =======================
     * VALIDATION MESSAGE
     * ======================= */
    protected function messages()
    {
        return [
            'nama.required' => 'Nama jaminan wajib diisi.',
            'nama.unique' => 'Nama jaminan sudah digunakan.',
            'jaminan.0.required' => 'Minimal satu detail jaminan harus diisi.',
            'jaminan.*.required' => 'Detail jaminan tidak boleh kosong.',
        ];
    }

    /* =======================
     * UPDATE
     * ======================= */
    public function update()
    {
        // 1. Bersihkan array dari baris yang kosong 
        $detailBersih = array_filter($this->jaminan, function ($value) {
            return !empty(trim($value));
        });

        // 2. Update array dengan data yang sudah bersih
        $this->jaminan = empty($detailBersih) ? [''] : array_values($detailBersih);

        // 3. Validasi (PENTING: tambahkan exception agar ignore ID jaminan yang sedang diedit)
        $validated = $this->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:jaminan,nama,' . $this->jaminanId],
            'jaminan' => ['required', 'array', 'min:1'],
            'jaminan.*' => ['required', 'string', 'max:255'],
        ]);

        // 4. Update Data Parent (Jaminan)
        $dataJaminan = Jaminan::findOrFail($this->jaminanId);
        $dataJaminan->update([
            'nama' => $validated['nama'],
            // Jika Anda ingin mengupdate siapa yang mengedit terakhir, bisa tambahkan user_id di sini
        ]);

        // 5. Update Data Child (JaminanDetail)
        // Cara paling mudah untuk input dinamis: hapus data child lama, masukkan yang baru
        JaminanDetail::where('jaminan_id', $this->jaminanId)->delete();

        foreach ($this->jaminan as $detailText) {
            JaminanDetail::create([
                'jaminan_id' => $this->jaminanId,
                'detail' => $detailText,
                'user_id' => $this->userLogin->id,
            ]);
        }

        // 6. Tampilkan Notifikasi & Redirect
        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Data jaminan berhasil diperbarui!',
            'icon' => 'success',
        ]);

        return redirect()->route('superadmin.pinjaman.jaminan');
    }

    /* =======================
     * RENDER
     * ======================= */
    public function render()
    {
        return view('livewire.superadmin.jaminan.edit', [
            'title' => 'Edit Jaminan',
        ]);
    }
}
