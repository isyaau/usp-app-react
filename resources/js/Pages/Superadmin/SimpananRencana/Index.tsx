import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { CalendarClock, Plus, Search } from 'lucide-react';

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
import type { Paginated, RencanaRow } from '@/types/models';

interface Props {
    rencana: Paginated<RencanaRow>;
    filters: { search: string };
}

const rupiah = (v: string | number | null) =>
    `Rp ${Number(v ?? 0).toLocaleString('id-ID')}`;

export default function SimpananRencanaIndex({ rencana, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [perPage, setPerPage] = useState(String(rencana.per_page));

    const apply = (overrides: { search?: string; per_page?: string } = {}) => {
        router.get(
            route('superadmin.simpanan.rencana'),
            {
                search: overrides.search ?? search,
                per_page: overrides.per_page ?? perPage,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Simpanan Rencana" />

            <PageHeader
                title="Simpanan Rencana"
                description="Kelola rencana simpanan anggota."
                icon={CalendarClock}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <a href={route('superadmin.simpanan.rencana.create')}>
                        <Plus />
                        Tambah Rencana
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
                            placeholder="Cari no. bukti / keterangan…"
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
                                <TableHead>Tanggal Mulai</TableHead>
                                <TableHead>Jatuh Tempo</TableHead>
                                <TableHead>No Bukti</TableHead>
                                <TableHead>Nominal</TableHead>
                                <TableHead>Bagi Hasil</TableHead>
                                <TableHead>Keterangan</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {rencana.data.length === 0 && (
                                <TableRow>
                                    <TableCell
                                        colSpan={8}
                                        className="h-32 text-center text-muted-foreground"
                                    >
                                        Tidak ada data simpanan rencana.
                                    </TableCell>
                                </TableRow>
                            )}
                            {rencana.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {rencana.from !== null ? rencana.from + i : i + 1}
                                    </TableCell>
                                    <TableCell className="whitespace-nowrap text-muted-foreground">
                                        {item.tanggal_mulai}
                                    </TableCell>
                                    <TableCell className="whitespace-nowrap text-muted-foreground">
                                        {item.tanggal_jatuhtempo}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.no_bukti}
                                        </span>
                                    </TableCell>
                                    <TableCell className="font-mono">{rupiah(item.nominal)}</TableCell>
                                    <TableCell>{item.bunga}%</TableCell>
                                    <TableCell className="max-w-56 truncate text-muted-foreground">
                                        {item.keterangan || '—'}
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center justify-end gap-1">
                                            {/* Edit/show tidak ada di aplikasi lama (stub kosong). */}
                                            <ConfirmDelete
                                                routeName="superadmin.simpanan.rencana.destroy"
                                                id={item.id}
                                                label={item.no_bukti}
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
                        links={rencana.links}
                        currentPage={rencana.current_page}
                        lastPage={rencana.last_page}
                        from={rencana.from}
                        to={rencana.to}
                        total={rencana.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
