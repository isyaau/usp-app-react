import { useState } from 'react';
import { Head, router, Link } from '@inertiajs/react';
import { HandCoins, Pencil, Search } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import type { Paginated, TagihanPinjamanRow } from '@/types/models';

interface Props {
    tagihan: Paginated<TagihanPinjamanRow>;
    filters: { search: string; status: string };
    rekap: {
        jumlah_pinjaman: number;
        total_plafon: number;
        total_pokok_terbayar: number;
        total_sisa_pokok: number;
    };
}

const rupiah = (v: string | number | null | undefined) =>
    `Rp ${Number(v ?? 0).toLocaleString('id-ID')}`;

export default function Index({ tagihan, filters, rekap }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [status, setStatus] = useState(filters.status ?? '');
    const [perPage, setPerPage] = useState(String(tagihan.per_page));

    const apply = (overrides: { search?: string; status?: string; per_page?: string } = {}) => {
        router.get(
            route('superadmin.pinjaman.tagihan'),
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
            <Head title="Tagihan Pinjaman" />

            <PageHeader
                title="Tagihan Pinjaman"
                description="Daftar tagihan pinjaman aktif: angsuran, sisa pokok, dan jatuh tempo."
                icon={HandCoins}
            />

            <div className="mb-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <Card>
                    <CardContent className="py-4">
                        <p className="text-xs text-muted-foreground">Jumlah Pinjaman Aktif</p>
                        <p className="text-2xl font-bold">{rekap.jumlah_pinjaman}</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent className="py-4">
                        <p className="text-xs text-muted-foreground">Total Plafon</p>
                        <p className="text-2xl font-bold">{rupiah(rekap.total_plafon)}</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent className="py-4">
                        <p className="text-xs text-muted-foreground">Pokok Terbayar</p>
                        <p className="text-2xl font-bold">{rupiah(rekap.total_pokok_terbayar)}</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent className="py-4">
                        <p className="text-xs text-muted-foreground">Sisa Pokok</p>
                        <p className="text-2xl font-bold">{rupiah(rekap.total_sisa_pokok)}</p>
                    </CardContent>
                </Card>
            </div>

            <Card className="gap-4 py-5">
                <div className="flex flex-wrap items-center gap-3 px-5">
                    <div className="relative min-w-56 flex-1">
                        <Search className="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && apply()}
                            placeholder="Cari no. pinjaman / anggota…"
                            className="pl-9"
                        />
                    </div>
                    <Select value={status} onValueChange={(v) => { setStatus(v); apply({ status: v }); }}>
                        <SelectTrigger className="w-32"><SelectValue placeholder="Status" /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="">Semua</SelectItem>
                            <SelectItem value="belum">Belum Lunas</SelectItem>
                            <SelectItem value="lunas">Lunas</SelectItem>
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
                                <TableHead>No. Pinjaman</TableHead>
                                <TableHead>Produk</TableHead>
                                <TableHead>Anggota</TableHead>
                                <TableHead className="text-right">Plafon</TableHead>
                                <TableHead className="text-right">Pokok Terbayar</TableHead>
                                <TableHead className="text-right">Sisa Pokok</TableHead>
                                <TableHead className="text-right">Angsuran / Bulan</TableHead>
                                <TableHead>Jatuh Tempo</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {tagihan.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={12} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data tagihan pinjaman.
                                    </TableCell>
                                </TableRow>
                            )}
                            {tagihan.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {tagihan.from !== null ? tagihan.from + i : i + 1}
                                    </TableCell>
                                    <TableCell className="whitespace-nowrap text-muted-foreground">{item.tanggal}</TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">{item.no_pinjaman}</span>
                                    </TableCell>
                                    <TableCell>{item.jenisPinjaman?.nama ?? '—'}</TableCell>
                                    <TableCell>
                                        {item.anggota ? (
                                            <>
                                                {item.anggota.nama}
                                                <span className="block font-mono text-xs text-muted-foreground">{item.anggota.no_anggota}</span>
                                            </>
                                        ) : '—'}
                                    </TableCell>
                                    <TableCell className="text-right font-mono">{rupiah(item.plafon)}</TableCell>
                                    <TableCell className="text-right font-mono">{rupiah(item.pokok_terbayar)}</TableCell>
                                    <TableCell className="text-right font-mono font-semibold">{rupiah(item.sisa_pokok)}</TableCell>
                                    <TableCell className="text-right font-mono">{rupiah(item.nominal_angsuran)}</TableCell>
                                    <TableCell className="whitespace-nowrap text-muted-foreground">{item.jatuh_tempo || '—'}</TableCell>
                                    <TableCell>
                                        <Badge
                                            variant="outline"
                                            className={
                                                item.lunas
                                                    ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                                                    : 'border-amber-500/40 bg-amber-500/10 text-amber-600 dark:text-amber-400'
                                            }
                                        >
                                            {item.lunas ? 'Lunas' : 'Belum Lunas'}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button variant="ghost" size="icon" asChild>
                                                <Link href={route('superadmin.pinjaman.pinjaman.edit', item.id)}>
                                                    <Pencil className="text-muted-foreground" />
                                                </Link>
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>

                <div className="border-t px-5 pt-4">
                    <Pagination
                        links={tagihan.links}
                        currentPage={tagihan.current_page}
                        lastPage={tagihan.last_page}
                        from={tagihan.from}
                        to={tagihan.to}
                        total={tagihan.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}