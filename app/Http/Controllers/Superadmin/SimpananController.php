<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\Marketing;
use App\Models\Simpanan;
use App\Models\SimpananJenis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\PngEncoder;

/**
 * Controller CRUD Data Simpanan (rekening simpanan anggota).
 * Migrasi dari Livewire Superadmin\Simpanan.
 */
class SimpananController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->string('search');

        $simpanan = Simpanan::query()
            ->with([
                'anggota:id,no_anggota,nama',
                'jenis_simpanan:id,kode,nama,bunga',
                'marketing:id,nama',
                'kantor:id,nama_kantor',
            ])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('no_rekening', 'ILIKE', "%{$search}%")
                        ->orWhereHas('anggota', fn ($a) => $a
                            ->where('nama', 'ILIKE', "%{$search}%")
                            ->orWhere('no_anggota', 'ILIKE', "%{$search}%"));
                });
            })
            ->orderBy('created_at', 'DESC')
            ->paginate((int) ($request->input('per_page') ?: 10))
            ->withQueryString();

        return inertia('Superadmin/Simpanan/Index', [
            'simpanan' => $simpanan,
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/Simpanan/Create', [
            ...$this->formOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRekening($request);
        // Kolom marketing_id NOT NULL di DB; bila kosong pakai user login.
        $validated['marketing_id'] ??= $request->user()->id;

        $validated['ttd'] = $this->saveSignature(
            $request->input('mode'),
            $request->file('uploaded_signature'),
            $request->input('signature_base64')
        );

        if ($request->boolean('require_signature') && empty($validated['ttd'])) {
            return back()->withErrors(['ttd' => 'Tanda tangan wajib diisi.']);
        }

        Simpanan::create([...$validated, 'user_id' => $request->user()->id]);

        return redirect()
            ->route('superadmin.simpanan')
            ->with('success', 'Rekening simpanan berhasil dibuat.');
    }

    public function show(Simpanan $simpanan)
    {
        $simpanan->load([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama,bunga',
            'marketing:id,nama',
            'kantor:id,nama_kantor',
        ]);

        return inertia('Superadmin/Simpanan/Show', [
            'simpananData' => $simpanan,
            'signatureUrl' => $simpanan->ttd ? asset('storage/'.$simpanan->ttd) : null,
        ]);
    }

    public function edit(Simpanan $simpanan)
    {
        $simpanan->load([
            'anggota:id,no_anggota,nama',
            'jenis_simpanan:id,kode,nama,bunga',
            'marketing:id,nama',
            'kantor:id,nama_kantor',
        ]);

        return inertia('Superadmin/Simpanan/Edit', [
            'simpananData' => $simpanan,
            'existingSignatureUrl' => $simpanan->ttd ? asset('storage/'.$simpanan->ttd) : null,
            ...$this->formOptions(),
        ]);
    }

    public function update(Request $request, Simpanan $simpanan)
    {
        $validated = $this->validateRekening($request, $simpanan->id);
        $validated['marketing_id'] ??= $simpanan->marketing_id ?? $request->user()->id;

        // TTD: hanya diganti bila ada data baru (draw/upload).
        $ttdBaru = $this->saveSignature(
            $request->input('mode'),
            $request->file('uploaded_signature'),
            $request->input('signature_base64')
        );

        if ($ttdBaru !== null) {
            // Hapus file lama bila berbeda.
            if ($simpanan->ttd && $simpanan->ttd !== $ttdBaru) {
                Storage::disk('public')->delete($simpanan->ttd);
            }
            $validated['ttd'] = $ttdBaru;
        } else {
            unset($validated['ttd']);
        }

        $simpanan->update([...$validated, 'user_id' => $request->user()->id]);

        return redirect()
            ->route('superadmin.simpanan')
            ->with('success', 'Data simpanan berhasil diperbarui.');
    }

    public function destroy(Simpanan $simpanan)
    {
        if ($simpanan->ttd && Storage::disk('public')->exists($simpanan->ttd)) {
            Storage::disk('public')->delete($simpanan->ttd);
        }
        $simpanan->delete();

        return redirect()
            ->route('superadmin.simpanan')
            ->with('success', 'Data simpanan berhasil dihapus.');
    }

    /* ------------------------------------------------------------------ */

    private function validateRekening(Request $request, ?int $ignoreId = null): array
    {
        $unique = $ignoreId
            ? 'unique:simpanan,no_rekening,'.$ignoreId
            : 'unique:simpanan,no_rekening';

        return $request->validate([
            'tanggal' => ['nullable', 'date'],
            'no_rekening' => ['required', 'string', 'max:255', $unique],
            'anggota_id' => ['required', 'integer', 'exists:anggota,id'],
            'jenis_id' => ['required', 'integer', 'exists:simpanan_jenis,id'],
            'marketing_id' => ['nullable', 'integer', 'exists:marketing,id'],
            'qq' => ['nullable', 'string', 'max:255'],
            'bunga' => ['nullable', 'string', 'max:255'],
            'nominal_setor' => ['nullable', 'numeric', 'max:999999999999'],
            'aktif' => ['nullable', 'boolean'],
            'sms' => ['nullable', 'boolean'],
            'blokir_simpanan' => ['nullable', 'boolean'],
            'blokir_nominal' => ['nullable', 'boolean'],
            'nominal_blokir' => ['nullable', 'numeric', 'max:999999999999'],
            'blokir_tgl' => ['nullable', 'boolean'],
            'tgl_blokir' => ['nullable', 'date'],
            'kantor_id' => ['nullable', 'integer', 'exists:kantor,id'],
        ]);
    }

    /**
     * Konversi tanda tangan mode draw (base64 PNG → file) atau upload.
     * Mengembalikan path relatif storage atau null bila tidak ada data baru.
     */
    private function saveSignature(?string $mode, $uploadedFile, ?string $base64): ?string
    {
        if ($mode === 'draw' && $base64) {
            [$meta, $data] = explode(',', $base64, 2);
            $image = ImageManager::withDriver(Driver::class)
                ->read($base64)
                ->encode(new PngEncoder());

            $filename = 'ttd_'.Str::uuid().'.png';
            $path = 'ttd/'.$filename;

            Storage::disk('public')->put($path, (string) $image);
            unset($data, $meta);

            return $path;
        }

        if ($mode === 'upload' && $uploadedFile) {
            $filename = 'ttd_'.Str::uuid().'.'.$uploadedFile->getClientOriginalExtension();

            return $uploadedFile->storeAs('ttd', $filename, 'public');
        }

        return null;
    }

    /** Opsi master untuk form rekening simpanan. */
    private function formOptions(): array
    {
        return [
            'jenisOptions' => SimpananJenis::with('idAccount:id,no_account,nama')
                ->orderBy('kode')
                ->get(['id', 'kode', 'nama', 'bunga', 'account_id', 'minimum', 'mengendap'])
                ->map(fn (SimpananJenis $j) => [
                    'id' => $j->id,
                    'kode' => $j->kode,
                    'nama' => $j->nama,
                    'bunga' => $j->bunga,
                    'account_no' => $j->idAccount?->no_account,
                    'minimum' => $j->minimum,
                    'mengendap' => $j->mengendap,
                ]),
            'marketingOptions' => Marketing::orderBy('nama')->get(['id', 'nama']),
            'kantorOptions' => Kantor::orderBy('nama_kantor')->get(['id', 'nama_kantor']),
            'anggotaOptions' => Anggota::where('status', 1)
                ->orderBy('no_anggota')
                ->get(['id', 'no_anggota', 'nama']),
        ];
    }
}
