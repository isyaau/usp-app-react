import { Link, Head, useForm } from "@inertiajs/react";
import { Banknote, LoaderCircle, Wallet, ArrowLeft, Send, CheckCircle, XCircle } from "lucide-react";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { PageHeader } from "@/Components/PageHeader";
import { Button } from "@/Components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Input } from "@/Components/ui/input";
import { Label } from "@/Components/ui/label";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
import { Badge } from "@/Components/ui/badge";
import type { PencairanPinjamanFormValues, PinjamanOptionLite, PencairanPinjamanRow } from "@/types/models";

interface Props {
    pencairan: PencairanPinjamanRow;
    pinjamanOptions: PinjamanOptionLite[];
    statusOptions: { value: string; label: string }[];
}

const METODE_OPTIONS = [
    { value: "transfer", label: "Transfer Bank" },
    { value: "tunai", label: "Tunai" },
    { value: "cek", label: "Cek" },
    { value: "giro", label: "Giro" },
];

const METODE_LABELS: Record<string, string> = {
    transfer: "Transfer Bank",
    tunai: "Tunai",
    cek: "Cek",
    giro: "Giro",
};

export default function PencairanPinjamanEdit({ pencairan, pinjamanOptions, statusOptions }: Props) {
    const form = useForm<PencairanPinjamanFormValues>({
        pinjaman_id: String(pencairan.pinjaman_id),
        tanggal_cair: pencairan.tanggal_cair,
        nominal_cair: String(pencairan.nominal_cair),
        metode_cair: pencairan.metode_cair,
        no_rekening: pencairan.no_rekening ?? "",
        nama_rekening: pencairan.nama_rekening ?? "",
        bank_id: pencairan.bank_id ?? "",
        biaya_admin: String(pencairan.biaya_admin ?? 0),
        potongan_simpanan: String(pencairan.potongan_simpanan ?? 0),
        keterangan: pencairan.keterangan ?? "",
        status: pencairan.status,
    });

    const selectedPinjaman = pinjamanOptions.find((p) => String(p.id) === form.data.pinjaman_id);
    const plafonPinjaman = selectedPinjaman ? Number(selectedPinjaman.plafon) : (pencairan.pinjaman ? Number(pencairan.pinjaman.plafon) : 0);
    const nominalCair = Number(form.data.nominal_cair) || 0;
    const biayaAdmin = Number(form.data.biaya_admin) || 0;
    const potonganSimpanan = Number(form.data.potongan_simpanan) || 0;
    const nominalBersih = nominalCair - biayaAdmin - potonganSimpanan;

    const isDicairkan = pencairan.status === "dicairkan";
    const isDisabled = isDicairkan;

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.put(route("superadmin.pencairan-pinjaman.update", pencairan.id), {
            preserveScroll: true,
        });
    };

    const handleApprove = () => {
        if (confirm("Setujui pencairan ini?")) {
            fetch(route("superadmin.pencairan-pinjaman.approve", pencairan.id), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]")?.getAttribute("content") ?? "",
                },
            }).then(() => window.location.reload());
        }
    };

    const handleReject = () => {
        const alasan = prompt("Masukkan alasan penolakan:");
        if (alasan) {
            fetch(route("superadmin.pencairan-pinjaman.reject", pencairan.id), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]")?.getAttribute("content") ?? "",
                },
                body: JSON.stringify({ alasan_penolakan: alasan }),
            }).then(() => window.location.reload());
        }
    };

    const handleCairkan = () => {
        if (confirm("Cairkan pencairan ini?")) {
            fetch(route("superadmin.pencairan-pinjaman.cairkan", pencairan.id), {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]")?.getAttribute("content") ?? "",
                },
            }).then(() => window.location.reload());
        }
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case "pending":
                return (
                    <Badge variant="outline" className="border-yellow-500/40 bg-yellow-500/10 text-yellow-600 dark:text-yellow-400">
                        Menunggu
                    </Badge>
                );
            case "disetujui":
                return (
                    <Badge variant="outline" className="border-blue-500/40 bg-blue-500/10 text-blue-600 dark:text-blue-400">
                        Disetujui
                    </Badge>
                );
            case "ditolak":
                return (
                    <Badge variant="outline" className="border-red-500/40 bg-red-500/10 text-red-600 dark:text-red-400">
                        Ditolak
                    </Badge>
                );
            case "dicairkan":
                return (
                    <Badge variant="outline" className="border-emerald-500/40 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        Dicairkan
                    </Badge>
                );
            default:
                return <Badge variant="outline">{status}</Badge>;
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Edit Pencairan - ${pencairan.pinjaman?.no_pinjaman}`} />

            <PageHeader
                title="Edit Pencairan Pinjaman"
                description={`No. Pinjaman: ${pencairan.pinjaman?.no_pinjaman}`}
                icon={Banknote}
                backHref={route("superadmin.pencairan-pinjaman")}
            />

            {!isDicairkan ? (
                <form onSubmit={submit} className="max-w-4xl">
                    <Card>
                        <CardHeader>
                            <CardTitle>Data Pencairan</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-5">
                            <div className="grid gap-5 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="pinjaman_id">
                                        Pinjaman <span className="text-brand-600">*</span>
                                    </Label>
                                    <Select
                                        value={form.data.pinjaman_id || undefined}
                                        onValueChange={(v) => form.setData("pinjaman_id", v)}
                                        disabled={isDisabled}
                                    >
                                        <SelectTrigger className="w-full" aria-label="Pilih Pinjaman">
                                            <SelectValue placeholder="-- Pilih Pinjaman --" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {pinjamanOptions.map((p) => (
                                                <SelectItem key={p.id} value={String(p.id)}>
                                                    <span className="font-mono text-xs">{p.no_pinjaman}</span>{" "}
                                                    — {p.anggota?.nama ?? "—"} ({p.jenisPinjaman?.nama ?? "—"})
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {form.errors.pinjaman_id && (
                                        <p className="text-sm text-brand-600">{form.errors.pinjaman_id}</p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="tanggal_cair">
                                        Tanggal Cair <span className="text-brand-600">*</span>
                                    </Label>
                                    <Input
                                        id="tanggal_cair"
                                        type="date"
                                        value={form.data.tanggal_cair}
                                        onChange={(e) => form.setData("tanggal_cair", e.target.value)}
                                        disabled={isDisabled}
                                    />
                                    {form.errors.tanggal_cair && (
                                        <p className="text-sm text-brand-600">{form.errors.tanggal_cair}</p>
                                    )}
                                </div>
                            </div>

                            <div className="grid gap-5 sm:grid-cols-3">
                                <div className="space-y-2">
                                    <Label htmlFor="nominal_cair">
                                        Nominal Cair <span className="text-brand-600">*</span>
                                    </Label>
                                    <Input
                                        id="nominal_cair"
                                        value={form.data.nominal_cair}
                                        onChange={(e) => form.setData("nominal_cair", e.target.value)}
                                        inputMode="numeric"
                                        placeholder="10000000"
                                        disabled={isDisabled}
                                    />
                                    {form.errors.nominal_cair && (
                                        <p className="text-sm text-brand-600">{form.errors.nominal_cair}</p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="metode_cair">Metode Cair <span className="text-brand-600">*</span></Label>
                                    <Select
                                        value={form.data.metode_cair}
                                        onValueChange={(v) => form.setData("metode_cair", v)}
                                        disabled={isDisabled}
                                    >
                                        <SelectTrigger className="w-full" aria-label="Pilih Metode Cair">
                                            <SelectValue placeholder="-- Pilih Metode --" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {METODE_OPTIONS.map((m) => (
                                                <SelectItem key={m.value} value={m.value}>
                                                    {m.label}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {form.errors.metode_cair && (
                                        <p className="text-sm text-brand-600">{form.errors.metode_cair}</p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="status">Status <span className="text-brand-600">*</span></Label>
                                    <Select
                                        value={form.data.status}
                                        onValueChange={(v) => form.setData("status", v)}
                                        disabled={isDisabled}
                                    >
                                        <SelectTrigger className="w-full" aria-label="Pilih Status">
                                            <SelectValue placeholder="-- Pilih Status --" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {statusOptions.map((s) => (
                                                <SelectItem key={s.value} value={s.value}>
                                                    {s.label}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {form.errors.status && (
                                        <p className="text-sm text-brand-600">{form.errors.status}</p>
                                    )}
                                </div>
                            </div>

                            <div className="grid gap-5 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="no_rekening">No. Rekening</Label>
                                    <Input
                                        id="no_rekening"
                                        value={form.data.no_rekening}
                                        onChange={(e) => form.setData("no_rekening", e.target.value)}
                                        placeholder="1234567890"
                                        disabled={isDisabled}
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="nama_rekening">Nama Rekening</Label>
                                    <Input
                                        id="nama_rekening"
                                        value={form.data.nama_rekening}
                                        onChange={(e) => form.setData("nama_rekening", e.target.value)}
                                        placeholder="Nama pemilik rekening"
                                        disabled={isDisabled}
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="bank_id">Bank</Label>
                                    <Input
                                        id="bank_id"
                                        value={form.data.bank_id}
                                        onChange={(e) => form.setData("bank_id", e.target.value)}
                                        placeholder="BCA, BRI, BNI, Mandiri, dll"
                                        disabled={isDisabled}
                                    />
                                </div>
                            </div>

                            <div className="grid gap-5 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="biaya_admin">Biaya Admin</Label>
                                    <Input
                                        id="biaya_admin"
                                        value={form.data.biaya_admin}
                                        onChange={(e) => form.setData("biaya_admin", e.target.value)}
                                        inputMode="numeric"
                                        placeholder="0"
                                        disabled={isDisabled}
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="potongan_simpanan">Potongan Simpanan</Label>
                                    <Input
                                        id="potongan_simpanan"
                                        value={form.data.potongan_simpanan}
                                        onChange={(e) => form.setData("potongan_simpanan", e.target.value)}
                                        inputMode="numeric"
                                        placeholder="0"
                                        disabled={isDisabled}
                                    />
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="keterangan">Keterangan</Label>
                                <Input
                                    id="keterangan"
                                    value={form.data.keterangan}
                                    onChange={(e) => form.setData("keterangan", e.target.value)}
                                    placeholder="Catatan tambahan..."
                                    disabled={isDisabled}
                                />
                            </div>

                            {/* Ringkasan */}
                            <div className="rounded-lg bg-muted/60 p-4 space-y-2">
                                <div className="flex justify-between text-sm">
                                    <span>Plafon Pinjaman</span>
                                    <span className="font-mono font-medium">Rp {plafonPinjaman.toLocaleString("id-ID")}</span>
                                </div>
                                <div className="flex justify-between text-sm">
                                    <span>Nominal Cair</span>
                                    <span className="font-mono font-medium">Rp {nominalCair.toLocaleString("id-ID")}</span>
                                </div>
                                <div className="flex justify-between text-sm text-muted-foreground">
                                    <span>Biaya Admin</span>
                                    <span className="font-mono">Rp {biayaAdmin.toLocaleString("id-ID")}</span>
                                </div>
                                <div className="flex justify-between text-sm text-muted-foreground">
                                    <span>Potongan Simpanan</span>
                                    <span className="font-mono">Rp {potonganSimpanan.toLocaleString("id-ID")}</span>
                                </div>
                                <hr className="my-2" />
                                <div className="flex justify-between text-lg font-bold text-emerald-600">
                                    <span>Nominal Bersih</span>
                                    <span className="font-mono">Rp {nominalBersih.toLocaleString("id-ID")}</span>
                                </div>
                                {plafonPinjaman > 0 && nominalCair > plafonPinjaman && (
                                    <p className="text-sm text-red-600">
                                        <span className="flex items-center gap-1">
                                            <Wallet className="size-4" />
                                            Nominal cair melebihi plafon pinjaman!
                                        </span>
                                    </p>
                                )}
                            </div>
                        </CardContent>
                    </Card>

                    <div className="mt-5 flex items-center justify-end gap-3">
                        <Button variant="outline" asChild>
                            <Link href={route("superadmin.pencairan-pinjaman")}>
                                <ArrowLeft className="size-4 mr-2" />
                                Kembali
                            </Link>
                        </Button>
                        <Button
                            type="submit"
                            disabled={form.processing || isDisabled}
                            className="bg-brand-600 hover:bg-brand-500"
                        >
                            {form.processing && <LoaderCircle className="animate-spin size-4" />}
                            Simpan Perubahan
                        </Button>
                    </div>
                </form>
            ) : (
                <div className="max-w-4xl">
                    <Card>
                        <CardHeader>
                            <div className="flex items-center justify-between">
                                <CardTitle>Data Pencairan (Sudah Dicairkan)</CardTitle>
                                <Badge variant="outline" className="border-emerald-500/40 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                    Dicairkan
                                </Badge>
                            </div>
                        </CardHeader>
                        <CardContent className="space-y-5">
                            <div className="grid gap-5 sm:grid-cols-2">
                                <div className="space-y-1">
                                    <Label className="text-muted-foreground">Pinjaman</Label>
                                    <p className="font-mono">{pencairan.pinjaman?.no_pinjaman}</p>
                                </div>
                                <div className="space-y-1">
                                    <Label className="text-muted-foreground">Tanggal Cair</Label>
                                    <p>{pencairan.tanggal_cair}</p>
                                </div>
                                <div className="space-y-1">
                                    <Label className="text-muted-foreground">Anggota</Label>
                                    <p>{pencairan.pinjaman?.anggota?.nama}</p>
                                </div>
                                <div className="space-y-1">
                                    <Label className="text-muted-foreground">Produk</Label>
                                    <p>{pencairan.pinjaman?.jenisPinjaman?.nama}</p>
                                </div>
                            </div>

                            <div className="grid gap-5 sm:grid-cols-3">
                                <div className="space-y-1">
                                    <Label className="text-muted-foreground">Metode Cair</Label>
                                    <Badge variant="secondary">{METODE_LABELS[pencairan.metode_cair] ?? pencairan.metode_cair}</Badge>
                                </div>
                                <div className="space-y-1">
                                    <Label className="text-muted-foreground">Nominal Cair</Label>
                                    <p className="font-mono text-lg">Rp {Number(pencairan.nominal_cair).toLocaleString("id-ID")}</p>
                                </div>
                                <div className="space-y-1">
                                    <Label className="text-muted-foreground">Nominal Bersih</Label>
                                    <p className="font-mono text-lg text-emerald-600">
                                        Rp {(Number(pencairan.nominal_cair) - Number(pencairan.biaya_admin ?? 0) - Number(pencairan.potongan_simpanan ?? 0)).toLocaleString("id-ID")}
                                    </p>
                                </div>
                            </div>

                            {pencairan.no_rekening && (
                                <div className="grid gap-5 sm:grid-cols-3">
                                    <div className="space-y-1">
                                        <Label className="text-muted-foreground">No. Rekening</Label>
                                        <p>{pencairan.no_rekening}</p>
                                    </div>
                                    <div className="space-y-1">
                                        <Label className="text-muted-foreground">Nama Rekening</Label>
                                        <p>{pencairan.nama_rekening}</p>
                                    </div>
                                    <div className="space-y-1">
                                        <Label className="text-muted-foreground">Bank</Label>
                                        <p>{pencairan.bank_id}</p>
                                    </div>
                                </div>
                            )}

                            <div className="grid gap-5 sm:grid-cols-2">
                                <div className="space-y-1">
                                    <Label className="text-muted-foreground">Biaya Admin</Label>
                                    <p className="font-mono">Rp {Number(pencairan.biaya_admin ?? 0).toLocaleString("id-ID")}</p>
                                </div>
                                <div className="space-y-1">
                                    <Label className="text-muted-foreground">Potongan Simpanan</Label>
                                    <p className="font-mono">Rp {Number(pencairan.potongan_simpanan ?? 0).toLocaleString("id-ID")}</p>
                                </div>
                            </div>

                            {pencairan.keterangan && (
                                <div className="space-y-1">
                                    <Label className="text-muted-foreground">Keterangan</Label>
                                    <p className="whitespace-pre-wrap">{pencairan.keterangan}</p>
                                </div>
                            )}

                            <div className="border-t pt-4 space-y-2 text-sm text-muted-foreground">
                                <div className="flex justify-between">
                                    <span>Dibuat oleh</span>
                                    <span>{pencairan.createdBy?.name ?? "-"}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span>Disetujui oleh</span>
                                    <span>{pencairan.approvedBy?.name ?? "-"}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span>Tanggal Disetujui</span>
                                    <span>{pencairan.approved_at ?? "-"}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span>Dicairkan oleh</span>
                                    <span>{pencairan.cairOleh?.name ?? "-"}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span>Tanggal Cair</span>
                                    <span>{pencairan.cair_at ?? "-"}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <div className="mt-5 flex items-center justify-end gap-3">
                        <Button variant="outline" asChild>
                            <Link href={route("superadmin.pencairan-pinjaman")}>
                                <ArrowLeft className="size-4 mr-2" />
                                Kembali
                            </Link>
                        </Button>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
