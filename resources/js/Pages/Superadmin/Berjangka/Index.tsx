import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { CalendarClock, Eye, Pencil, Plus, Search } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { ConfirmDelete } from '@/Components/ConfirmDelete';
import { Badge } from '@/Components/ui/badge';
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
import type { DepositoRow, Paginated } from '@/types/models';

interface Props {
    berjangka: Paginated<DepositoRow>;
    filters: { search: string };
}

export default function BerjangkaIndex({ berjangka, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [perPage, setPerPage] = useState(String(berjangka.per_page));

    const apply = (overrides: { search?: string; per_page?: string } = {}) => {
        router.get(
            route('superadmin.simpanan-berjangka'),
            {
                search: overrides.search ?? search,
                per_page: overrides.per_page ?? perPage,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Simpanan Berjangka" />

            <PageHeader
                title="Simpanan Berjangka"
                description="Kelola rekening simpanan berjangka (deposito) anggota."
                icon={CalendarClock}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <a href={route('superadmin.simpanan-berjangka.create')}>
                        <Plus />
                        Tambah Deposito
                    </a>
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
                            placeholder="Cari no. deposito / nama anggota…"
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
                                <TableHead>No. Deposito</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>Anggota</TableHead>
                                <TableHead>Produk</TableHead>
                                <TableHead>Jangka Waktu</TableHead>
                                <TableHead className="text-right">Nominal</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {berjangka.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={9} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data simpanan berjangka.
                                    </TableCell>
                                </TableRow>
                            )}
                            {berjangka.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {berjangka.from !== null ? berjangka.from + i : i + 1}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.no_deposito}
                                        </span>
                                    </TableCell>
                                    <TableCell className="whitespace-nowrap text-muted-foreground">
                                        {item.tanggal}
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{item.anggota?.nama}</span>
                                        <span className="block font-mono text-xs text-muted-foreground">
                                            {item.anggota?.no_anggota}
                                        </span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.produk?.nama ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.jangka_waktu ? `${item.jangka_waktu} bln` : '—'}
                                    </TableCell>
                                    <TableCell className="text-right font-mono">
                                        Rp {Number(item.nominal).toLocaleString('id-ID')}
                                    </TableCell>
                                    <TableCell>
                                        <Badge variant={item.blokir === '1' ? 'destructive' : 'success'}>
                                            {item.blokir === '1' ? 'Diblokir' : 'Aktif'}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <a
                                                    href={route('superadmin.simpanan-berjangka.show', item.id)}
                                                    aria-label={`Lihat ${item.no_deposito}`}
                                                >
                                                    <Eye className="size-4" />
                                                </a>
                                            </Button>
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <a
                                                    href={route('superadmin.simpanan-berjangka.edit', item.id)}
                                                    aria-label={`Edit ${item.no_deposito}`}
                                                >
                                                    <Pencil className="size-4" />
                                                </a>
                                            </Button>
                                            <ConfirmDelete
                                                routeName="superadmin.simpanan-berjangka.destroy"
                                                id={item.id}
                                                label={item.no_deposito}
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
                        links={berjangka.links}
                        currentPage={berjangka.current_page}
                        lastPage={berjangka.last_page}
                        from={berjangka.from}
                        to={berjangka.to}
                        total={berjangka.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
