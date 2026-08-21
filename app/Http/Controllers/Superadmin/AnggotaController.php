<?php

namespace App\Http\Controllers\Superadmin;

use App\Exports\AnggotaExport;
use App\Exports\AnggotaTemplateExport;
use App\Imports\AnggotaImport;
use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\Kelompok;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Controller CRUD Anggota untuk frontend Inertia (React + TypeScript).
 * Menggantikan Livewire Superadmin\Anggota.
 */
class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $anggota = Anggota::query()
            ->with(['kelompok:id,nama', 'kantor:id,nama_kantor'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = $request->string('search');
                $q->where(fn ($qq) => $qq
                    ->where('nama', 'LIKE', "%{$term}%")
                    ->orWhere('no_anggota', 'LIKE', "%{$term}%"));
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($request->integer('per_page', 10))
            ->withQueryString();

        return inertia('Superadmin/Anggota/Index', [
            'anggota' => $anggota,
            'filters' => ['search' => $request->input('search', '')],
        ]);
    }

    public function create()
    {
        return inertia('Superadmin/Anggota/Create', [
            'kelompoks' => Kelompok::select('id', 'nama')->orderBy('nama')->get(),
            'kantors' => Kantor::select('id', 'nama_kantor')->orderBy('nama_kantor')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateAnggota($request);

        $validated['foto'] = $this->simpanFoto(
            $request->file('foto'),
            $validated['nama']
        );

        Anggota::create([...$validated, 'user_id' => $request->user()->id]);

        return redirect()
            ->route('superadmin.anggota')
            ->with('flash.status', 'Data anggota berhasil disimpan!');
    }

    public function show(Anggota $anggota)
    {
        $anggota->load([
            'kelompok:id,nama',
            'kantor:id,nama_kantor',
            'provinsi:code,name',
            'kota:code,name',
            'kecamatan:code,name',
            'kelurahan:code,name',
        ]);

        return inertia('Superadmin/Anggota/Show', ['anggotaData' => $anggota]);
    }

    public function edit(Anggota $anggota)
    {
        return inertia('Superadmin/Anggota/Edit', [
            'anggotaData' => $anggota,
            'kelompoks' => Kelompok::select('id', 'nama')->orderBy('nama')->get(),
            'kantors' => Kantor::select('id', 'nama_kantor')->orderBy('nama_kantor')->get(),
        ]);
    }

    public function update(Request $request, Anggota $anggota)
    {
        $validated = $this->validateAnggota($request, $anggota->id);

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika bukan default
            if ($anggota->foto && $anggota->foto !== 'anggota/foto-default.jpg') {
                Storage::disk('public')->delete($anggota->foto);
            }
            $validated['foto'] = $this->simpanFoto(
                $request->file('foto'),
                $validated['nama']
            );
        } else {
            unset($validated['foto']);
        }

        $anggota->update($validated);

        return redirect()
            ->route('superadmin.anggota')
            ->with('flash.status', 'Data anggota berhasil diperbarui!');
    }

    public function destroy(Anggota $anggota)
    {
        if ($anggota->foto && $anggota->foto !== 'anggota/foto-default.jpg') {
            Storage::disk('public')->delete($anggota->foto);
        }

        $anggota->delete();

        return redirect()
            ->route('superadmin.anggota')
            ->with('flash.status', 'Data anggota berhasil dihapus!');
    }

    /* ====================================================================
     |  Export & Import
     * ==================================================================== */

    public function exportPdf(Request $request)
    {
        $query = Anggota::query();

        if ($mulai = $request->input('mulai')) {
            $query->where('created_at', '>=', Carbon::createFromFormat('d-m-Y', $mulai)->startOfDay());
        }

        if ($sampai = $request->input('sampai')) {
            $query->where('created_at', '<=', Carbon::createFromFormat('d-m-Y', $sampai)->endOfDay());
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.anggota', [
            'anggota' => $query->get(),
            'mulai' => $mulai,
            'sampai' => $sampai,
        ])->setPaper('A4', 'landscape');

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();
        $canvas = $dompdf->getCanvas();
        $canvas->page_text(420, 570, 'Halaman {PAGE_NUM} / {PAGE_COUNT}', null, 10, [0, 0, 0]);

        $filename = 'data_anggota_'.($mulai ?? 'all').'-'.($sampai ?? 'all').'.pdf';

        return response()->streamDownload(fn () => print ($pdf->output()), $filename);
    }

    public function exportExcel(Request $request)
    {
        $mulai = $request->input('mulai');
        $sampai = $request->input('sampai');

        return Excel::download(
            new AnggotaExport($mulai, $sampai),
            'data_anggota_'.($mulai ?? 'all').'-'.($sampai ?? 'all').'.xlsx'
        );
    }

    public function downloadTemplate()
    {
        return Excel::download(new AnggotaTemplateExport, 'template_anggota.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ], [
            'file.required' => 'File wajib dipilih.',
            'file.mimes' => 'File harus berformat xlsx atau csv.',
        ]);

        try {
            Excel::import(new AnggotaImport($request->user()->id), $request->file('file')->getRealPath());

            return back()->with('flash.status', 'Data anggota berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $msg = $failures[0]->errors()[0] ?? 'Data tidak valid atau duplikat.';

            return back()->with('flash.error', $msg);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('flash.error', 'Import gagal. Periksa format dan isi file Anda.');
        }
    }

    /* ====================================================================
     |  Helper internal
     * ==================================================================== */

    private function validateAnggota(Request $request, ?int $ignoreId = null): array
    {
        $emailRule = $ignoreId
            ? 'required|email|max:255|unique:anggota,email,'.$ignoreId
            : 'required|email|max:255|unique:anggota,email';

        return $request->validate([
            'no_anggota' => 'required|string|max:255|unique:anggota,no_anggota,'.($ignoreId ?? 'NULL'),
            'pin' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'kelompok_id' => 'nullable|integer|exists:kelompok,id',
            'kantor_id' => 'nullable|integer|exists:kantor,id',
            'provinsi_id' => 'required|string|exists:indonesia_provinces,code',
            'kota_id' => 'required|string|exists:indonesia_cities,code',
            'kecamatan_id' => 'required|string|exists:indonesia_districts,code',
            'kelurahan_id' => 'required|string|exists:indonesia_villages,code',
            'email' => $emailRule,
            'tempat_lahir' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'agama' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'pendidikan' => 'required|string|max:255',
            'status_perkawinan' => 'required|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'pasangan' => 'nullable|string|max:255',
            'telepon' => 'required|string|max:255',
            'no_hp' => 'required|string|max:255',
            'jenis_identitas' => 'required|string|max:255',
            'no_identitas' => 'required|string|max:255',
            'npwp' => 'required|string|max:255',
            'ibu' => 'required|string|max:255',
            'pengurus' => 'boolean',
            'pengurus_jabatan' => 'nullable|string|max:255',
            'tgl_pengurus_diangkat' => 'nullable|date',
            'tgl_pengurus_berhenti' => 'nullable|date',
            'pengurus_berhenti' => 'nullable|string|max:255',
            'pengawas' => 'boolean',
            'pengawas_jabatan' => 'nullable|string|max:255',
            'tgl_pengawas_diangkat' => 'nullable|date',
            'tgl_pengawas_berhenti' => 'nullable|date',
            'pengawas_berhenti' => 'nullable|string|max:255',
            'waris1' => 'nullable|string|max:255',
            'hubungan_waris1' => 'nullable|string|max:255',
            'waris2' => 'nullable|string|max:255',
            'hubungan_waris2' => 'nullable|string|max:255',
            'status' => 'boolean',
            'tgl_anggota_berhenti' => 'nullable|date',
            'anggota_berhenti' => 'nullable|string|max:500',
            'foto' => $ignoreId
                ? 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048'
                : 'required|file|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'no_anggota.required' => 'Nomor anggota wajib diisi.',
            'no_anggota.unique' => 'Nomor anggota sudah digunakan.',
            'pin.required' => 'PIN wajib diisi.',
            'nama.required' => 'Nama wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'provinsi_id.required' => 'Provinsi wajib dipilih.',
            'kota_id.required' => 'Kota/Kabupaten wajib dipilih.',
            'kecamatan_id.required' => 'Kecamatan wajib dipilih.',
            'kelurahan_id.required' => 'Kelurahan wajib dipilih.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'agama.required' => 'Agama wajib diisi.',
            'pekerjaan.required' => 'Pekerjaan wajib diisi.',
            'pendidikan.required' => 'Pendidikan wajib diisi.',
            'status_perkawinan.required' => 'Status perkawinan wajib diisi.',
            'telepon.required' => 'Telepon wajib diisi.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'jenis_identitas.required' => 'Jenis identitas wajib dipilih.',
            'no_identitas.required' => 'Nomor identitas wajib diisi.',
            'npwp.required' => 'NPWP wajib diisi.',
            'ibu.required' => 'Nama ibu kandung wajib diisi.',
            'foto.required' => 'Foto wajib diunggah.',
            'foto.mimes' => 'Foto harus berformat jpg, jpeg, png, atau webp.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
        ]);
    }

    /** Resize + compress foto lalu simpan ke disk public/anggota. */
    private function simpanFoto($file, string $nama): string
    {
        $slug = Str::slug($nama);
        $random = rand(10000, 99999);
        $ext = strtolower($file->getClientOriginalExtension());
        $path = "anggota/{$slug}-{$random}.{$ext}";

        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());
        $image->scaleDown(2000);

        $encoder = match ($ext) {
            'png' => new PngEncoder(),
            'webp' => new \Intervention\Image\Encoders\WebpEncoder(quality: 70),
            default => new JpegEncoder(quality: 70),
        };

        Storage::disk('public')->put($path, (string) $image->encode($encoder));

        return $path;
    }
}
