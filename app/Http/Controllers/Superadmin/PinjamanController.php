<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\PinjamanProduk;
use Illuminate\Http\Request;

/**
 * Controller CRUD Data Pinjaman.
 *
 * Komponen Livewire lama (Superadmin\Pinjaman) adalah salinan modul Anggota yang
 * tidak pernah menyentuh tabel `pinjaman` — dibangun ulang langsung dari skema DB:
 * semua kolom NOT NULL, jadi setiap field diberi nilai default yang aman.
 */
class PinjamanController extends Controller
{
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
            'anggotaOptions' => Anggota::orderBy('nama')
                ->get(['id', 'no_anggota', 'nama']),
            'jenisOptions' => PinjamanProduk::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePinjaman($request);

        // Semua kolom tabel pinjaman NOT NULL — isi default aman untuk
        // field lanjutan yang belum dipakai form.
        $defaults = [
            'proposal_id' => 0,
            'jaminan_id' => 0,
            'marketing_id' => $request->user()->id,
            'sektor_id' => 0,
            'angsuran' => '0',
            'nominal_angsuran' => '0',
            'periode' => '1',
            'pembayaran' => 'tunai',
            'manual' => '0',
            'tabungan_id' => 0,
            'kode_id' => 0,
            'kode_koreksi' => '',
            'swp_id' => 0,
            'spp_id' => 0,
            'angsuranke' => '0',
            'rekening_koran' => '',
            'cair_simpanan' => '0',
            'sms' => '1',
            'aktif' => '1',
            'kantor_id' => null,
            'user_id' => $request->user()->id,
        ];

        // kantor_id mengikuti kantor anggota bila ada.
        $kantorAnggota = Anggota::find($validated['anggota_id'])?->kantor_id;
        if ($kantorAnggota) {
            $defaults['kantor_id'] = $kantorAnggota;
        }

        Pinjaman::create($validated + $defaults);

        return redirect()
            ->route('superadmin.pinjaman.pinjaman')
            ->with('success', 'Data pinjaman berhasil dibuat.');
    }

    public function destroy(Pinjaman $pinjaman)
    {
        $pinjaman->delete();

        return redirect()
            ->route('superadmin.pinjaman.pinjaman')
            ->with('success', 'Data pinjaman berhasil dihapus.');
    }

    /* ------------------------------------------------------------------ */

    private function validatePinjaman(Request $request): array
    {
        return $request->validate([
            'tanggal' => ['required', 'date'],
            'no_pinjaman' => ['required', 'string', 'max:255', 'unique:pinjaman,no_pinjaman'],
            'anggota_id' => ['required', 'integer', 'exists:anggota,id'],
            'jenis_id' => ['required', 'integer', 'exists:pinj_jenis,id'],
            'plafon' => ['required', 'numeric'],
            'bunga' => ['nullable', 'numeric'],
            'jangka_waktu' => ['required', 'numeric', 'min:1'],
            'satuan' => ['required', 'in:hari,bulan,tahun'],
        ]);
    }
}
