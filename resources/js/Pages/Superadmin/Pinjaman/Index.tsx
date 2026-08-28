import { useState } from 'react';
import { Link, Head, router, useForm } from '@inertiajs/react';
import {
    AlertCircle,
    Banknote,
    Calculator,
    Eye,
    FileDown,
    LoaderCircle,
    MoreHorizontal,
    Pencil,
    Plus,
    Printer,
    Search,
    Sheet,
    Table as TableIcon,
    Upload,
} from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { ConfirmDelete } from '@/Components/ConfirmDelete';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card } from '@/Components/ui/card';
import { CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
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
import type { Paginated, PinjamanRow } from '@/types/models';

interface Props {
    pinjaman: Paginated<PinjamanRow>;
    filters: { search: string };
}

const rupiah = (v: string | number | null) =>
    `Rp ${Number(v ?? 0).toLocaleString('id-ID')}`;

export default function PinjamanIndex({ pinjaman, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [perPage, setPerPage] = useState(String(pinjaman.per_page));

    const importForm = useForm<{ file: File | null }>({ file: null });

    const apply = (overrides: { search?: string; per_page?: string } = {}) => {
        router.get(
            route('superadmin.pinjaman.pinjaman'),
            {
                search: overrides.search ?? search,
                per_page: overrides.per_page ?? perPage,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Data Pinjaman" />

            <PageHeader
                title="Data Pinjaman"
                description="Kelola rekening pinjaman anggota."
                icon={Banknote}
            >
                <Button variant="outline" asChild title="Simulasi angsuran pinjaman">
                    <Link href={route('superadmin.pinjaman.pinjaman.simulasi')}>
                        <Calculator />
                        Simulasi
                    </Link>
                </Button>
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route('superadmin.pinjaman.pinjaman.create')} preload="hover">
                        <Plus />
                        Tambah Pinjaman
                    </Link>
                </Button>
            </PageHeader>

            <Card className="gap-4 py-5">
                <CardHeader className="flex-row flex-wrap items-center justify-between gap-3 space-y-0 px-5">
                    <CardTitle className="flex items-center gap-2 text-base">
                        <div className="p-2 rounded-lg bg-green-50 text-green-600">
                            <Upload className="size-4" />
                        </div>
                        Impor Data Pinjaman
                    </CardTitle>
                </CardHeader>
                <CardContent className="px-5 space-y-4">
                    <div className="flex items-center gap-3 p-3 rounded-lg bg-muted/30 border border-border/50">
                        <Sheet className="size-5 text-muted-foreground shrink-0" />
                        <div className="flex-1 min-w-0">
                            <p className="text-sm font-medium text-foreground">Template Impor Pinjaman</p>
                            <p className="text-xs text-muted-foreground">
                                Kolom: Tanggal, No Pinjaman, No Anggota, Produk, Plafon, Bunga, Jangka Waktu,
                                Satuan, Nominal Angsuran, Aktif
                            </p>
                        </div>
                        <Button
                            variant="ghost"
                            size="sm"
                            asChild
                            className="shrink-0 text-brand-600 hover:text-brand-700 hover:bg-brand-50 gap-1.5"
                        >
                            <a href={route('superadmin.pinjaman.pinjaman.template')}>
                                <FileDown className="size-3.5" />
                                Unduh Template (.xlsx)
                            </a>
                        </Button>
                    </div>

                    <form
                        onSubmit={(e) => {
                            e.preventDefault();
                            importForm.post(route('superadmin.pinjaman.pinjaman.import'), {
                                forceFormData: true,
                            });
                        }}
                        className="space-y-3"
                    >
                        <Input
                            id="import-file"
                            type="file"
                            accept=".xlsx,.csv"
                            onChange={(e) => importForm.setData('file', e.target.files?.[0] ?? null)}
                            className="cursor-pointer"
                        />
                        {importForm.errors.file && (
                            <p className="text-sm text-destructive flex items-center gap-1.5">
                                <AlertCircle className="size-4" />
                                {importForm.errors.file}
                            </p>
                        )}
                        <Button
                            type="submit"
                            size="sm"
                            disabled={importForm.processing || !importForm.data.file}
                            className="w-full sm:w-auto bg-brand-600 hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed gap-2"
                        >
                            {importForm.processing && <LoaderCircle className="animate-spin size-4" />}
                            {importForm.processing ? 'Memproses...' : 'Impor Sekarang'}
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <Card className="gap-4 py-5 mt-5">
                <div className="flex flex-wrap items-center gap-3 px-5">
                    <div className="relative min-w-56 flex-1">
                        <Search className="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && apply()}
                            placeholder="Cari no. pinjaman / anggota…"
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
                                <TableHead>Tanggal</TableHead>
                                <TableHead>No. Pinjaman</TableHead>
                                <TableHead>Produk</TableHead>
                                <TableHead>Anggota</TableHead>
                                <TableHead>Jangka Waktu</TableHead>
                                <TableHead>Bunga</TableHead>
                                <TableHead>Plafon</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {pinjaman.data.length === 0 && (
                                <TableRow>
                                    <TableCell
                                        colSpan={10}
                                        className="h-32 text-center text-muted-foreground"
                                    >
                                        Tidak ada data pinjaman.
                                    </TableCell>
                                </TableRow>
                            )}
                            {pinjaman.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {pinjaman.from !== null ? pinjaman.from + i : i + 1}
                                    </TableCell>
                                    <TableCell className="whitespace-nowrap text-muted-foreground">
                                        {item.tanggal}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.no_pinjaman}
                                        </span>
                                    </TableCell>
                                    <TableCell>{item.jenisPinjaman?.nama ?? '—'}</TableCell>
                                    <TableCell>
                                        {item.anggota ? (
                                            <>
                                                {item.anggota.nama}
                                                <span className="block font-mono text-xs text-muted-foreground">
                                                    {item.anggota.no_anggota}
                                                </span>
                                            </>
                                        ) : (
                                            '—'
                                        )}
                                    </TableCell>
                                    <TableCell className="whitespace-nowrap text-muted-foreground">
                                        {item.jangka_waktu} {item.satuan}
                                    </TableCell>
                                    <TableCell>{item.bunga}%</TableCell>
                                    <TableCell className="font-mono">{rupiah(item.plafon)}</TableCell>
                                    <TableCell>
                                        <Badge
                                            variant="outline"
                                            className={
                                                item.aktif === '1'
                                                    ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                                                    : 'border-muted-foreground/30 bg-muted text-muted-foreground'
                                            }
                                        >
                                            {item.aktif === '1' ? 'Aktif' : 'Nonaktif'}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button variant="ghost" size="icon" asChild title="Detail">
                                                <Link href={route('superadmin.pinjaman.pinjaman.show', item.id)}>
                                                    <Eye className="text-muted-foreground" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" asChild title="Edit">
                                                <Link href={route('superadmin.pinjaman.pinjaman.edit', item.id)}>
                                                    <Pencil className="text-muted-foreground" />
                                                </Link>
                                            </Button>
                                            <DropdownMenu>
                                                <DropdownMenuTrigger asChild>
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        title="Cetak / Lainnya"
                                                        className="data-[state=open]:bg-muted"
                                                    >
                                                        <MoreHorizontal className="text-muted-foreground" />
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent align="end">
                                                    <DropdownMenuLabel className="text-xs text-muted-foreground">
                                                        Cetak
                                                    </DropdownMenuLabel>
                                                    <DropdownMenuItem onSelect={() => window.open(route('superadmin.pinjaman.pinjaman.cetak-data', item.id), '_blank')}>
                                                        <Printer />
                                                        Cetak Data Pinjaman
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem onSelect={() => window.open(route('superadmin.pinjaman.pinjaman.cetak-simulasi', item.id), '_blank')}>
                                                        <Calculator />
                                                        Cetak Simulasi
                                                    </DropdownMenuItem>
                                                    <DropdownMenuItem onSelect={() => window.open(route('superadmin.pinjaman.pinjaman.cetak-angsuran', item.id), '_blank')}>
                                                        <TableIcon />
                                                        Cetak Tabel Angsuran
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
                                            <ConfirmDelete
                                                routeName="superadmin.pinjaman.pinjaman.destroy"
                                                id={item.id}
                                                label={item.no_pinjaman}
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
                        links={pinjaman.links}
                        currentPage={pinjaman.current_page}
                        lastPage={pinjaman.last_page}
                        from={pinjaman.from}
                        to={pinjaman.to}
                        total={pinjaman.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
