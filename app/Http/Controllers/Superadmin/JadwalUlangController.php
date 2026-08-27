<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Anggota;
use App\Models\AngsuranPinjaman;
use App\Models\JadwalUlang;
use App\Models\JadwalUlangBiaya;
use App\Models\JadwalUlangDetail;
use App\Models\JadwalUlangJaminan;
use App\Models\JadwalUlangPenjamin;
use App\Models\JadwalUlangSaksi;
use App\Models\JadwalUlangSurat;
use App\Models\Jaminan;
use App\Models\Kantor;
use App\Models\Marketing;
use App\Models\Pinjaman;
use App\Models\PinjamanProduk;
use App\Models\Simpanan;
use App\Models\SimpananKode;
use App\Services\LoanCalculationService;
use Illuminate\Http\Request;

/**
 * Controller Jadwal Ulang Pinjaman (menu "Jadwal Ulang" di bawah modul Pinjaman).
 *
 * Form memakai ulang PinjamanForm (6 tab: Pinjaman, Biaya, Jaminan, Saksi,
 * Surat, Penjamin) dengan konteks reschedule: input "No. Pinjaman Lama"
 * (pinjaman asal). Semua data disimpan di jadwal_ulang (header) +
 * jadwal_ulang_detail (deret angsuran baru) + 5 tabel detail pendukung.
 */
