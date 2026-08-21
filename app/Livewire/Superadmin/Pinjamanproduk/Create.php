<?php

namespace App\Livewire\Superadmin\Pinjamanproduk;

use App\Models\Account;
use App\Models\Parameter;
use App\Models\PinjamanProduk;
use App\Models\PinjamanKomponen;
use App\Models\PinjamanKolektabilitas;
use Livewire\Component;

class Create extends Component
{
    public $userLogin;
    public $account = [];
    public $parameter = [];

    /* ================= Pinjaman Jenis ================= */
    public $kode;
    public $nama;
    public $account_id;
    public $bunga;
    public $account_bunga;
    public $account_ditangguhkan;
    public $kas;
    public $account_bank;
    public $insentif;
    public $simpanan_pokok = 0;
    public $nominal_simpanan_pokok;
    public $toleransi;
    public $angsuran;

    /* ================= CHECKBOX ================= */
    public $is_aktif = 0;
    public $swp_cair = 0;
    public $swp_angsur = 0;
    public $swp_persen = 0;
    public $ditangguhkan = 0;
    public $nominal_simpanan = null;

    /* ================= DATA ARRAY ================= */
    public $komponen = [];

    public array $listAngsuran = [
        'Anuitas',
        'Flat',
        'Flat Efektif',
        'Pokok Tetap',
        'Bagi Hasil Menurun',
    ];

    public $kolektabilitas = [
        ['label' => 'Lancar', 'keterangan' => '', 'kualitas_id' => 1],
        ['label' => 'Kurang Lancar', 'keterangan' => '', 'kualitas_id' => 2],
        ['label' => 'Diragukan', 'keterangan' => '', 'kualitas_id' => 3],
        ['label' => 'Macet', 'keterangan' => '', 'kualitas_id' => 4],
    ];

    public array $listKodeRumus = [
        ['kode' => 'JT',  'keterangan' => 'Jatuh Tempo (Bulan)'],
        ['kode' => 'TBX', 'keterangan' => 'Tunggakan Bunga (X)'],
        ['kode' => 'TPX', 'keterangan' => 'Tunggakan Pokok (X)'],
        ['kode' => 'TBB', 'keterangan' => 'Tunggakan Bunga (Bulan)'],
        ['kode' => 'TPB', 'keterangan' => 'Tunggakan Pokok (Bulan)'],
    ];

    /* ================= MODAL KOLEKTABILITAS ================= */
    public $rumus = '';
    public $selectedIndex = null;
    public $showRumusModal = false;

    /* ================= MODAL KOMPONEN (FORMULA) ================= */
    public $isFormulaModalOpen = false;
    public $formulaValue = '';
    public $targetIndex = null;
    public $targetField = null;

    /* ================= LIFECYCLE ================= */
    public function mount()
    {
        $this->userLogin = auth()->user();
        $this->account = Account::orderBy('no_account')->get();
        $this->parameter = Parameter::where('jenis', 2)->orderBy('nama')->get();
        $this->komponen[] = $this->emptyRow();
    }

    /* ================= LOGIC ================= */
    public function updatedIsAktif($value)
    {
        $this->is_aktif = $value ? 1 : 0;

        if ($this->is_aktif == 0) {
            $this->swp_cair = 0;
            $this->swp_angsur = 0;
            $this->swp_persen = 0;
            $this->nominal_simpanan = null;
        }
    }

    private function emptyRow(): array
    {
        return [
            'nama'       => '',
            'nominal'    => null,
            'persen'     => false,
            'account_id' => '',
            'c'          => false,
            'a'          => false,
            'p'          => false,
            'rumus_c'    => '',
            'rumus_a'    => '',
            'rumus_p'    => '',
        ];
    }

    /* --- Modal Kolektabilitas --- */
    public function openRumusModal($index)
    {
        $this->selectedIndex = $index;
        $this->rumus = $this->kolektabilitas[$index]['keterangan'];
        $this->showRumusModal = true;
    }

    public function insertRumusKolektabilitas($text)
    {
        $this->rumus .= $this->rumus === '' ? $text : ' ' . $text;
    }

