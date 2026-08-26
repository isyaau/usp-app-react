<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Deposito;
use App\Models\Kantor;
use App\Models\PenaltiSimpananBerjangka;
use App\Models\PencairanSimpananBerjangka;
use App\Models\SetoranSimpananBerjangka;
use Illuminate\Http\Request;

class TransaksiSimpananBerjangkaController extends Controller
{
    private function variantMeta(string $routeName): array
    {
        $map = [
            'setoran-simpanan-berjangka' => ['page' => 'SetoranSimpananBerjangka', 'title' => 'Setoran Simpanan Berjangka', 'model' => SetoranSimpananBerjangka::class, 'prefix' => 'SB'],
            'pencairan-simpanan-berjangka' => ['page' => 'PencairanSimpananBerjangka', 'title' => 'Pencairan Simpanan Berjangka', 'model' => PencairanSimpananBerjangka::class, 'prefix' => 'PC'],
            'penalti-simpanan-berjangka' => ['page' => 'PenaltiSimpananBerjangka', 'title' => 'Penalti Simpanan Berjangka', 'model' => PenaltiSimpananBerjangka::class, 'prefix' => 'PT'],
        ];
        foreach ($map as $key => $meta) {
            if (str_contains($routeName, $key)) return $meta;
        }
        return ['page' => 'SetoranSimpananBerjangka', 'title' => 'Setoran Simpanan Berjangka', 'model' => SetoranSimpananBerjangka::class, 'prefix' => 'SB'];
    }

    private function detectRouteName(): string
    {
        return request()->route()?->getName() ?? '';
    }

    public function index(Request $request)
    {
        $routeName = $this->detectRouteName();
        $meta = $this->variantMeta($routeName);

        $data = $meta['model']::query()
            ->with(['anggota:id,no_anggota,nama', 'deposito:id,no_deposito', 'user:id,nama', 'kantor:id,nama_kantor'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($qq) => $qq
                    ->where('no_transaksi', 'LIKE', "%{$term}%")
                    ->orWhereHas('anggota', fn ($a) => $a->where('nama', 'LIKE', "%{$term}%")));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('mulai'), fn ($q) => $q->whereDate('tgl_transaksi', '>=', $request->date('mulai')))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('tgl_transaksi', '<=', $request->date('sampai')))
            ->orderByDesc('tgl_transaksi')
            ->orderByDesc('id')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia("Superadmin/{$meta['page']}/Index", [
            'transaksi' => $data,
            'filters' => $request->only(['search', 'status', 'mulai', 'sampai']),
            'variantTitle' => $meta['title'],
        ]);
    }

    public function create()
    {
        $routeName = $this->detectRouteName();
        $meta = $this->variantMeta($routeName);

        return inertia("Superadmin/{$meta['page']}/Create", [
            'anggotas' => Anggota::select('id', 'no_anggota', 'nama')->get(),
            'depositos' => Deposito::select('id', 'no_deposito', 'anggota_id', 'nominal', 'jangka_waktu')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $routeName = $this->detectRouteName();
        $meta = $this->variantMeta($routeName);

        $validated = $request->validate($this->rules($meta['page']));
        $validated['no_transaksi'] = $meta['prefix'] . '-' . date('YmdHis') . rand(10, 99);
        $validated['user_id'] = auth()->id();
        $validated['status'] = $validated['status'] ?? 'draft';

        $meta['model']::create($validated);

        $base = str_replace('.store', '', $routeName);
        return redirect()->route($base)->with('success', $meta['title'] . ' berhasil ditambahkan.');
    }

    public function show($id)
    {
        $routeName = $this->detectRouteName();
        $meta = $this->variantMeta($routeName);

        $item = $meta['model']::with(['anggota', 'deposito', 'user', 'kantor'])->findOrFail($id);
        $data = $item->toArray();
        $data['variant_title'] = $meta['title'];

        return inertia("Superadmin/{$meta['page']}/Show", ['transaksi' => $data]);
    }

    public function edit($id)
    {
        $routeName = $this->detectRouteName();
        $meta = $this->variantMeta($routeName);

        $item = $meta['model']::findOrFail($id);
        $data = $item->toArray();
        $data['variant_title'] = $meta['title'];

        return inertia("Superadmin/{$meta['page']}/Edit", [
            'transaksi' => $data,
            'anggotas' => Anggota::select('id', 'no_anggota', 'nama')->get(),
            'depositos' => Deposito::select('id', 'no_deposito', 'anggota_id', 'nominal', 'jangka_waktu')->get(),
            'kantors' => Kantor::select('id', 'kode', 'nama_kantor')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $routeName = $this->detectRouteName();
        $meta = $this->variantMeta($routeName);

        $validated = $request->validate($this->rules($meta['page'], true));
        $meta['model']::findOrFail($id)->update($validated);

        $base = str_replace(['.update', '.destroy'], '', $routeName);
        return redirect()->route($base)->with('success', $meta['title'] . ' berhasil diupdate.');
    }

    public function destroy($id)
    {
        $routeName = $this->detectRouteName();
        $meta = $this->variantMeta($routeName);

        $meta['model']::findOrFail($id)->delete();

        $base = str_replace(['.update', '.destroy'], '', $routeName);
        return redirect()->route($base)->with('success', $meta['title'] . ' berhasil dihapus.');
    }

    public function depositoByAnggota(Anggota $anggota)
    {
        return response()->json(
            Deposito::where('anggota_id', $anggota->id)
                ->select('id', 'no_deposito', 'nominal', 'jangka_waktu')
                ->get()
        );
    }

    private function rules(string $page, bool $update = false): array
    {
        $r = $update ? 'sometimes' : 'required';
        $common = [
            'tgl_transaksi' => "$r|date",
            'anggota_id' => "$r|exists:anggota,id",
            'deposito_id' => "$r|exists:deposito,id",
            'keterangan' => 'nullable|string',
            'kantor_id' => "$r|exists:kantor,id",
            'status' => 'in:draft,posted,batal',
        ];

        if ($page === 'SetoranSimpananBerjangka') {
            return [...$common, 'nominal' => "$r|numeric|min:1"];
        }
        if ($page === 'PencairanSimpananBerjangka') {
            return [...$common, 'nominal_pokok' => "$r|numeric|min:0", 'nominal_bunga' => 'nullable|numeric|min:0', 'nominal_pajak' => 'nullable|numeric|min:0', 'nominal_penalti' => 'nullable|numeric|min:0', 'nominal_diterima' => "$r|numeric|min:0"];
        }
        return [...$common, 'nominal_penalti' => "$r|numeric|min:0", 'nominal_pajak' => 'nullable|numeric|min:0', 'total_penalti' => "$r|numeric|min:0"];
    }
}