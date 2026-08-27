import { Head } from '@inertiajs/react';
import { BadgeCheck } from 'lucide-react';
import { PageProps } from '@/types';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { Pagination } from '@/Components/Pagination';
import { ReportFilterBar } from '@/Components/ReportFilterBar';
import { PageHeader } from '@/Components/PageHeader';

type Row = {
    id: number;
    no_deposito: string;
    tanggal: string | null;
    no_anggota: string;
    nama_anggota: string;
    produk: string;
    jangka_waktu: string;
    bunga: string;
    nominal: number;
    status_bunga: number;
    kantor: string;
};

type Props = PageProps & {
    data: { data: Row[]; from: number | null; to: number | null; total: number };
    filters: any;
    kantors: { id: number; kode: string; nama_kantor: string }[];
    variantTitle?: string;
};

const fmt = (n: number) => 'Rp ' + (n ?? 0).toLocaleString('id-ID');

export default function PostingBagiHasil({ data, filters, kantors, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Laporan Posting Bagi Hasil Simpanan Berjangka'} />
            <PageHeader
                title={variantTitle || 'Laporan Posting Bagi Hasil Simpanan Berjangka'}
                description="Status posting bagi hasil simpanan berjangka."
                icon={BadgeCheck}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.simpanan-berjangka.posting-bagi-hasil"
                        filters={filters}
                        kantors={kantors}
                        showKantor
                        printRoute="superadmin.laporan-cs.simpanan-berjangka.posting-bagi-hasil.cetak"
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Deposito</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama Anggota</TableHead>
                                <TableHead>Produk</TableHead>
                                <TableHead>Jangka Waktu</TableHead>
                                <TableHead className="text-right">Bunga</TableHead>
                                <TableHead className="text-right">Nominal</TableHead>
                                <TableHead>Status Bagi Hasil</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={10} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data.
                                    </TableCell>
                                </TableRow>
                            )}
                            {data.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">{data.from !== null ? data.from + i : i + 1}</TableCell>
                                    <TableCell className="font-mono">{item.no_deposito}</TableCell>
                                    <TableCell>{item.tanggal}</TableCell>
                                    <TableCell>{item.no_anggota}</TableCell>
                                    <TableCell>{item.nama_anggota}</TableCell>
                                    <TableCell>{item.produk}</TableCell>
                                    <TableCell>{item.jangka_waktu}</TableCell>
                                    <TableCell className="text-right tabular-nums">{item.bunga}%</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.nominal)}</TableCell>
                                    <TableCell>
                                        {item.status_bunga === 1 ? (
                                            <span className="rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">Sudah Diposting</span>
                                        ) : (
                                            <span className="rounded-md bg-muted px-2 py-0.5 text-xs font-medium text-muted-foreground">Belum Diposting</span>
                                        )}
                                    </TableCell>
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
