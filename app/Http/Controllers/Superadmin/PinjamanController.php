<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Anggota;
use App\Models\Jaminan;
use App\Models\Kantor;
use App\Models\Marketing;
use App\Models\Pinjaman;
use App\Models\PinjamanBiaya;
use App\Models\PinjamanJaminan;
use App\Models\PinjamanPenjamin;
use App\Models\PinjamanProduk;
use App\Models\PinjamanSaksi;
use App\Models\PinjamanSurat;
use App\Models\Simpanan;
use App\Models\SimpananKode;
use App\Services\LoanCalculationService;
use Illuminate\Http\Request;

/**
 * Controller CRUD Transaksi Pinjaman (6 tab: Pinjaman, Biaya, Jaminan,
 * Saksi, Surat, Penjamin). Perhitungan angsuran didelegasikan ke
 * App\Services\LoanCalculationService (authoritative).
 */
class PinjamanController extends Controller
{
    private const SATUAN = ['hari', 'minggu', 'bulan', 'tahun'];

    private const LIST_SEKTOR = [
        ['id' => 1, 'nama' => 'PNS'],
        ['id' => 2, 'nama' => 'Swasta'],
        ['id' => 3, 'nama' => 'Wirausaha'],
        ['id' => 4, 'nama' => 'Petani'],
        ['id' => 5, 'nama' => 'Nelayan'],
        ['id' => 6, 'nama' => 'Lainnya'],
    ];

    private const LIST_BAYAR_POKOK_PER = ['1', '2', '3', '6', '12'];

    private const LIST_SURAT = [
        ['id' => 1, 'nama' => 'Surat Perjanjian Pinjaman'],
        ['id' => 2, 'nama' => 'Surat Kuasa'],
        ['id' => 3, 'nama' => 'Surat Pernyataan'],
        ['id' => 4, 'nama' => 'Tanda Terima Jaminan'],
        ['id' => 5, 'nama' => 'Proposal Pinjaman'],
    ];

