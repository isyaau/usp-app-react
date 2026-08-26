import { useState } from 'react';
import { Link, Head, router } from '@inertiajs/react';
import { Eye, Pencil, Users } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { ConfirmDelete } from '@/Components/ConfirmDelete';
import { Pagination } from '@/Components/Pagination';

interface KolektifRow {
    id: number;
    no_transaksi: string;
    tgl_transaksi: string;
    jenis: string;
    metode_pembayaran: string;
    nominal_total: number | string;
    jumlah_anggota: number;
    status: string;
    keterangan: string | null;
    kelompok?: { id: number; nama: string } | null;
    user?: { id: number; nama: string } | null;
    kantor?: { id: number; nama_kantor: string } | null;
}

interface PaginatedLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginatedLink[];
    path: string;
}

export interface KolektifModulConfig {
    label: string;
    routeIndex: string;
    routeCreate: string;
    icon: LucideIcon;
    description: string;
}

const STATUS_BADGE: Record<string, string> = {
    draft: 'bg-amber-500/90',
    posted: 'bg-emerald-600',
    batal: 'bg-rose-600',
};

function formatRp(v: number | string) {
    return `Rp ${Number(v ?? 0).toLocaleString('id-ID')}`;
}

export default function KolektifIndex({
    transaksi, filters, config,
}: {
    transaksi: Paginated<KolektifRow>;
    filters: { search?: string; status?: string; mulai?: string; sampai?: string };
    config: KolektifModulConfig;
}) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState(filters.status || 'all');
    const [mulai, setMulai] = useState(filters.mulai ?? '');
    const [sampai, setSampai] = useState(filters.sampai ?? '');

    const apply = () => {
        router.get(route(config.routeIndex), {
            search, status: status === 'all' ? '' : status, mulai, sampai,
        }, { preserveState: true });
    };

    return (
        <AuthenticatedLayout>
            <Head title={config.label} />
            <PageHeader title={config.label} description={config.description} icon={config.icon}>
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route(config.routeCreate)}>Tambah Transaksi</Link>
                </Button>
            </PageHeader>

            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2 text-base">
                        <Users className="size-4 text-brand-600" /> Filter Data
                    </CardTitle>
                    <CardDescription>Cari berdasarkan no transaksi / nama kelompok, atau filter status &amp; rentang tanggal.</CardDescription>
                </CardHeader>
                <CardContent className="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                    <div className="space-y-2">
                        <Label>Pencarian</Label>
                        <Input placeholder="No transaksi / nama kelompok..." value={search}
                            onChange={e => setSearch(e.target.value)}
                            onKeyDown={e => e.key === 'Enter' && apply()} />
                    </div>
                    <div className="space-y-2">
                        <Label>Status</Label>
                        <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            value={status} onChange={e => setStatus(e.target.value)}>
                            <option value="all">Semua</option>
                            <option value="draft">Draft</option>
                            <option value="posted">Posted</option>
                            <option value="batal">Batal</option>
                        </select>
                    </div>
                    <div className="space-y-2">
                        <Label>Dari Tanggal</Label>
                        <Input type="date" value={mulai} onChange={e => setMulai(e.target.value)} />
                    </div>
                    <div className="space-y-2">
                        <Label>Sampai Tanggal</Label>
                        <Input type="date" value={sampai} onChange={e => setSampai(e.target.value)} />
                    </div>
                    <div className="flex items-end">
                        <Button onClick={apply} className="w-full">Terapkan</Button>
                    </div>
                </CardContent>
            </Card>

            <Card className="mt-4">
                <CardContent className="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">No</TableHead>
                                <TableHead>No. Transaksi</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>Kelompok</TableHead>
                                <TableHead className="text-right">Total</TableHead>
                                <TableHead className="text-center">Anggota</TableHead>
                                <TableHead className="text-center">Status</TableHead>
                                <TableHead className="w-24">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {transaksi.data.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={8} className="h-24 text-center text-muted-foreground">Tidak ada data.</TableCell>
                                </TableRow>
                            ) : transaksi.data.map((row, i) => (
                                <TableRow key={row.id}>
                                    <TableCell>{(transaksi.current_page - 1) * transaksi.per_page + i + 1}</TableCell>
                                    <TableCell className="font-mono text-xs">{row.no_transaksi}</TableCell>
                                    <TableCell className="text-xs">{new Date(row.tgl_transaksi).toLocaleDateString('id-ID')}</TableCell>
                                    <TableCell className="text-xs">{row.kelompok?.nama ?? '-'}</TableCell>
                                    <TableCell className="text-right font-mono text-xs font-bold">{formatRp(row.nominal_total)}</TableCell>
                                    <TableCell className="text-center text-xs">{row.jumlah_anggota}</TableCell>
                                    <TableCell className="text-center">
                                        <Badge className={STATUS_BADGE[row.status] ?? ''}>{row.status}</Badge>
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex gap-1">
                                            <Button variant="ghost" size="sm" asChild>
                                                <Link href={route(config.routeIndex + '.show', row.id)}><Eye className="size-4" /></Link>
                                            </Button>
                                            <Button variant="ghost" size="sm" asChild>
                                                <Link href={route(config.routeIndex + '.edit', row.id)}><Pencil className="size-4" /></Link>
                                            </Button>
                                            <ConfirmDelete
                                                routeName={config.routeIndex + '.destroy'}
                                                id={row.id}
                                                label={row.no_transaksi}
                                            />
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>

            <div className="mt-4">
                <Pagination
                    links={transaksi.links}
                    currentPage={transaksi.current_page}
                    lastPage={transaksi.last_page}
                    from={transaksi.from}
                    to={transaksi.to}
                    total={transaksi.total}
                />
            </div>
        </AuthenticatedLayout>
    );
}