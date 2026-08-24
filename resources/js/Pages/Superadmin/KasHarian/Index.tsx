import { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import {
    Plus,
    Search,
    MoreHorizontal,
    Edit,
    Trash2,
    Calendar,
    DollarSign,
    ArrowDownLeft,
    ArrowUpRight,
    FileDown,
    FileSpreadsheet,
    FileText,
    Sheet,
    Upload,
    LoaderCircle,
    AlertCircle,
    Eye,
} from 'lucide-react';
import { format } from 'date-fns';
import { id } from 'date-fns/locale';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { ConfirmDelete } from '@/Components/ConfirmDelete';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
} from '@/Components/ui/dropdown-menu';

interface KasHarian {
    id: number;
    tanggal: string;
    kas_awal: number;
    kas_masuk: number;
    kas_keluar: number;
    kas_akhir: number;
    user: {
        name: string;
    };
    created_at: string;
}

interface Props {
    kasHarian: {
        data: KasHarian[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: {
        search?: string;
    };
}

export default function Index({ kasHarian, filters }: Props) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [perPage, setPerPage] = useState(String(kasHarian.per_page));
    const [mulai, setMulai] = useState('');
    const [sampai, setSampai] = useState('');

    const importForm = useForm<{ file: File | null }>({ file: null });

    const formatCurrency = (value: number | string) => {
        const num = typeof value === 'string' ? parseFloat(value) : value;
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(isNaN(num) ? 0 : num);
    };

    const formatDate = (dateString: string) => {
        return format(new Date(dateString), 'dd MMM yyyy', { locale: id });
    };

    const apply = () => {
        router.get(
            route('superadmin.kas-harian'),
            { search, per_page: perPage },
            { preserveState: true },
        );
    };

    const exportQuery = () =>
        new URLSearchParams({
            mulai: mulai || '',
            sampai: sampai || '',
        }).toString();

    const toNum = (value: number | string): number => {
        const num = typeof value === 'string' ? parseFloat(value) : value;
        return isNaN(num) ? 0 : num;
    };

    const totalKasAwal = kasHarian.data.reduce((sum, item) => sum + toNum(item.kas_awal), 0);
    const totalKasMasuk = kasHarian.data.reduce((sum, item) => sum + toNum(item.kas_masuk), 0);
    const totalKasKeluar = kasHarian.data.reduce((sum, item) => sum + toNum(item.kas_keluar), 0);
    const totalKasAkhir = kasHarian.data.reduce((sum, item) => sum + toNum(item.kas_akhir), 0);

    return (
        <AuthenticatedLayout>
            <Head title="Kas Harian - Superadmin" />

            <PageHeader
                title="Kas Harian"
                description="Kelola pencatatan kas harian (Kas Awal, Masuk, Keluar, Akhir)"
                icon={DollarSign}
            >
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route('superadmin.kas-harian.create')} preload="hover">
                        <Plus className="size-4" />
                        Tambah Kas Harian
                    </Link>
                </Button>
            </PageHeader>

            <div className="grid gap-5 lg:grid-cols-4">
                {/* ============================ Summary Cards ============================ */}
                <Card className="border-border/50 shadow-sm hover:shadow-md transition-shadow duration-200 lg:col-span-1">
                    <CardHeader className="pb-3">
                        <CardTitle className="flex items-center gap-2 text-base font-semibold">
                            <div className="p-2 rounded-lg bg-brand-50 text-brand-600">
                                <DollarSign className="size-4" />
                            </div>
                            Total Kas Awal
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-0">
                        <p className="text-2xl font-bold font-mono tabular-nums text-foreground">
                            {formatCurrency(totalKasAwal)}
                        </p>
                    </CardContent>
                </Card>

                <Card className="border-border/50 shadow-sm hover:shadow-md transition-shadow duration-200 lg:col-span-1">
                    <CardHeader className="pb-3">
                        <CardTitle className="flex items-center gap-2 text-base font-semibold text-green-600">
                            <div className="p-2 rounded-lg bg-green-50 text-green-600">
                                <ArrowDownLeft className="size-4" />
                            </div>
                            Total Kas Masuk
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-0">
                        <p className="text-2xl font-bold font-mono tabular-nums text-green-600">
                            {formatCurrency(totalKasMasuk)}
                        </p>
                    </CardContent>
                </Card>

                <Card className="border-border/50 shadow-sm hover:shadow-md transition-shadow duration-200 lg:col-span-1">
                    <CardHeader className="pb-3">
                        <CardTitle className="flex items-center gap-2 text-base font-semibold text-red-600">
                            <div className="p-2 rounded-lg bg-red-50 text-red-600">
                                <ArrowUpRight className="size-4" />
                            </div>
                            Total Kas Keluar
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-0">
                        <p className="text-2xl font-bold font-mono tabular-nums text-red-600">
                            {formatCurrency(totalKasKeluar)}
                        </p>
                    </CardContent>
                </Card>

                <Card className="border-border/50 shadow-sm hover:shadow-md transition-shadow duration-200 lg:col-span-1">
                    <CardHeader className="pb-3">
                        <CardTitle className="flex items-center gap-2 text-base font-semibold">
                            <div className="p-2 rounded-lg bg-purple-50 text-purple-600">
                                <DollarSign className="size-4" />
                            </div>
                            Total Kas Akhir
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="pt-0">
                        <p className="text-2xl font-bold font-mono tabular-nums text-purple-600">
                            {formatCurrency(totalKasAkhir)}
                        </p>
                    </CardContent>
                </Card>
            </div>

            {/* ============================ Tabel ============================== */}
            <Card className="mt-5">
                <CardHeader className="flex-row flex-wrap items-center justify-between gap-3">
                    <CardTitle className="text-base">Daftar Kas Harian</CardTitle>
                    <div className="flex items-center gap-2">
                        <div className="relative">
                            <Search className="absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                id="search-input"
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                onKeyDown={(e) => e.key === 'Enter' && apply()}
                                placeholder="Cari tanggal…"
                                className="w-56 pl-8"
                            />
                        </div>
                        <select
                            aria-label="Baris per halaman"
                            value={perPage}
                            onChange={(e) => {
                                setPerPage(e.target.value);
                                router.get(
                                    route('superadmin.kas-harian'),
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
                            <TableRow className="bg-muted">
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>
                                    <Calendar className="h-4 w-4 inline mr-1" />
                                    Tanggal
                                </TableHead>
                                <TableHead className="text-right">
                                    <DollarSign className="h-4 w-4 inline mr-1" />
                                    Kas Awal
                                </TableHead>
                                <TableHead className="text-right">
                                    <ArrowDownLeft className="h-4 w-4 inline mr-1 text-green-600" />
                                    Kas Masuk
                                </TableHead>
                                <TableHead className="text-right">
                                    <ArrowUpRight className="h-4 w-4 inline mr-1 text-red-600" />
                                    Kas Keluar
                                </TableHead>
                                <TableHead className="text-right font-semibold">
                                    Kas Akhir
                                </TableHead>
                                <TableHead className="w-16">Petugas</TableHead>
                                <TableHead className="w-52 text-center">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {kasHarian.data.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={8} className="py-10 text-center text-muted-foreground">
                                        Belum ada data kas harian
                                    </TableCell>
                                </TableRow>
                            ) : (
                                kasHarian.data.map((item, index) => (
                                    <TableRow key={item.id} className="hover:bg-accent/50">
                                        <TableCell className="font-mono text-sm text-muted-foreground">
                                            {(kasHarian.from ?? 0) + index}
                                        </TableCell>
                                        <TableCell className="font-medium">{formatDate(item.tanggal)}</TableCell>
                                        <TableCell className="text-right font-mono">{formatCurrency(item.kas_awal)}</TableCell>
                                        <TableCell className="text-right font-mono text-green-600">{formatCurrency(item.kas_masuk)}</TableCell>
                                        <TableCell className="text-right font-mono text-red-600">{formatCurrency(item.kas_keluar)}</TableCell>
                                        <TableCell className="text-right font-mono font-semibold text-lg text-purple-600">{formatCurrency(item.kas_akhir)}</TableCell>
                                        <TableCell className="text-sm">{item.user?.name ?? '-'}</TableCell>
                                        <TableCell className="space-x-1 text-right whitespace-nowrap">
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                asChild
                                                title="Detail"
                                            >
                                                <Link href={route('superadmin.kas-harian.show', item.id)}>
                                                    <Eye className="size-4" />
                                                </Link>
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                asChild
                                                title="Edit"
                                            >
                                                <Link href={route('superadmin.kas-harian.edit', item.id)}>
                                                    <Edit className="size-4" />
                                                </Link>
                                            </Button>
                                            <ConfirmDelete
                                                routeName="superadmin.kas-harian.destroy"
                                                id={item.id}
                                                label={formatDate(item.tanggal)}
                                                description={`Data kas harian tanggal ${formatDate(item.tanggal)} akan dihapus permanen.`}
                                            />
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>

                    <Pagination
                        links={kasHarian.links}
                        currentPage={kasHarian.current_page}
                        lastPage={kasHarian.last_page}
                        from={kasHarian.from}
                        to={kasHarian.to}
                        total={kasHarian.total}
                    />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}