<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Kantor;
use App\Models\Simpanan;
use App\Models\SimpananRencana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller CRUD Simpanan Rencana.
 * Migrasi dari Livewire Superadmin\SimpananRencana (Create & Delete saja —
 * Edit/Show di aplikasi lama adalah stub kosong).
 */
class SimpananRencanaController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->string('search');

        $rencana = SimpananRencana::query()
            ->with('kantor:id,nama_kantor')
            ->withCount('details')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('no_bukti', 'ILIKE', "%{$search}%")
                        ->orWhere('keterangan', 'ILIKE', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'DESC')
            ->paginate((int) ($request->input('per_page') ?: 10))
            ->withQueryString();

        return inertia('Superadmin/SimpananRencana/Index', [
            'rencana' => $rencana,
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/SimpananRencana/Create', [
            'kantorOptions' => Kantor::orderBy('nama_kantor')->get(['id', 'nama_kantor']),
            'rekeningOptions' => $this->rekeningOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRencana($request);

        if (empty($validated['simpanan_ids'])) {
            return back()
                ->withErrors(['simpanan_ids' => 'Silakan pilih minimal 1 simpanan.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Kolom bunga & keterangan NOT NULL di DB.
            $rencana = SimpananRencana::create([
                ...collect($validated)->except('simpanan_ids')->all(),
                'bunga' => $validated['bunga'] ?? 0,
                'keterangan' => $validated['keterangan'] ?? '',
                'user_id' => $request->user()->id,
            ]);

            foreach ($validated['simpanan_ids'] as $simpananId) {
                // Abaikan id yang tidak valid tanpa menggagalkan seluruh transaksi.
                if (Simpanan::find($simpananId)) {
                    $rencana->details()->create([
                        'simpanan_id' => $simpananId,
                        'user_id' => $request->user()->id,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('superadmin.simpanan.rencana')
                ->with('success', 'Simpanan rencana berhasil dibuat.');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(SimpananRencana $rencana)
    {
        DB::beginTransaction();
        try {
            $rencana->details()->delete();
            $rencana->delete();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return redirect()
            ->route('superadmin.simpanan.rencana')
            ->with('success', 'Simpanan rencana berhasil dihapus.');
    }

    /* ------------------------------------------------------------------ */

    private function validateRencana(Request $request): array
    {
        return $request->validate([
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_jatuhtempo' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'no_bukti' => ['required', 'string', 'max:255', 'unique:simpanan_rencana,no_bukti'],
            'jangka_waktu' => ['required', 'numeric', 'min:1'],
            'satuan' => ['required', 'in:hari,bulan,tahun'],
            'nominal' => ['required', 'numeric'],
            'bunga' => ['nullable', 'numeric'],
            'keterangan' => ['nullable', 'string'],
            'kantor_id' => ['required', 'integer', 'exists:kantor,id'],
            'simpanan_ids' => ['required', 'array', 'min:1'],
            'simpanan_ids.*' => ['integer', 'exists:simpanan,id'],
        ]);
    }

    /** Daftar semua rekening untuk pemilih (pola sama dengan Livewire lama). */
    private function rekeningOptions(): array
    {
        return Simpanan::query()
            ->with(['jenis_simpanan:id,nama', 'anggota:id,nama'])
            ->orderBy('no_rekening')
            ->get(['id', 'no_rekening', 'anggota_id', 'jenis_id'])
            ->map(fn (Simpanan $s) => [
                'id' => $s->id,
                'no_rekening' => $s->no_rekening,
                'jenis_nama' => $s->jenis_simpanan?->nama,
                'anggota_nama' => $s->anggota?->nama,
            ])
            ->all();
    }
}
