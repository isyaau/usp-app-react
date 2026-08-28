import { useState } from 'react';
import { Link, Head, router } from '@inertiajs/react';
import { Eye, Pencil, Trash2, Wallet } from 'lucide-react';

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
import type { Paginated, PenghapusanPinjamanRow } from '@/types/models';

interface Props {
    transaksi: Paginated<PenghapusanPinjamanRow>;
    filters: { search: string; status: string; mulai: string; sampai: string };
}

const STATUS_BADGE: Record<PenghapusanPinjamanRow['status'], string> = {
    draft: 'bg-amber-500/90 hover:bg-amber-500',
    posted: 'bg-emerald-600 hover:bg-emerald-600',
    batal: 'bg-rose-600 hover:bg-rose-600',
};

const rupiah = (v: string | number | null | undefined) =>
    `Rp ${Number(v ?? 0).toLocaleString('id-ID')}`;

export default function Index({ transaksi, filters }: Props) {
    const root = 'superadmin.pinjaman.penghapusan';
    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState(filters.status || 'all');
    const [mulai, setMulai] = useState(filters.mulai ?? '');
    const [sampai, setSampai] = useState(filters.sampai ?? '');

    const apply = () => {
        router.get(route(root), {
            search,
            status: status === 'all' ? '' : status,
            mulai,
            sampai,
        }, { preserveState: true });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Penghapusan Pinjaman" />

            <PageHeader
                title="Penghapusan Pinjaman"
                description="Catat penghapusan (hapus buku) pinjaman beserta sisa pokok yang dihapus."
                icon={Wallet}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route(`${root}.create`)}>Tambah Penghapusan</Link>
                </Button>
            </PageHeader>

            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2 text-base">
                        <Wallet className="size-4 text-brand-600" />
                        Filter Data
                    </CardTitle>
                    <CardDescription>
                        Cari berdasarkan no transaksi / no pinjaman / nama anggota, atau filter
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
                            placeholder="No transaksi / pinjaman / anggota…"
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
                        <Button id="btn-filter" onClick={apply} className="w-full bg-brand-600 hover:bg-brand-500">
                            Terapkan Filter
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <Card className="mt-5">
                <CardHeader>
                    <CardTitle className="text-base">Daftar Penghapusan Pinjaman</CardTitle>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Transaksi</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>Anggota</TableHead>
                                <TableHead>No Pinjaman</TableHead>
                                <TableHead className="text-right">Sisa Pokok</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {transaksi.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={8} className="py-10 text-center text-muted-foreground">
                                        Tidak ada data penghapusan pinjaman.
                                    </TableCell>
                                </TableRow>
                            )}
                            {transaksi.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell>
                                        {(transaksi.current_page - 1) * transaksi.per_page + i + 1}
                                    </TableCell>
                                    <TableCell className="font-mono text-xs">{item.no_transaksi}</TableCell>
                                    <TableCell>{item.tgl_transaksi}</TableCell>
                                    <TableCell className="font-medium">
                                        {item.pinjaman?.anggota?.nama ?? '-'}
                                        <span className="block font-mono text-xs text-muted-foreground">
                                            {item.pinjaman?.anggota?.no_anggota ?? '-'}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.pinjaman?.no_pinjaman ?? '-'}
                                        </span>
                                    </TableCell>
                                    <TableCell className="text-right font-semibold tabular-nums">
                                        {rupiah(item.sisa_pokok)}
                                    </TableCell>
                                    <TableCell>
                                        <Badge className={STATUS_BADGE[item.status] ?? ''}>
                                            {item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                                        </Badge>
                                    </TableCell>
                                    <TableCell className="space-x-1 whitespace-nowrap text-right">
                                        <Button variant="ghost" size="icon" asChild title="Detail">
                                            <Link href={route(`${root}.show`, item.id)}>
                                                <Eye className="size-4" />
                                            </Link>
                                        </Button>
                                        <Button variant="ghost" size="icon" asChild title="Edit">
                                            <Link href={route(`${root}.edit`, item.id)}>
                                                <Pencil className="size-4" />
                                            </Link>
                                        </Button>
                                        <ConfirmDelete
                                            routeName={`${root}.destroy`}
                                            id={item.id}
                                            label={item.no_transaksi}
                                            description={`Penghapusan "${item.no_transaksi}" akan dihapus permanen.`}
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