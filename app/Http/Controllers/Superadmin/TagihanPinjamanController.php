<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AngsuranPinjaman;
use App\Models\Pinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller daftar tagihan pinjaman (menu "Tagihan Pinjaman").
 *
 * Menampilkan pinjaman aktif beserta plafon, pokok yang sudah dibayar,
 * sisa pokok, angsuran per bulan, dan jatuh tempo (read-only).
 */
class TagihanPinjamanController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->string('search');
        $status = (string) $request->string('status');

        $pokokTerbayar = 'COALESCE((SELECT SUM(ap.nominal_pokok) FROM angsuran_pinjaman ap WHERE ap.pinjaman_id = pinjaman.id), 0)';

        $tagihan = Pinjaman::query()
            ->with([
                'jenisPinjaman:id,nama',
                'anggota:id,no_anggota,nama',
            ])
            ->where('aktif', '1')
            ->select('pinjaman.*')
            ->selectRaw("{$pokokTerbayar} AS pokok_terbayar")
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('no_pinjaman', 'ILIKE', "%{$search}%")
                        ->orWhereHas('anggota', fn ($a) => $a
                            ->where('nama', 'ILIKE', "%{$search}%")
                            ->orWhere('no_anggota', 'ILIKE', "%{$search}%"));
                });
            })
            ->when($status === 'lunas', fn ($q) => $q->whereRaw("{$pokokTerbayar} >= CAST(pinjaman.plafon AS NUMERIC)"))
            ->when($status === 'belum', fn ($q) => $q->whereRaw("{$pokokTerbayar} < CAST(pinjaman.plafon AS NUMERIC)"))
            ->orderBy('created_at', 'DESC')
            ->paginate((int) ($request->input('per_page') ?: 10))
            ->withQueryString()
            ->through(fn (Pinjaman $p) => $this->toRow($p));

        return inertia('Superadmin/TagihanPinjaman/Index', [
            'tagihan' => $tagihan,
            'filters' => $request->only(['search', 'status']),
            'rekap' => $this->rekap(),
        ]);
    }

    private function toRow(Pinjaman $p): array
    {
        $plafon = (float) $p->plafon;
        $pokokTerbayar = (float) $p->pokok_terbayar;

        return [
            'id' => $p->id,
            'tanggal' => $p->tanggal,
            'no_pinjaman' => $p->no_pinjaman,
            'anggota' => $p->anggota ? [
                'id' => $p->anggota->id,
                'no_anggota' => $p->anggota->no_anggota,
                'nama' => $p->anggota->nama,
            ] : null,
            'jenisPinjaman' => $p->jenisPinjaman ? [
                'id' => $p->jenisPinjaman->id,
                'nama' => $p->jenisPinjaman->nama,
            ] : null,
            'plafon' => $plafon,
            'pokok_terbayar' => $pokokTerbayar,
            'sisa_pokok' => max(0, $plafon - $pokokTerbayar),
            'nominal_angsuran' => (float) $p->nominal_angsuran,
            'bunga' => $p->bunga,
            'jangka_waktu' => $p->jangka_waktu,
            'satuan' => $p->satuan,
            'angsuranke' => (int) $p->angsuranke,
            'jatuh_tempo' => $p->jatuh_tempo,
            'lunas' => (float) $p->pokok_terbayar >= $plafon,
        ];
    }

    /** Ringkasan jumlah & nominal untuk header kartu. */
    private function rekap(): array
    {
        $totalPlafon = (float) Pinjaman::where('aktif', '1')->sum(DB::raw('CAST(plafon AS NUMERIC)'));
        $pokokTerbayar = (float) AngsuranPinjaman::query()
            ->whereRaw('EXISTS (SELECT 1 FROM pinjaman pp WHERE pp.id = angsuran_pinjaman.pinjaman_id AND pp.aktif = 1::text)')
            ->sum('nominal_pokok');

        return [
            'jumlah_pinjaman' => (int) Pinjaman::where('aktif', '1')->count(),
            'total_plafon' => $totalPlafon,
            'total_pokok_terbayar' => $pokokTerbayar,
            'total_sisa_pokok' => max(0, $totalPlafon - $pokokTerbayar),
        ];
    }
}