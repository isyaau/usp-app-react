import { Head } from '@inertiajs/react';
import { PieChart } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { ReportFilterBar } from '@/Components/ReportFilterBar';
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

interface GrafikRow {
    id: number;
    bulan: string;
    total_setoran: string | number;
    total_penarikan: string | number;
    saldo_akhir: string | number;
}

interface Props {
    data: Paginated<GrafikRow>;
    filters: Record<string, string>;
    kantors?: Array<{ id: number; kode: string; nama_kantor: string }>;
    jenisList?: Array<{ id: number; kode: string; nama: string }>;
    variantTitle: string;
}

export default function RekapitulasiSimpananGrafik({ data, filters, kantors, jenisList, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Rekapitulasi Simpanan Grafik'} />

            <PageHeader
                title={variantTitle || 'Rekapitulasi Simpanan Grafik'}
                description="Rekapitulasi data simpanan dalam bentuk grafik."
                icon={PieChart}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.simpanan.rekapitulasi-grafik"
                        filters={filters}
                        kantors={kantors}
                        showKantor
                        showDateRange
                        printRoute="superadmin.laporan-cs.simpanan.rekapitulasi-grafik.cetak"
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>Bulan</TableHead>
                                <TableHead>Total Setoran</TableHead>
                                <TableHead>Total Penarikan</TableHead>
                                <TableHead>Saldo Akhir</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={5} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data.
                                    </TableCell>
                                </TableRow>
                            )}
                            {data.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {data.from !== null ? data.from + i : i + 1}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.bulan ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {Number(item.total_setoran).toLocaleString('id-ID')}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {Number(item.total_penarikan).toLocaleString('id-ID')}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {Number(item.saldo_akhir).toLocaleString('id-ID')}
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
