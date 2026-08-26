<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\PencairanPinjaman;
use App\Models\Pinjaman;
use App\Models\User;
use Illuminate\Http\Request;

class PencairanPinjamanController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->string("search");
        $status = (string) $request->string("status");
        $perPage = (int) ($request->input("per_page") ?: 10);

        $pencairan = PencairanPinjaman::query()
            ->with([
                "pinjaman:id,no_pinjaman,plafon,anggota_id,jenis_id",
                "pinjaman.anggota:id,no_anggota,nama",
                "pinjaman.jenisPinjaman:id,nama",
                "approvedBy:id,nama",
                "cairOleh:id,nama",
                "createdBy:id,nama",
                "kantor:id,nama_kantor",
            ])
            ->when($search !== "", function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->whereHas("pinjaman", fn ($p) => $p
                        ->where("no_pinjaman", "ILIKE", "%{$search}%")
                        ->orWhereHas("anggota", fn ($a) => $a
                            ->where("nama", "ILIKE", "%{$search}%")
                            ->orWhere("no_anggota", "ILIKE", "%{$search}%")
                        )
                    );
                });
            })
            ->when($status !== "", fn ($q) => $q->where("status", $status))
            ->orderBy("created_at", "DESC")
            ->paginate($perPage)
            ->withQueryString();

        return inertia("Superadmin/PencairanPinjaman/Index", [
            "pencairan" => $pencairan,
            "filters" => ["search" => $search, "status" => $status],
        ]);
    }

    public function create()
    {
        // Hanya pinjaman yang aktif dan belum memiliki pencairan dengan status dicairkan
        $pinjamanOptions = Pinjaman::query()
            ->where("aktif", "1")
            ->whereDoesntHave("pencairan", fn ($q) => $q->where("status", "dicairkan"))
            ->with(["anggota:id,no_anggota,nama", "jenisPinjaman:id,nama"])
            ->orderBy("created_at", "DESC")
            ->get(["id", "no_pinjaman", "plafon", "anggota_id", "jenis_id"]);

        return inertia("Superadmin/PencairanPinjaman/Create", [
            "pinjamanOptions" => $pinjamanOptions,
            "statusOptions" => [
                ["value" => "pending", "label" => "Menunggu"],
                ["value" => "disetujui", "label" => "Disetujui"],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePencairan($request);

        $defaults = [
            "status" => $validated["status"] ?? "pending",
            "approved_by" => $validated["status"] === "disetujui" ? $request->user()->id : null,
            "approved_at" => $validated["status"] === "disetujui" ? now() : null,
            "cair_oleh" => null,
            "cair_at" => null,
            "created_by" => $request->user()->id,
            "kantor_id" => $request->user()->kantor_id ?? null,
        ];

        PencairanPinjaman::create($validated + $defaults);

        return redirect()
            ->route("superadmin.pencairan-pinjaman")
            ->with("success", "Data pencairan pinjaman berhasil dibuat.");
    }

    public function show(PencairanPinjaman $pencairan)
    {
        $pencairan->load([
            "pinjaman.anggota",
            "pinjaman.jenisPinjaman",
            "approvedBy",
            "cairOleh",
            "createdBy",
            "kantor",
        ]);

        return inertia("Superadmin/PencairanPinjaman/Show", [
            "pencairan" => $pencairan,
        ]);
    }

    public function edit(PencairanPinjaman $pencairan)
    {
        $pinjamanOptions = Pinjaman::query()
            ->where("aktif", "1")
            ->with(["anggota:id,no_anggota,nama", "jenisPinjaman:id,nama"])
            ->orderBy("created_at", "DESC")
            ->get(["id", "no_pinjaman", "plafon", "anggota_id", "jenis_id"]);

        return inertia("Superadmin/PencairanPinjaman/Edit", [
            "pencairan" => $pencairan->load("pinjaman.anggota", "pinjaman.jenisPinjaman"),
            "pinjamanOptions" => $pinjamanOptions,
            "statusOptions" => [
                ["value" => "pending", "label" => "Menunggu"],
                ["value" => "disetujui", "label" => "Disetujui"],
                ["value" => "ditolak", "label" => "Ditolak"],
            ],
        ]);
    }

    public function update(Request $request, PencairanPinjaman $pencairan)
    {
        // Jika sudah dicairkan, tidak bisa diubah
        if ($pencairan->status === "dicairkan") {
            return redirect()
                ->route("superadmin.pencairan-pinjaman")
                ->with("error", "Data pencairan yang sudah dicairkan tidak dapat diubah.");
        }

        $validated = $this->validatePencairan($request);

        $updates = $validated;

        // Handle status changes
        if (isset($validated["status"])) {
            if ($validated["status"] === "disetujui" && $pencairan->status !== "disetujui") {
                $updates["approved_by"] = $request->user()->id;
                $updates["approved_at"] = now();
            } elseif ($validated["status"] === "ditolak") {
                $updates["approved_by"] = $request->user()->id;
                $updates["approved_at"] = now();
            } elseif ($validated["status"] === "pending") {
                $updates["approved_by"] = null;
                $updates["approved_at"] = null;
            }
        }

        $pencairan->update($updates);

        return redirect()
            ->route("superadmin.pencairan-pinjaman")
            ->with("success", "Data pencairan pinjaman berhasil diperbarui.");
    }

    public function approve(Request $request, PencairanPinjaman $pencairan)
    {
        if ($pencairan->status !== "pending") {
            return redirect()
                ->back()
                ->with("error", "Hanya pencairan dengan status Menunggu yang bisa disetujui.");
        }

        $pencairan->update([
            "status" => "disetujui",
            "approved_by" => $request->user()->id,
            "approved_at" => now(),
        ]);

        return redirect()
            ->back()
            ->with("success", "Pencairan pinjaman berhasil disetujui.");
    }

    public function reject(Request $request, PencairanPinjaman $pencairan)
    {
        if ($pencairan->status === "dicairkan") {
            return redirect()
                ->back()
                ->with("error", "Pencairan yang sudah dicairkan tidak bisa ditolak.");
        }

        $request->validate([
            "alasan_penolakan" => ["required", "string", "max:500"],
        ]);

        $pencairan->update([
            "status" => "ditolak",
            "approved_by" => $request->user()->id,
            "approved_at" => now(),
            "keterangan" => ($pencairan->keterangan ? $pencairan->keterangan . "\n\n" : "") .
                "ALASAN PENOLAKAN: " . $request->alasan_penolakan,
        ]);

        return redirect()
            ->back()
            ->with("success", "Pencairan pinjaman berhasil ditolak.");
    }

    public function cairkan(Request $request, PencairanPinjaman $pencairan)
    {
        if ($pencairan->status !== "disetujui") {
            return redirect()
                ->back()
                ->with("error", "Hanya pencairan yang sudah disetujui yang bisa dicairkan.");
        }

        $pencairan->update([
            "status" => "dicairkan",
            "cair_oleh" => $request->user()->id,
            "cair_at" => now(),
        ]);

        return redirect()
            ->back()
            ->with("success", "Pencairan pinjaman berhasil dicairkan.");
    }

    public function destroy(PencairanPinjaman $pencairan)
    {
        if ($pencairan->status === "dicairkan") {
            return redirect()
                ->route("superadmin.pencairan-pinjaman")
                ->with("error", "Data pencairan yang sudah dicairkan tidak dapat dihapus.");
        }

        $pencairan->delete();

        return redirect()
            ->route("superadmin.pencairan-pinjaman")
            ->with("success", "Data pencairan pinjaman berhasil dihapus.");
    }

    private function validatePencairan(Request $request): array
    {
        return $request->validate([
            "pinjaman_id" => ["required", "integer", "exists:pinjaman,id"],
            "tanggal_cair" => ["required", "date"],
            "nominal_cair" => ["required", "numeric", "min:1"],
            "metode_cair" => ["required", "in:transfer,tunai,cek,giro"],
            "no_rekening" => ["nullable", "string", "max:50"],
            "nama_rekening" => ["nullable", "string", "max:100"],
            "bank_id" => ["nullable", "string", "max:50"],
            "biaya_admin" => ["nullable", "numeric", "min:0"],
            "potongan_simpanan" => ["nullable", "numeric", "min:0"],
            "keterangan" => ["nullable", "string"],
            "status" => ["nullable", "in:pending,disetujui,ditolak"],
        ]);
    }
}
