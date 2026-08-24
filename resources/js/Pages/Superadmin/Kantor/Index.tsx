import { useState } from 'react';
import { Link, Head, router} from '@inertiajs/react';
import { Building2, Eye, Pencil, Plus, Search } from 'lucide-react';

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
import type { KantorRow, Paginated } from '@/types/models';

interface Props {
    kantor: Paginated<KantorRow>;
    filters: { search: string };
}

export default function KantorIndex({ kantor, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [perPage, setPerPage] = useState(String(kantor.per_page));

    const apply = (overrides: { search?: string; per_page?: string } = {}) => {
        router.get(
            route('superadmin.kantor'),
            {
                search: overrides.search ?? search,
                per_page: overrides.per_page ?? perPage,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Data Kantor" />

            <PageHeader
                title="Data Kantor"
                description="Kelola data kantor/kas koperasi."
                icon={Building2}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route('superadmin.kantor.create')} preload="hover">
                        <Plus />
                        Tambah Kantor
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
                            placeholder="Cari nama / kode kantor…"
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
                                <TableHead>Nama Kantor</TableHead>
                                <TableHead>Alamat</TableHead>
                                <TableHead>Wilayah</TableHead>
                                <TableHead>Pejabat</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {kantor.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={7} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data kantor.
                                    </TableCell>
                                </TableRow>
                            )}
                            {kantor.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {kantor.from !== null ? kantor.from + i : i + 1}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.kode}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{item.nama_kantor}</span>
                                    </TableCell>
                                    <TableCell className="max-w-48 truncate text-muted-foreground">
                                        {item.alamat_kantor}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {[item.kelurahan?.name, item.kecamatan?.name, item.kota?.name]
                                            .filter(Boolean)
                                            .join(', ') || '—'}
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{item.pejabat}</span>
                                        <span className="block text-xs text-muted-foreground">
                                            {item.jabatan}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <Link href={route('superadmin.kantor.show', item.id)} aria-label={`Lihat ${item.nama_kantor}`}>
                                                    <Eye className="size-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <Link href={route('superadmin.kantor.edit', item.id)} aria-label={`Edit ${item.nama_kantor}`}>
                                                    <Pencil className="size-4" />
                                                </Link>
                                            </Button>
                                            <ConfirmDelete
                                                routeName="superadmin.kantor.destroy"
                                                id={item.id}
                                                label={item.nama_kantor}
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
                        links={kantor.links}
                        currentPage={kantor.current_page}
                        lastPage={kantor.last_page}
                        from={kantor.from}
                        to={kantor.to}
                        total={kantor.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
