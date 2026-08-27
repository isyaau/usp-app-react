import { Head } from '@inertiajs/react';
import { PieChart } from 'lucide-react';
import { PageProps } from '@/types';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { Pagination } from '@/Components/Pagination';
import { ReportFilterBar } from '@/Components/ReportFilterBar';
import { PageHeader } from '@/Components/PageHeader';

type Row = {
    id: number;
    produk: string;
    kantor: string;
    jumlah: number;
    total_nominal: number;
    total_bagi_hasil: number;
};

type Props = PageProps & {
    data: { data: Row[]; from: number | null; to: number | null; total: number };
    filters: any;
    kantors: { id: number; kode: string; nama_kantor: string }[];
    variantTitle?: string;
};

const fmt = (n: number) => 'Rp ' + (n ?? 0).toLocaleString('id-ID');

export default function RekapitulasiBagiHasil({ data, filters, kantors, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Rekapitulasi Pengeluaran Bagi Hasil Simpanan Berjangka'} />
            <PageHeader
                title={variantTitle || 'Rekapitulasi Pengeluaran Bagi Hasil Simpanan Berjangka'}
                description="Rekapitulasi pengeluaran bagi hasil per produk dan kantor."
                icon={PieChart}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.simpanan-berjangka.rekapitulasi-bagi-hasil"
                        filters={filters}
                        kantors={kantors}
                        showKantor
                        printRoute="superadmin.laporan-cs.simpanan-berjangka.rekapitulasi-bagi-hasil.cetak"
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>Produk</TableHead>
                                <TableHead>Kantor</TableHead>
                                <TableHead className="text-right">Jumlah</TableHead>
                                <TableHead className="text-right">Total Nominal</TableHead>
                                <TableHead className="text-right">Total Bagi Hasil</TableHead>
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
                                <TableRow key={item.id + '-' + i}>
                                    <TableCell className="text-muted-foreground">{data.from !== null ? data.from + i : i + 1}</TableCell>
                                    <TableCell className="font-medium">{item.produk}</TableCell>
                                    <TableCell>{item.kantor}</TableCell>
                                    <TableCell className="text-right tabular-nums">{item.jumlah}</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.total_nominal)}</TableCell>
                                    <TableCell className="text-right font-semibold tabular-nums">{fmt(item.total_bagi_hasil)}</TableCell>
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