    public function index(Request $request)
    {
        $search = (string) $request->string('search');

        $pinjaman = Pinjaman::query()
            ->with([
                'jenisPinjaman:id,nama',
                'anggota:id,no_anggota,nama',
                'kantor:id,nama_kantor',
            ])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('no_pinjaman', 'ILIKE', "%{$search}%")
                        ->orWhereHas('anggota', fn ($a) => $a
                            ->where('nama', 'ILIKE', "%{$search}%")
                            ->orWhere('no_anggota', 'ILIKE', "%{$search}%"));
                });
            })
            ->orderBy('created_at', 'DESC')
            ->paginate((int) ($request->input('per_page') ?: 10))
            ->withQueryString();

        return inertia('Superadmin/Pinjaman/Index', [
            'pinjaman' => $pinjaman,
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/Pinjaman/Create', [
            ...$this->formData(),
            'nomorOtomatis' => $this->generateNoPinjaman(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $pinjaman = Pinjaman::create($data);
        $this->saveRelated($pinjaman, $request);

        return redirect()
            ->route('superadmin.pinjaman.pinjaman')
            ->with('flash.status', 'Transaksi pinjaman berhasil dibuat.');
    }

    public function edit(Pinjaman $pinjaman)
    {
        $pinjaman->load([
            'biaya', 'jaminan', 'saksi', 'surat', 'penjamin',
            'anggota:id,no_anggota,nama,alamat,no_identitas,telepon,status',
        ]);

        return inertia('Superadmin/Pinjaman/Edit', [
            ...$this->formData(),
            'pinjaman' => $pinjaman,
        ]);
    }

    public function update(Request $request, Pinjaman $pinjaman)
    {
        $data = $this->validatedData($request, $pinjaman->id);

        $pinjaman->update($data);
        $this->saveRelated($pinjaman, $request);

        return redirect()
            ->route('superadmin.pinjaman.pinjaman')
            ->with('flash.status', 'Transaksi pinjaman berhasil diperbarui.');
    }

    public function destroy(Pinjaman $pinjaman)
    {
        foreach ([
            PinjamanBiaya::class,
            PinjamanJaminan::class,
            PinjamanSaksi::class,
            PinjamanSurat::class,
            PinjamanPenjamin::class,
        ] as $model) {
            $model::where('pinjaman_id', $pinjaman->id)->delete();
        }

        $pinjaman->delete();

        return redirect()
            ->route('superadmin.pinjaman.pinjaman')
            ->with('flash.status', 'Transaksi pinjaman berhasil dihapus.');
    }

    /* ------------------------------------------------------------------ */

    /** Data opsi untuk dropdown / lookup pada form Create & Edit. */
    private function formData(): array
    {
        return [
            'anggotaOptions' => Anggota::orderBy('nama')->get([
                'id', 'no_anggota', 'nama', 'alamat', 'no_identitas', 'telepon', 'status',
            ]),
            'jenisOptions' => PinjamanProduk::orderBy('nama')->get([
                'id', 'nama', 'angsuran', 'bunga', 'nominal_simpanan',
                'nominal_simpanan_pokok', 'simpanan', 'swp_cair', 'swp_angsur',
            ]),
            'marketingOptions' => Marketing::orderBy('nama')->get(['id', 'kode', 'nama']),
            'accountOptions' => Account::orderBy('no_account')->get(['id', 'no_account', 'nama']),
            'jaminanTypes' => Jaminan::with('details:id,jaminan_id,detail')->orderBy('nama')->get(['id', 'nama']),
            'simpananOptions' => Simpanan::with('anggota:id,no_anggota,nama')
                ->orderBy('no_rekening')
                ->get(['id', 'no_rekening', 'anggota_id', 'aktif']),
            'kodeTarikanOptions' => SimpananKode::where('tarikan', '1')
                ->orderBy('kode')
                ->get(['id', 'kode', 'nama']),
            'sektorOptions' => self::LIST_SEKTOR,
            'bayarPokokPerOptions' => self::LIST_BAYAR_POKOK_PER,
            'suratOptions' => self::LIST_SURAT,
            'satuanOptions' => collect(self::SATUAN)->map(fn ($s) => [
                'value' => $s,
                'label' => ucfirst($s),
            ]),
        ];
    }

    /** Validasi + data aman untuk create & update. */
    private function validatedData(Request $request, ?int $ignoreId = null): array
    {
        $suffix = is_null($ignoreId) ? '' : ','.$ignoreId;

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'no_pinjaman' => ['required', 'string', 'max:255', "unique:pinjaman,no_pinjaman{$suffix}"],
            'anggota_id' => ['required', 'integer', 'exists:anggota,id'],
            'jenis_id' => ['required', 'integer', 'exists:pinj_jenis,id'],
            'jaminan_id' => ['nullable', 'integer', 'exists:jaminan,id'],
            'marketing_id' => ['nullable', 'integer'],
            'sektor_id' => ['nullable', 'integer'],
            'plafon' => ['required', 'numeric', 'gt:0'],
            'bunga' => ['required', 'numeric', 'min:0', 'max:100'],
            'jangka_waktu' => ['required', 'integer', 'min:1'],
            'satuan' => ['required', 'in:'.implode(',', self::SATUAN)],
            'bayar_pokok_per' => ['nullable', 'string'],
            'pembayaran' => ['nullable', 'string', 'max:255'],
            'manual' => ['nullable', 'string'],
            'tabungan_id' => ['nullable', 'integer'],
            'kode_id' => ['nullable', 'integer'],
            'kode_koreksi' => ['nullable', 'string', 'max:255'],
            'swp' => ['nullable', 'string'],
            'spp' => ['nullable', 'string'],

            'biaya' => ['array'],
            'biaya.*.nama' => ['nullable', 'string', 'max:255'],
            'biaya.*.nominal' => ['nullable', 'string'],
            'biaya.*.persen' => ['nullable', 'boolean'],
            'biaya.*.account_id' => ['nullable', 'integer', 'exists:account,id'],

            'jaminan' => ['array'],
            'jaminan.*.nama' => ['nullable', 'string', 'max:255'],
            'jaminan.*.keterangan' => ['nullable', 'string', 'max:255'],
            'jaminan.*.nominal' => ['nullable', 'string'],

            'saksi' => ['array'],
            'saksi.*.nama' => ['required_with:saksi', 'nullable', 'string', 'max:255'],

            'surat' => ['array'],
            'surat.*.surat' => ['nullable', 'string', 'max:255'],
            'surat.*.surat_id' => ['nullable', 'integer'],
            'surat.*.keterangan' => ['nullable', 'string', 'max:255'],

            'penjamin' => ['array'],
            'penjamin.*.nama' => ['nullable', 'string', 'max:255'],
            'penjamin.*.hubungan' => ['nullable', 'string', 'max:255'],
            'penjamin.*.alamat' => ['nullable', 'string', 'max:255'],
            'penjamin.*.no_ktp' => ['nullable', 'string', 'max:255'],
            'penjamin.*.telepon' => ['nullable', 'string', 'max:255'],

            'cair_simpanan' => ['nullable', 'boolean'],
            'sms' => ['nullable', 'boolean'],
            'rekening_koran' => ['nullable', 'boolean'],
            'aktif' => ['nullable', 'boolean'],
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'no_pinjaman.required' => 'No. Pinjaman wajib diisi.',
            'no_pinjaman.unique' => 'No. Pinjaman sudah digunakan.',
            'anggota_id.required' => 'Anggota wajib dipilih.',
            'anggota_id.exists' => 'Anggota tidak valid.',
            'jenis_id.required' => 'Produk pinjaman wajib dipilih.',
            'jenis_id.exists' => 'Produk pinjaman tidak valid.',
            'plafon.required' => 'Plafon wajib diisi.',
            'plafon.gt' => 'Plafon harus lebih besar dari 0.',
            'bunga.required' => 'Bagi hasil wajib diisi.',
            'bunga.max' => 'Bagi hasil melebihi batas maksimal.',
            'jangka_waktu.required' => 'Jangka waktu wajib diisi.',
            'jangka_waktu.min' => 'Jangka waktu harus lebih besar dari 0.',
            'satuan.required' => 'Satuan wajib dipilih.',
        ]);

        // Kantor mengikuti kantor anggota.
        $kantorId = Anggota::find($validated['anggota_id'])?->kantor_id;

        // Hitung angsuran secara authoritative via service.
        $produk = PinjamanProduk::find($validated['jenis_id']);
        $hasil = app(LoanCalculationService::class)->calculate([
            'plafon' => (float) $validated['plafon'],
            'bunga' => (float) $validated['bunga'],
            'jangka_waktu' => (int) $validated['jangka_waktu'],
            'satuan' => $validated['satuan'],
            'metode' => $produk?->angsuran,
        ]);

        // Jatuh tempo otomatis dari tanggal + jangka + satuan.
        $tanggal = $validated['tanggal'];
        $jangka = (int) $validated['jangka_waktu'];
        $jatuhTempo = $this->hitungJatuhTempo($tanggal, $jangka, $validated['satuan']);

        return [
            'tanggal' => $validated['tanggal'],
            'no_pinjaman' => $validated['no_pinjaman'],
            'anggota_id' => (int) $validated['anggota_id'],
            'jenis_id' => (int) $validated['jenis_id'],
            'jaminan_id' => (int) ($validated['jaminan_id'] ?? 0),
            'marketing_id' => (int) ($validated['marketing_id'] ?? auth()->id()),
            'sektor_id' => (int) ($validated['sektor_id'] ?? 0),
            'plafon' => $validated['plafon'],
            'bunga' => $validated['bunga'],
            'jangka_waktu' => (string) $jangka,
            'satuan' => $validated['satuan'],
            'angsuran' => $produk?->angsuran ?? 'Flat',
            'nominal_angsuran' => (string) $hasil['nominal_angsuran'],
            'periode' => (string) $hasil['jumlah_periode'],
            'bayar_pokok_per' => $validated['bayar_pokok_per'] ?? '',
            'pembayaran' => $validated['pembayaran'] ?? 'manual',
            'manual' => (string) ($validated['manual'] ?? '0'),
            'tabungan_id' => (int) ($validated['tabungan_id'] ?? 0),
            'kode_id' => (int) ($validated['kode_id'] ?? 0),
            'kode_koreksi' => $validated['kode_koreksi'] ?? '',
            'swp_id' => (int) ($validated['swp'] ?? 0),
            'spp_id' => (int) ($validated['spp'] ?? 0),
            'proposal_id' => 0,
            'angsuranke' => '0',
            'rekening_koran' => $request->boolean('rekening_koran') ? '1' : '',
            'cair_simpanan' => $request->boolean('cair_simpanan') ? '1' : '',
            'sms' => $request->boolean('sms') ? '1' : '',
            'aktif' => $request->has('aktif') ? ($request->boolean('aktif') ? '1' : '0') : '1',
            'jatuh_tempo' => $jatuhTempo,
            'kantor_id' => $kantorId,
            'user_id' => auth()->id(),
        ];
    }

    /** Simpan data pendukung (biaya, jaminan, saksi, surat, penjamin). */
    private function saveRelated(Pinjaman $pinjaman, Request $request): void
    {
        $userId = auth()->id();

        PinjamanBiaya::where('pinjaman_id', $pinjaman->id)->delete();
        foreach ($request->input('biaya', []) as $b) {
            if (! $b['nama'] && ! $b['nominal']) {
                continue;
            }
            PinjamanBiaya::create([
                'pinjaman_id' => $pinjaman->id,
                'nama' => $b['nama'] ?? '',
                'nominal' => (string) ($b['nominal'] ?? 0),
                'persen' => ! empty($b['persen']) ? '1' : '0',
                'account_id' => (int) ($b['account_id'] ?? 0),
                'user_id' => $userId,
            ]);
        }

        PinjamanJaminan::where('pinjaman_id', $pinjaman->id)->delete();
        foreach ($request->input('jaminan', []) as $j) {
            if (! $j['nama']) {
                continue;
            }
            PinjamanJaminan::create([
                'pinjaman_id' => $pinjaman->id,
                'nama' => $j['nama'] ?? '',
                'keterangan' => $j['keterangan'] ?? '',
                'nominal' => (string) ($j['nominal'] ?? 0),
                'user_id' => $userId,
            ]);
        }

        PinjamanSaksi::where('pinjaman_id', $pinjaman->id)->delete();
        foreach ($request->input('saksi', []) as $s) {
            if (! $s['nama']) {
                continue;
            }
            PinjamanSaksi::create([
                'pinjaman_id' => $pinjaman->id,
                'nama' => $s['nama'] ?? '',
                'tempat_lahir' => $s['tempat_lahir'] ?? '',
                'tgl_lahir' => $s['tgl_lahir'] ?? '',
                'no_ktp' => $s['no_ktp'] ?? '',
                'alamat' => $s['alamat'] ?? '',
                'pekerjaan_id' => (int) ($s['pekerjaan_id'] ?? 0),
                'user_id' => $userId,
            ]);
        }

        PinjamanSurat::where('pinjaman_id', $pinjaman->id)->delete();
        foreach ($request->input('surat', []) as $s) {
            if (! $s['surat']) {
                continue;
            }
            PinjamanSurat::create([
                'pinjaman_id' => $pinjaman->id,
                'surat_id' => (int) ($s['surat_id'] ?? 0),
                'keterangan' => $s['keterangan'] ?? '',
                'surat' => $s['surat'] ?? '',
                'user_id' => $userId,
            ]);
        }

        PinjamanPenjamin::where('pinjaman_id', $pinjaman->id)->delete();
        foreach ($request->input('penjamin', []) as $p) {
            if (! $p['nama']) {
                continue;
            }
            PinjamanPenjamin::create([
                'pinjaman_id' => $pinjaman->id,
                'nama' => $p['nama'] ?? '',
                'hubungan' => $p['hubungan'] ?? '',
                'alamat' => $p['alamat'] ?? '',
                'no_ktp' => $p['no_ktp'] ?? '',
                'telepon' => $p['telepon'] ?? '',
                'ibu' => $p['ibu'] ?? '',
                'tampil' => '1',
                'user_id' => $userId,
            ]);
        }
    }

    /** Generate No. Pinjaman otomatis: {kodeKantor}.{yyMM}{urutan}. */
    private function generateNoPinjaman(): string
    {
        $kodeKantor = Kantor::query()->value('kode') ?? '000';
        $prefix = sprintf('%s.%s', $kodeKantor, now()->format('ym'));

        $last = Pinjaman::where('no_pinjaman', 'LIKE', $prefix.'%')
            ->orderByRaw('"no_pinjaman" DESC')
            ->value('no_pinjaman');

        $urutan = 1;
        if ($last) {
            $angka = (int) substr($last, strrpos($last, '.') + 1);
            $urutan = $angka + 1;
        }

        return $prefix.str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
    }

    private function hitungJatuhTempo(string $tanggal, int $jangka, string $satuan): string
    {
        $date = \Carbon\Carbon::parse($tanggal);
        return match ($satuan) {
            'hari' => $date->addDays($jangka)->format('Y-m-d'),
            'minggu' => $date->addWeeks($jangka)->format('Y-m-d'),
            'tahun' => $date->addYears($jangka)->format('Y-m-d'),
            default => $date->addMonths($jangka)->format('Y-m-d'),
        };
    }
}
