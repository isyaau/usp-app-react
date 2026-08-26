import { useState } from "react";
import { Link, Head, router } from "@inertiajs/react";
import { Banknote, Plus, Search, CheckCircle, XCircle, Send } from "lucide-react";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { PageHeader } from "@/Components/PageHeader";
import { Pagination } from "@/Components/Pagination";
import { ConfirmDelete } from "@/Components/ConfirmDelete";
import { Badge } from "@/Components/ui/badge";
import { Button } from "@/Components/ui/button";
import { Card } from "@/Components/ui/card";
import { Input } from "@/Components/ui/input";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/Components/ui/select";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/Components/ui/table";
import type { Paginated, PencairanPinjamanRow } from "@/types/models";

interface Props {
    pencairan: Paginated<PencairanPinjamanRow>;
    filters: { search: string; status: string };
}

const STATUS_OPTIONS = [
    { value: "", label: "Semua Status" },
    { value: "pending", label: "Menunggu" },
    { value: "disetujui", label: "Disetujui" },
    { value: "ditolak", label: "Ditolak" },
    { value: "dicairkan", label: "Dicairkan" },
];

const METODE_LABELS: Record<string, string> = {
    transfer: "Transfer",
    tunai: "Tunai",
    cek: "Cek",
    giro: "Giro",
};

const rupiah = (v: string | number | null) =>
    `Rp ${Number(v ?? 0).toLocaleString("id-ID")}`;

