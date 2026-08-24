import { useState } from 'react';
import { Link, Head, router, useForm} from '@inertiajs/react';
import {
    Bookmark,
    Eye,
    LoaderCircle,
    Pencil,
    Plus,
    Search,
} from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { ConfirmDelete } from '@/Components/ConfirmDelete';
import { Button } from '@/Components/ui/button';
import { Card } from '@/Components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
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
import type { AccGroupOption, AccHeaderRow, Paginated } from '@/types/models';

interface Props {
    headers: Paginated<AccHeaderRow>;
    filters: { search: string };
}

export default function AccHeaderIndex({ headers, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [perPage, setPerPage] = useState(String(headers.per_page));
    const [groupOpen, setGroupOpen] = useState(false);

    const groupForm = useForm<{ nama: string }>({ nama: '' });

    const apply = (overrides: { search?: string; per_page?: string } = {}) => {
        router.get(
            route('superadmin.account-header'),
            {
                search: overrides.search ?? search,
                per_page: overrides.per_page ?? perPage,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    const saveGroup = (e: React.FormEvent) => {
        e.preventDefault();
        groupForm.post(route('superadmin.acc-group.store'), {
            onSuccess: () => {
                groupForm.reset();
                setGroupOpen(false);
                router.reload({ only: [] });
            },
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Account Header" />

            <PageHeader
                title="Account Header"
                description="Kelola header akun (COA) koperasi."
                icon={Bookmark}
            >
                <Button
                    variant="outline"
                    onClick={() => setGroupOpen(true)}
                >
                    Kelola Grup
                </Button>
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route('superadmin.account-header.create')}>
                        <Plus />
                        Tambah Header
                    </Link>
                </Button>
            </PageHeader>

            {/* Dialog kelola grup akun */}
            <Dialog open={groupOpen} onOpenChange={setGroupOpen}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Tambah Grup Akun</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={saveGroup} className="space-y-4">
                        <div className="space-y-2">
                            <Label htmlFor="group-nama">
                                Nama Grup <span className="text-brand-600">*</span>
                            </Label>
                            <Input
                                id="group-nama"
                                value={groupForm.data.nama}
                                onChange={(e) => groupForm.setData('nama', e.target.value)}
                                placeholder="Aktiva Lancar"
                            />
                            {groupForm.errors.nama && (
                                <p className="text-sm text-brand-600">{groupForm.errors.nama}</p>
                            )}
                        </div>
                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => setGroupOpen(false)}
                            >
                                Batal
                            </Button>
                            <Button
                                type="submit"
                                disabled={groupForm.processing}
                                className="bg-brand-600 hover:bg-brand-500"
                            >
                                {groupForm.processing && (
                                    <LoaderCircle className="animate-spin" />
                                )}
                                Simpan Grup
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <Card className="gap-4 py-5">
                <div className="flex flex-wrap items-center gap-3 px-5">
                    <div className="relative min-w-56 flex-1">
                        <Search className="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && apply()}
                            placeholder="Cari nama / nomor header…"
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
                                <TableHead>No. Header</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead>Grup</TableHead>
                                <TableHead>Jenis</TableHead>
                                <TableHead>Keterangan</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {headers.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={7} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data header.
                                    </TableCell>
                                </TableRow>
                            )}
                            {headers.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {headers.from !== null ? headers.from + i : i + 1}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.no_header}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{item.nama}</span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.group?.nama ?? '—'}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-full bg-brand-600/10 px-2.5 py-0.5 text-xs font-medium text-brand-700 dark:text-brand-300">
                                            {item.jenis}
                                        </span>
                                    </TableCell>
                                    <TableCell className="max-w-48 truncate text-muted-foreground">
                                        {item.keterangan}
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <Link href={route('superadmin.account-header.show', item.id)} aria-label={`Lihat ${item.nama}`}>
                                                    <Eye className="size-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <Link href={route('superadmin.account-header.edit', item.id)} aria-label={`Edit ${item.nama}`}>
                                                    <Pencil className="size-4" />
                                                </Link>
                                            </Button>
                                            <ConfirmDelete
                                                routeName="superadmin.account-header.destroy"
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
                        links={headers.links}
                        currentPage={headers.current_page}
                        lastPage={headers.last_page}
                        from={headers.from}
                        to={headers.to}
                        total={headers.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
