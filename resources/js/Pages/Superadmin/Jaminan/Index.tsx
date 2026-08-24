import { useState } from 'react';
import { Link, Head, router} from '@inertiajs/react';
import { Eye, Package, Pencil, Plus, Search } from 'lucide-react';

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
import type { JaminanRow } from '@/types/jaminan';
import type { Paginated } from '@/types/models';

interface Props {
    jaminan: Paginated<JaminanRow>;
    filters: { search: string };
}

export default function JaminanIndex({ jaminan, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [perPage, setPerPage] = useState(String(jaminan.per_page));

    const apply = (overrides: { search?: string; per_page?: string } = {}) => {
        router.get(
            route('superadmin.pinjaman.jaminan'),
            {
                search: overrides.search ?? search,
                per_page: overrides.per_page ?? perPage,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Data Jaminan" />

            <PageHeader
                title="Data Jaminan"
                description="Kelola kategori dan detail jaminan pinjaman."
                icon={Package}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route('superadmin.pinjaman.jaminan.create')}>
                        <Plus />
                        Tambah Jaminan
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
                            placeholder="Cari nama jaminan…"
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
                                <TableHead>Nama Jaminan</TableHead>
                                <TableHead>Detail</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {jaminan.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={4} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data jaminan.
                                    </TableCell>
                                </TableRow>
                            )}
                            {jaminan.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {jaminan.from !== null ? jaminan.from + i : i + 1}
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{item.nama}</span>
                                    </TableCell>
                                    <TableCell className="max-w-md">
                                        <div className="flex flex-wrap gap-1">
                                            {(item.details ?? []).slice(0, 3).map((d, di) => (
                                                <span
                                                    key={d.id ?? di}
                                                    className="rounded bg-muted px-1.5 py-0.5 text-xs text-muted-foreground"
                                                >
                                                    {d.detail}
                                                </span>
                                            ))}
                                            {(item.details?.length ?? 0) > 3 && (
                                                <span className="rounded bg-brand-600/10 px-1.5 py-0.5 text-xs font-medium text-brand-700 dark:text-brand-300">
                                                    +{(item.details?.length ?? 0) - 3} lainnya
                                                </span>
                                            )}
                                        </div>
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <Link href={route('superadmin.pinjaman.jaminan.show', item.id)} aria-label={`Lihat ${item.nama}`}>
                                                    <Eye className="size-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <Link href={route('superadmin.pinjaman.jaminan.edit', item.id)} aria-label={`Edit ${item.nama}`}>
                                                    <Pencil className="size-4" />
                                                </Link>
                                            </Button>
                                            <ConfirmDelete
                                                routeName="superadmin.pinjaman.jaminan.destroy"
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
                        links={jaminan.links}
                        currentPage={jaminan.current_page}
                        lastPage={jaminan.last_page}
                        from={jaminan.from}
                        to={jaminan.to}
                        total={jaminan.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
