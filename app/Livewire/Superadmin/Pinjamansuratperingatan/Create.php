<?php

namespace App\Livewire\Superadmin\Pinjamansuratperingatan;

use App\Models\Jaminan;
use App\Models\JaminanDetail;
use Livewire\Component;

class Create extends Component
{
    public $userLogin, $nama;
    public int $aktif = 1;

    // Array untuk menampung baris detail jaminan secara dinamis
    public $jaminan = [''];


    /* =======================
     * LIFECYCLE HOOKS
     * ======================= */
    public function mount()
    {
        $this->userLogin = auth()->user();
    }

    // Menggunakan updated() global untuk mendeteksi perubahan pada nested array
    public function updated($propertyName, $value)
    {
        // Cek jika input yang diketik adalah bagian dari array 'jaminan'
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

        // Pastikan form tidak pernah benar-benar kosong (minimal 1 baris)
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
     * STORE
     * ======================= */
    public function store()
    {
        // 1. Bersihkan array dari baris yang kosong
        $detailBersih = array_filter($this->jaminan, function ($value) {
            return !empty(trim($value));
        });

        // 2. Update array dengan data yang sudah bersih
        $this->jaminan = empty($detailBersih) ? [''] : array_values($detailBersih);

        // 3. Validasi (Untuk CREATE, tidak perlu pengecualian ID pada unique)
        $validated = $this->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:jaminan,nama'],
            'jaminan' => ['required', 'array', 'min:1'],
            'jaminan.*' => ['required', 'string', 'max:255'],
        ]);

        // 4. BIKIN BARU Data Parent (Jaminan) dan ambil instance-nya
        $jaminanBaru = Jaminan::create([
            'nama' => $validated['nama'],
            'user_id' => $this->userLogin->id,
        ]);

        // 5. BIKIN BARU Data Child (JaminanDetail) menggunakan ID dari Jaminan yang baru dibuat
        // Tidak perlu delete() karena ini data baru
        foreach ($this->jaminan as $detailText) {
            JaminanDetail::create([
                'jaminan_id' => $jaminanBaru->id, // Gunakan ID dari variabel $jaminanBaru
                'detail' => $detailText,
                'user_id' => $this->userLogin->id,
            ]);
        }

        // 6. Tampilkan Notifikasi
        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text' => 'Data jaminan berhasil ditambahkan!',
            'icon' => 'success',
        ]);

        // 7. Redirect SPA Livewire v3
        return $this->redirect('/superadmin/pinjaman/jaminan', navigate: true);
    }

    /* =======================
     * RENDER
     * ======================= */
    public function render()
    {
        return view('livewire.superadmin.pinjamansuratperingatan.create', [
            'title' => 'Tambah Surat Peringatan',
        ]);
    }
}
