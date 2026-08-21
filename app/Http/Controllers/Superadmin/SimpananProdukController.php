<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\SimpananBunga;
use App\Models\SimpananJenis;
use App\Models\SimpananJenisKode;
use App\Models\SimpananKode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller CRUD Produk Simpanan (simpanan_jenis) untuk frontend Inertia.
 * Menggantikan Livewire Superadmin\Simpananproduk — termasuk bunga flat/
 * bertingkat dan pemetaan kode transaksi (simpanan_jenis_kode).
 */
class SimpananProdukController extends Controller
{
    public function index(Request $request)
    {
        $produk = SimpananJenis::query()
            ->with('idAccount:id,no_account,nama')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($qq) => $qq
                    ->where('nama', 'LIKE', "%{$term}%")
                    ->orWhere('kode', 'LIKE', "%{$term}%"));
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia('Superadmin/SimpananProduk/Index', [
            'produk' => $produk,
            'filters' => ['search' => $request->input('search', '')],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/SimpananProduk/Create', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduk($request);

        DB::transaction(function () use ($request, $validated) {
            $jenis = SimpananJenis::create([
                ...$validated['produk'],
                'user_id' => $request->user()->id,
            ]);

            // Bunga: satu baris untuk flat, banyak baris untuk bertingkat
            foreach ($validated['bunga_rows'] as $row) {
                SimpananBunga::create([
                    ...$row,
                    'jenis_id' => $jenis->id,
                    'user_id' => $request->user()->id,
                ]);
            }

            foreach ($validated['kode_ids'] as $kodeId) {
                SimpananJenisKode::create([
                    'jenis_id' => $jenis->id,
                    'kode_id' => $kodeId,
                    'user_id' => $request->user()->id,
                ]);
            }
        });

        return redirect()
            ->route('superadmin.simpanan.produk-simpanan')
            ->with('flash.status', 'Produk simpanan berhasil dibuat!');
    }

    public function show(SimpananJenis $produk)
    {
        $produk->load([
            'idAccount:id,no_account,nama',
            'bungaAccount:id,no_account,nama',
            'biayaAccount:id,no_account,nama',
            'pajakAccount:id,no_account,nama',
            'androidAccount:id,no_account,nama',
            'bungaKode:kode,nama',
            'biayaKode:kode,nama',
            'pajakKode:kode,nama',
            'tingkat:id,jenis_id,minimal,maksimal,bunga',
            'simpananKodes:kode,nama',
        ]);

        return inertia('Superadmin/SimpananProduk/Show', ['produkData' => $produk]);
    }

    public function edit(SimpananJenis $produk)
    {
        $produk->load(['tingkat', 'simpananKodes']);

        return inertia('Superadmin/SimpananProduk/Edit', [
            ...$this->formData(),
            'produkData' => $produk,
        ]);
    }

    public function update(Request $request, SimpananJenis $produk)
    {
        $validated = $this->validateProduk($request);

        DB::transaction(function () use ($produk, $validated) {
            $produk->update($validated['produk']);

            $produk->tingkat()->delete();
            foreach ($validated['bunga_rows'] as $row) {
                SimpananBunga::create([...$row, 'jenis_id' => $produk->id, 'user_id' => $produk->user_id]);
            }

            // Sinkronkan pivot kode transaksi
            SimpananJenisKode::where('jenis_id', $produk->id)->delete();
            foreach ($validated['kode_ids'] as $kodeId) {
                SimpananJenisKode::create([
                    'jenis_id' => $produk->id,
                    'kode_id' => $kodeId,
                    'user_id' => $produk->user_id,
                ]);
            }
        });

        return redirect()
            ->route('superadmin.simpanan.produk-simpanan')
            ->with('flash.status', 'Produk simpanan berhasil diperbarui!');
    }

    public function destroy(SimpananJenis $produk)
    {
        $produk->delete();

        return redirect()
            ->route('superadmin.simpanan.produk-simpanan')
            ->with('flash.status', 'Produk simpanan berhasil dihapus!');
    }

    private function formData(): array
    {
        return [
            'accounts' => Account::orderBy('no_account')->get(['id', 'no_account', 'nama']),
            'kodes' => SimpananKode::orderBy('kode')->get(['id', 'kode', 'nama']),
        ];
    }

    private function validateProduk(Request $request): array
    {
        $validated = $request->validate([
            'produk.kode' => 'required|string|max:255|unique:simpanan_jenis,kode,'.($request->route('produk')->id ?? ''),
            'produk.nama' => 'required|string|max:255|unique:simpanan_jenis,nama,'.($request->route('produk')->id ?? ''),
            'produk.account_id' => 'required|integer|exists:account,id',
            'produk.minimum' => 'nullable|numeric|min:0',
            'produk.mengendap' => 'nullable|numeric|min:0',
            'produk.bunga_id' => 'nullable|integer|exists:simpanan_kode,id',
            'produk.jenis_bunga' => 'required|integer|in:1,2',
            'produk.account_bunga' => 'nullable|integer|exists:account,id',
            'produk.rumus_bunga' => 'nullable|integer|in:1,2,3',
            'produk.bulan' => 'nullable|boolean',
            'produk.biaya_id' => 'nullable|integer|exists:simpanan_kode,id',
            'produk.biaya' => 'nullable|numeric|min:0',
            'produk.account_biaya' => 'nullable|integer|exists:account,id',
            'produk.pajak_id' => 'nullable|integer|exists:simpanan_kode,id',
            'produk.pajak' => 'nullable|numeric|min:0',
            'produk.account_pajak' => 'nullable|integer|exists:account,id',
            'produk.saldo_pajak' => 'nullable|boolean',
            'produk.android' => 'nullable|integer|exists:simpanan_kode,id',
            'produk.nominal_android' => 'nullable|numeric|min:0',
            'produk.account_android' => 'nullable|integer|exists:account,id',
            'produk.nominal' => 'nullable|numeric|min:0',
            'produk.jenis' => 'required|integer|between:1,7',
            'produk.setor_id' => 'nullable|integer|exists:simpanan_kode,id',
            'produk.tarik_id' => 'nullable|integer|exists:simpanan_kode,id',
            'produk.insentif' => 'nullable|numeric|min:0',
            'produk.saham' => 'nullable|boolean',

            'bunga_flat' => 'nullable|numeric|min:0',
            'tingkat' => 'required_if:produk.jenis_bunga,2|array',
            'tingkat.*.minimal' => 'nullable|numeric|min:0',
            'tingkat.*.maksimal' => 'nullable|numeric|min:0',
            'tingkat.*.bunga' => 'required_with:tingkat.*.minimal|numeric|min:0',

            'kode_ids' => 'present|array',
            'kode_ids.*' => 'integer|exists:simpanan_kode,id',
        ], [
            'produk.kode.required' => 'Kode produk simpanan wajib diisi.',
            'produk.kode.unique' => 'Kode produk simpanan sudah digunakan.',
            'produk.nama.required' => 'Nama produk simpanan wajib diisi.',
            'produk.nama.unique' => 'Nama produk simpanan sudah digunakan.',
            'produk.account_id.required' => 'Akun simpanan wajib dipilih.',
            'produk.jenis.required' => 'Jenis simpanan wajib dipilih.',
            'tingkat.*.bunga.required_with' => 'Bunga tiap tingkat wajib diisi.',
        ]);

        // Susun baris bunga sesuai jenis
        if ((int) $validated['produk']['jenis_bunga'] === 2) {
            $rows = collect($validated['tingkat'] ?? [])
                ->filter(fn ($r) => filled($r['minimal']) && filled($r['maksimal']) && filled($r['bunga']))
                ->map(fn ($r) => [
                    'minimal' => $r['minimal'],
                    'maksimal' => $r['maksimal'],
                    'bunga' => $r['bunga'],
                ])
                ->values()
                ->all();
            abort_if(empty($rows), 422, 'Minimal satu tingkatan bunga harus diisi.');
            $validated['produk']['bunga'] = null;
        } else {
            abort_if(blank($validated['bunga_flat'] ?? null), 422, 'Bunga flat wajib diisi.');
            $rows = [['minimal' => null, 'maksimal' => null, 'bunga' => $validated['bunga_flat']]];
            $validated['produk']['bunga'] = $validated['bunga_flat'];
        }

        unset($validated['tingkat'], $validated['bunga_flat']);
        $validated['bunga_rows'] = $rows;

        // Normalisasi boolean
        $validated['produk']['bulan'] = (bool) ($validated['produk']['bulan'] ?? false);
        $validated['produk']['saldo_pajak'] = (bool) ($validated['produk']['saldo_pajak'] ?? false);
        $validated['produk']['saham'] = (bool) ($validated['produk']['saham'] ?? false);

        return $validated;
    }
}
