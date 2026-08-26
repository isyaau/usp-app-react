import { Link, Head } from "@inertiajs/react";
import { Banknote, Wallet, ArrowLeft, User, Calendar, Banknote as BanknoteIcon, CreditCard, CheckCircle, Send, XCircle } from "lucide-react";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { PageHeader } from "@/Components/PageHeader";
import { Button } from "@/Components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/Components/ui/card";
import { Badge } from "@/Components/ui/badge";
import type { PencairanPinjamanRow } from "@/types/models";

interface Props {
    pencairan: PencairanPinjamanRow;
}

const METODE_LABELS: Record<string, string> = {
    transfer: "Transfer Bank",
    tunai: "Tunai",
    cek: "Cek",
    giro: "Giro",
};

const rupiah = (v: string | number | null) =>
    `Rp ${Number(v ?? 0).toLocaleString("id-ID")}`;

export default function PencairanPinjamanShow({ pencairan }: Props) {
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

    const nominalBersih = Number(pencairan.nominal_cair) - Number(pencairan.biaya_admin ?? 0) - Number(pencairan.potongan_simpanan ?? 0);

    return (
        <AuthenticatedLayout>
            <Head title={`Detail Pencairan - ${pencairan.pinjaman?.no_pinjaman}`} />

            <PageHeader
                title="Detail Pencairan Pinjaman"
                description={`No. Pinjaman: ${pencairan.pinjaman?.no_pinjaman}`}
                icon={Banknote}
                backHref={route("superadmin.pencairan-pinjaman")}
            >
                <div className="flex items-center gap-2">
                    <Button variant="outline" asChild>
                        <Link href={route("superadmin.pencairan-pinjaman.edit", pencairan.id)}>
                            Edit
                        </Link>
                    </Button>
                </div>
            </PageHeader>

            <div className="grid gap-6 lg:grid-cols-3 max-w-6xl">
                <div className="lg:col-span-2 space-y-5">
                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi Pinjaman</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-1">
                                    <label className="text-sm text-muted-foreground">No. Pinjaman</label>
                                    <p className="font-mono text-lg">{pencairan.pinjaman?.no_pinjaman}</p>
                                </div>
                                <div className="space-y-1">
                                    <label className="text-sm text-muted-foreground">Produk Pinjaman</label>
                                    <p>{pencairan.pinjaman?.jenisPinjaman?.nama ?? "-"}</p>
                                </div>
                                <div className="space-y-1">
                                    <label className="text-sm text-muted-foreground">Anggota</label>
                                    <p className="font-medium">{pencairan.pinjaman?.anggota?.nama ?? "-"}</p>
                                    <p className="text-sm text-muted-foreground font-mono">
                                        {pencairan.pinjaman?.anggota?.no_anggota}
                                    </p>
                                </div>
                                <div className="space-y-1">
                                    <label className="text-sm text-muted-foreground">Plafon Pinjaman</label>
                                    <p className="font-mono text-lg font-bold">{rupiah(pencairan.pinjaman?.plafon)}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <div className="flex items-center justify-between">
                                <CardTitle>Informasi Pencairan</CardTitle>
                                {getStatusBadge(pencairan.status)}
                            </div>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-1">
                                    <label className="text-sm text-muted-foreground">Tanggal Cair</label>
                                    <p className="flex items-center gap-2">
                                        <Calendar className="size-4 text-muted-foreground" />
                                        {pencairan.tanggal_cair}
                                    </p>
                                </div>
                                <div className="space-y-1">
                                    <label className="text-sm text-muted-foreground">Metode Cair</label>
                                    <Badge variant="secondary">
                                        {METODE_LABELS[pencairan.metode_cair] ?? pencairan.metode_cair}
                                    </Badge>
                                </div>
                                <div className="space-y-1">
                                    <label className="text-sm text-muted-foreground">Nominal Cair</label>
                                    <p className="font-mono text-xl font-bold">{rupiah(pencairan.nominal_cair)}</p>
                                </div>
                                <div className="space-y-1">
                                    <label className="text-sm text-muted-foreground">Nominal Bersih</label>
                                    <p className="font-mono text-xl font-bold text-emerald-600">{rupiah(nominalBersih)}</p>
                                </div>
                                {pencairan.no_rekening && (
                                    <>
                                        <div className="space-y-1">
                                            <label className="text-sm text-muted-foreground">No. Rekening</label>
                                            <p className="font-mono">{pencairan.no_rekening}</p>
                                        </div>
                                        <div className="space-y-1">
                                            <label className="text-sm text-muted-foreground">Nama Rekening</label>
                                            <p>{pencairan.nama_rekening}</p>
                                        </div>
                                        <div className="space-y-1">
                                            <label className="text-sm text-muted-foreground">Bank</label>
                                            <p>{pencairan.bank_id}</p>
                                        </div>
                                    </>
                                )}
                                <div className="space-y-1">
                                    <label className="text-sm text-muted-foreground">Biaya Admin</label>
                                    <p className="font-mono">{rupiah(pencairan.biaya_admin)}</p>
                                </div>
                                <div className="space-y-1">
                                    <label className="text-sm text-muted-foreground">Potongan Simpanan</label>
                                    <p className="font-mono">{rupiah(pencairan.potongan_simpanan)}</p>
                                </div>
                            </div>

                            {pencairan.keterangan && (
                                <div className="border-t pt-4 space-y-1">
                                    <label className="text-sm text-muted-foreground">Keterangan</label>
                                    <p className="whitespace-pre-wrap">{pencairan.keterangan}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {pencairan.status !== "pending" && (
                        <Card>
                            <CardHeader>
                                <CardTitle>Riwayat Persetujuan</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                {pencairan.approved_by && (
                                    <div className="flex items-center gap-4 p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20">
                                        <CheckCircle className="size-5 text-emerald-600 dark:text-emerald-400" />
                                        <div>
                                            <p className="font-medium">Disetujui</p>
                                            <p className="text-sm text-muted-foreground">
                                                Oleh: {pencairan.approvedBy?.name ?? "-"} • {pencairan.approved_at ?? "-"}
                                            </p>
                                        </div>
                                    </div>
                                )}
                                {pencairan.cair_oleh && (
                                    <div className="flex items-center gap-4 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                                        <Send className="size-5 text-blue-600 dark:text-blue-400" />
                                        <div>
                                            <p className="font-medium">Dicairkan</p>
                                            <p className="text-sm text-muted-foreground">
                                                Oleh: {pencairan.cairOleh?.name ?? "-"} • {pencairan.cair_at ?? "-"}
                                            </p>
                                        </div>
                                    </div>
                                )}
                                {pencairan.status === "ditolak" && (
                                    <div className="flex items-center gap-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/20">
                                        <XCircle className="size-5 text-red-600 dark:text-red-400" />
                                        <div>
                                            <p className="font-medium">Ditolak</p>
                                            <p className="text-sm text-muted-foreground">
                                                Oleh: {pencairan.approvedBy?.name ?? "-"} • {pencairan.approved_at ?? "-"}
                                            </p>
                                        </div>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    )}
                </div>

                <div className="space-y-5">
                    <Card>
                        <CardHeader>
                            <CardTitle>Ringkasan</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            <div className="space-y-1">
                                <label className="text-sm text-muted-foreground">Plafon Pinjaman</label>
                                <p className="font-mono text-right">{rupiah(pencairan.pinjaman?.plafon)}</p>
                            </div>
                            <div className="space-y-1">
                                <label className="text-sm text-muted-foreground">Nominal Cair</label>
                                <p className="font-mono text-right">{rupiah(pencairan.nominal_cair)}</p>
                            </div>
                            <div className="space-y-1 text-muted-foreground">
                                <label className="text-sm">Biaya Admin</label>
                                <p className="font-mono text-right">{rupiah(pencairan.biaya_admin)}</p>
                            </div>
                            <div className="space-y-1 text-muted-foreground">
                                <label className="text-sm">Potongan Simpanan</label>
                                <p className="font-mono text-right">{rupiah(pencairan.potongan_simpanan)}</p>
                            </div>
                            <hr />
                            <div className="space-y-1">
                                <label className="text-sm font-medium">Nominal Bersih Diterima</label>
                                <p className="font-mono text-right text-lg text-emerald-600 font-bold">{rupiah(nominalBersih)}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi Sistem</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3 text-sm text-muted-foreground">
                            <div className="flex justify-between">
                                <span>Dibuat oleh</span>
                                <span className="font-medium">{pencairan.createdBy?.name ?? "-"}</span>
                            </div>
                            <div className="flex justify-between">
                                <span>Tanggal Buat</span>
                                <span className="font-medium">{pencairan.created_at}</span>
                            </div>
                            {pencairan.approved_by && (
                                <>
                                    <div className="flex justify-between">
                                        <span>Disetujui oleh</span>
                                        <span className="font-medium">{pencairan.approvedBy?.name ?? "-"}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span>Tanggal Setuju</span>
                                        <span className="font-medium">{pencairan.approved_at}</span>
                                    </div>
                                </>
                            )}
                            {pencairan.cair_oleh && (
                                <>
                                    <div className="flex justify-between">
                                        <span>Dicairkan oleh</span>
                                        <span className="font-medium">{pencairan.cairOleh?.name ?? "-"}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span>Tanggal Cair</span>
                                        <span className="font-medium">{pencairan.cair_at}</span>
                                    </div>
                                </>
                            )}
                            <div className="flex justify-between">
                                <span>Kantor</span>
                                <span className="font-medium">{pencairan.kantor?.nama_kantor ?? "-"}</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
