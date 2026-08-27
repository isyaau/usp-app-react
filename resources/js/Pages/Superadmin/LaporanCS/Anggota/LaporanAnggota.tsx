import { FileText, Printer, Users } from 'lucide-react';
import { Head } from '@inertiajs/react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { ReportFilterBar } from '@/Components/ReportFilterBar';
import { Button } from '@/Components/ui/button';
import { Card } from '@/Components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import type { AnggotaRow, Paginated } from '@/types/models';

interface Filters {
    search?: string;
    kelompok_id?: string;
    kantor_id?: string;
    mulai?: string;
    sampai?: string;
}

interface KelompokOption {
    id: number;
    kode: string;
    nama: string;
}

interface KantorOption {
    id: number;
    kode: string;
    nama_kantor: string;
}

interface LaporanAnggotaRow extends AnggotaRow {
    tgl_daftar?: string | null;
}

interface Props {
    data: Paginated<AnggotaRow>;
    filters: Filters;
    kelompoks: KelompokOption[];
    kantors: KantorOption[];
    variantTitle: string;
}

const PRINT_ROUTE = 'superadmin.laporan-cs.anggota.laporan.cetak';

export default function LaporanAnggota({ data, filters, kelompoks, kantors, variantTitle }: Props) {
    const handlePrint = () => {
        const params: Record<string, string> = {};
        Object.entries(filters).forEach(([k, v]) => {
            if (v !== '' && v != null) params[k] = String(v);
        });
        window.open(route(PRINT_ROUTE, params), '_blank');
    };

    return (
        <AuthenticatedLayout>
            <Head title="Laporan Anggota" />

            <PageHeader
                title="Laporan Anggota"
                description={variantTitle || 'Daftar seluruh anggota koperasi.'}
                icon={Users}
            >
                <Button variant="outline" onClick={handlePrint}>
                    <Printer className="size-4" />
                    Cetak
                </Button>
            </PageHeader>

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.anggota.laporan"
                        filters={filters}
                        kelompoks={kelompoks}
                        kantors={kantors}
                        showKelompok
                        showKantor
                        showDateRange
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead>Kelompok</TableHead>
                                <TableHead>Kantor</TableHead>
                                <TableHead>Alamat</TableHead>
                                <TableHead>Email</TableHead>
                                <TableHead>Telepon</TableHead>
                                <TableHead>Tgl Daftar</TableHead>
                                <TableHead>Status</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={10} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data anggota.
                                    </TableCell>
                                </TableRow>
                            )}
                            {data.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {data.from !== null ? data.from + i : i + 1}
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-mono text-xs">{item.no_anggota}</span>
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{item.nama}</span>
                                    </TableCell>
                                    <TableCell>{item.kelompok?.nama ?? '—'}</TableCell>
                                    <TableCell>{item.kantor?.nama_kantor ?? '—'}</TableCell>
                                    <TableCell className="max-w-48 truncate text-muted-foreground">
                                        {item.alamat ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.email ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.telepon ?? item.no_hp ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {(item as LaporanAnggotaRow).tgl_daftar ?? '—'}
                                    </TableCell>
                                    <TableCell>
                                        <span
                                            className={
                                                item.status
                                                    ? 'rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700'
                                                    : 'rounded-md bg-muted px-2 py-0.5 text-xs font-medium text-muted-foreground'
                                            }
                                        >
                                            {item.status ? 'Aktif' : 'Nonaktif'}
                                        </span>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>

                <div className="border-t px-5 pt-4">
                    <Pagination
                        links={data.links}
                        currentPage={data.current_page}
                        lastPage={data.last_page}
                        from={data.from}
                        to={data.to}
                        total={data.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