export default function PencairanPinjamanIndex({ pencairan, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? "");
    const [statusFilter, setStatusFilter] = useState(filters.status ?? "");
    const [perPage, setPerPage] = useState(String(pencairan.per_page));

    const apply = (overrides: { search?: string; status?: string; per_page?: string } = {}) => {
        router.get(
            route("superadmin.pencairan-pinjaman"),
            {
                search: overrides.search ?? search,
                status: overrides.status ?? statusFilter,
                per_page: overrides.per_page ?? perPage,
            },
            { preserveState: true, preserveScroll: true }
        );
    };

    const handleApprove = (id: number) => {
        if (confirm("Setujui pencairan ini?")) {
            router.post(route("superadmin.pencairan-pinjaman.approve", id), {
                preserveScroll: true,
            });
        }
    };

    const handleReject = (id: number) => {
        const alasan = prompt("Masukkan alasan penolakan:");
        if (alasan) {
            router.post(route("superadmin.pencairan-pinjaman.reject", id), {
                data: { alasan_penolakan: alasan },
                preserveScroll: true,
            });
        }
    };

    const handleCairkan = (id: number) => {
        if (confirm("Cairkan pencairan ini?")) {
            router.post(route("superadmin.pencairan-pinjaman.cairkan", id), {
                preserveScroll: true,
            });
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

    const getActionButtons = (item: PencairanPinjamanRow) => {
        const actions = [];

        if (item.status === "pending") {
            actions.push(
                <Button
                    key="approve"
                    variant="outline"
                    size="icon"
                    className="h-8 w-8 text-emerald-600 hover:bg-emerald-50"
                    onClick={() => handleApprove(item.id)}
                    title="Setujui"
                >
                    <CheckCircle className="size-4" />
                </Button>
            );
            actions.push(
                <Button
                    key="reject"
                    variant="outline"
                    size="icon"
                    className="h-8 w-8 text-red-600 hover:bg-red-50"
                    onClick={() => handleReject(item.id)}
                    title="Tolak"
                >
                    <XCircle className="size-4" />
                </Button>
            );
        } else if (item.status === "disetujui") {
            actions.push(
                <Button
                    key="cairkan"
                    variant="outline"
                    size="icon"
                    className="h-8 w-8 text-blue-600 hover:bg-blue-50"
                    onClick={() => handleCairkan(item.id)}
                    title="Cairkan"
                >
                    <Send className="size-4" />
                </Button>
            );
            actions.push(
                <Button
                    key="reject"
                    variant="outline"
                    size="icon"
                    className="h-8 w-8 text-red-600 hover:bg-red-50"
                    onClick={() => handleReject(item.id)}
                    title="Tolak"
                >
                    <XCircle className="size-4" />
                </Button>
            );
        }

        actions.push(
            <ConfirmDelete
                key="delete"
                routeName="superadmin.pencairan-pinjaman.destroy"
                id={item.id}
                label={item.pinjaman?.no_pinjaman ?? ""}
                disabled={item.status === "dicairkan"}
            />
        );

        return actions;
    };

    return (
        <AuthenticatedLayout>
            <Head title="Pencairan Pinjaman - Superadmin" />

            <PageHeader
                title="Pencairan Pinjaman"
                description="Kelola pencairan dana pinjaman anggota."
                icon={Banknote}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route("superadmin.pencairan-pinjaman.create")} preload="hover">
                        <Plus className="size-4" />
                        Tambah Pencairan
                    </Link>
                </Button>
            </PageHeader>

            <Card className="gap-4 py-5">
                <div className="flex flex-wrap items-center gap-3 px-5">
                    <div className="relative min-w-56 flex-1">
                        <Search className="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={(e) => e.key === "Enter" && apply()}
                            placeholder="Cari no. pinjaman / anggota…"
                            className="pl-9"
                        />
                    </div>
                    <Select value={statusFilter} onValueChange={(v) => { setStatusFilter(v); apply({ status: v }); }}>
                        <SelectTrigger className="w-40">
                            <SelectValue placeholder="Status" />
                        </SelectTrigger>
                        <SelectContent>
                            {STATUS_OPTIONS.map((s) => (
                                <SelectItem key={s.value} value={s.value}>
                                    {s.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <Select
                        value={perPage}
                        onValueChange={(v) => {
                            setPerPage(v);
                            apply({ per_page: v });
                        }}
                    >
                        <SelectTrigger className="w-28">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            {["10", "25", "50", "100"].map((n) => (
                                <SelectItem key={n} value={n}>
                                    {n} / hal.
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>Tanggal Cair</TableHead>
                                <TableHead>No. Pinjaman</TableHead>
                                <TableHead>Produk</TableHead>
                                <TableHead>Anggota</TableHead>
                                <TableHead>Metode</TableHead>
                                <TableHead className="text-right">Nominal Cair</TableHead>
                                <TableHead className="text-right">Bersih</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {pencairan.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={10} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data pencairan pinjaman.
                                    </TableCell>
                                </TableRow>
                            )}
                            {pencairan.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {pencairan.from !== null ? pencairan.from + i : i + 1}
                                    </TableCell>
                                    <TableCell className="whitespace-nowrap text-muted-foreground">
                                        {item.tanggal_cair}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.pinjaman?.no_pinjaman ?? "-"}
                                        </span>
                                    </TableCell>
                                    <TableCell>{item.pinjaman?.jenisPinjaman?.nama ?? "-"}</TableCell>
                                    <TableCell>
                                        {item.pinjaman?.anggota ? (
                                            <>
                                                {item.pinjaman.anggota.nama}
                                                <span className="block font-mono text-xs text-muted-foreground">
                                                    {item.pinjaman.anggota.no_anggota}
                                                </span>
                                            </>
                                        ) : (
                                            "-"
                                        )}
                                    </TableCell>
                                    <TableCell className="whitespace-nowrap">
                                        <Badge variant="secondary" className="text-xs">
                                            {METODE_LABELS[item.metode_cair] ?? item.metode_cair}
                                        </Badge>
                                    </TableCell>
                                    <TableCell className="font-mono text-right">{rupiah(item.nominal_cair)}</TableCell>
                                    <TableCell className="font-mono text-right text-emerald-600">
                                        {rupiah(Number(item.nominal_cair) - Number(item.biaya_admin ?? 0) - Number(item.potongan_simpanan ?? 0))}
                                    </TableCell>
                                    <TableCell>{getStatusBadge(item.status)}</TableCell>
                                    <TableCell>
                                        <div className="flex items-center justify-end gap-1">
                                            {getActionButtons(item)}
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>

                <div className="border-t px-5 pt-4">
                    <Pagination
                        links={pencairan.links}
                        currentPage={pencairan.current_page}
                        lastPage={pencairan.last_page}
                        from={pencairan.from}
                        to={pencairan.to}
                        total={pencairan.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
