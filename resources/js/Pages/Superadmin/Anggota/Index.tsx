import { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import {
    Eye,
    FileDown,
    FileSpreadsheet,
    FileText,
    LoaderCircle,
    Pencil,
    Search,
    Sheet,
    Upload,
    Users,
    AlertCircle,
} from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { ConfirmDelete } from '@/Components/ConfirmDelete';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import type { Paginated } from '@/types/models';
import type { AnggotaRow } from '@/types/models';

interface Props {
    anggota: Paginated<AnggotaRow>;
    filters: { search: string };
}

export default function AnggotaIndex({ anggota, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [perPage, setPerPage] = useState(String(anggota.per_page));
    const [mulai, setMulai] = useState('');
    const [sampai, setSampai] = useState('');

    const importForm = useForm<{ file: File | null }>({ file: null });

    const apply = () => {
        router.get(
            route('superadmin.anggota'),
            { search, per_page: perPage },
            { preserveState: true },
        );
    };

    const exportQuery = () =>
        new URLSearchParams({
            mulai: mulai || '',
            sampai: sampai || '',
        }).toString();

    return (
        <AuthenticatedLayout>
            <Head title="Data Anggota" />

            <PageHeader
                title="Data Anggota"
                description="Kelola data anggota koperasi."
                icon={Users}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route('superadmin.anggota.create')} preload="hover">
                        Tambah Anggota
                    </Link>
                </Button>
            </PageHeader>

            <div className="grid gap-5 lg:grid-cols-3">
                {/* ============================ Ekspor ============================ */}
                <Card className="border-border/50 shadow-sm hover:shadow-md transition-shadow duration-200">
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
                            Ekspor data anggota ke PDF atau Excel dengan filter tanggal opsional.
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
                                    placeholder="DD/MM/YYYY"
                                    title="Opsional: filter data dari tanggal ini"
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
                                    placeholder="DD/MM/YYYY"
                                    title="Opsional: filter data sampai tanggal ini"
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
                                <a
                                    id="btn-export-pdf"
                                    href={`${route('superadmin.anggota.export-pdf')}?${exportQuery()}`}
                                >
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
                                <a
                                    id="btn-export-excel"
                                    href={`${route('superadmin.anggota.export-excel')}?${exportQuery()}`}
                                >
                                    <FileSpreadsheet className="size-3.5" />
                                    Excel
                                </a>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                {/* ============================ Impor ============================= */}
                <Card className="border-border/50 shadow-sm hover:shadow-md transition-shadow duration-200 lg:col-span-2">
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
                            Impor data anggota dari file Excel. Unduh template terlebih dahulu untuk format yang benar.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4 pt-0">
                        <div className="flex items-center gap-3 p-3 rounded-lg bg-muted/30 border border-border/50">
                            <Sheet className="size-5 text-muted-foreground shrink-0" />
                            <div className="flex-1 min-w-0">
                                <p className="text-sm font-medium text-foreground">Template Impor Anggota</p>
                                <p className="text-xs text-muted-foreground">Kolom: No Anggota, Nama, Alamat, Telepon, No HP, Kelompok, Kantor, Status</p>
                            </div>
                            <Button
                                variant="ghost"
                                size="sm"
                                asChild
                                className="shrink-0 text-brand-600 hover:text-brand-700 hover:bg-brand-50 gap-1.5"
                            >
                                <a
                                    id="link-template"
                                    href={route('superadmin.anggota.template')}
                                    className="flex items-center gap-1.5"
                                >
                                    <FileDown className="size-3.5" />
                                    Unduh Template (.xlsx)
                                </a>
                            </Button>
                        </div>

                        <form
                            onSubmit={(e) => {
                                e.preventDefault();
                                importForm.post(
                                    route('superadmin.anggota.import'),
                                    { forceFormData: true },
                                );
                            }}
                            className="space-y-3"
                        >
                            <div className="relative">
                                <Input
                                    id="import-file"
                                    type="file"
                                    accept=".xlsx,.csv"
                                    onChange={(e) =>
                                        importForm.setData(
                                            'file',
                                            e.target.files?.[0] ?? null,
                                        )
                                    }
                                    className="file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer"
                                />
                                <div className="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div className="text-center px-4">
                                        <p className="text-sm font-medium text-muted-foreground">
                                            Seret file Excel (.xlsx) ke sini
                                        </p>
                                        <p className="text-xs text-muted-foreground/70 mt-0.5">
                                            atau klik untuk memilih file
                                        </p>
                                    </div>
                                </div>
                            </div>
                            {importForm.errors.file && (
                                <p className="text-sm text-destructive flex items-center gap-1.5">
                                    <AlertCircle className="size-4" />
                                    {importForm.errors.file}
                                </p>
                            )}
                            <Button
                                type="submit"
                                size="sm"
                                disabled={
                                    importForm.processing || !importForm.data.file
                                }
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

            {/* ============================ Tabel ============================== */}
            <Card className="mt-5">
                <CardHeader className="flex-row flex-wrap items-center justify-between gap-3">
                    <CardTitle className="text-base">Daftar Anggota</CardTitle>
                    <div className="flex items-center gap-2">
                        <div className="relative">
                            <Search className="absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                id="search-input"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                onKeyDown={(e) =>
                                    e.key === 'Enter' && apply()
                                }
                                placeholder="Cari nama / no anggota…"
                                className="w-56 pl-8"
                            />
                        </div>
                        <select
                            aria-label="Baris per halaman"
                            value={perPage}
                            onChange={(e) => {
                                setPerPage(e.target.value);
                                router.get(
                                    route('superadmin.anggota'),
                                    { search, per_page: e.target.value },
                                    { preserveState: true },
                                );
                            }}
                            className="border-input h-9 rounded-md border bg-card px-2 text-sm shadow-xs outline-none"
                        >
                            {['10', '25', '50', '100'].map((n) => (
                                <option key={n} value={n}>
                                    {n}
                                </option>
                            ))}
                        </select>
                    </div>
                </CardHeader>
                <CardContent>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead>Kontak</TableHead>
                                <TableHead>Foto</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="text-right">
                                    Aksi
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {anggota.data.length === 0 && (
                                <TableRow>
                                    <TableCell
                                        colSpan={7}
                                        className="py-10 text-center text-muted-foreground"
                                    >
                                        Tidak ada data anggota.
                                    </TableCell>
                                </TableRow>
                            )}
                            {anggota.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell>
                                        {(anggota.current_page - 1) *
                                            anggota.per_page +
                                            i +
                                            1}
                                    </TableCell>
                                    <TableCell className="font-mono text-xs">
                                        {item.no_anggota}
                                    </TableCell>
                                    <TableCell className="font-medium">
                                        {item.nama}
                                    </TableCell>
                                    <TableCell className="font-mono text-xs">
                                        {[
                                            item.telepon,
                                            item.no_hp,
                                        ]
                                            .filter(Boolean)
                                            .join(' / ') || '-'}
                                    </TableCell>
                                    <TableCell>
                                        {item.foto ? (
                                            <img
                                                src={`/storage/${item.foto}`}
                                                alt={item.nama}
                                                className="size-9 rounded-md object-cover"
                                            />
                                        ) : (
                                            <span className="text-muted-foreground">
                                                -
                                            </span>
                                        )}
                                    </TableCell>
                                    <TableCell>
                                        {Number(item.status) === 1 ? (
                                            <Badge className="bg-emerald-600 hover:bg-emerald-600">
                                                Aktif
                                            </Badge>
                                        ) : (
                                            <Badge variant="secondary">
                                                Berhenti
                                            </Badge>
                                        )}
                                    </TableCell>
                                    <TableCell className="space-x-1 text-right whitespace-nowrap">
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            asChild
                                            title="Detail"
                                        >
                                            <Link
                                                href={route(
                                                    'superadmin.anggota.show',
                                                    item.id,
                                                )}
                                            >
                                                <Eye className="size-4" />
                                            </Link>
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            asChild
                                            title="Edit"
                                        >
                                            <Link
                                                href={route(
                                                    'superadmin.anggota.edit',
                                                    item.id,
                                                )}
                                            >
                                                <Pencil className="size-4" />
                                            </Link>
                                        </Button>
                                        <ConfirmDelete
                                            routeName="superadmin.anggota.destroy"
                                            id={item.id}
                                            label={item.nama}
                                            description={`Anggota "${item.nama}" beserta fotonya akan dihapus permanen.`}
                                        />
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>

                    <Pagination
                        links={anggota.links}
                        currentPage={anggota.current_page}
                        lastPage={anggota.last_page}
                        from={anggota.from}
                        to={anggota.to}
                        total={anggota.total}
                    />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
