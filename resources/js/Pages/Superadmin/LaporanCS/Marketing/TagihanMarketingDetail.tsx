import { Head } from '@inertiajs/react';
import { FileText } from 'lucide-react';
import { PageProps } from '@/types';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { Pagination } from '@/Components/Pagination';
import { ReportFilterBar } from '@/Components/ReportFilterBar';
import { PageHeader } from '@/Components/PageHeader';

type Row = {
    id: number;
    no_pinjaman: string;
    tanggal: string | null;
    no_anggota: string;
    nama_anggota: string;
    produk: string;
    marketing: string;
    plafon: number;
    terbayar: number;
    sisa_pokok: number;
    angsuran_ke: number;
    jangka_waktu: number;
};

type Props = PageProps & {
    data: { data: Row[]; from: number | null; to: number | null; total: number };
    filters: any;
    kantors: { id: number; kode: string; nama_kantor: string }[];
    marketings: { id: number; kode: string; nama: string }[];
    variantTitle?: string;
};

const fmt = (n: number) => 'Rp ' + (n ?? 0).toLocaleString('id-ID');

export default function TagihanMarketingDetail({ data, filters, kantors, marketings, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Laporan Tagihan Marketing Detail'} />
            <PageHeader title={variantTitle || 'Laporan Tagihan Marketing Detail'} description="Detail tagihan pinjaman per marketing." icon={FileText} />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.marketing.tagihan-marketing-detail"
                        filters={filters}
                        kantors={kantors}
                        marketings={marketings}
                        showMarketing
                        showKantor
                        printRoute="superadmin.laporan-cs.marketing.tagihan-marketing-detail.cetak"
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Pinjaman</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama Anggota</TableHead>
                                <TableHead>Produk</TableHead>
                                <TableHead className="text-right">Plafon</TableHead>
                                <TableHead className="text-right">Terbayar</TableHead>
                                <TableHead className="text-right">Sisa Pokok</TableHead>
                                <TableHead className="text-center">Angsuran</TableHead>
                                <TableHead>Marketing</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={11} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data.
                                    </TableCell>
                                </TableRow>
                            )}
                            {data.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">{data.from !== null ? data.from + i : i + 1}</TableCell>
                                    <TableCell className="font-mono">{item.no_pinjaman}</TableCell>
                                    <TableCell>{item.tanggal}</TableCell>
                                    <TableCell>{item.no_anggota}</TableCell>
                                    <TableCell>{item.nama_anggota}</TableCell>
                                    <TableCell>{item.produk}</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.plafon)}</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.terbayar)}</TableCell>
                                    <TableCell className="text-right font-semibold tabular-nums">{fmt(item.sisa_pokok)}</TableCell>
                                    <TableCell className="text-center">{item.angsuran_ke}/{item.jangka_waktu}</TableCell>
                                    <TableCell>{item.marketing}</TableCell>
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