    public function clearRumusKolektabilitas()
    {
        $this->rumus = '';
    }

    public function saveRumusKolektabilitas()
    {
        $this->kolektabilitas[$this->selectedIndex]['keterangan'] = trim($this->rumus);
        $this->showRumusModal = false;
    }

    public function closeRumusModal()
    {
        $this->showRumusModal = false;
    }

    /* --- Modal Formula Komponen --- */
    public function openFormulaModal($index, $field)
    {
        $this->targetIndex = $index;
        $this->targetField = $field;
        $this->formulaValue = $this->komponen[$index][$field] ?? '';
        $this->isFormulaModalOpen = true;
    }

    public function closeFormulaModal()
    {
        $this->isFormulaModalOpen = false;
        $this->formulaValue = '';
        $this->targetIndex = null;
        $this->targetField = null;
    }

    public function insertFormula($value)
    {
        $this->formulaValue .= $this->formulaValue === '' ? $value : ' ' . $value;
    }

    public function clearFormula()
    {
        $this->formulaValue = '';
    }

    public function saveFormula()
    {
        if ($this->targetIndex !== null && $this->targetField !== null) {
            $this->komponen[$this->targetIndex][$this->targetField] = trim($this->formulaValue);
        }
        $this->closeFormulaModal();
    }


    /* ================= LIFECYCLE HOOKS ================= */
    // Method ini otomatis terpanggil oleh Livewire setiap kali ada property yang berubah
    public function updated($property, $value)
    {
        // Cek apakah yang diketik adalah input 'nama' di dalam array komponen
        // Format property dari Livewire v3 akan seperti: komponen.0.nama, komponen.1.nama, dst.
        if (preg_match('/^komponen\.(\d+)\.nama$/', $property, $matches)) {
            $index = (int) $matches[1];

            // Jika baris yang diubah adalah baris TERAKHIR, dan isinya tidak kosong
            if ($index === count($this->komponen) - 1 && !empty(trim($value))) {
                // Tambahkan 1 baris kosong baru di bawahnya
                $this->komponen[] = $this->emptyRow();
            }
        }
    }

    /* ================= HAPUS BARIS KOMPONEN ================= */
    public function removeKomponen($index)
    {
        // Hapus baris yang dipilih
        unset($this->komponen[$index]);
        // Re-index array agar urutan indexnya (0, 1, 2) kembali normal
        $this->komponen = array_values($this->komponen);

        // Pastikan tabel tidak pernah benar-benar kosong
        if (count($this->komponen) === 0) {
            $this->komponen[] = $this->emptyRow();
        }
    }

    /* ================= VALIDATION ================= */
    protected function rules()
    {
        return [
            'nama' => 'required|string|max:100',
            'kode' => 'required|string|max:50',
            'account_id' => 'required|integer',
            'bunga' => 'required|numeric',
            'account_bunga' => 'required|integer',
            'account_bank' => 'nullable|integer',
            'insentif' => 'required|numeric',
            'is_aktif' => 'required|boolean',
            'swp_cair' => 'nullable|boolean',
            'swp_angsur' => 'nullable|boolean',
            'ditangguhkan' => 'nullable|boolean',
            'swp_persen' => 'nullable|boolean',
            'simpanan_pokok' => 'nullable|boolean',
            'nominal_simpanan_pokok' => 'nullable|numeric',
            'toleransi' => 'required|integer',
            'angsuran' => 'required|string|max:255',
            'kolektabilitas' => 'required|array',
            'komponen' => 'required|array',
            'komponen.*.nama' => 'required|string',
            'komponen.*.account_id' => 'required|integer',
        ];
    }

