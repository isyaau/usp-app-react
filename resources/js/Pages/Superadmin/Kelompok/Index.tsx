import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { Eye, Pencil, Plus, Search, Users } from 'lucide-react';

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
import type { KelompokRow, Paginated } from '@/types/models';

interface Props {
    kelompok: Paginated<KelompokRow>;
    filters: { search: string };
}

export default function KelompokIndex({ kelompok, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [perPage, setPerPage] = useState(String(kelompok.per_page));

    const apply = (overrides: { search?: string; per_page?: string } = {}) => {
        router.get(
            route('superadmin.kelompok'),
            {
                search: overrides.search ?? search,
                per_page: overrides.per_page ?? perPage,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Data Kelompok" />

            <PageHeader
                title="Data Kelompok"
                description="Kelola kelompok anggota koperasi."
                icon={Users}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <a href={route('superadmin.kelompok.create')}>
                        <Plus />
                        Tambah Kelompok
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
                            placeholder="Cari nama / kode kelompok…"
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

                <div className="px-5">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>Kode</TableHead>
                                <TableHead>Nama Kelompok</TableHead>
                                <TableHead>Ketua</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {kelompok.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={5} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data kelompok.
                                    </TableCell>
                                </TableRow>
                            )}
                            {kelompok.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {kelompok.from !== null ? kelompok.from + i : i + 1}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.kode}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{item.nama}</span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.ketua?.nama ?? '—'}
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <a href={route('superadmin.kelompok.show', item.id)} aria-label={`Lihat ${item.nama}`}>
                                                    <Eye className="size-4" />
                                                </a>
                                            </Button>
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <a href={route('superadmin.kelompok.edit', item.id)} aria-label={`Edit ${item.nama}`}>
                                                    <Pencil className="size-4" />
                                                </a>
                                            </Button>
                                            <ConfirmDelete
                                                routeName="superadmin.kelompok.destroy"
                                                id={item.id}
                                                label={item.nama}
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
                        links={kelompok.links}
                        currentPage={kelompok.current_page}
                        lastPage={kelompok.last_page}
                        from={kelompok.from}
                        to={kelompok.to}
                        total={kelompok.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
