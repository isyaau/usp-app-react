<?php

namespace App\Livewire\Superadmin\Berjangka;

use App\Models\Account;
use App\Models\Anggota;
use App\Models\Deposito;
use App\Models\DepositoJenis;
use App\Models\Kantor;
use App\Models\Kelompok;
use App\Models\Marketing;
use App\Models\Simpanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravolt\Indonesia\Models\Province;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use SweetAlert2\Laravel\Traits\WithSweetAlert;

#[Title('Tambah Simpanan Berjangka')]
class Create extends Component
{
    use WithSweetAlert;
    use WithFileUploads;
    use WithPagination;

    public $searchAnggota = '';
    public $no_anggota;
    public $nama_anggota;
    public $anggota_id;

    protected $paginationTheme = 'bootstrap'; // agar pagination tampil sesuai Bootstrap


    public $searchMarketing = '';
    public $selectedMarketingId;
    public $selectedMarketingKode;
    public $selectedMarketingNama;


    public $tabunganbunga_id;   // ID untuk database
    public $no_rekening;        // tampil di field
    public $searchSimpanan = '';
    public $jenisFilter = '';   // filter radio


    public $tabungantempo_id;
    public $selectedNoRekeningTempo;
    public $searchTempo = '';
    public $jenisTempoFilter = ''; // filter radio button



    // Fields
    public $tanggal;
    public $no_deposito;
    public $jenis_id;
    public $marketing_id;
    public $qq;
    public $jangka_waktu;
    public $bunga;
    public $nominal;
    public $otomatis = 0;
    public $bayar_bunga;
    public $diawal;
    public $bunga_accrual = 0;
    public $account_bungaaccrual;


    public $bayar_jatuhtempo;
    public $blokir = 0;
    public $kantor_id;
    public $user_id;

    public $nama;
    public $jatuh_tempo;
    public $nominal_bagihasil;






    // List
    public $produks;

    public $bungaaccruals;
    public $marketings;
    public $diawals;
    public $tabunganbungas;
    public $tabungantempos;
    public $tabungans;


    public $userLogin;

    public $activeTab = 'bagi_hasil';

    public $kelompoks;
    public $kantors;



    public array $listPembayaran = [
        1 => 'Tiap Bulan',
        2 => 'Diawal',
        3 => 'Diakhir',
    ];

    public function updatingSearchAnggota()
    {
        $this->resetPage(); // reset ke halaman 1 setiap search
    }


    public function pilihAnggota($id, $no, $nama)
    {
        $this->anggota_id = $id;
        $this->no_anggota = $no;
        $this->nama = $nama;
    }

    public function updatingSearchMarketing()
    {
        $this->resetPage(); // reset pagination jika search
    }

    public function pilihMarketing($id, $kode, $nama)
    {
        $this->marketing_id = $id;
        $this->selectedMarketingId = $id;
        $this->selectedMarketingKode = $kode;
        $this->selectedMarketingNama = $nama;
    }


    public function openSimpananModal()
    {
        $this->dispatch('showSimpananModal');
    }

    public function selectSimpanan($id)
    {
        $simpanan = Simpanan::with(['anggota', 'jenis_simpanan'])->find($id);

        $this->tabunganbunga_id = $simpanan->id;
        $this->no_rekening = $simpanan->no_rekening;
    }


    public function openTabunganTempoModal()
    {
        $this->dispatchBrowserEvent('show-tabungan-tempo-modal');
    }

