import { Head } from '@inertiajs/react';
import { CalendarCheck } from 'lucide-react';
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
    jml_transaksi: number;
    total_terkumpul: number;
    target: number;
    persentase: number;
    periode: string;
};

type Props = PageProps & {
    data: { data: Row[]; from: number | null; to: number | null; total: number };
    filters: any;
    kantors: { id: number; kode: string; nama_kantor: string }[];
    marketings: { id: number; kode: string; nama: string }[];
    variantTitle?: string;
};

const fmt = (n: number) => 'Rp ' + (n ?? 0).toLocaleString('id-ID');

export default function PencapaianAngsuranBulanan({ data, filters, kantors, marketings, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Laporan Persentase Pencapaian Angsuran Bulanan'} />
            <PageHeader
                title={variantTitle || 'Laporan Persentase Pencapaian Angsuran Bulanan'}
                description="Persentase pencapaian angsuran bulanan per marketing."
                icon={CalendarCheck}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.marketing.pencapaian-angsuran-bulanan"
                        filters={filters}
                        kantors={kantors}
                        marketings={marketings}
                        showMarketing
                        showKantor
                        showDateRange
                        printRoute="superadmin.laporan-cs.marketing.pencapaian-angsuran-bulanan.cetak"
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>Kode</TableHead>
                                <TableHead>Nama Marketing</TableHead>
                                <TableHead className="text-center">Jml Transaksi</TableHead>
                                <TableHead className="text-right">Total Terkumpul</TableHead>
                                <TableHead className="text-right">Target</TableHead>
                                <TableHead className="text-right">Persentase</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={7} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data.
                                    </TableCell>
                                </TableRow>
                            )}
                            {data.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">{data.from !== null ? data.from + i : i + 1}</TableCell>
                                    <TableCell className="font-mono">{item.kode}</TableCell>
                                    <TableCell className="font-medium">{item.nama}</TableCell>
                                    <TableCell className="text-center">{item.jml_transaksi}</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.total_terkumpul)}</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.target)}</TableCell>
                                    <TableCell className="text-right font-semibold tabular-nums">{item.persentase}%</TableCell>
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
