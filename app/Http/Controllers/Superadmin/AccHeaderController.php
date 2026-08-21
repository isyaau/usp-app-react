<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\AccGroup;
use App\Models\AccHeader;
use Illuminate\Http\Request;

/**
 * Controller CRUD Account Header untuk frontend Inertia (React + TypeScript).
 * Menggantikan Livewire Superadmin\Accheader — termasuk logika radio "jenis"
 * yang bergantung pada nama group.
 */
class AccHeaderController extends Controller
{
    public function index(Request $request)
    {
        $headers = AccHeader::query()
            ->with('group:id,nama')
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($qq) => $qq
                    ->where('nama', 'LIKE', "%{$term}%")
                    ->orWhere('no_header', 'LIKE', "%{$term}%"));
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia('Superadmin/AccHeader/Index', [
            'headers' => $headers,
            'filters' => ['search' => $request->input('search', '')],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/AccHeader/Create', [
            'groups' => AccGroup::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateHeader($request);

        AccHeader::create([
            ...$validated,
            'modal' => false,
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('superadmin.account-header')
            ->with('flash.status', 'Account Header berhasil dibuat!');
    }

    public function show(AccHeader $header)
    {
        $header->load('group:id,nama');

        return inertia('Superadmin/AccHeader/Show', ['headerData' => $header]);
    }

    public function edit(AccHeader $header)
    {
        return inertia('Superadmin/AccHeader/Edit', [
            'headerData' => $header,
            'groups' => AccGroup::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    public function update(Request $request, AccHeader $header)
    {
        $validated = $this->validateHeader($request, $header->id);

        $header->update($validated);

        return redirect()
            ->route('superadmin.account-header')
            ->with('flash.status', 'Account Header berhasil diperbarui!');
    }

    public function destroy(AccHeader $header)
    {
        $header->delete();

        return redirect()
            ->route('superadmin.account-header')
            ->with('flash.status', 'Account Header berhasil dihapus!');
    }

    private function validateHeader(Request $request, ?int $ignoreId = null): array
    {
        $suffix = is_null($ignoreId) ? '' : ','.$ignoreId;

        return $request->validate([
            'group_id' => 'required|integer|exists:acc_group,id',
            'nama' => 'required|string|max:255|unique:acc_header,nama'.$suffix,
            'no_header' => 'required|string|max:50|unique:acc_header,no_header'.$suffix,
            'keterangan' => 'required|string|max:1000',
            'jenis' => 'required|string|max:255',
        ], [
            'group_id.required' => 'Group wajib dipilih.',
            'group_id.exists' => 'Group tidak ditemukan di database.',
            'nama.required' => 'Nama Header wajib diisi.',
            'nama.unique' => 'Nama Header sudah ada di database.',
            'no_header.required' => 'Nomor Header wajib diisi.',
            'no_header.unique' => 'Nomor Header sudah ada di database.',
            'keterangan.required' => 'Keterangan wajib diisi.',
            'jenis.required' => 'Jenis wajib dipilih.',
        ]);
    }

    /**
     * Opsi radio "Jenis" berdasarkan nama group — replikasi getRadioByGroup()
     * dari komponen Livewire lama.
     */
    public static function jenisOptions(string $groupName): array
    {
        $groupName = strtoupper(trim($groupName));
        $items = [];

        if (in_array($groupName, ['AKTIVA LANCAR', 'AKTIVA TETAP', 'AKTIVA LAIN'])) {
            $items['Aktiva'] = ['Kas', 'Bank', 'Tabungan & Simpanan Berjangka',
                'Surat-surat berharga', 'Piutang', 'Pinjaman yang diberikan',
                'BMPP kepada calon anggota, koperasi lain dan anggotanya',
                'Pendapatan yang masih harus diterima', 'Penyertaan pada non koperasi', 'Aktiva Tetap'];
        }

        if (in_array($groupName, ['HUTANG LANCAR', 'HUTANG JANGKA PANJANG'])) {
            $items['Kewajiban'] = ['Kewajiban Tertimbang'];
        }

        if ($groupName === 'MODAL') {
            $items['Modal'] = ['Modal Anggota', 'Modal Penyetaraan', 'Modal Penyertaan',
                'Cadangan Umum', 'Cadangan Tujuan Resiko', 'Modal Sumbangan', 'SHU Yang belum dibagi'];
        }

        if ($groupName === 'PENDAPATAN') {
            $items['Pendapatan'] = ['Partisipasi Anggota'];
        }

        if ($groupName === 'BIAYA') {
            $items['Biaya'] = ['Biaya Operasional', 'Gaji dan Honorarium Karyawan'];
        }

        if (in_array($groupName, ['AKTIVA LANCAR', 'AKTIVA TETAP', 'AKTIVA LAIN',
            'HUTANG LANCAR', 'HUTANG JANGKA PANJANG', 'MODAL'])) {
            $items['Cadangan'] = ['Cadangan Penghapusan Pinjaman',
                'Cadangan Penghapusan Pinjaman dari SHU'];
        }

        return $items;
    }
}
