<?php
namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Kantor;
use App\Models\LaporanKasHarian;
use App\Models\LaporanTransaksiPinjaman;
use App\Models\LaporanTransaksiSimpanan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    private function meta(string $routeName): array
    {
        $map = [
            "laporan-kas-harian" => ["page" => "LaporanKasHarian", "title" => "Laporan Transaksi Kas Harian", "model" => LaporanKasHarian::class, "prefix" => "LK", "routeKey" => "laporan-kas-harian"],
            "laporan-transaksi-simpanan" => ["page" => "LaporanTransaksiSimpanan", "title" => "Laporan Transaksi Simpanan", "model" => LaporanTransaksiSimpanan::class, "prefix" => "LS", "routeKey" => "laporan-transaksi-simpanan"],
            "laporan-transaksi-pinjaman" => ["page" => "LaporanTransaksiPinjaman", "title" => "Laporan Transaksi Pinjaman", "model" => LaporanTransaksiPinjaman::class, "prefix" => "LP", "routeKey" => "laporan-transaksi-pinjaman"],
        ];
        foreach ($map as $key => $m) {
            if (str_contains($routeName, $key)) return $m;
        }
        return reset($map);
    }

    private function detectRouteName(): string { return request()->route()?->getName() ?? ""; }

    private function indexRoute(string $routeKey): string
    {
        return "superadmin.laporan.{$routeKey}";
    }

    public function index(Request $request)
    {
        $m = $this->meta($this->detectRouteName());
        $hasAnggota = in_array($m["page"], ["LaporanTransaksiSimpanan", "LaporanTransaksiPinjaman"]);

        $query = $m["model"]::query()
            ->with($hasAnggota
                ? ["anggota:id,no_anggota,nama", "user:id,nama", "kantor:id,nama_kantor"]
                : ["user:id,nama", "kantor:id,nama_kantor"]);

        $query->when($request->filled("search"), function ($q) use ($request, $hasAnggota) {
            $term = $request->string("search");
            $q->where(fn ($qq) => $qq->where("no_laporan", "LIKE", "%{$term}%")
                ->orWhere($hasAnggota ? "jenis_transaksi" : "keterangan", "LIKE", "%{$term}%"));
        })
        ->when($request->filled("status"), fn ($q) => $q->where("status", $request->string("status")))
        ->when($request->filled("mulai"), fn ($q) => $q->whereDate("tgl_laporan", ">=", $request->date("mulai")))
        ->when($request->filled("sampai"), fn ($q) => $q->whereDate("tgl_laporan", "<=", $request->date("sampai")))
        ->orderByDesc("tgl_laporan")->orderByDesc("id");

        $paginated = $query->paginate($request->integer("per_page", 10))->withQueryString();

        // Map laporan fields to transaksi shape for frontend compatibility
        $paginated->getCollection()->transform(function ($item) {
            $item->no_transaksi = $item->no_laporan;
            $item->tgl_transaksi = $item->tgl_laporan;
            // For KasHarian: show saldo_akhir as the main nominal
            if (property_exists($item, 'saldo_akhir')) {
                $item->nominal = $item->saldo_akhir;
            }
            return $item;
        });

        return inertia("Superadmin/{$m["page"]}/Index", [
            "transaksi" => $paginated,
            "filters" => $request->only(["search", "status", "mulai", "sampai"]),
            "variantTitle" => $m["title"],
        ]);
    }

    public function create()
    {
        $m = $this->meta($this->detectRouteName());
        $hasAnggota = in_array($m["page"], ["LaporanTransaksiSimpanan", "LaporanTransaksiPinjaman"]);
        $data = ["kantors" => Kantor::select("id", "kode", "nama_kantor")->get()];
        if ($hasAnggota) $data["anggotas"] = Anggota::select("id", "no_anggota", "nama")->get();
        return inertia("Superadmin/{$m["page"]}/Create", $data);
    }

    public function store(Request $request)
    {
        $m = $this->meta($this->detectRouteName());
        $hasAnggota = in_array($m["page"], ["LaporanTransaksiSimpanan", "LaporanTransaksiPinjaman"]);

        $rules = [
            "tgl_laporan" => "required|date",
            "keterangan" => "nullable|string",
            "kantor_id" => "required|exists:kantor,id",
            "status" => "in:draft,posted,batal",
        ];
        if ($m["page"] === "LaporanKasHarian") {
            $rules = array_merge($rules, [
                "saldo_awal" => "required|numeric|min:0",
                "total_pemasukan" => "required|numeric|min:0",
                "total_pengeluaran" => "required|numeric|min:0",
                "saldo_akhir" => "required|numeric",
            ]);
        } else {
            $rules = array_merge($rules, [
                "anggota_id" => "required|exists:anggota,id",
                "jenis_transaksi" => "required|string",
                "nominal" => "required|numeric|min:0",
            ]);
        }

        $validated = $request->validate($rules);
        $validated["no_laporan"] = $m["prefix"] . "-" . date("YmdHis") . rand(10, 99);
        $validated["user_id"] = auth()->id();
        $validated["status"] = $validated["status"] ?? "draft";

        $m["model"]::create($validated);
        return redirect()->route($this->indexRoute($m["routeKey"]))->with("success", $m["title"] . " berhasil ditambahkan.");
    }

    public function show($id)
    {
        $m = $this->meta($this->detectRouteName());
        $hasAnggota = in_array($m["page"], ["LaporanTransaksiSimpanan", "LaporanTransaksiPinjaman"]);
        $item = $m["model"]::with($hasAnggota ? ["anggota", "user", "kantor"] : ["user", "kantor"])->findOrFail($id);
        $data = $item->toArray();
        $data["no_transaksi"] = $data["no_laporan"] ?? null;
        $data["tgl_transaksi"] = $data["tgl_laporan"] ?? null;
        return inertia("Superadmin/{$m["page"]}/Show", ["transaksi" => $data]);
    }

    public function edit($id)
    {
        $m = $this->meta($this->detectRouteName());
        $hasAnggota = in_array($m["page"], ["LaporanTransaksiSimpanan", "LaporanTransaksiPinjaman"]);
        $item = $m["model"]::findOrFail($id)->toArray();
        $item["no_transaksi"] = $item["no_laporan"] ?? null;
        $item["tgl_transaksi"] = $item["tgl_laporan"] ?? null;
        $data = ["transaksi" => $item, "kantors" => Kantor::select("id", "kode", "nama_kantor")->get()];
        if ($hasAnggota) $data["anggotas"] = Anggota::select("id", "no_anggota", "nama")->get();
        return inertia("Superadmin/{$m["page"]}/Edit", $data);
    }

    public function update(Request $request, $id)
    {
        $m = $this->meta($this->detectRouteName());
        $hasAnggota = in_array($m["page"], ["LaporanTransaksiSimpanan", "LaporanTransaksiPinjaman"]);
        $rules = ["tgl_laporan" => "sometimes|date", "keterangan" => "nullable|string", "kantor_id" => "sometimes|exists:kantor,id", "status" => "in:draft,posted,batal"];
        if ($m["page"] === "LaporanKasHarian") {
            $rules = array_merge($rules, ["saldo_awal" => "sometimes|numeric|min:0", "total_pemasukan" => "sometimes|numeric|min:0", "total_pengeluaran" => "sometimes|numeric|min:0", "saldo_akhir" => "sometimes|numeric"]);
        } else {
            $rules = array_merge($rules, ["anggota_id" => "sometimes|exists:anggota,id", "jenis_transaksi" => "sometimes|string", "nominal" => "sometimes|numeric|min:0"]);
        }
        $validated = $request->validate($rules);
        $m["model"]::findOrFail($id)->update($validated);
        return redirect()->route($this->indexRoute($m["routeKey"]))->with("success", $m["title"] . " berhasil diupdate.");
    }

    public function destroy($id)
    {
        $m = $this->meta($this->detectRouteName());
        $m["model"]::findOrFail($id)->delete();
        return redirect()->route($this->indexRoute($m["routeKey"]))->with("success", "Laporan berhasil dihapus.");
    }
}
