import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { Eye, Pencil, Wallet } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { ConfirmDelete } from '@/Components/ConfirmDelete';
import { Pagination } from '@/Components/Pagination';
import type { Paginated, TransaksiSimpananRow } from '@/types/models';

interface Props {
    transaksi: Paginated<TransaksiSimpananRow>;
    filters: {
        search: string;
        status: string;
        mulai: string;
        sampai: string;
    };
}

const STATUS_BADGE: Record<TransaksiSimpananRow['status'], string> = {
    draft: 'bg-amber-500/90 hover:bg-amber-500',
    posted: 'bg-emerald-600 hover:bg-emerald-600',
    batal: 'bg-rose-600 hover:bg-rose-600',
};

/** Konfigurasi modul transaksi simpanan (dipakai Index + Form). */
export interface ModulConfig {
    /** Label menu, mis. "Setoran Simpanan". */
    label: string;
    /** Nama route index, mis. 'superadmin.transaksi-simpanan.setoran-simpanan'. */
    routeIndex: string;
    /** Nama route create. */
    routeCreate: string;
    /** Ikon judul halaman. */
    icon: LucideIcon;
    /** Suffix deskripsi di header. */
    description: string;
}

export function StatusBadge({ status }: { status: TransaksiSimpananRow['status'] }) {
    return (
        <Badge className={STATUS_BADGE[status] ?? ''}>
            {status.charAt(0).toUpperCase() + status.slice(1)}
        </Badge>
    );
}

function formatRupiah(value: string | number) {
    return `Rp ${Number(value ?? 0).toLocaleString('id-ID')}`;
}

/**
 * Halaman daftar transaksi simpanan.
 * Dipakai bersama oleh modul Setoran/Tarikan/Penutupan lewat config.
 */
function TransaksiIndex({ transaksi, filters, config }: Props & { config: ModulConfig }) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState(filters.status || 'all');
    const [mulai, setMulai] = useState(filters.mulai ?? '');
    const [sampai, setSampai] = useState(filters.sampai ?? '');

    const apply = () => {
        router.get(
            route(config.routeIndex),
            {
                search,
                status: status === 'all' ? '' : status,
                mulai,
                sampai,
            },
            { preserveState: true },
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title={config.label} />

            <PageHeader
                title={config.label}
                description={config.description}
                icon={config.icon}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <a href={route(config.routeCreate)}>Tambah Transaksi</a>
                </Button>
            </PageHeader>

            {/* ============================ Filter ============================ */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2 text-base">
                        <Wallet className="size-4 text-brand-600" />
                        Filter Data
                    </CardTitle>
                    <CardDescription>
                        Cari berdasarkan no transaksi / nama anggota, atau filter
                        status &amp; rentang tanggal.
                    </CardDescription>
                </CardHeader>
                <CardContent className="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                    <div className="space-y-2">
                        <Label htmlFor="filter-search">Pencarian</Label>
                        <Input
                            id="filter-search"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && apply()}
                            placeholder="No transaksi / anggota…"
                        />
                    </div>
                    <div className="space-y-2">
                        <Label>Status</Label>
                        <Select value={status} onValueChange={setStatus}>
                            <SelectTrigger className="w-full">
                                <SelectValue placeholder="Semua status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Semua</SelectItem>
                                <SelectItem value="draft">Draft</SelectItem>
                                <SelectItem value="posted">Posted</SelectItem>
                                <SelectItem value="batal">Batal</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div className="space-y-2">
                        <Label htmlFor="filter-mulai">Dari Tanggal</Label>
                        <Input
                            id="filter-mulai"
                            type="date"
                            value={mulai}
                            onChange={(e) => setMulai(e.target.value)}
                        />
                    </div>
                    <div className="space-y-2">
                        <Label htmlFor="filter-sampai">Sampai Tanggal</Label>
                        <Input
                            id="filter-sampai"
                            type="date"
                            value={sampai}
                            onChange={(e) => setSampai(e.target.value)}
                        />
                    </div>
                    <div className="flex items-end">
                        <Button
                            id="btn-filter"
                            onClick={apply}
                            className="w-full bg-brand-600 hover:bg-brand-500"
                        >
                            Terapkan Filter
                        </Button>
                    </div>
                </CardContent>
            </Card>

            {/* ============================ Tabel ============================= */}
            <Card className="mt-5">
                <CardHeader className="flex-row flex-wrap items-center justify-between gap-3">
                    <CardTitle className="text-base">
                        Daftar {config.label}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Transaksi</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>Anggota</TableHead>
                                <TableHead>No Rekening</TableHead>
                                <TableHead>Kode</TableHead>
                                <TableHead className="text-right">Nominal</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {transaksi.data.length === 0 && (
                                <TableRow>
                                    <TableCell
                                        colSpan={9}
                                        className="py-10 text-center text-muted-foreground"
                                    >
                                        Tidak ada data transaksi.
                                    </TableCell>
                                </TableRow>
                            )}
                            {transaksi.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell>
                                        {(transaksi.current_page - 1) *
                                            transaksi.per_page +
                                            i +
                                            1}
                                    </TableCell>
                                    <TableCell className="font-mono text-xs">
                                        {item.no_transaksi}
                                    </TableCell>
                                    <TableCell>{item.tgl_transaksi}</TableCell>
                                    <TableCell className="font-medium">
                                        {item.anggota?.nama ?? '-'}
                                    </TableCell>
                                    <TableCell className="font-mono text-xs">
                                        {item.simpanan?.no_rekening ?? '-'}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.kodeTransaksi?.kode ?? '-'}
                                        </span>
                                    </TableCell>
                                    <TableCell className="text-right font-semibold tabular-nums">
                                        {formatRupiah(item.nominal)}
                                    </TableCell>
                                    <TableCell>
                                        <StatusBadge status={item.status} />
                                    </TableCell>
                                    <TableCell className="space-x-1 text-right whitespace-nowrap">
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            asChild
                                            title="Detail"
                                        >
                                            <a
                                                href={route(
                                                    `${config.routeIndex}.show`,
                                                    item.id,
                                                )}
                                            >
                                                <Eye className="size-4" />
                                            </a>
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            asChild
                                            title="Edit"
                                        >
                                            <a
                                                href={route(
                                                    `${config.routeIndex}.edit`,
                                                    item.id,
                                                )}
                                            >
                                                <Pencil className="size-4" />
                                            </a>
                                        </Button>
                                        <ConfirmDelete
                                            routeName={`${config.routeIndex}.destroy`}
                                            id={item.id}
                                            label={item.no_transaksi}
                                            description={`Transaksi "${item.no_transaksi}" akan dihapus permanen.`}
                                        />
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>

                    <Pagination
                        links={transaksi.links}
                        currentPage={transaksi.current_page}
                        lastPage={transaksi.last_page}
                        from={transaksi.from}
                        to={transaksi.to}
                        total={transaksi.total}
                    />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}

export default TransaksiIndex;
