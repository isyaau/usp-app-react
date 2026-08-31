import { useState } from 'react';
import { Link, Head, router } from '@inertiajs/react';
import { CalendarClock, Eye, Pencil, Plus, Printer, Search } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { ConfirmDelete } from '@/Components/ConfirmDelete';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import type { Paginated, JadwalUlangRow } from '@/types/models';

interface Props {
    jadwal: Paginated<JadwalUlangRow>;
    filters: { search: string; status: string };
}

const rupiah = (v: string | number | null) =>
    `Rp ${Number(v ?? 0).toLocaleString('id-ID')}`;

const statusBadge: Record<string, string> = {
    draft: 'border-amber-500/40 bg-amber-500/10 text-amber-600 dark:text-amber-400',
    posted: 'border-emerald-500/40 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
    batal: 'border-muted-foreground/30 bg-muted text-muted-foreground',
};

export default function Index({ jadwal, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState(filters.status ?? '');
    const [perPage, setPerPage] = useState(String(jadwal.per_page));

    const apply = (overrides: { search?: string; status?: string; per_page?: string } = {}) => {
        router.get(
            route('superadmin.pinjaman.jadwal-ulang'),
            {
                search: overrides.search ?? search,
                status: overrides.status ?? status,
                per_page: overrides.per_page ?? perPage,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Jadwal Ulang Pinjaman" />

            <PageHeader
                title="Jadwal Ulang Pinjaman"
                description="Kelola perhitungan ulang jadwal angsuran pinjaman."
                icon={CalendarClock}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route('superadmin.pinjaman.jadwal-ulang.create')} preload="hover">
                        <Plus />
                        Tambah Jadwal Ulang
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
                            onKeyDown={(e) => e.key === 'Enter' && apply()}
                            placeholder="Cari no. transaksi / anggota…"
                            className="pl-9"
                        />
                    </div>
                    <Select value={status} onValueChange={(v) => { setStatus(v); apply({ status: v }); }}>
                        <SelectTrigger className="w-32"><SelectValue placeholder="Status" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Semua</SelectItem>
                            <SelectItem value="draft">Draft</SelectItem>
                            <SelectItem value="posted">Posted</SelectItem>
                            <SelectItem value="batal">Batal</SelectItem>
                        </SelectContent>
                    </Select>
                    <Select value={perPage} onValueChange={(v) => { setPerPage(v); apply({ per_page: v }); }}>
                        <SelectTrigger className="w-28"><SelectValue /></SelectTrigger>
                        <SelectContent>
                            {['10', '25', '50', '100'].map((n) => (
                                <SelectItem key={n} value={n}>{n} / hal.</SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>No. Pinjaman Lama</TableHead>
                                <TableHead>Anggota</TableHead>
                                <TableHead>Plafon</TableHead>
                                <TableHead>Bunga</TableHead>
                                <TableHead>Jangka</TableHead>
                                <TableHead>Angsuran</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                                    {jadwal.data.length === 0 && (
                                        <TableRow><TableCell colSpan={10} className="h-32 text-center text-muted-foreground">
                                            Tidak ada data jadwal ulang.
                                        </TableCell></TableRow>
                                    )}
                            {jadwal.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {jadwal.from !== null ? jadwal.from + i : i + 1}
                                    </TableCell>
                                    <TableCell className="whitespace-nowrap text-muted-foreground">{item.tgl_transaksi}</TableCell>
                                    <TableCell>
                                        <span className="font-mono text-xs text-muted-foreground">{item.no_pinjaman_lama || '—'}</span>
                                    </TableCell>
                                    <TableCell>
                                        {item.pinjaman?.anggota ? (
                                            <>
                                                {item.pinjaman.anggota.nama}
                                                <span className="block font-mono text-xs text-muted-foreground">{item.pinjaman.anggota.no_anggota}</span>
                                            </>
                                        ) : '—'}
                                    </TableCell>
                                    <TableCell className="font-mono">{rupiah(item.plafon)}</TableCell>
                                    <TableCell>{item.bunga}%</TableCell>
                                    <TableCell className="whitespace-nowrap text-muted-foreground">{item.jangka_waktu} {item.satuan}</TableCell>
                                    <TableCell className="font-mono">{rupiah(item.nominal_angsuran)}</TableCell>
                                    <TableCell>
                                        <Badge variant="outline" className={statusBadge[item.status] ?? ''}>
                                            {item.status}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button variant="ghost" size="icon" asChild title="Detail">
                                                <Link href={route('superadmin.pinjaman.jadwal-ulang.show', item.id)}>
                                                    <Eye className="text-muted-foreground" />
                                                </Link>
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                title="Cetak"
                                                onClick={() =>
                                                    window.open(
                                                        route('superadmin.pinjaman.jadwal-ulang.show', item.id),
                                                        '_blank',
                                                    )
                                                }
                                            >
                                                <Printer className="text-muted-foreground" />
                                            </Button>
                                            <Button variant="ghost" size="icon" asChild>
                                                <Link href={route('superadmin.pinjaman.jadwal-ulang.edit', item.id)}>
                                                    <Pencil className="text-muted-foreground" />
                                                </Link>
                                            </Button>
                                            <ConfirmDelete
                                                routeName="superadmin.pinjaman.jadwal-ulang.destroy"
                                                id={item.id}
                                                label={item.no_transaksi}
                                            />
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>

                <div className="border-t px-5 pt-4">
                    <Pagination
                        links={jadwal.links}
                        currentPage={jadwal.current_page}
                        lastPage={jadwal.last_page}
                        from={jadwal.from}
                        to={jadwal.to}
                        total={jadwal.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
