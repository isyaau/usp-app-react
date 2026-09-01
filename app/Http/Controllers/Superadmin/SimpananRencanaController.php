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

    public function edit(SimpananRencana $rencana)
    {
        $rencana->load(['details:id,rencana_id,simpanan_id', 'kantor:id,nama_kantor']);

        return inertia('Superadmin/SimpananRencana/Edit', [
            'rencanaData' => $rencana,
            'kantorOptions' => Kantor::orderBy('nama_kantor')->get(['id', 'nama_kantor']),
            'rekeningOptions' => $this->rekeningOptions(),
        ]);
    }

    public function update(Request $request, SimpananRencana $rencana)
    {
        $validated = $this->validateRencana($request, $rencana->id);

        if (empty($validated['simpanan_ids'])) {
            return back()
                ->withErrors(['simpanan_ids' => 'Silakan pilih minimal 1 simpanan.'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $rencana->update([
                ...collect($validated)->except('simpanan_ids')->all(),
                'bunga' => $validated['bunga'] ?? 0,
                'keterangan' => $validated['keterangan'] ?? '',
            ]);

            // Sinkronkan detail rekening yang terlibat.
            $rencana->details()->delete();
            foreach ($validated['simpanan_ids'] as $simpananId) {
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
                ->with('success', 'Simpanan rencana berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(SimpananRencana $rencana)
    {
        $rencana->load([
            'kantor:id,nama_kantor',
            'user:id,nama',
            'details:id,rencana_id,simpanan_id',
        ]);

        $rekeningList = Simpanan::query()
            ->with(['jenis_simpanan:id,nama', 'anggota:id,no_anggota,nama'])
            ->whereIn('id', $rencana->details->pluck('simpanan_id'))
            ->get(['id', 'no_rekening', 'anggota_id', 'jenis_id']);

        return inertia('Superadmin/SimpananRencana/Show', [
            'rencanaData' => $rencana,
            'rekeningList' => $rekeningList,
        ]);
    }

    public function cetak(SimpananRencana $rencana)
    {
        $rencana->load(['kantor:id,nama_kantor']);

        $rekeningList = Simpanan::query()
            ->with(['jenis_simpanan:id,nama', 'anggota:id,no_anggota,nama'])
            ->whereIn('id', \App\Models\SimpananRencanaDetail::where('rencana_id', $rencana->id)->pluck('simpanan_id'))
            ->get(['id', 'no_rekening', 'anggota_id', 'jenis_id']);

        return $this->streamPdf(
            'pdf.simpanan-rencana-detail',
            ['rencana' => $rencana, 'rekeningList' => $rekeningList],
            'rencana_simpanan_'.$rencana->no_bukti.'.pdf',
            'portrait'
        );
    }

    public function simulasi(SimpananRencana $rencana)
    {
        $rencana->load(['kantor:id,nama_kantor']);
        $hasil = $this->hitungSimulasi($rencana);

        return inertia('Superadmin/SimpananRencana/Simulasi', [
            'rencanaData' => $rencana,
            'hasil' => $hasil,
        ]);
    }

    public function cetakSimulasi(SimpananRencana $rencana)
    {
        $rencana->load(['kantor:id,nama_kantor']);
        $hasil = $this->hitungSimulasi($rencana);

        return $this->streamPdf(
            'pdf.simpanan-rencana-simulasi',
            ['rencana' => $rencana, 'hasil' => $hasil],
            'simulasi_rencana_'.$rencana->no_bukti.'.pdf',
            'landscape'
        );
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

    private function validateRencana(Request $request, ?int $ignoreId = null): array
    {
        $noBuktiRule = $ignoreId
            ? 'unique:simpanan_rencana,no_bukti,'.$ignoreId.',id'
            : 'unique:simpanan_rencana,no_bukti';

        return $request->validate([
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_jatuhtempo' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'no_bukti' => ['required', 'string', 'max:255', $noBuktiRule],
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

    /**
     * Hitung jadwal setoran menuju target nominal.
     * Setoran pokok tetap per periode + bagi hasil dari saldo berjalan.
     */
    private function hitungSimulasi(SimpananRencana $rencana): array
    {
        $nominal = abs((float) $rencana->nominal);
        $bunga = (float) $rencana->bunga;
        $jangka = max(1, (int) $rencana->jangka_waktu);
        $satuan = $rencana->satuan;

        $jumlahPeriode = match ($satuan) {
            'tahun' => $jangka * 12,
            'hari' => $jangka,
            default => $jangka,
        };
        $jumlahPeriode = max(1, $jumlahPeriode);

        $setoranPokok = round($nominal / $jumlahPeriode);
        $bungaPerPeriode = $bunga / 100 / 12;

        $saldo = 0.0;
        $jadwal = [];
        $totalBunga = 0.0;

        for ($i = 1; $i <= $jumlahPeriode; $i++) {
            $pokok = ($i === $jumlahPeriode) ? $nominal - ($setoranPokok * ($i - 1)) : $setoranPokok;
            $saldoAwal = $saldo;
            $saldo += $pokok;
            $bungaPeriode = round($saldo * $bungaPerPeriode);
            $saldo += $bungaPeriode;
            $totalBunga += $bungaPeriode;

            $jadwal[] = [
                'ke' => $i,
                'setoran' => $pokok,
                'bunga' => $bungaPeriode,
                'total_setor' => $pokok + $bungaPeriode,
                'saldo' => round($saldo),
            ];
        }

        return [
            'nominal' => $nominal,
            'bunga_tahun' => $bunga,
            'jumlah_periode' => $jumlahPeriode,
            'satuan_periode' => $satuan,
            'setoran_pokok' => $setoranPokok,
            'total_bunga' => round($totalBunga),
            'saldo_akhir' => round($saldo),
            'jadwal' => $jadwal,
        ];
    }

    /** Stream PDF via DomPDF (portrait/landscape). */
    private function streamPdf(string $view, array $data, string $filename, string $orientation = 'landscape')
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, $data)->setPaper('a4', $orientation);
        $pdf->getDomPDF()->render();

        return response()->streamDownload(fn () => print($pdf->output()), $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
