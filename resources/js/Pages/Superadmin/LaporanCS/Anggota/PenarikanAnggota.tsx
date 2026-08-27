import { ArrowUpFromLine, Printer } from 'lucide-react';
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
import type { Paginated } from '@/types/models';

interface Filters {
    search?: string;
    kantor_id?: string;
    mulai?: string;
    sampai?: string;
}

interface KantorOption {
    id: number;
    kode: string;
    nama_kantor: string;
}

interface TarikanRow {
    id: number;
    no_transaksi: string;
    tgl_transaksi: string;
    nominal: string | number;
    status: string;
    anggota?: { id: number; no_anggota: string; nama: string } | null;
    kantor?: { id: number; nama_kantor: string } | null;
}

interface Props {
    data: Paginated<TarikanRow>;
    filters: Filters;
    kantors: KantorOption[];
    variantTitle: string;
}

const PRINT_ROUTE = 'superadmin.laporan-cs.anggota.penarikan.cetak';

const fmt = (v: string | number) => new Intl.NumberFormat('id-ID').format(Number(v));

export default function PenarikanAnggota({ data, filters, kantors, variantTitle }: Props) {
    const handlePrint = () => {
        const params: Record<string, string> = {};
        Object.entries(filters).forEach(([k, v]) => {
            if (v !== '' && v != null) params[k] = String(v);
        });
        window.open(route(PRINT_ROUTE, params), '_blank');
    };

    return (
        <AuthenticatedLayout>
            <Head title="Penarikan Anggota" />

            <PageHeader
                title="Penarikan Anggota"
                description={variantTitle || 'Daftar transaksi penarikan dana anggota.'}
                icon={ArrowUpFromLine}
            >
                <Button variant="outline" onClick={handlePrint}>
                    <Printer className="size-4" />
                    Cetak
                </Button>
            </PageHeader>

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.anggota.penarikan"
                        filters={filters}
                        kantors={kantors}
                        showKantor
                        showDateRange
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Transaksi</TableHead>
                                <TableHead>Tgl Transaksi</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama Anggota</TableHead>
                                <TableHead className="text-right">Nominal</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Kantor</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={8} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data penarikan.
                                    </TableCell>
                                </TableRow>
                            )}
                            {data.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {data.from !== null ? data.from + i : i + 1}
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-mono text-xs">{item.no_transaksi}</span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.tgl_transaksi}
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-mono text-xs">
                                            {item.anggota?.no_anggota ?? '—'}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{item.anggota?.nama ?? '—'}</span>
                                    </TableCell>
                                    <TableCell className="text-right font-medium">
                                        {fmt(item.nominal)}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 text-xs font-medium text-muted-foreground">
                                            {item.status}
                                        </span>
                                    </TableCell>
                                    <TableCell>{item.kantor?.nama_kantor ?? '—'}</TableCell>
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
