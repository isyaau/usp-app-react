import { Head } from '@inertiajs/react';
import { BarChart3 } from 'lucide-react';
import { PageProps } from '@/types';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { Pagination } from '@/Components/Pagination';
import { ReportFilterBar } from '@/Components/ReportFilterBar';
import { PageHeader } from '@/Components/PageHeader';

type Row = {
    id: number;
    kode: string;
    nama: string;
    jumlah_pinjaman: number;
    total_plafon: number;
    rata_rata: number;
};

type Props = PageProps & {
    data: { data: Row[]; from: number | null; to: number | null; total: number };
    filters: any;
    kantors: { id: number; kode: string; nama_kantor: string }[];
    marketings: { id: number; kode: string; nama: string }[];
    variantTitle?: string;
};

const fmt = (n: number) => 'Rp ' + (n ?? 0).toLocaleString('id-ID');

export default function RekapitulasiPinjamanMarketing({ data, filters, kantors, marketings, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Rekapitulasi Pinjaman Marketing'} />
            <PageHeader title={variantTitle || 'Rekapitulasi Pinjaman Marketing'} description="Rekapitulasi pinjaman per marketing." icon={BarChart3} />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.marketing.rekapitulasi-pinjaman-marketing"
                        filters={filters}
                        kantors={kantors}
                        marketings={marketings}
                        showMarketing
                        showKantor
                        printRoute="superadmin.laporan-cs.marketing.rekapitulasi-pinjaman-marketing.cetak"
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>Kode</TableHead>
                                <TableHead>Nama Marketing</TableHead>
                                <TableHead className="text-right">Jumlah Pinjaman</TableHead>
                                <TableHead className="text-right">Total Plafon</TableHead>
                                <TableHead className="text-right">Rata-Rata</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={6} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data.
                                    </TableCell>
                                </TableRow>
                            )}
                            {data.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">{data.from !== null ? data.from + i : i + 1}</TableCell>
                                    <TableCell className="font-mono">{item.kode}</TableCell>
                                    <TableCell className="font-medium">{item.nama}</TableCell>
                                    <TableCell className="text-right tabular-nums">{item.jumlah_pinjaman}</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.total_plafon)}</TableCell>
                                    <TableCell className="text-right font-semibold tabular-nums">{fmt(item.rata_rata)}</TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>

                {data.data.length > 0 && (
                    <div className="px-5">
                        <Pagination data={data} />
                    </div>
                )}
            </Card>
        </AuthenticatedLayout>
    );
}
