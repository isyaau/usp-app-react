import { useState } from 'react';
import { Link, Head, router} from '@inertiajs/react';
import {
    Eye,
    Pencil,
    Plus,
    Search,
    ShieldCheck,
    UsersRound,
} from 'lucide-react';

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
import type { Paginated, Role, UserRow } from '@/types/models';

interface Props {
    users: Paginated<UserRow>;
    filters: { search: string };
}

const ROLE_VARIANT: Record<Role, 'default' | 'success' | 'warning'> = {
    superadmin: 'default',
    admin: 'success',
    user: 'warning',
};

export default function UserIndex({ users, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [perPage, setPerPage] = useState(String(users.per_page));

    const apply = (overrides: { search?: string; per_page?: string } = {}) => {
        router.get(
            route('superadmin.user'),
            {
                search: overrides.search ?? search,
                per_page: overrides.per_page ?? perPage,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Data User" />

            <PageHeader
                title="Data User"
                description="Kelola akun pengguna sistem KSP KOPINKA."
                icon={ShieldCheck}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route('superadmin.user.create')} preload="hover">
                        <Plus />
                        Tambah User
                    </Link>
                </Button>
            </PageHeader>

            <Card className="gap-4 py-5">
                {/* Toolbar */}
                <div className="flex flex-wrap items-center gap-3 px-5">
                    <div className="relative min-w-56 flex-1">
                        <Search className="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && apply()}
                            placeholder="Cari nama user…"
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

                {/* Tabel */}
                <div className="px-5">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead>Username</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Role</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {users.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={6} className="h-32 text-center text-muted-foreground">
                                        <UsersRound className="mx-auto mb-2 size-8 opacity-40" />
                                        Tidak ada data user.
                                    </TableCell>
                                </TableRow>
                            )}
                            {users.data.map((user, i) => (
                                <TableRow key={user.id}>
                                    <TableCell className="text-muted-foreground">
                                        {users.from !== null ? users.from + i : i + 1}
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{user.nama}</span>
                                    </TableCell>
                                    <TableCell className="font-mono text-xs">{user.username}</TableCell>
                                    <TableCell className="text-muted-foreground">{user.email}</TableCell>
                                    <TableCell>
                                        <Badge variant={ROLE_VARIANT[user.role]} className="capitalize">
                                            {user.role}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <Link href={route('superadmin.user.show', user.id)}
                                                    aria-label={`Lihat ${user.nama}`}
                                                >
                                                    <Eye className="size-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <Link href={route('superadmin.user.edit', user.id)}
                                                    aria-label={`Edit ${user.nama}`}
                                                >
                                                    <Pencil className="size-4" />
                                                </Link>
                                            </Button>
                                            <ConfirmDelete
                                                routeName="superadmin.user.destroy"
                                                id={user.id}
                                                label={user.nama}
                                            />
                                        </div>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>

                {/* Paginasi */}
                <div className="border-t px-5 pt-4">
                    <Pagination
                        links={users.links}
                        currentPage={users.current_page}
                        lastPage={users.last_page}
                        from={users.from}
                        to={users.to}
                        total={users.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
