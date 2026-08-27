import { Head } from '@inertiajs/react';
import { Coins } from 'lucide-react';
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
    rate_insentif: number;
    insentif: number;
};

type Props = PageProps & {
    data: { data: Row[]; from: number | null; to: number | null; total: number };
    filters: any;
    kantors: { id: number; kode: string; nama_kantor: string }[];
    marketings: { id: number; kode: string; nama: string }[];
    variantTitle?: string;
};

const fmt = (n: number) => 'Rp ' + (n ?? 0).toLocaleString('id-ID');

export default function InsentifMarketingAngsuranPinjaman({ data, filters, kantors, marketings, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Laporan Insentif Marketing Angsuran Pinjaman'} />
            <PageHeader
                title={variantTitle || 'Laporan Insentif Marketing Angsuran Pinjaman'}
                description="Insentif marketing dari angsuran pinjaman."
                icon={Coins}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.marketing.insentif-marketing-angsuran-pinjaman"
                        filters={filters}
                        kantors={kantors}
                        marketings={marketings}
                        showMarketing
                        showKantor
                        showDateRange
                        printRoute="superadmin.laporan-cs.marketing.insentif-marketing-angsuran-pinjaman.cetak"
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
                                <TableHead className="text-right">% Insentif</TableHead>
                                <TableHead className="text-right">Insentif</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={12} className="h-32 text-center text-muted-foreground">
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
                                    <TableCell className="text-right tabular-nums">{item.rate_insentif || 0}%</TableCell>
                                    <TableCell className="text-right font-semibold tabular-nums">{fmt(item.insentif)}</TableCell>
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
