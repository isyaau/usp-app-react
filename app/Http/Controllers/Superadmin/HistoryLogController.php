<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\HistoryLog;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Riwayat Perubahan Data (audit log) — read only.
 * Melihat siapa yang membuat/mengubah/menghapus data kapan.
 */
class HistoryLogController extends Controller
{
    /** Label tampilan tabel per nama tabel basis data. */
    private const TABLE_LABELS = [
        'users' => 'User',
        'kantor' => 'Kantor',
        'marketing' => 'Marketing',
        'kelompok' => 'Kelompok',
        'anggota' => 'Anggota',
        'acc_group' => 'Group Account',
        'acc_header' => 'Header Account',
        'account' => 'Account',
        'parameter' => 'Parameter',
        'simpanan_jenis_kode' => 'Kode Transaksi',
        'simpanan_jenis' => 'Jenis Simpanan',
        'simpanan_jenis_bunga' => 'Bunga Simpanan',
        'simpanan' => 'Simpanan',
        'simpanan_rencana' => 'Simpanan Rencana',
        'deposito_jenis' => 'Produk Berjangka',
        'deposito' => 'Simpanan Berjangka',
        'pinj_jenis' => 'Produk Pinjaman',
        'pinj_jenis_komponen' => 'Komponen Produk Pinjaman',
        'jaminan' => 'Jaminan',
        'jaminan_detail' => 'Jaminan Detail',
        'pinjaman' => 'Pinjaman',
        'proposal' => 'Proposal',
        'proposal_biaya' => 'Biaya Proposal',
        'jadwal_ulang' => 'Jadwal Ulang',
        'jadwal_ulang_detail' => 'Jadwal Ulang Detail',
        'tagihan_pinjaman' => 'Tagihan Pinjaman',
        'kas_harian' => 'Kas Harian',
        'angsuran_kolektif' => 'Angsuran Kolektif',
        'angsuran_kolektif_detail' => 'Angsuran Kolektif Detail',
        'transaksi_simpanan' => 'Transaksi Simpanan',
        'angsuran_pinjaman' => 'Angsuran Pinjaman',
    ];

    public function index(Request $request)
    {
        $logs = HistoryLog::query()
            ->with('user:id,nama,username,avatar')
            ->when($request->filled('table'), fn ($q) => $q->where('table', $request->string('table')))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->string('action')))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('created_at', '>=', $request->string('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('created_at', '<=', $request->string('date_to')))
            ->latest('id')
            ->paginate($request->integer('per_page', 25))
            ->withQueryString();

        return inertia('Superadmin/HistoryLog/Index', [
            'logs' => $logs,
            'filters' => $request->only(['table', 'action', 'user_id', 'date_from', 'date_to', 'per_page']),
            'tables' => HistoryLog::query()->distinct()->orderBy('table')->pluck('table'),
            'users' => User::query()->select('id', 'nama', 'username', 'avatar')->orderBy('nama')->get(),
            'labels' => self::TABLE_LABELS,
        ]);
    }
}