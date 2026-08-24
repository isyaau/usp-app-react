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
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2 text-base">
                            <FileDown className="size-4 text-brand-600" />
                            Ekspor Data
                        </CardTitle>
                        <CardDescription>
                            Filter berdasarkan rentang tanggal dibuat (format
                            bebas, boleh dikosongkan).
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid grid-cols-2 gap-3">
                            <div className="space-y-2">
                                <Label htmlFor="export-mulai">Dari</Label>
                                <Input
                                    id="export-mulai"
                                    type="date"
                                    value={mulai}
                                    onChange={(e) => setMulai(e.target.value)}
                                />
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="export-sampai">Sampai</Label>
                                <Input
                                    id="export-sampai"
                                    type="date"
                                    value={sampai}
                                    onChange={(e) => setSampai(e.target.value)}
                                />
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <Button variant="outline" size="sm" asChild>
                                <a
                                    id="btn-export-pdf"
                                    href={`${route('superadmin.anggota.export-pdf')}?${exportQuery()}`}
                                >
                                    <FileText /> PDF
                                </a>
                            </Button>
                            <Button variant="outline" size="sm" asChild>
                                <a
                                    id="btn-export-excel"
                                    href={`${route('superadmin.anggota.export-excel')}?${exportQuery()}`}
                                >
                                    <FileSpreadsheet /> Excel
                                </a>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                {/* ============================ Impor ============================= */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2 text-base">
                            <Upload className="size-4 text-brand-600" />
                            Impor Data
                        </CardTitle>
                        <CardDescription>
                            Unggah file Excel sesuai template. Kolom Kelompok &amp;
                            Kantor dicocokkan dari nama.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <a
                            id="link-template"
                            href={route('superadmin.anggota.template')}
                            className="inline-flex items-center gap-1.5 text-sm font-medium text-brand-600 underline-offset-4 hover:underline"
                        >
                            <Sheet className="size-4" />
                            Unduh Template Excel
                        </a>

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
                            />
                            {importForm.errors.file && (
                                <p className="text-sm text-destructive">
                                    {importForm.errors.file}
                                </p>
                            )}
                            <Button
                                type="submit"
                                size="sm"
                                disabled={
                                    importForm.processing || !importForm.data.file
                                }
                                className="bg-brand-600 hover:bg-brand-500"
                            >
                                {importForm.processing && (
                                    <LoaderCircle className="animate-spin" />
                                )}
                                Impor Sekarang
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
                                <TableHead>Alamat</TableHead>
                                <TableHead>Telepon</TableHead>
                                <TableHead>No HP</TableHead>
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
                                        colSpan={9}
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
                                    <TableCell className="max-w-56 truncate">
                                        {item.alamat ?? '-'}
                                    </TableCell>
                                    <TableCell>{item.telepon ?? '-'}</TableCell>
                                    <TableCell>{item.no_hp ?? '-'}</TableCell>
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
