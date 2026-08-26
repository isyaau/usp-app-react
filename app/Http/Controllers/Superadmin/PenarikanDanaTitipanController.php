<?php
namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\PenarikanDanaTitipanAnggota;
use Illuminate\Http\Request;

class PenarikanDanaTitipanController extends Controller
{
    private const ROUTE_PREFIX = "superadmin.transaksi-titipan.penarikan-dana-titipan";

    public function index(Request $request)
    {
        $data = PenarikanDanaTitipanAnggota::query()
            ->with(["anggota:id,no_anggota,nama", "user:id,nama", "kantor:id,nama_kantor"])
            ->when($request->filled("search"), function ($q) use ($request) {
                $term = $request->string("search");
                $q->where(fn ($qq) => $qq
                    ->where("no_transaksi", "LIKE", "%{$term}%")
                    ->orWhereHas("anggota", fn ($a) => $a->where("nama", "LIKE", "%{$term}%")));
            })
            ->when($request->filled("status"), fn ($q) => $q->where("status", $request->string("status")))
            ->when($request->filled("mulai"), fn ($q) => $q->whereDate("tgl_transaksi", ">=", $request->date("mulai")))
            ->when($request->filled("sampai"), fn ($q) => $q->whereDate("tgl_transaksi", "<=", $request->date("sampai")))
            ->orderByDesc("tgl_transaksi")
            ->orderByDesc("id")
            ->paginate($request->integer("per_page", 10))
            ->withQueryString();

        return inertia("Superadmin/PenarikanDanaTitipan/Index", [
            "transaksi" => $data,
            "filters" => $request->only(["search", "status", "mulai", "sampai"]),
            "variantTitle" => "Penarikan Dana Titipan Anggota",
        ]);
    }

    public function create()
    {
        return inertia("Superadmin/PenarikanDanaTitipan/Create", [
            "anggotas" => Anggota::select("id", "no_anggota", "nama")->get(),
            "kantors" => Kantor::select("id", "kode", "nama_kantor")->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "tgl_transaksi" => "required|date",
            "anggota_id" => "required|exists:anggota,id",
            "nominal_penarikan" => "required|numeric|min:1",
            "keterangan" => "nullable|string",
            "kantor_id" => "required|exists:kantor,id",
            "status" => "in:draft,posted,batal",
        ]);

        $validated["no_transaksi"] = "DT-" . date("YmdHis") . rand(10, 99);
        $validated["user_id"] = auth()->id();
        $validated["status"] = $validated["status"] ?? "draft";

        PenarikanDanaTitipanAnggota::create($validated);

        return redirect()->route(self::ROUTE_PREFIX)
            ->with("success", "Penarikan dana titipan berhasil ditambahkan.");
    }

    public function show(PenarikanDanaTitipanAnggota $penarikanDanaTitipan)
    {
        $penarikanDanaTitipan->load(["anggota", "user", "kantor"]);
        $data = $penarikanDanaTitipan->toArray();

        return inertia("Superadmin/PenarikanDanaTitipan/Show", ["transaksi" => $data]);
    }

    public function edit(PenarikanDanaTitipanAnggota $penarikanDanaTitipan)
    {
        return inertia("Superadmin/PenarikanDanaTitipan/Edit", [
            "transaksi" => $penarikanDanaTitipan->toArray(),
            "anggotas" => Anggota::select("id", "no_anggota", "nama")->get(),
            "kantors" => Kantor::select("id", "kode", "nama_kantor")->get(),
        ]);
    }

    public function update(Request $request, PenarikanDanaTitipanAnggota $penarikanDanaTitipan)
    {
        $validated = $request->validate([
            "tgl_transaksi" => "sometimes|date",
            "anggota_id" => "sometimes|exists:anggota,id",
            "nominal_penarikan" => "sometimes|numeric|min:1",
            "keterangan" => "nullable|string",
            "kantor_id" => "sometimes|exists:kantor,id",
            "status" => "in:draft,posted,batal",
        ]);

        $penarikanDanaTitipan->update($validated);

        return redirect()->route(self::ROUTE_PREFIX)
            ->with("success", "Penarikan dana titipan berhasil diupdate.");
    }

    public function destroy(PenarikanDanaTitipanAnggota $penarikanDanaTitipan)
    {
        $penarikanDanaTitipan->delete();

        return redirect()->route(self::ROUTE_PREFIX)
            ->with("success", "Penarikan dana titipan berhasil dihapus.");
    }
}
