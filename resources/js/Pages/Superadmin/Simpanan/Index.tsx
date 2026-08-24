import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { Eye, Pencil, Plus, Search, Wallet } from 'lucide-react';

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
import type { Paginated, SimpananRow } from '@/types/models';

interface Props {
    simpanan: Paginated<SimpananRow>;
    filters: { search: string };
}

export default function SimpananIndex({ simpanan, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [perPage, setPerPage] = useState(String(simpanan.per_page));

    const apply = (overrides: { search?: string; per_page?: string } = {}) => {
        router.get(
            route('superadmin.simpanan'),
            {
                search: overrides.search ?? search,
                per_page: overrides.per_page ?? perPage,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Data Simpanan" />

            <PageHeader
                title="Data Simpanan"
                description="Kelola rekening simpanan anggota."
                icon={Wallet}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <a href={route('superadmin.simpanan.create')}>
                        <Plus />
                        Tambah Simpanan
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
                            placeholder="Cari no. rekening / nama anggota…"
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
                                <TableHead>No. Rekening</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>Anggota</TableHead>
                                <TableHead>Produk</TableHead>
                                <TableHead>Marketing</TableHead>
                                <TableHead>Kantor</TableHead>
                                <TableHead className="text-right">Setoran Awal</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {simpanan.data.length === 0 && (
                                <TableRow>
                                    <TableCell
                                        colSpan={10}
                                        className="h-32 text-center text-muted-foreground"
                                    >
                                        Tidak ada data simpanan.
                                    </TableCell>
                                </TableRow>
                            )}
                            {simpanan.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {simpanan.from !== null ? simpanan.from + i : i + 1}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.no_rekening}
                                        </span>
                                    </TableCell>
                                    <TableCell className="whitespace-nowrap text-muted-foreground">
                                        {item.tanggal ?? '—'}
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">
                                            {item.anggota?.nama ?? '—'}
                                        </span>
                                        <span className="block font-mono text-xs text-muted-foreground">
                                            {item.anggota?.no_anggota}
                                        </span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.jenis_simpanan?.nama ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.marketing?.nama ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.kantor?.nama_kantor ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-right font-mono">
                                        Rp {Number(item.nominal_setor ?? 0).toLocaleString('id-ID')}
                                    </TableCell>
                                    <TableCell>
                                        <Badge variant={item.aktif === '1' ? 'success' : 'secondary'}>
                                            {item.aktif === '1' ? 'Aktif' : 'Nonaktif'}
                                        </Badge>
                                        {item.sms === '1' && (
                                            <Badge variant="outline" className="ml-1">
                                                SMS
                                            </Badge>
                                        )}
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <a
                                                    href={route('superadmin.simpanan.show', item.id)}
                                                    aria-label={`Lihat ${item.no_rekening}`}
                                                >
                                                    <Eye className="size-4" />
                                                </a>
                                            </Button>
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <a
                                                    href={route('superadmin.simpanan.edit', item.id)}
                                                    aria-label={`Edit ${item.no_rekening}`}
                                                >
                                                    <Pencil className="size-4" />
                                                </a>
                                            </Button>
                                            <ConfirmDelete
                                                routeName="superadmin.simpanan.destroy"
                                                id={item.id}
                                                label={item.no_rekening}
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
                        links={simpanan.links}
                        currentPage={simpanan.current_page}
                        lastPage={simpanan.last_page}
                        from={simpanan.from}
                        to={simpanan.to}
                        total={simpanan.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
