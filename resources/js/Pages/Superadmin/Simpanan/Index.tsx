import { useState } from 'react';
import { Link, Head, router, useForm } from '@inertiajs/react';
import {
    AlertCircle,
    Eye,
    FileDown,
    FileSpreadsheet,
    FileText,
    LoaderCircle,
    MoreHorizontal,
    Pencil,
    Plus,
    Printer,
    Search,
    Sheet,
    Upload,
    Wallet,
} from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { ConfirmDelete } from '@/Components/ConfirmDelete';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
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
import type { Paginated, SimpananRow } from '@/types/models';
import { JENIS_SIMPANAN_LABELS } from '@/types/simpanan';

interface Props {
    simpanan: Paginated<SimpananRow>;
    filters: { search: string };
}

export default function SimpananIndex({ simpanan, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [perPage, setPerPage] = useState(String(simpanan.per_page));
    const [mulai, setMulai] = useState('');
    const [sampai, setSampai] = useState('');

    const importForm = useForm<{ file: File | null }>({ file: null });

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

    const exportQuery = () =>
        new URLSearchParams({
            mulai: mulai || '',
            sampai: sampai || '',
        }).toString();

    return (
        <AuthenticatedLayout>
            <Head title="Data Simpanan" />

            <PageHeader
                title="Data Simpanan"
                description="Kelola rekening simpanan anggota."
                icon={Wallet}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route('superadmin.simpanan.create')} preload="hover">
                        <Plus />
                        Tambah Simpanan
                    </Link>
                </Button>
            </PageHeader>

            <div className="grid gap-5 lg:grid-cols-3">
                {/* ============================ Ekspor ============================ */}
                <Card className="border-border/50 shadow-sm">
                    <CardHeader className="pb-3">
                        <div className="flex items-center justify-between">
                            <CardTitle className="flex items-center gap-2 text-base font-semibold">
                                <div className="p-2 rounded-lg bg-brand-50 text-brand-600">
                                    <FileDown className="size-4" />
                                </div>
                                Ekspor Data
                            </CardTitle>
                        </div>
                        <CardDescription className="text-sm text-muted-foreground">
                            Ekspor data simpanan ke PDF atau Excel dengan filter tanggal opsional.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4 pt-0">
                        <div className="grid grid-cols-2 gap-3">
                            <div className="space-y-1.5">
                                <Label htmlFor="export-mulai" className="text-xs font-medium text-muted-foreground">
                                    Periode Mulai
                                </Label>
                                <Input
                                    id="export-mulai"
                                    type="date"
                                    value={mulai}
                                    onChange={(e) => setMulai(e.target.value)}
                                    className="h-9 text-sm"
                                />
                                <p className="text-[10px] text-muted-foreground">Kosongkan untuk semua data</p>
                            </div>
                            <div className="space-y-1.5">
                                <Label htmlFor="export-sampai" className="text-xs font-medium text-muted-foreground">
                                    Periode Selesai
                                </Label>
                                <Input
                                    id="export-sampai"
                                    type="date"
                                    value={sampai}
                                    onChange={(e) => setSampai(e.target.value)}
                                    className="h-9 text-sm"
                                />
                                <p className="text-[10px] text-muted-foreground">Kosongkan untuk semua data</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2 pt-1">
                            <Button
                                variant="outline"
                                size="sm"
                                asChild
                                className="flex-1 min-w-[120px] gap-1.5 hover:bg-brand-50 hover:text-brand-700 hover:border-brand-200 border-brand-100 text-brand-700"
                            >
                                <a href={`${route('superadmin.simpanan.export-pdf')}?${exportQuery()}`}>
                                    <FileText className="size-3.5" />
                                    PDF
                                </a>
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                asChild
                                className="flex-1 min-w-[120px] gap-1.5 hover:bg-green-50 hover:text-green-700 hover:border-green-200 border-green-100 text-green-700"
                            >
                                <a href={`${route('superadmin.simpanan.export-excel')}?${exportQuery()}`}>
                                    <FileSpreadsheet className="size-3.5" />
                                    Excel
                                </a>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                {/* ============================ Impor ============================= */}
                <Card className="border-border/50 shadow-sm lg:col-span-2">
                    <CardHeader className="pb-3">
                        <div className="flex items-center justify-between">
                            <CardTitle className="flex items-center gap-2 text-base font-semibold">
                                <div className="p-2 rounded-lg bg-green-50 text-green-600">
                                    <Upload className="size-4" />
                                </div>
                                Impor Data
                            </CardTitle>
                        </div>
                        <CardDescription className="text-sm text-muted-foreground">
                            Impor data simpanan dari file Excel. Unduh template terlebih dahulu untuk format yang benar.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4 pt-0">
                        <div className="flex items-center gap-3 p-3 rounded-lg bg-muted/30 border border-border/50">
                            <Sheet className="size-5 text-muted-foreground shrink-0" />
                            <div className="flex-1 min-w-0">
                                <p className="text-sm font-medium text-foreground">Template Impor Simpanan</p>
                                <p className="text-xs text-muted-foreground">
                                    Kolom: Tanggal, No Rekening, No Anggota, Produk, Marketing, QQ, Bagi Hasil,
                                    Nominal Setor, Aktif, SMS, Blokir
                                </p>
                            </div>
                            <Button
                                variant="ghost"
                                size="sm"
                                asChild
                                className="shrink-0 text-brand-600 hover:text-brand-700 hover:bg-brand-50 gap-1.5"
                            >
                                <a href={route('superadmin.simpanan.template')} className="flex items-center gap-1.5">
                                    <FileDown className="size-3.5" />
                                    Unduh Template (.xlsx)
                                </a>
                            </Button>
                        </div>

                        <form
                            onSubmit={(e) => {
                                e.preventDefault();
                                importForm.post(
                                    route('superadmin.simpanan.import'),
                                    { forceFormData: true },
                                );
                            }}
                            className="space-y-3"
                        >
                            <Input
                                id="import-file"
                                type="file"
                                accept=".xlsx,.csv"
                                onChange={(e) =>
                                    importForm.setData('file', e.target.files?.[0] ?? null)
                                }
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
                                {importForm.processing && (
                                    <LoaderCircle className="animate-spin size-4" />
                                )}
                                {importForm.processing ? 'Memproses...' : 'Impor Sekarang'}
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>

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
                                <TableHead>Tanggal</TableHead>
                                <TableHead>No. Rekening</TableHead>
                                <TableHead>Produk Simpanan</TableHead>
                                <TableHead>Jenis Simpanan</TableHead>
                                <TableHead>Nama Anggota</TableHead>
                                <TableHead>Bagi Hasil</TableHead>
                                <TableHead>Status Blokir</TableHead>
                                <TableHead>Status Aktif</TableHead>
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
                                    <TableCell className="whitespace-nowrap text-muted-foreground">
                                        {item.tanggal ?? '—'}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.no_rekening}
                                        </span>
                                    </TableCell>
                                    <TableCell className="font-medium">
                                        {item.jenis_simpanan?.nama ?? '—'}
                                    </TableCell>
                                    <TableCell>
                                        <Badge variant="secondary">
                                            {item.jenis_simpanan?.jenis != null
                                                ? (JENIS_SIMPANAN_LABELS[item.jenis_simpanan.jenis] ?? '—')
                                                : '—'}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">
                                            {item.anggota?.nama ?? '—'}
                                        </span>
                                        <span className="block font-mono text-xs text-muted-foreground">
                                            {item.anggota?.no_anggota}
                                        </span>
                                    </TableCell>
                                    <TableCell className="whitespace-nowrap text-muted-foreground">
                                        {item.jenis_simpanan?.bunga != null
                                            ? `${Number(item.jenis_simpanan.bunga)}%`
                                            : '—'}
                                    </TableCell>
                                    <TableCell>
                                        <Badge
                                            variant={item.blokir_simpanan === '1' ? 'destructive' : 'secondary'}
                                        >
                                            {item.blokir_simpanan === '1' ? 'Diblokir' : 'Tidak'}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <Badge variant={item.aktif === '1' ? 'success' : 'secondary'}>
                                            {item.aktif === '1' ? 'Aktif' : 'Nonaktif'}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>
                                        <div className="flex items-center justify-end gap-1">
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <Link href={route('superadmin.simpanan.show', item.id)}
                                                    aria-label={`Lihat ${item.no_rekening}`}
                                                >
                                                    <Eye className="size-4" />
                                                </Link>
                                            </Button>
                                            <Button variant="ghost" size="icon" className="size-8" asChild>
                                                <Link href={route('superadmin.simpanan.edit', item.id)}
                                                    aria-label={`Edit ${item.no_rekening}`}
                                                >
                                                    <Pencil className="size-4" />
                                                </Link>
                                            </Button>
                                            <DropdownMenu>
                                                <DropdownMenuTrigger asChild>
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        title="Cetak / Lainnya"
                                                        aria-label={`Menu ${item.no_rekening}`}
                                                        className="data-[state=open]:bg-muted"
                                                    >
                                                        <MoreHorizontal className="text-muted-foreground" />
                                                    </Button>
                                                </DropdownMenuTrigger>
                                                <DropdownMenuContent align="end">
                                                    <DropdownMenuLabel className="text-xs text-muted-foreground">
                                                        Cetak
                                                    </DropdownMenuLabel>
                                                    <DropdownMenuItem
                                                        onSelect={() =>
                                                            window.open(route('superadmin.simpanan.cetak-data', item.id), '_blank')
                                                        }
                                                    >
                                                        <Printer />
                                                        Cetak Data Simpanan
                                                    </DropdownMenuItem>
                                                </DropdownMenuContent>
                                            </DropdownMenu>
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