    protected function messages()
    {
        return [
            'nama.required' => 'Nama produk simpanan wajib diisi.',
            'kode.required' => 'Kode produk simpanan wajib diisi.',
            'account_id.required' => 'Akun simpanan wajib dipilih.',
            'bunga.required' => 'Bunga wajib diisi.',
            'account_bunga.required' => 'Akun bunga wajib dipilih.',
            'insentif.required' => 'Insentif wajib diisi.',
            'toleransi.required' => 'Toleransi wajib diisi.',
            'angsuran.required' => 'Angsuran wajib diisi.',
            'komponen.*.nama.required' => 'Nama komponen wajib diisi.',
            'komponen.*.account_id.required' => 'Akun komponen wajib dipilih.',
        ];
    }

    /* ================= STORE ================= */
    /* ================= STORE ================= */
    public function store()
    {
        // 1. FILTER BARIS KOSONG:
        // Hapus baris komponen yang namanya kosong (baris terakhir yang otomatis bertambah tapi tidak diisi)
        $this->komponen = array_filter($this->komponen, function ($item) {
            return !empty(trim($item['nama']));
        });

        // Re-index array setelah difilter
        $this->komponen = array_values($this->komponen);

        // 2. JALANKAN VALIDASI
        $this->validate();

        // Create the loan product
        $pinjamanProduk = PinjamanProduk::create([
            // ... (Kode create PinjamanProduk Anda tetap sama seperti sebelumnya) ...
            'kode' => $this->kode,
            'nama' => $this->nama,
            'account_id' => $this->account_id,
            'bunga' => $this->bunga,
            'account_bunga' => $this->account_bunga,
            'nominal_simpanan' => $this->nominal_simpanan,
            'account_ditangguhkan' => $this->account_ditangguhkan,
            'kas' => $this->kas ?? 0,
            'account_bank' => $this->account_bank ?? 0,
            'insentif' => $this->insentif,
            'simpanan' => (int) $this->is_aktif,
            'swp_cair' => (int) $this->swp_cair,
            'ditangguhkan' => (int) $this->ditangguhkan,
            'swp_angsur' => (int) $this->swp_angsur,
            'swp_persen' => (int) $this->swp_persen,
            'simpanan_pokok' => (int) $this->simpanan_pokok,
            'nominal_simpanan_pokok' => $this->nominal_simpanan_pokok ?? 0,
            'toleransi' => $this->toleransi,
            'angsuran' => $this->angsuran,
            'user_id' => $this->userLogin->id,
        ]);

        // Save kolektabilitas data
        foreach ($this->kolektabilitas as $kolektabilitasData) {
            // ... (Kode create PinjamanKolektabilitas Anda tetap sama) ...
            PinjamanKolektabilitas::create([
                'pinj_jenis_id' => $pinjamanProduk->id,
                'kualitas_id' => $kolektabilitasData['kualitas_id'],
                'keterangan' => $kolektabilitasData['keterangan'] ?? null,
                'user_id' => $this->userLogin->id,
            ]);
        }

        // Save komponen data
        foreach ($this->komponen as $komponenData) {
            // ... (Kode create PinjamanKomponen Anda tetap sama) ...
            PinjamanKomponen::create([
                'pinj_jenis_id' => $pinjamanProduk->id,
                'nama' => $komponenData['nama'],
                'nominal' => $komponenData['nominal'] ?? 0,
                'persen' => (int) $komponenData['persen'],
                'account_id' => $komponenData['account_id'],
                'cair' => (int) $komponenData['c'],
                'angsuran' => (int) $komponenData['a'],
                'penalti' => (int) $komponenData['p'],
                'tunggakan' => 0,
                'denda_t' => 0,
                'denda_h' => 0,
                'rumus_c' => $komponenData['rumus_c'],
                'rumus_a' => $komponenData['rumus_a'],
                'rumus_p' => $komponenData['rumus_p'],
                'user_id' => $this->userLogin->id,
            ]);
        }

        session()->flash('swal', [
            'title' => 'Berhasil',
            'text' => 'Produk pinjaman berhasil dibuat',
            'icon' => 'success',
        ]);

        return redirect()->to('/superadmin/pinjaman/produk');
    }

    public function render()
    {
        return view('livewire.superadmin.pinjamanproduk.create', [
            'title' => 'Tambah Produk Pinjaman',
        ]);
    }
}