class JadwalUlangController extends Controller
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
        $status = (string) $request->string('status');

        $jadwal = JadwalUlang::query()
            ->with(['pinjaman.anggota:id,no_anggota,nama', 'kantor:id,nama_kantor'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('no_transaksi', 'ILIKE', "%{$search}%")
                        ->orWhere('no_pinjaman_lama', 'ILIKE', "%{$search}%")
                        ->orWhereHas('pinjaman.anggota', fn ($a) => $a
                            ->where('nama', 'ILIKE', "%{$search}%")
                            ->orWhere('no_anggota', 'ILIKE', "%{$search}%"));
                });
            })
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->orderBy('created_at', 'DESC')
            ->paginate((int) ($request->input('per_page') ?: 10))
            ->withQueryString();

        return inertia('Superadmin/JadwalUlang/Index', [
            'jadwal' => $jadwal,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/JadwalUlang/Create', [
            ...$this->formData(),
        ]);
    }

    public function edit(JadwalUlang $jadwalUlang)
    {
        $jadwalUlang->load([
            'biaya', 'jaminan', 'saksi', 'surat', 'penjamin',
            'anggota:id,no_anggota,nama,alamat,no_identitas,telepon,status',
        ]);

        return inertia('Superadmin/JadwalUlang/Edit', [
            ...$this->formData(),
            'transaksi' => $this->asEditRow($jadwalUlang),
        ]);
    }

    public function show(JadwalUlang $jadwalUlang)
    {
        $jadwalUlang->load([
            'pinjaman.anggota', 'user', 'kantor', 'details',
            'biaya', 'jaminan', 'saksi', 'surat', 'penjamin',
        ]);

        return inertia('Superadmin/JadwalUlang/Show', [
            'transaksi' => $jadwalUlang,
        ]);
    }

    /**
     * Daftar pinjaman aktif milik anggota + sisa pokok (untuk lookup).
     */
    public function pinjamanByAnggota(Anggota $anggota)
    {
        $pinjaman = Pinjaman::where('anggota_id', $anggota->id)
            ->where('aktif', '1')
            ->with('jenisPinjaman:id,nama', 'anggota:id,no_anggota,nama')
            ->get();

        $rows = $pinjaman->map(function ($p) {
            return [
                'id' => $p->id,
                'no_pinjaman' => $p->no_pinjaman,
                'tanggal' => $p->tanggal,
                'plafon' => (float) $p->plafon,
                'nominal_angsuran' => (float) $p->nominal_angsuran,
                'bunga' => (float) $p->bunga,
                'jangka_waktu' => (int) $p->jangka_waktu,
                'bayar_pokok_per' => $p->bayar_pokok_per,
                'satuan' => $p->satuan,
                'angsuran' => $p->angsuran,
                'produk' => $p->jenisPinjaman?->nama,
                'no_anggota' => $p->anggota?->no_anggota,
                'nama_anggota' => $p->anggota?->nama,
                'sisa_pokok' => $this->sisaPokok($p),
            ];
        });

        return response()->json($rows);
    }

    /**
     * Data lengkap pinjaman asal (PinjamanEditRow) untuk mengisi form reschedule.
     */
    public function pinjamanAsal(Pinjaman $pinjaman)
    {
        $pinjaman->load([
            'biaya', 'jaminan', 'saksi', 'surat', 'penjamin',
            'anggota:id,no_anggota,nama,alamat,no_identitas,telepon,status',
        ]);

        return $this->asEditRow($pinjaman);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $data['header']['no_transaksi'] = $this->generateNoTransaksi();
        $jadwalUlang = JadwalUlang::create($data['header']);
        $this->saveSchedule($jadwalUlang, $data['details']);
        $this->saveRelated($jadwalUlang, $data['related']);

        return redirect()
            ->route('superadmin.pinjaman.jadwal-ulang')
            ->with('flash.status', 'Jadwal ulang pinjaman berhasil dibuat.');
    }

    public function update(Request $request, JadwalUlang $jadwalUlang)
    {
        $data = $this->validatedData($request);

        $jadwalUlang->update($data['header']);
        JadwalUlangDetail::where('jadwal_ulang_id', $jadwalUlang->id)->delete();
        $this->saveSchedule($jadwalUlang, $data['details']);
        $this->saveRelated($jadwalUlang, $data['related']);

        return redirect()
            ->route('superadmin.pinjaman.jadwal-ulang')
            ->with('flash.status', 'Jadwal ulang pinjaman berhasil diperbarui.');
    }

    public function destroy(JadwalUlang $jadwalUlang)
    {
        $jadwalUlang->delete();

        return redirect()
            ->route('superadmin.pinjaman.jadwal-ulang')
            ->with('flash.status', 'Jadwal ulang pinjaman berhasil dihapus.');
    }

    /* ------------------------------------------------------------------ */

    /** Data opsi untuk dropdown / lookup form (sama dengan form Pinjaman). */
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

    /** Sisa pokok = plafon - total nominal_pokok yang sudah dibayar. */
    private function sisaPokok(Pinjaman $pinjaman): float
    {
        $terbayar = (float) AngsuranPinjaman::where('pinjaman_id', $pinjaman->id)
            ->sum('nominal_pokok');
        return max(0, round((float) $pinjaman->plafon - $terbayar, 2));
    }

    /** Validasi + susun header / details / related dari payload PinjamanForm. */
    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'no_pinjaman' => ['nullable', 'string', 'max:255'],
            'no_pinjaman_lama' => ['nullable', 'string', 'max:255'],
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
            'jenis_angsuran' => ['nullable', 'string', 'max:255'],
            'manual' => ['nullable', 'string'],
            'tabungan_id' => ['nullable', 'integer'],
            'kode_id' => ['nullable', 'integer'],
            'kode_koreksi' => ['nullable', 'string', 'max:255'],
            'swp' => ['nullable', 'string'],
            'spp' => ['nullable', 'string'],
            'keterangan' => ['nullable', 'string', 'max:255'],

            'biaya' => ['array'],
            'biaya.*.nama' => ['nullable', 'string', 'max:255'],
            'biaya.*.nominal' => ['nullable', 'string'],
            'biaya.*.persen' => ['nullable', 'boolean'],
            'biaya.*.account_id' => ['nullable', 'integer'],

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
            'anggota_id.required' => 'Anggota wajib dipilih.',
            'jenis_id.required' => 'Produk pinjaman wajib dipilih.',
            'plafon.required' => 'Plafon wajib diisi.',
            'plafon.gt' => 'Plafon harus lebih besar dari 0.',
            'bunga.required' => 'Bagi hasil wajib diisi.',
            'jangka_waktu.required' => 'Jangka waktu wajib diisi.',
            'jangka_waktu.min' => 'Jangka waktu harus lebih besar dari 0.',
            'satuan.required' => 'Satuan wajib dipilih.',
        ]);

        $noPinjamanLama = $validated['no_pinjaman_lama'] ?? '';
        $pinjaman = Pinjaman::where('no_pinjaman', $noPinjamanLama)
            ->when($noPinjamanLama === '', fn ($q) => $q->where('anggota_id', $validated['anggota_id']))
            ->latest('id')
            ->first();

        $produk = PinjamanProduk::find($validated['jenis_id']);
        $metode = $validated['jenis_angsuran'] ?? $produk?->angsuran ?? 'Flat';

        $hasil = app(LoanCalculationService::class)->calculate([
            'plafon' => (float) $validated['plafon'],
            'bunga' => (float) $validated['bunga'],
            'jangka_waktu' => (int) $validated['jangka_waktu'],
            'satuan' => $validated['satuan'],
            'metode' => $metode,
        ]);

        $details = [];
        foreach ($hasil['jadwal'] as $row) {
            $details[] = [
                'angsuran_ke' => $row['ke'],
                'nominal_pokok' => $row['pokok'],
                'nominal_bunga' => $row['bunga'],
                'total_angsuran' => $row['angsuran'],
                'sisa_pokok' => $row['sisa'],
            ];
        }

        $kantorId = Anggota::find($validated['anggota_id'])?->kantor_id
            ?? auth()->user()->kantor_id ?? 1;

        $header = [
            'no_pinjaman_lama' => $noPinjamanLama,
            'no_pinjaman' => $validated['no_pinjaman'] ?? '',
            'tanggal' => $validated['tanggal'],
            'tgl_transaksi' => $validated['tanggal'],
            'pinjaman_id' => $pinjaman?->id ?? 0,
            'anggota_id' => (int) $validated['anggota_id'],
            'jenis_id' => (int) $validated['jenis_id'],
            'jaminan_id' => (int) ($validated['jaminan_id'] ?? 0),
            'marketing_id' => (int) ($validated['marketing_id'] ?? auth()->id()),
            'sektor_id' => (int) ($validated['sektor_id'] ?? 0),
            'plafon' => $validated['plafon'],
            'sisa_pokok' => $pinjaman ? $this->sisaPokok($pinjaman) : (float) $validated['plafon'],
            'bunga' => $validated['bunga'],
            'jangka_waktu' => (string) $validated['jangka_waktu'],
            'satuan' => $validated['satuan'],
            'metode' => $metode,
            'jenis_angsuran' => $metode,
            'bayar_pokok_per' => $validated['bayar_pokok_per'] ?? '',
            'pembayaran' => $validated['pembayaran'] ?? 'manual',
            'jatuh_tempo' => $this->hitungJatuhTempo($validated['tanggal'], (int) $validated['jangka_waktu'], $validated['satuan']),
            'manual' => (string) ($validated['manual'] ?? '0'),
            'tabungan_id' => (int) ($validated['tabungan_id'] ?? 0),
            'kode_id' => (int) ($validated['kode_id'] ?? 0),
            'kode_koreksi' => $validated['kode_koreksi'] ?? '',
            'swp_id' => (int) ($validated['swp'] ?? 0),
            'spp_id' => (int) ($validated['spp'] ?? 0),
            'periode' => (int) $hasil['jumlah_periode'],
            'nominal_angsuran' => $hasil['nominal_angsuran'],
            'total_bunga' => $hasil['total_bunga'],
            'cair_simpanan' => $request->boolean('cair_simpanan') ? '1' : '',
            'sms' => $request->boolean('sms') ? '1' : '',
            'rekening_koran' => $request->boolean('rekening_koran') ? '1' : '',
            'aktif' => $request->boolean('aktif') ? '1' : '0',
            'keterangan' => $validated['keterangan'] ?? null,
            'user_id' => auth()->id(),
            'kantor_id' => $kantorId,
            'status' => $request->boolean('aktif') ? 'posted' : 'draft',
        ];

        $related = $request->only(['biaya', 'jaminan', 'saksi', 'surat', 'penjamin']);

        return ['header' => $header, 'details' => $details, 'related' => $related];
    }

    private function saveSchedule(JadwalUlang $jadwalUlang, array $details): void
    {
        foreach ($details as $row) {
            JadwalUlangDetail::create($row + ['jadwal_ulang_id' => $jadwalUlang->id]);
        }
    }

    private function saveRelated(JadwalUlang $jadwalUlang, array $related): void
    {
        $userId = auth()->id();

        JadwalUlangBiaya::where('jadwal_ulang_id', $jadwalUlang->id)->delete();
        foreach ($related['biaya'] ?? [] as $b) {
            if (! $b['nama'] && ! $b['nominal']) {
                continue;
            }
            JadwalUlangBiaya::create([
                'jadwal_ulang_id' => $jadwalUlang->id,
                'nama' => $b['nama'] ?? '',
                'nominal' => (string) ($b['nominal'] ?? 0),
                'persen' => ! empty($b['persen']) ? '1' : '0',
                'account_id' => (int) ($b['account_id'] ?? 0),
                'user_id' => $userId,
            ]);
        }

        JadwalUlangJaminan::where('jadwal_ulang_id', $jadwalUlang->id)->delete();
        foreach ($related['jaminan'] ?? [] as $j) {
            if (! $j['nama']) {
                continue;
            }
            JadwalUlangJaminan::create([
                'jadwal_ulang_id' => $jadwalUlang->id,
                'nama' => $j['nama'] ?? '',
                'keterangan' => $j['keterangan'] ?? '',
                'nominal' => (string) ($j['nominal'] ?? 0),
                'user_id' => $userId,
            ]);
        }

        JadwalUlangSaksi::where('jadwal_ulang_id', $jadwalUlang->id)->delete();
        foreach ($related['saksi'] ?? [] as $s) {
            if (! $s['nama']) {
                continue;
            }
            JadwalUlangSaksi::create([
                'jadwal_ulang_id' => $jadwalUlang->id,
                'nama' => $s['nama'] ?? '',
                'tempat_lahir' => $s['tempat_lahir'] ?? '',
                'tgl_lahir' => $s['tgl_lahir'] ?? '',
                'no_ktp' => $s['no_ktp'] ?? '',
                'alamat' => $s['alamat'] ?? '',
                'pekerjaan_id' => (int) ($s['pekerjaan_id'] ?? 0),
                'user_id' => $userId,
            ]);
        }

        JadwalUlangSurat::where('jadwal_ulang_id', $jadwalUlang->id)->delete();
        foreach ($related['surat'] ?? [] as $s) {
            if (! $s['surat']) {
                continue;
            }
            JadwalUlangSurat::create([
                'jadwal_ulang_id' => $jadwalUlang->id,
                'surat_id' => (int) ($s['surat_id'] ?? 0),
                'keterangan' => $s['keterangan'] ?? '',
                'surat' => $s['surat'] ?? '',
                'user_id' => $userId,
            ]);
        }

        JadwalUlangPenjamin::where('jadwal_ulang_id', $jadwalUlang->id)->delete();
        foreach ($related['penjamin'] ?? [] as $p) {
            if (! $p['nama']) {
                continue;
            }
            JadwalUlangPenjamin::create([
                'jadwal_ulang_id' => $jadwalUlang->id,
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

    /** Konversi JadwalUlang / Pinjaman menjadi bentuk PinjamanEditRow utk form. */
    private function asEditRow($model): array
    {
        // Normalisasi relasi anggota & relasi pendukung seragam (JadwalUlang atau Pinjaman).
        $anggota = $this->resolveAnggota($model);
        $jenisId = $model->jenis_id ?? $model->pinjaman?->jenis_id;

        return [
            'id' => $model->id,
            'no_transaksi' => $model instanceof JadwalUlang ? $model->no_transaksi : null,
            'tanggal' => $model->tanggal ? $model->tanggal : $model->tgl_transaksi,
            'no_pinjaman' => $model instanceof Pinjaman ? '' : ($model->no_pinjaman ?? ''),
            'no_pinjaman_lama' => $model instanceof JadwalUlang
                ? ($model->no_pinjaman_lama ?? $model->pinjaman?->no_pinjaman)
                : $model->no_pinjaman,
            'anggota_id' => (string) ($anggota->id ?? $model->anggota_id ?? 0),
            'jenis_id' => (string) ($jenisId ?? ''),
            'jaminan_id' => $model->jaminan_id ? (string) $model->jaminan_id : null,
            'marketing_id' => $model->marketing_id ? (string) $model->marketing_id : null,
            'sektor_id' => $model->sektor_id ? (string) $model->sektor_id : null,
            'plafon' => (string) $model->plafon,
            'bunga' => (string) $model->bunga,
            'jangka_waktu' => (string) $model->jangka_waktu,
            'satuan' => $model->satuan,
            'bayar_pokok_per' => $model->bayar_pokok_per ?? null,
            'jatuh_tempo' => $model->jatuh_tempo ?? null,
            'pembayaran' => $model->pembayaran ?? 'manual',
            'manual' => $model->manual ?? '0',
            'tabungan_id' => $model->tabungan_id ? (string) $model->tabungan_id : null,
            'kode_id' => $model->kode_id ? (string) $model->kode_id : null,
            'kode_koreksi' => $model->kode_koreksi ?? null,
            'swp_id' => (string) ($model->swp_id ?? 0),
            'spp_id' => (string) ($model->spp_id ?? 0),
            'angsuran' => $model->angsuran ?? $model->jenis_angsuran ?? $model->metode ?? null,
            'nominal_angsuran' => (string) $model->nominal_angsuran,
            'cair_simpanan' => $model->cair_simpanan ?? '',
            'sms' => $model->sms ?? '',
            'rekening_koran' => $model->rekening_koran ?? '',
            'aktif' => $model->aktif ?? '1',
            'anggota' => $anggota ? [
                'id' => $anggota->id,
                'no_anggota' => $anggota->no_anggota,
                'nama' => $anggota->nama,
                'alamat' => $anggota->alamat ?? '',
                'no_identitas' => $anggota->no_identitas ?? '',
                'telepon' => $anggota->telepon ?? '',
            ] : null,
            'biaya' => $this->mapDetail($model, 'biaya', ['nama', 'nominal', 'persen', 'account_id']),
            'jaminan' => $this->mapDetail($model, 'jaminan', ['nama', 'keterangan', 'nominal']),
            'saksi' => $this->mapDetail($model, 'saksi', ['nama', 'tempat_lahir', 'tgl_lahir', 'no_ktp', 'alamat', 'pekerjaan_id']),
            'surat' => $this->mapDetail($model, 'surat', ['surat_id', 'keterangan', 'surat']),
            'penjamin' => $this->mapDetail($model, 'penjamin', ['nama', 'hubungan', 'alamat', 'no_ktp', 'telepon', 'ibu']),
        ];
    }

    private function resolveAnggota($model)
    {
        if (property_exists($model, 'relationLoaded') && $model->relationLoaded('anggota')) {
            return $model->anggota;
        }
        if (isset($model->anggota_id) && $model->anggota_id) {
            return Anggota::find($model->anggota_id);
        }
        return $model->pinjaman?->anggota;
    }

    private function mapDetail($model, string $relation, array $keys): array
    {
        $rows = $model->$relation ?? collect();
        return collect($rows)->map(function ($row) use ($keys) {
            $out = [];
            foreach ($keys as $k) {
                $v = $row->$k ?? $row[$k] ?? null;
                if ($k === 'persen') {
                    $v = $v === '1' || $v === true;
                }
                $out[$k] = $v;
            }
            return $out;
        })->values()->all();
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

    private function generateNoTransaksi(): string
    {
        return 'JU-'.now()->format('YmdHis').rand(10, 99);
    }
}