    public function selectTabunganTempo($id)
    {
        $tempo = Simpanan::with(['anggota', 'jenis_simpanan'])->find($id);

        $this->tabungantempo_id = $tempo->id;                // ID untuk database
        $this->selectedNoRekeningTempo = $tempo->no_rekening; // tampil di field
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    private function generateNoDeposito($tanggal)
    {
        if (!$tanggal instanceof Carbon) {
            $tanggal = Carbon::parse($tanggal);
        }

        $prefix = '55.';
        $tahun = $tanggal->format('y');
        $bulan = $tanggal->format('m');

        $kodeAwal = $prefix . $tahun . $bulan; // 55.2603

        $last = Deposito::where('no_deposito', 'like', $kodeAwal . '%')
            ->orderBy('no_deposito', 'desc')
            ->first();

        if (!$last) {
            $nextNumber = 1;
        } else {
            $lastNumber = (int) substr($last->no_deposito, strlen($kodeAwal));
            $nextNumber = $lastNumber + 1;
        }

        return $kodeAwal . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function mount()
    {
        $this->userLogin = auth()->guard('web')->user();
        $this->kelompoks = Kelompok::all();
        $this->tabungans = Simpanan::orderBy('no_rekening')->get();
        $this->kantors = Kantor::orderBy('nama_kantor')->get();
        $this->produks = DepositoJenis::orderBy('nama')->get();
        $this->marketings = Marketing::orderBy('nama')->get();
        $this->bungaaccruals = Account::orderBy('nama')->get();
        $this->tabunganbungas = Simpanan::orderBy('no_rekening')->get();
        $this->tabungantempos = Simpanan::orderBy('no_rekening')->get();

        // tanggal hari ini
        $tanggal = Carbon::now();
        $this->tanggal = $tanggal->format('d-m-Y');
        $this->no_deposito = $this->generateNoDeposito(now());
    }

    public function updatedJangkaWaktu($value)
    {
        if ($value && $this->tanggal) {
            // pastikan $value integer
            $months = (int) $value;

            // Konversi tanggal awal ke Carbon
            $start = Carbon::createFromFormat('d-m-Y', $this->tanggal);

            // Tambahkan jangka waktu (bulan)
            $due = $start->copy()->addMonths($months);

            // Format kembali ke d-m-Y
            $this->jatuh_tempo = $due->format('d-m-Y');
        } else {
            $this->jatuh_tempo = null; // reset jika jangka_waktu kosong
        }
    }

    public function updatedBunga($value)
    {
        $this->calculateBagihasil();
    }

    public function updatedNominal($value)
    {
        $this->calculateBagihasil();
    }

    // Method perhitungan
    protected function calculateBagihasil()
    {
        $bunga = (float) $this->bunga;
        $nominal = (float) $this->nominal;

        if ($bunga > 0 && $nominal > 0) {
            $this->nominal_bagihasil = round(($bunga / 100 * $nominal) / 12);
        } else {
            $this->nominal_bagihasil = 0;
        }
    }

    // Togle Form

    public function render()
    {
        $anggotas = Anggota::query()
            ->when($this->searchAnggota, function ($query) {
                $query->where('nama', 'like', '%' . $this->searchAnggota . '%')
                    ->orWhere('no_anggota', 'like', '%' . $this->searchAnggota . '%');
            })
            ->orderBy('nama')
            ->paginate(10);

        $query = Simpanan::with(['anggota', 'jenis_simpanan'])
            ->where(function ($q) {
                $q->where('no_rekening', 'like', '%' . $this->searchSimpanan . '%')
                    ->orWhereHas('anggota', function ($q) {
                        $q->where('nama', 'like', '%' . $this->searchSimpanan . '%');
                    });
            });

        if ($this->jenisFilter) {
            $query->whereHas('jenis_simpanan', function ($q) {
                $q->where('nama', $this->jenisFilter);
            });
        }

        $simpananList = $query->paginate(10);

        $query = Simpanan::with(['anggota', 'jenis_simpanan'])
            ->where(function ($q) {
                $q->where('no_rekening', 'like', '%' . $this->searchTempo . '%')
                    ->orWhereHas('anggota', function ($q) {
                        $q->where('nama', 'like', '%' . $this->searchTempo . '%');
                    });
            });

        // Filter radio jenis simpanan
        if ($this->jenisTempoFilter) {
            $query->whereHas('jenis_simpanan', function ($q) {
                $q->where('nama', $this->jenisTempoFilter);
            });
        }

        $tabunganTempoList = $query->paginate(10);

        return view('livewire.superadmin.berjangka.create', [
            'anggotas' => $anggotas,
            'simpananList' => $simpananList,
            'tabunganTempoList' => $tabunganTempoList,
            'title' => 'Tambah Simpanan Berjangka',
        ]);
    }

    public function rules()
    {
        return [
            'tanggal'              => 'required|date',
            'no_deposito'          => 'required|string|max:255',
            'anggota_id'           => 'nullable',
            'jenis_id'             => 'nullable',
            'marketing_id'         => 'nullable',
            'qq'                   => 'nullable|string|max:255',
            'jangka_waktu'         => 'nullable',
            'bunga'                => 'nullable',
            'nominal'              => 'nullable',
            'otomatis'             => 'nullable',
            'bayar_bunga'          => 'nullable',
            'diawal'               => 'required',
            'bunga_accrual'        => 'nullable',
            'account_bungaaccrual' => 'nullable',
            'tabunganbunga_id'     => 'nullable',
            'tabungantempo_id'     => 'nullable',
            'bayar_jatuhtempo'     => 'required',
            'blokir'               => 'nullable',
            'kantor_id'            => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'diawal.required' => 'Pembayaran wajib diisi.',
            'bayar_bunga.required' => 'Pembayaran Bunga wajib diisi.',
            'bayar_jatuhtempo.required' => 'Pembayaran Jatuh Tempo wajib diisi.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',

            'no_deposito.required' => 'Nomor deposito wajib diisi.',
            'no_deposito.string' => 'Nomor deposito harus berupa teks.',
            'no_deposito.max' => 'Nomor deposito maksimal 255 karakter.',

            'anggota_id.required' => 'Anggota wajib dipilih.',
            'jenis_id.required' => 'Jenis deposito wajib dipilih.',

            'jangka_waktu.required' => 'Jangka waktu wajib diisi.',
            'jangka_waktu.numeric' => 'Jangka waktu harus berupa angka.',

            'bunga.required' => 'Bunga wajib diisi.',
            'bunga.numeric' => 'Bunga harus berupa angka.',

            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
        ];
    }


    public function store()
    {
        // Validasi
        $validated = $this->validate();
        $user_id = $this->userLogin->id;


        // Gabungkan user_id
        $validated['otomatis'] = $this->otomatis ? 1 : 0;
        $validated['bunga_accrual'] = $this->bunga_accrual ? 1 : 0;
        $validated['user_id'] = $user_id;
        $validated['tabunganbunga_id'] = $this->tabunganbunga_id;

        // ===============================
        // DEBUG DATA
        // ===============================


        // ===============================
        // SIMPAN DATA
        // ===============================
        Deposito::create([
            'tanggal'              => $validated['tanggal'],
            'no_deposito'          => $validated['no_deposito'],
            'anggota_id'           => $validated['anggota_id'],
            'jenis_id'             => $validated['jenis_id'],
            'marketing_id'         => $validated['marketing_id'] ?? null,
            'qq'                   => $validated['qq'] ?? null,
            'jangka_waktu'         => $validated['jangka_waktu'],
            'bunga'                => $validated['bunga'],
            'nominal'              => $validated['nominal'],
            'otomatis'             => $validated['otomatis'] ?? null,
            'bayar_bunga'          => $validated['bayar_bunga'] ?? null,
            'diawal'               => $validated['diawal'] ?? null,
            'bunga_accrual'        => $validated['bunga_accrual'] ?? null,
            'account_bungaaccrual' => $validated['account_bungaaccrual'] ?? null,
            'tabunganbunga_id'     => $validated['tabunganbunga_id'] ?? null,
            'tabungantempo_id'     => $validated['tabungantempo_id'] ?? null,
            'bayar_jatuhtempo'     => $validated['bayar_jatuhtempo'] ?? null,
            'blokir'               => $validated['blokir'] ?? null,
            'kantor_id'            => $validated['kantor_id'] ?? null,
            'user_id'              => $user_id
        ]);

        session()->flash('swal', [
            'title' => 'Berhasil!',
            'text'  => 'Data anggota berhasil disimpan!',
            'icon'  => 'success',
            'confirmButtonText' => 'Oke'
        ]);

        return $this->redirect('/superadmin/simpanan-berjangka', navigate: true);
    }
}
