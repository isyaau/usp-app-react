import { useState } from 'react';
import { Link, Head, router} from '@inertiajs/react';
import { ArrowLeftRight, Eye, Pencil, Plus, Search } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { ConfirmDelete } from '@/Components/ConfirmDelete';
import { Button } from '@/Components/ui/button';
import { Card } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
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
import type { Paginated, SimpananKodeRow } from '@/types/models';

interface Props {
    kodeTransaksi: Paginated<SimpananKodeRow>;
    filters: { search: string };
}

export default function KodetransaksiIndex({ kodeTransaksi, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [perPage, setPerPage] = useState(String(kodeTransaksi.per_page));

    const apply = (overrides: { search?: string; per_page?: string } = {}) => {
        router.get(
            route('superadmin.simpanan.kode-transaksi'),
            {
                search: overrides.search ?? search,
                per_page: overrides.per_page ?? perPage,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Kode Transaksi" />

            <PageHeader
                title="Kode Transaksi"
                description="Pemetaan kode transaksi ke akun debet/kredit."
                icon={ArrowLeftRight}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route('superadmin.simpanan.kode-transaksi.create')} preload="hover">
                        <Plus />
                        Tambah Kode
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
                            placeholder="Cari nama / kode…"
                            className="pl-9"
                        />
                    </div>
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
                            {['10', '25', '50', '100'].map((n) => (
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
                                <TableHead>Kode</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead>Account Debet</TableHead>
                                <TableHead>Account Kredit</TableHead>
                                <TableHead>Flag</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {kodeTransaksi.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={7} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data kode transaksi.
                                    </TableCell>
                                </TableRow>
                            )}
                            {kodeTransaksi.data.map((item, i) => {
                                const activeFlags = (
                                    [
                                        'setoran',
                                        'tarikan',
                                        'transfer',
                                        'pokok',
                                        'wajib',
                                        'sukarela',
                                        'pinjaman',
                                        'saham',
                                        'pokok_pinjaman',
                                        'rencana',
                                    ] as const
                                ).filter((f) => item[f]);

                                return (
                                    <TableRow key={item.id}>
                                        <TableCell className="text-muted-foreground">
                                            {kodeTransaksi.from !== null ? kodeTransaksi.from + i : i + 1}
                                        </TableCell>
                                        <TableCell>
                                            <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                                {item.kode}
                                            </span>
                                        </TableCell>
                                        <TableCell>
                                            <span className="font-medium">{item.nama}</span>
                                        </TableCell>
                                        <TableCell className="font-mono text-xs text-muted-foreground">
                                            {item.debetAccount?.no_account ?? '—'}
                                        </TableCell>
                                        <TableCell className="font-mono text-xs text-muted-foreground">
                                            {item.kreditAccount?.no_account ?? '—'}
                                        </TableCell>
                                        <TableCell>
                                            <div className="flex max-w-52 flex-wrap gap-1">
                                                {activeFlags.length === 0 && (
                                                    <span className="text-xs text-muted-foreground">—</span>
                                                )}
                                                {activeFlags.map((f) => (
                                                    <span
                                                        key={f}
                                                        className="rounded bg-brand-600/10 px-1.5 py-0.5 text-[10px] font-medium text-brand-700 dark:text-brand-300"
                                                    >
                                                        {f}
                                                    </span>
                                                ))}
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            <div className="flex items-center justify-end gap-1">
                                                <Button variant="ghost" size="icon" className="size-8" asChild>
                                                    <Link href={route('superadmin.simpanan.kode-transaksi.show', item.id)} aria-label={`Lihat ${item.nama}`}>
                                                        <Eye className="size-4" />
                                                    </Link>
                                                </Button>
                                                <Button variant="ghost" size="icon" className="size-8" asChild>
                                                    <Link href={route('superadmin.simpanan.kode-transaksi.edit', item.id)} aria-label={`Edit ${item.nama}`}>
                                                        <Pencil className="size-4" />
                                                    </Link>
                                                </Button>
                                                <ConfirmDelete
                                                    routeName="superadmin.simpanan.kode-transaksi.destroy"
                                                    id={item.id}
                                                    label={item.nama}
                                                />
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                </div>

                <div className="border-t px-5 pt-4">
                    <Pagination
                        links={kodeTransaksi.links}
                        currentPage={kodeTransaksi.current_page}
                        lastPage={kodeTransaksi.last_page}
                        from={kodeTransaksi.from}
                        to={kodeTransaksi.to}
                        total={kodeTransaksi.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
