import { useState } from 'react';
import { Link, Head, router} from '@inertiajs/react';
import { BookOpen, Eye, Pencil, Plus, Search } from 'lucide-react';

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
import type { AccountRow, Paginated } from '@/types/models';

interface Props {
    accounts: Paginated<AccountRow>;
    filters: { search: string };
}

export default function AccountIndex({ accounts, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [perPage, setPerPage] = useState(String(accounts.per_page));

    const apply = (overrides: { search?: string; per_page?: string } = {}) => {
        router.get(
            route('superadmin.account'),
            {
                search: overrides.search ?? search,
                per_page: overrides.per_page ?? perPage,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Data Account" />

            <PageHeader
                title="Data Account"
                description="Kelola akun (COA) tingkat detail."
                icon={BookOpen}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route('superadmin.account.create')}>
                        <Plus />
                        Tambah Account
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
                            placeholder="Cari nama / nomor account…"
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
                                <TableHead>No. Account</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead>Header</TableHead>
                                <TableHead>Tipe</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {accounts.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={6} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data account.
                                    </TableCell>
                                </TableRow>
                            )}
                            {accounts.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {accounts.from !== null ? accounts.from + i : i + 1}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.no_account}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{item.nama}</span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.header
                                            ? `${item.header.no_header} — ${item.header.nama}`
                                            : '—'}
                                    </TableCell>
                                    <TableCell>
                                        <span
                                            className={`rounded-full px-2.5 py-0.5 text-xs font-semibold ${
                                                item.tipe === 'Debet'
                                                    ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                                                    : 'bg-sky-500/10 text-sky-700 dark:text-sky-400'
                                            }`}
                                        >
                                            {item.tipe}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <Link href={route('superadmin.account.show', item.id)} aria-label={`Lihat ${item.nama}`}>
                                                    <Eye className="size-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <Link href={route('superadmin.account.edit', item.id)} aria-label={`Edit ${item.nama}`}>
                                                    <Pencil className="size-4" />
                                                </Link>
                                            </Button>
                                            <ConfirmDelete
                                                routeName="superadmin.account.destroy"
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
                        links={accounts.links}
                        currentPage={accounts.current_page}
                        lastPage={accounts.last_page}
                        from={accounts.from}
                        to={accounts.to}
                        total={accounts.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
