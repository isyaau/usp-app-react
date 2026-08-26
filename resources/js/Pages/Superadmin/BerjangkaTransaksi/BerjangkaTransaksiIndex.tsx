import { useState } from 'react';
import { Link, Head, router } from '@inertiajs/react';
import { type LucideIcon, Wallet } from 'lucide-react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { ConfirmDelete } from '@/Components/ConfirmDelete';
import { Pagination } from '@/Components/Pagination';

export interface BerjangkaModulConfig {
    label: string;
    routeIndex: string;
    routeCreate: string;
    icon: LucideIcon;
    description: string;
}

interface TransaksiRow {
    id: number;
    no_transaksi: string;
    tgl_transaksi: string;
    anggota?: { id: number; nama: string; no_anggota?: string };
    deposito?: { id: number; no_deposito?: string };
    nominal?: number;
    nominal_pokok?: number;
    nominal_diterima?: number;
    total_penalti?: number;
    status: string;
}

interface Paginated<T> {
    data: T[];
    links: any[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    per_page: number;
}

interface Props {
    transaksi: Paginated<TransaksiRow>;
    filters: { search?: string; status?: string; mulai?: string; sampai?: string };
    variantTitle: string;
    config: BerjangkaModulConfig;
}

const STATUS: Record<string, string> = {
    draft: 'bg-amber-500/90 hover:bg-amber-500',
    posted: 'bg-emerald-600 hover:bg-emerald-600',
    batal: 'bg-rose-600 hover:bg-rose-600',
};

function Rp(v: number | string | undefined) {
    return 'Rp ' + Number(v ?? 0).toLocaleString('id-ID');
}

export default function BerjangkaTransaksiIndex({ transaksi, filters, variantTitle, config }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState(filters.status || 'all');
    const [mulai, setMulai] = useState(filters.mulai ?? '');
    const [sampai, setSampai] = useState(filters.sampai ?? '');

    const apply = () => {
        router.get(route(config.routeIndex), {
            search,
            status: status === 'all' ? '' : status,
            mulai,
            sampai,
        }, { preserveState: true });
    };

    const getNominal = (row: TransaksiRow) => {
        if (row.nominal !== undefined) return Rp(row.nominal);
        if (row.nominal_pokok !== undefined) return Rp(row.nominal_pokok);
        if ((row as any).nominal_penarikan !== undefined) return Rp((row as any).nominal_penarikan);
        if (row.total_penalti !== undefined) return Rp(row.total_penalti);
        return '-';
    };

    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || config.label} />

            <PageHeader title={variantTitle || config.label} description={config.description} icon={config.icon}>
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route(config.routeCreate)}>Tambah Transaksi</Link>
                </Button>
            </PageHeader>

            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2 text-base">
                        <Wallet className="size-4 text-brand-600" />
                        Filter Data
                    </CardTitle>
                    <CardDescription>Cari berdasarkan no transaksi / nama anggota, atau filter status & rentang tanggal.</CardDescription>
                </CardHeader>
                <CardContent className="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                    <div className="space-y-2">
                        <Label>Pencarian</Label>
                        <Input value={search} onChange={e => setSearch(e.target.value)} onKeyDown={e => e.key === 'Enter' && apply()} placeholder="No transaksi / anggota..." />
                    </div>
                    <div className="space-y-2">
                        <Label>Status</Label>
                        <Select value={status} onValueChange={setStatus}>
                            <SelectTrigger><SelectValue placeholder="Semua status" /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Semua</SelectItem>
                                <SelectItem value="draft">Draft</SelectItem>
                                <SelectItem value="posted">Posted</SelectItem>
                                <SelectItem value="batal">Batal</SelectItem>
                            </SelectContent>
                        </Select>
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
                        <Button onClick={apply} className="w-full bg-brand-600 hover:bg-brand-500">Terapkan Filter</Button>
                    </div>
                </CardContent>
            </Card>

            <Card className="mt-5">
                <CardHeader className="flex-row flex-wrap items-center justify-between gap-3">
                    <CardTitle className="text-base">Daftar {config.label}</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Transaksi</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>Anggota</TableHead>
                                <TableHead className="text-right">Nominal</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {transaksi.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={7} className="py-10 text-center text-muted-foreground">Tidak ada data transaksi.</TableCell>
                                </TableRow>
                            )}
                            {transaksi.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell>{(transaksi.current_page - 1) * transaksi.per_page + i + 1}</TableCell>
                                    <TableCell className="font-mono text-xs">{item.no_transaksi}</TableCell>
                                    <TableCell>{new Date(item.tgl_transaksi).toLocaleDateString('id-ID')}</TableCell>
                                    <TableCell className="font-medium">{item.anggota?.nama ?? '-'}</TableCell>
                                    <TableCell className="text-right font-semibold tabular-nums">{getNominal(item)}</TableCell>
                                    <TableCell><Badge className={STATUS[item.status] ?? ''}>{item.status}</Badge></TableCell>
                                    <TableCell className="space-x-1 text-right whitespace-nowrap">
                                        <Button variant="ghost" size="icon" asChild title="Detail">
                                            <Link href={route(config.routeIndex + '.show', item.id)}>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="size-4"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                            </Link>
                                        </Button>
                                        <Button variant="ghost" size="icon" asChild title="Edit">
                                            <Link href={route(config.routeIndex + '.edit', item.id)}>
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="size-4"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                            </Link>
                                        </Button>
                                        <ConfirmDelete routeName={config.routeIndex + '.destroy'} id={item.id} label={item.no_transaksi} />
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>

                    <div className="mt-4">
                        <Pagination links={transaksi.links} currentPage={transaksi.current_page} lastPage={transaksi.last_page} from={transaksi.from} to={transaksi.to} total={transaksi.total} />
                    </div>
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
