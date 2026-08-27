<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\LoanCostComponent;
use App\Models\Marketing;
use App\Models\PinjamanProduk;
use App\Models\Proposal;
use App\Models\ProposalBiaya;
use App\Services\LoanCalculationService;
use Illuminate\Http\Request;

/**
 * Controller CRUD Proposal Pinjaman (menu "Proposal" di bawah modul Pinjaman).
 *
 * Form 2 kolom: kiri = data pinjaman, kanan = informasi debitur + tabel biaya
 * + Total Terima. Angsuran dihitung via LoanCalculationService; Total Terima
 * dihitung di backend = Plafon - SUM(biaya yang is_deducted_from_disbursement).
 */
class ProposalController extends Controller
{
    private const SATUAN = ['hari', 'minggu', 'bulan', 'tahun'];
    private const METODE = ['Anuitas', 'Flat', 'Flat Efektif', 'Pokok Tetap', 'Bagi Hasil Menurun'];
    private const LIST_BAYAR_POKOK_PER = ['1', '2', '3', '6', '12'];

    public function index(Request $request)
    {
        $search = (string) $request->string('search');

        $proposal = Proposal::query()
            ->with(['anggota:id,no_anggota,nama', 'jenisPinjaman:id,nama', 'kantor:id,nama_kantor'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('no_bukti', 'ILIKE', "%{$search}%")
                        ->orWhereHas('anggota', fn ($a) => $a
                            ->where('nama', 'ILIKE', "%{$search}%")
                            ->orWhere('no_anggota', 'ILIKE', "%{$search}%"));
                });
            })
            ->orderBy('created_at', 'DESC')
            ->paginate((int) ($request->input('per_page') ?: 10))
            ->withQueryString();

        return inertia('Superadmin/Proposal/Index', [
            'proposal' => $proposal,
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/Proposal/Create', [
            ...$this->formData(),
            'noBuktiOtomatis' => $this->generateNoBukti(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $proposal = Proposal::create($data);
        $this->saveBiaya($proposal, $request);

        return redirect()
            ->route('superadmin.pinjaman.proposal')
            ->with('flash.status', 'Proposal pinjaman berhasil dibuat.');
    }

    public function edit(Proposal $proposal)
    {
        $proposal->load([
            'biaya',
            'anggota:id,no_anggota,nama,alamat,no_identitas,telepon,status',
            'marketing:id,kode,nama',
        ]);

        return inertia('Superadmin/Proposal/Edit', [
            ...$this->formData(),
            'proposal' => $proposal,
        ]);
    }

    public function update(Request $request, Proposal $proposal)
    {
        $data = $this->validatedData($request, $proposal->id);

        $proposal->update($data);
        $this->saveBiaya($proposal, $request);

        return redirect()
            ->route('superadmin.pinjaman.proposal')
            ->with('flash.status', 'Proposal pinjaman berhasil diperbarui.');
    }

    public function destroy(Proposal $proposal)
    {
        ProposalBiaya::where('proposal_id', $proposal->id)->delete();
        $proposal->delete();

        return redirect()
            ->route('superadmin.pinjaman.proposal')
            ->with('flash.status', 'Proposal pinjaman berhasil dihapus.');
    }

    /* ------------------------------------------------------------------ */

    private function formData(): array
    {
        return [
            'anggotaOptions' => Anggota::orderBy('nama')->get([
                'id', 'no_anggota', 'nama', 'alamat', 'no_identitas', 'telepon', 'status', 'no_hp',
            ]),
            'produkOptions' => PinjamanProduk::orderBy('nama')->get([
                'id', 'nama', 'angsuran', 'bunga',
            ]),
            'marketingOptions' => Marketing::orderBy('nama')->get(['id', 'kode', 'nama']),
            'accountOptions' => Account::orderBy('no_account')->get(['id', 'no_account', 'nama']),
            'costComponents' => LoanCostComponent::where('active', '1')->orderBy('name')->get(),
            'satuanOptions' => collect(self::SATUAN)->map(fn ($s) => [
                'value' => $s,
                'label' => ucfirst($s),
            ]),
            'metodeOptions' => self::METODE,
            'bayarPokokPerOptions' => self::LIST_BAYAR_POKOK_PER,
        ];
    }

    /** Validasi + data aman untuk create & update. */
    private function validatedData(Request $request, ?int $ignoreId = null): array
    {
        $suffix = is_null($ignoreId) ? '' : ','.$ignoreId;

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'no_bukti' => ['required', 'string', 'max:255', "unique:proposal,no_bukti{$suffix}"],
            'anggota_id' => ['required', 'integer', 'exists:anggota,id'],
            'jenis_id' => ['required', 'integer', 'exists:pinj_jenis,id'],
            'marketing_id' => ['nullable', 'integer'],
            'plafon' => ['required', 'numeric', 'gt:0'],
            'bunga' => ['required', 'numeric', 'min:0', 'max:100'],
            'jangka_waktu' => ['required', 'integer', 'min:1'],
            'satuan' => ['required', 'in:'.implode(',', self::SATUAN)],
            'bayar_pokok_per' => ['nullable', 'string'],
            'pembayaran' => ['nullable', 'string', 'max:255'],
            'setiap_saat' => ['nullable', 'boolean'],
            'jenis_angsuran' => ['nullable', 'string', 'max:255'],
            'penggunaan_kredit' => ['nullable', 'string', 'max:255'],
            'jaminan' => ['nullable', 'string', 'max:255'],
            'nominal_angsuran' => ['nullable', 'numeric', 'min:0'],

            'biaya' => ['array'],
            'biaya.*.component_id' => ['nullable', 'integer', 'exists:loan_cost_components,id'],
            'biaya.*.nama' => ['nullable', 'string', 'max:255'],
            'biaya.*.nominal' => ['nullable', 'string'],
            'biaya.*.persen' => ['nullable', 'boolean'],
            'biaya.*.account_id' => ['nullable', 'integer', 'exists:account,id'],
            'biaya.*.is_deducted_from_disbursement' => ['nullable', 'boolean'],
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'no_bukti.required' => 'No. Bukti wajib diisi.',
            'no_bukti.unique' => 'No. Bukti sudah digunakan.',
            'anggota_id.required' => 'Nama Debitur wajib dipilih.',
            'anggota_id.exists' => 'Debitur tidak valid.',
            'jenis_id.required' => 'Produk pinjaman wajib dipilih.',
            'jenis_id.exists' => 'Produk pinjaman tidak valid.',
            'plafon.required' => 'Plafon wajib diisi.',
            'plafon.gt' => 'Plafon harus lebih besar dari 0.',
            'bunga.required' => 'B. Hasil / Tahun wajib diisi.',
            'bunga.max' => 'B. Hasil / Tahun melebihi batas maksimal.',
            'jangka_waktu.required' => 'Jangka waktu wajib diisi.',
            'jangka_waktu.min' => 'Jangka waktu harus lebih besar dari 0.',
            'satuan.required' => 'Satuan wajib dipilih.',
        ]);

        // Kantor mengikuti kantor anggota.
        $kantorId = Anggota::find($validated['anggota_id'])?->kantor_id;

        // Nama produk (jenis_id) -> metode angsuran.
        $produk = PinjamanProduk::find($validated['jenis_id']);
        $metode = $validated['jenis_angsuran'] ?: ($produk?->angsuran ?: 'Flat');

        // Hitung angsuran authoritative via service.
        $hasil = app(LoanCalculationService::class)->calculate([
            'plafon' => (float) $validated['plafon'],
            'bunga' => (float) $validated['bunga'],
            'jangka_waktu' => (int) $validated['jangka_waktu'],
            'satuan' => $validated['satuan'],
            'metode' => $metode,
        ]);

        // Biaya -> nominal riil (persen dikonversi terhadap plafon).
        $plafon = (float) $validated['plafon'];
        $biayaRill = collect($request->input('biaya', []))
            ->filter(fn ($b) => $b['nama'] ?? null)
            ->map(function ($b) use ($plafon) {
                $nominal = (float) ($b['nominal'] ?? 0);
                if (! empty($b['persen'])) {
                    $nominal = $plafon * ($nominal / 100);
                }
                return [
                    'nominal' => $nominal,
                    'deduct' => ! empty($b['is_deducted_from_disbursement']),
                ];
            });

        $totalBiaya = round($biayaRill->sum('nominal'), 0);
        $totalPotongan = round($biayaRill->where('deduct', true)->sum('nominal'), 0);
        $totalTerima = round($plafon - $totalPotongan, 0);

        // Total Terima tidak boleh negatif.
        abort_if($totalTerima < 0, 422, 'Total biaya melebihi plafon.');

        return [
            'tanggal' => $validated['tanggal'],
            'no_bukti' => $validated['no_bukti'],
            'anggota_id' => (int) $validated['anggota_id'],
            'jenis_id' => (int) $validated['jenis_id'],
            'marketing_id' => (int) ($validated['marketing_id'] ?? 0),
            'plafon' => (string) $plafon,
            'bunga' => (string) $validated['bunga'],
            'jangka_waktu' => (string) $validated['jangka_waktu'],
            'satuan' => $validated['satuan'],
            'bayar_pokok_per' => $validated['bayar_pokok_per'] ?? '',
            'pembayaran' => $validated['pembayaran'] ?? 'per-jangka',
            'setiap_saat' => $request->boolean('setiap_saat') ? '1' : '0',
            'jenis_angsuran' => $metode,
            'nominal_angsuran' => (string) $hasil['nominal_angsuran'],
            'penggunaan_kredit' => $validated['penggunaan_kredit'] ?? '',
            'jaminan' => $validated['jaminan'] ?? '',
            'total_biaya' => (string) $totalBiaya,
            'total_terima' => (string) $totalTerima,
            'status' => '1',
            'kantor_id' => $kantorId,
            'user_id' => auth()->id(),
        ];
    }

    /** Simpan detail biaya proposal (delete + recreate). */
    private function saveBiaya(Proposal $proposal, Request $request): void
    {
        $userId = auth()->id();
        $plafon = max(0, (float) $proposal->plafon);

        ProposalBiaya::where('proposal_id', $proposal->id)->delete();

        foreach ($request->input('biaya', []) as $b) {
            if (! ($b['nama'] ?? null) && ! ($b['nominal'] ?? null)) {
                continue;
            }
            $nominal = (float) ($b['nominal'] ?? 0);
            if (! empty($b['persen'])) {
                $nominal = $plafon * ($nominal / 100);
            }
            ProposalBiaya::create([
                'proposal_id' => $proposal->id,
                'component_id' => (int) ($b['component_id'] ?? 0),
                'nama' => $b['nama'] ?? '',
                'nominal' => (string) round($nominal, 0),
                'persen' => ! empty($b['persen']) ? '1' : '0',
                'account_id' => (int) ($b['account_id'] ?? 0),
                'is_deducted_from_disbursement' => ! empty($b['is_deducted_from_disbursement']) ? '1' : '0',
                'user_id' => $userId,
            ]);
        }
    }

    /** Generate No. Bukti otomatis: {kodeKantor}.{yyMM}{urutan}. */
    private function generateNoBukti(): string
    {
        $kodeKantor = Kantor::query()->value('kode') ?? '000';
        $prefix = sprintf('%s.%s', $kodeKantor, now()->format('ym'));

        $last = Proposal::where('no_bukti', 'LIKE', $prefix.'%')
            ->orderByRaw('"no_bukti" DESC')
            ->value('no_bukti');

        $urutan = 1;
        if ($last) {
            $angka = (int) substr($last, strrpos($last, '.') + 1);
            $urutan = $angka + 1;
        }

        return $prefix.str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
    }
}
