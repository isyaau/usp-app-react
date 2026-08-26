import { Link, Head, useForm } from "@inertiajs/react";
import { Banknote, LoaderCircle, Wallet, ArrowLeft } from "lucide-react";

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
import type { PencairanPinjamanFormValues, PinjamanOptionLite } from "@/types/models";

interface Props {
    pinjamanOptions: PinjamanOptionLite[];
    statusOptions: { value: string; label: string }[];
}

const METODE_OPTIONS = [
    { value: "transfer", label: "Transfer Bank" },
    { value: "tunai", label: "Tunai" },
    { value: "cek", label: "Cek" },
    { value: "giro", label: "Giro" },
];

export default function PencairanPinjamanCreate({ pinjamanOptions, statusOptions }: Props) {
    const form = useForm<PencairanPinjamanFormValues>({
        pinjaman_id: "",
        tanggal_cair: new Date().toISOString().slice(0, 10),
        nominal_cair: "",
        metode_cair: "transfer",
        no_rekening: "",
        nama_rekening: "",
        bank_id: "",
        biaya_admin: "0",
        potongan_simpanan: "0",
        keterangan: "",
        status: "pending",
    });

    // Cari plafon pinjaman yang dipilih
    const selectedPinjaman = pinjamanOptions.find((p) => String(p.id) === form.data.pinjaman_id);
    const plafonPinjaman = selectedPinjaman ? Number(selectedPinjaman.plafon) : 0;
    const nominalCair = Number(form.data.nominal_cair) || 0;
    const biayaAdmin = Number(form.data.biaya_admin) || 0;
    const potonganSimpanan = Number(form.data.potongan_simpanan) || 0;
    const nominalBersih = nominalCair - biayaAdmin - potonganSimpanan;

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(route("superadmin.pencairan-pinjaman.store"), {
            preserveScroll: true,
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Tambah Pencairan Pinjaman" />

            <PageHeader
                title="Tambah Pencairan Pinjaman"
                description="Buat pencairan dana pinjaman baru untuk anggota."
                icon={Banknote}
                backHref={route("superadmin.pencairan-pinjaman")}
            />

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

                        {/* Detail Rekening (untuk transfer) */}
                        <div className="grid gap-5 sm:grid-cols-2" id="rekeningFields">
                            <div className="space-y-2">
                                <Label htmlFor="no_rekening">No. Rekening</Label>
                                <Input
                                    id="no_rekening"
                                    value={form.data.no_rekening}
                                    onChange={(e) => form.setData("no_rekening", e.target.value)}
                                    placeholder="1234567890"
                                />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="nama_rekening">Nama Rekening</Label>
                                <Input
                                    id="nama_rekening"
                                    value={form.data.nama_rekening}
                                    onChange={(e) => form.setData("nama_rekening", e.target.value)}
                                    placeholder="Nama pemilik rekening"
                                />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="bank_id">Bank</Label>
                                <Input
                                    id="bank_id"
                                    value={form.data.bank_id}
                                    onChange={(e) => form.setData("bank_id", e.target.value)}
                                    placeholder="BCA, BRI, BNI, Mandiri, dll"
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
                        disabled={form.processing}
                        className="bg-brand-600 hover:bg-brand-500"
                    >
                        {form.processing && <LoaderCircle className="animate-spin size-4" />}
                        Simpan Pencairan
                    </Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
