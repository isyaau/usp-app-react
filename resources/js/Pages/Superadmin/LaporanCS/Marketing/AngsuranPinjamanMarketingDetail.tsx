import { Head } from '@inertiajs/react';
import { ListChecks } from 'lucide-react';
import { PageProps } from '@/types';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { Pagination } from '@/Components/Pagination';
import { ReportFilterBar } from '@/Components/ReportFilterBar';
import { PageHeader } from '@/Components/PageHeader';

type Row = {
    id: number;
    no_transaksi: string;
    tanggal: string | null;
    no_pinjaman: string;
    no_anggota: string;
    nama_anggota: string;
    produk: string;
    marketing: string;
    angsuran_ke: number;
    nominal_pokok: number;
    nominal_bunga: number;
    total_angsuran: number;
    denda: number;
};

type Props = PageProps & {
    data: { data: Row[]; from: number | null; to: number | null; total: number };
    filters: any;
    kantors: { id: number; kode: string; nama_kantor: string }[];
    marketings: { id: number; kode: string; nama: string }[];
    variantTitle?: string;
};

const fmt = (n: number) => 'Rp ' + (n ?? 0).toLocaleString('id-ID');

export default function AngsuranPinjamanMarketingDetail({ data, filters, kantors, marketings, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Laporan Angsuran Pinjaman Marketing Detail'} />
            <PageHeader
                title={variantTitle || 'Laporan Angsuran Pinjaman Marketing Detail'}
                description="Detail angsuran pinjaman per marketing."
                icon={ListChecks}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.marketing.angsuran-pinjaman-marketing-detail"
                        filters={filters}
                        kantors={kantors}
                        marketings={marketings}
                        showMarketing
                        showKantor
                        showDateRange
                        printRoute="superadmin.laporan-cs.marketing.angsuran-pinjaman-marketing-detail.cetak"
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Transaksi</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>No Pinjaman</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama Anggota</TableHead>
                                <TableHead>Produk</TableHead>
                                <TableHead>Marketing</TableHead>
                                <TableHead className="text-center">Angs. Ke</TableHead>
                                <TableHead className="text-right">Pokok</TableHead>
                                <TableHead className="text-right">Bunga</TableHead>
                                <TableHead className="text-right">Total</TableHead>
                                <TableHead className="text-right">Denda</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={13} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data.
                                    </TableCell>
                                </TableRow>
                            )}
                            {data.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">{data.from !== null ? data.from + i : i + 1}</TableCell>
                                    <TableCell className="font-mono">{item.no_transaksi}</TableCell>
                                    <TableCell>{item.tanggal}</TableCell>
                                    <TableCell className="font-mono">{item.no_pinjaman}</TableCell>
                                    <TableCell>{item.no_anggota}</TableCell>
                                    <TableCell>{item.nama_anggota}</TableCell>
                                    <TableCell>{item.produk}</TableCell>
                                    <TableCell>{item.marketing}</TableCell>
                                    <TableCell className="text-center">{item.angsuran_ke}</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.nominal_pokok)}</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.nominal_bunga)}</TableCell>
                                    <TableCell className="text-right font-semibold tabular-nums">{fmt(item.total_angsuran)}</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.denda)}</TableCell>
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
