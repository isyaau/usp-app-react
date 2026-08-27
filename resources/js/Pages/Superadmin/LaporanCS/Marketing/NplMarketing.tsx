import { Head } from '@inertiajs/react';
import { AlertTriangle } from 'lucide-react';
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
    sisa_pokok: number;
    angsuran_ke: number;
    jangka_waktu: number;
    tanggal_tunggakan: string | null;
    hari_tunggakan: number;
};

type Props = PageProps & {
    data: { data: Row[]; from: number | null; to: number | null; total: number };
    filters: any;
    kantors: { id: number; kode: string; nama_kantor: string }[];
    marketings: { id: number; kode: string; nama: string }[];
    variantTitle?: string;
};

const fmt = (n: number) => 'Rp ' + (n ?? 0).toLocaleString('id-ID');

export default function NplMarketing({ data, filters, kantors, marketings, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Laporan NPL Marketing'} />
            <PageHeader title={variantTitle || 'Laporan NPL Marketing'} description="Pinjaman bermasalah (NPL) per marketing." icon={AlertTriangle} />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.marketing.npl-marketing"
                        filters={filters}
                        kantors={kantors}
                        marketings={marketings}
                        showMarketing
                        showKantor
                        printRoute="superadmin.laporan-cs.marketing.npl-marketing.cetak"
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Pinjaman</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama Anggota</TableHead>
                                <TableHead>Produk</TableHead>
                                <TableHead className="text-right">Plafon</TableHead>
                                <TableHead className="text-right">Sisa Pokok</TableHead>
                                <TableHead className="text-center">Angsuran</TableHead>
                                <TableHead>Tanggal Tunggakan</TableHead>
                                <TableHead className="text-center">Hari Tunggakan</TableHead>
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
                                    <TableCell>{item.no_anggota}</TableCell>
                                    <TableCell>{item.nama_anggota}</TableCell>
                                    <TableCell>{item.produk}</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.plafon)}</TableCell>
                                    <TableCell className="text-right font-semibold tabular-nums">{fmt(item.sisa_pokok)}</TableCell>
                                    <TableCell className="text-center">{item.angsuran_ke}/{item.jangka_waktu}</TableCell>
                                    <TableCell>{item.tanggal_tunggakan}</TableCell>
                                    <TableCell className="text-center">
                                        {item.hari_tunggakan > 0 ? (
                                            <span className="rounded-md bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">{item.hari_tunggakan} hr</span>
                                        ) : (
                                            <span className="text-muted-foreground">—</span>
                                        )}
                                    </TableCell>
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
