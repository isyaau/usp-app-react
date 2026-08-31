import { useState } from 'react';
import { Head, router, Link } from '@inertiajs/react';
import { Eye, FileSpreadsheet, HandCoins, MoreHorizontal, Pencil, Printer, Search } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { Button } from '@/Components/ui/button';
import { Card, CardContent } from '@/Components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import { Input } from '@/Components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import type { Paginated, TagihanPinjamanRow } from '@/types/models';

interface Props {
    tagihan: Paginated<TagihanPinjamanRow>;
    filters: { search: string; status: string; mulai?: string; sampai?: string };
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
    const [mulai, setMulai] = useState(filters.mulai ?? '');
    const [sampai, setSampai] = useState(filters.sampai ?? '');
    const [perPage, setPerPage] = useState(String(tagihan.per_page));

    const apply = (overrides: { search?: string; status?: string; mulai?: string; sampai?: string; per_page?: string } = {}) => {
        router.get(
            route('superadmin.pinjaman.tagihan'),
            {
                search: overrides.search ?? search,
                status: overrides.status ?? status,
                mulai: overrides.mulai ?? mulai,
                sampai: overrides.sampai ?? sampai,
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
                    <div className="flex items-center gap-2">
                        <span className="text-xs text-muted-foreground">Jatuh Tempo</span>
                        <Input
                            type="date"
                            value={mulai}
                            onChange={(e) => setMulai(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && apply()}
                            className="w-40"
                        />
                        <span className="text-xs text-muted-foreground">s/d</span>
                        <Input
                            type="date"
                            value={sampai}
                            onChange={(e) => setSampai(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && apply()}
                            className="w-40"
                        />
                        <Button variant="outline" size="sm" onClick={() => apply()}>Terapkan</Button>
                    </div>
                    <Select value={perPage} onValueChange={(v) => { setPerPage(v); apply({ per_page: v }); }}>
                        <SelectTrigger className="w-28"><SelectValue /></SelectTrigger>
                        <SelectContent>
                            {['10', '25', '50', '100'].map((n) => (
                                <SelectItem key={n} value={n}>{n} / hal.</SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                    <div className="flex items-center gap-2">
                        <Button
                            variant="outline"
                            onClick={() =>
                                window.open(
                                    route('superadmin.pinjaman.tagihan.cetak', { search, status, mulai, sampai }),
                                    '_blank',
                                )
                            }
                        >
                            <Printer className="size-4" />
                            Cetak
                        </Button>
                        <Button
                            variant="outline"
                            onClick={() =>
                                window.open(
                                    route('superadmin.pinjaman.tagihan.excel', { search, status, mulai, sampai }),
                                    '_blank',
                                )
                            }
                        >
                            <FileSpreadsheet className="size-4" />
                            Excel
                        </Button>
                    </div>
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No. Pinjaman</TableHead>
                                <TableHead>Tgl Bayar</TableHead>
                                <TableHead>Anggota</TableHead>
                                <TableHead className="text-right">Plafon</TableHead>
                                <TableHead className="text-right">Jangka Waktu</TableHead>
                                <TableHead>Satuan</TableHead>
                                <TableHead className="text-right">Angsuran</TableHead>
                                <TableHead className="text-right">Sisa</TableHead>
                                <TableHead className="text-right">Tunggakan</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {tagihan.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={11} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data tagihan pinjaman.
                                    </TableCell>
                                </TableRow>
                            )}
                            {tagihan.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {tagihan.from !== null ? tagihan.from + i : i + 1}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">{item.no_pinjaman}</span>
                                    </TableCell>
                                    <TableCell className="whitespace-nowrap text-muted-foreground">{item.tgl_bayar || '—'}</TableCell>
                                    <TableCell>
                                        {item.anggota ? (
                                            <>
                                                {item.anggota.nama}
                                                <span className="block font-mono text-xs text-muted-foreground">{item.anggota.no_anggota}</span>
                                            </>
                                        ) : '—'}
                                    </TableCell>
                                    <TableCell className="text-right font-mono">{rupiah(item.plafon)}</TableCell>
                                    <TableCell className="whitespace-nowrap text-right text-muted-foreground">{item.jangka_waktu}</TableCell>
                                    <TableCell>{item.satuan}</TableCell>
                                    <TableCell className="text-right font-mono">{rupiah(item.nominal_angsuran)}</TableCell>
                                    <TableCell className="text-right font-mono font-semibold">{rupiah(item.sisa_pokok)}</TableCell>
                                    <TableCell className="text-right font-mono">{rupiah(item.tunggakan)}</TableCell>
                                    <TableCell>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                asChild
                                                title="Detail"
                                            >
                                                <Link href={route('superadmin.pinjaman.pinjaman.show', item.id)}>
                                                    <Eye className="text-muted-foreground" />
                                                </Link>
                                            </Button>
                                            <DropdownMenu>
                                                <DropdownMenuTrigger asChild>
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        title="Aksi Lainnya"
                                                        className="data-[state=open]:bg-muted"
                                                    >
                                                        <MoreHorizontal className="text-muted-foreground" />
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent align="end">
                                                    <DropdownMenuLabel className="text-xs text-muted-foreground">
                                                        Aksi
                                                    </DropdownMenuLabel>
                                                    <DropdownMenuItem
                                                        onSelect={() =>
                                                            window.open(
                                                                route('superadmin.pinjaman.pinjaman.cetak-angsuran', item.id),
                                                                '_blank',
                                                            )
                                                        }
                                                    >
                                                        <Printer />
                                                        Cetak Angsuran
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem asChild>
                                                        <Link href={route('superadmin.transaksi-pinjaman.angsuran-pinjaman.create')}>
                                                            <HandCoins />
                                                            Proses Angsuran
                                                        </Link>
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem asChild>
                                                        <Link href={route('superadmin.pinjaman.pinjaman.edit', item.id)}>
                                                            <Pencil />
                                                            Edit Pinjaman
                                                        </Link>
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
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