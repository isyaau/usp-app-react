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
import type { DepositoJenisRow, Paginated } from '@/types/models';

interface Props {
    produk: Paginated<DepositoJenisRow>;
    filters: { search: string };
}

export default function ProdukIndex({ produk, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [perPage, setPerPage] = useState(String(produk.per_page));

    const apply = (overrides: { search?: string; per_page?: string } = {}) => {
        router.get(
            route('superadmin.simpanan-berjangka.produk'),
            {
                search: overrides.search ?? search,
                per_page: overrides.per_page ?? perPage,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Produk Simpanan Berjangka" />

            <PageHeader
                title="Produk Simpanan Berjangka"
                description="Kelola produk deposito dan parameternya."
                icon={Package}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route('superadmin.simpanan-berjangka.produk.create')}>
                        <Plus />
                        Tambah Produk
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
                            placeholder="Cari kode / nama produk…"
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
                                <TableHead>Nama Produk</TableHead>
                                <TableHead>Jangka Waktu</TableHead>
                                <TableHead>Bunga (%)</TableHead>
                                <TableHead>Penalti (%)</TableHead>
                                <TableHead>Pajak (%)</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {produk.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={8} className="h-32 text-center text-muted-foreground">
                                        Tidak ada produk deposito.
                                    </TableCell>
                                </TableRow>
                            )}
                            {produk.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {produk.from !== null ? produk.from + i : i + 1}
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
                                        {item.jangka_waktu ? `${item.jangka_waktu} bln` : '—'}
                                    </TableCell>
                                    <TableCell className="font-mono text-xs">
                                        {item.bunga ?? '—'}
                                    </TableCell>
                                    <TableCell className="font-mono text-xs text-muted-foreground">
                                        {item.penalti ?? '—'}
                                    </TableCell>
                                    <TableCell className="font-mono text-xs text-muted-foreground">
                                        {item.pajak ?? '—'}
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <Link href={route('superadmin.simpanan-berjangka.produk.show', item.id)}
                                                    aria-label={`Lihat ${item.nama}`}
                                                >
                                                    <Eye className="size-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <Link href={route('superadmin.simpanan-berjangka.produk.edit', item.id)}
                                                    aria-label={`Edit ${item.nama}`}
                                                >
                                                    <Pencil className="size-4" />
                                                </Link>
                                            </Button>
                                            <ConfirmDelete
                                                routeName="superadmin.simpanan-berjangka.produk.destroy"
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
                        links={produk.links}
                        currentPage={produk.current_page}
                        lastPage={produk.last_page}
                        from={produk.from}
                        to={produk.to}
                        total={produk.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
