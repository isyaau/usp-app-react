<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Parameter;
use App\Models\PinjamanKomponen;
use App\Models\PinjamanKolektabilitas;
use App\Models\PinjamanProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller CRUD Produk Pinjaman untuk frontend Inertia.
 * Menggantikan Livewire Superadmin\Pinjamanproduk — termasuk
 * kolektabilitas (4 kualitas) dan komponen dinamis dengan rumus.
 */
class PinjamanProdukController extends Controller
{
    public const LIST_ANGSURAN = [
        'Anuitas', 'Flat', 'Flat Efektif', 'Pokok Tetap', 'Bagi Hasil Menurun',
    ];

    public function index(Request $request)
    {
        $produk = PinjamanProduk::query()
            ->with('account:id,no_account,nama')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($qq) => $qq
                    ->where('nama', 'LIKE', "%{$term}%")
                    ->orWhere('kode', 'LIKE', "%{$term}%"));
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia('Superadmin/PinjamanProduk/Index', [
            'produk' => $produk,
            'filters' => ['search' => $request->input('search', '')],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/PinjamanProduk/Create', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduk($request);

        $produk = DB::transaction(function () use ($request, $validated) {
            $produk = PinjamanProduk::create([
                ...$validated['produk'],
                'user_id' => $request->user()->id,
            ]);

            foreach ($validated['kolektabilitas'] as $k) {
                PinjamanKolektabilitas::create([
                    ...$k,
                    'pinj_jenis_id' => $produk->id,
                    'user_id' => $request->user()->id,
                ]);
            }

            foreach ($validated['komponen'] as $c) {
                PinjamanKomponen::create([
                    ...$c,
                    'pinj_jenis_id' => $produk->id,
                    'tunggakan' => 0,
                    'denda_t' => 0,
                    'denda_h' => 0,
                    'user_id' => $request->user()->id,
                ]);
            }

            return $produk;
        });

        return redirect()
            ->route('superadmin.pinjaman.produk')
            ->with('flash.status', 'Produk pinjaman berhasil dibuat!');
    }

    public function show(PinjamanProduk $produk)
    {
        $produk->load([
            'account:id,no_account,nama',
            'kolektabilitas:id,pinj_jenis_id,kualitas_id,keterangan',
            'komponen:id,pinj_jenis_id,nama,nominal,persen,account_id,cair,angsuran,penalti,rumus_c,rumus_a,rumus_p',
        ]);

        return inertia('Superadmin/PinjamanProduk/Show', ['produkData' => $produk]);
    }

    public function edit(PinjamanProduk $produk)
    {
        $produk->load([
            'kolektabilitas',
            'komponen',
        ]);

        return inertia('Superadmin/PinjamanProduk/Edit', [
            ...$this->formData(),
            'produkData' => $produk,
        ]);
    }

    public function update(Request $request, PinjamanProduk $produk)
    {
        $validated = $this->validateProduk($request);

        DB::transaction(function () use ($produk, $validated) {
            $produk->update($validated['produk']);

            // Ganti seluruh detail
            $produk->kolektabilitas()->delete();
            $produk->komponen()->delete();

            foreach ($validated['kolektabilitas'] as $k) {
                PinjamanKolektabilitas::create([...$k, 'pinj_jenis_id' => $produk->id, 'user_id' => $produk->user_id]);
            }
            foreach ($validated['komponen'] as $c) {
                PinjamanKomponen::create([
                    ...$c,
                    'pinj_jenis_id' => $produk->id,
                    'tunggakan' => 0, 'denda_t' => 0, 'denda_h' => 0,
                    'user_id' => $produk->user_id,
                ]);
            }
        });

        return redirect()
            ->route('superadmin.pinjaman.produk')
            ->with('flash.status', 'Produk pinjaman berhasil diperbarui!');
    }

    public function destroy(PinjamanProduk $produk)
    {
        $produk->delete();

        return redirect()
            ->route('superadmin.pinjaman.produk')
            ->with('flash.status', 'Produk pinjaman berhasil dihapus!');
    }

    private function formData(): array
    {
        return [
            'accounts' => Account::orderBy('no_account')->get(['id', 'no_account', 'nama']),
            'listAngsuran' => self::LIST_ANGSURAN,
            // Parameter jenis=2 = token rumus (metode perhitungan) yang dipakai
            // menyusun rumus Cair / Angsuran / Penalti & kolektabilitas
            // (dari tabel parameter + seeder).
            'parameters' => Parameter::where('jenis', 2)->orderBy('nama')->get(['id', 'nama']),
        ];
    }

    private function validateProduk(Request $request): array
    {
        $validated = $request->validate([
            'produk.kode' => 'required|string|max:50',
            'produk.nama' => 'required|string|max:100',
            'produk.account_id' => 'required|integer|exists:account,id',
            'produk.bunga' => 'required|numeric|min:0',
            'produk.account_bunga' => 'required|integer|exists:account,id',
            'produk.account_ditangguhkan' => 'nullable|integer|exists:account,id',
            'produk.kas' => 'nullable|numeric|min:0',
            'produk.account_bank' => 'nullable|integer|exists:account,id',
            'produk.insentif' => 'required|numeric|min:0',
            'produk.simpanan' => 'required|boolean',
            'produk.swp_cair' => 'nullable|boolean',
            'produk.swp_angsur' => 'nullable|boolean',
            'produk.swp_persen' => 'nullable|boolean',
            'produk.ditangguhkan' => 'nullable|boolean',
            'produk.nominal_simpanan' => 'nullable|numeric|min:0',
            'produk.simpanan_pokok' => 'nullable|boolean',
            'produk.nominal_simpanan_pokok' => 'nullable|numeric|min:0',
            'produk.toleransi' => 'required|integer|min:0',
            'produk.angsuran' => 'required|string|max:255',

            'kolektabilitas' => 'required|array|min:4',
            'kolektabilitas.*.kualitas_id' => 'required|integer|between:1,4',
            'kolektabilitas.*.keterangan' => 'nullable|string|max:1000',

            'komponen' => 'required|array|min:1',
            'komponen.*.nama' => 'required|string|max:255',
            'komponen.*.nominal' => 'nullable|numeric',
            'komponen.*.persen' => 'nullable|boolean',
            'komponen.*.account_id' => 'required|integer|exists:account,id',
            'komponen.*.cair' => 'nullable|boolean',
            'komponen.*.angsuran' => 'nullable|boolean',
            'komponen.*.penalti' => 'nullable|boolean',
            'komponen.*.rumus_c' => 'nullable|string|max:500',
            'komponen.*.rumus_a' => 'nullable|string|max:500',
            'komponen.*.rumus_p' => 'nullable|string|max:500',
        ], [
            'produk.nama.required' => 'Nama produk pinjaman wajib diisi.',
            'produk.kode.required' => 'Kode produk pinjaman wajib diisi.',
            'produk.account_id.required' => 'Akun pinjaman wajib dipilih.',
            'produk.bunga.required' => 'Bunga wajib diisi.',
            'produk.account_bunga.required' => 'Akun bunga wajib dipilih.',
            'produk.insentif.required' => 'Insentif wajib diisi.',
            'produk.toleransi.required' => 'Toleransi wajib diisi.',
            'produk.angsuran.required' => 'Metode angsuran wajib diisi.',
            'komponen.*.nama.required' => 'Nama komponen wajib diisi.',
            'komponen.*.account_id.required' => 'Akun komponen wajib dipilih.',
        ]);

        // Normalisasi boolean & baris komponen kosong
        $validated['produk']['simpanan'] = (bool) ($validated['produk']['simpanan'] ?? false);
        $validated['komponen'] = collect($validated['komponen'])
            ->filter(fn ($c) => trim((string) $c['nama']) !== '')
            ->values()
            ->map(function ($c) {
                foreach (['persen', 'cair', 'angsuran', 'penalti'] as $f) {
                    $c[$f] = (int) (bool) ($c[$f] ?? false);
                }
                $c['nominal'] = $c['nominal'] ?? 0;

                return $c;
            })
            ->all();

        if (empty($validated['komponen'])) {
            abort(422, 'Minimal satu komponen harus diisi.');
        }

        return $validated;
    }
}
