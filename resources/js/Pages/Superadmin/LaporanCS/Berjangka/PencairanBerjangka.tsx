import { Head } from '@inertiajs/react';
import { ArrowRightLeft } from 'lucide-react';
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
    no_deposito: string;
    no_anggota: string;
    nama_anggota: string;
    produk: string;
    nominal_pokok: number;
    nominal_bunga: number;
    nominal_pajak: number;
    nominal_penalti: number;
    nominal_diterima: number;
    kantor: string;
};

type Props = PageProps & {
    data: { data: Row[]; from: number | null; to: number | null; total: number };
    filters: any;
    kantors: { id: number; kode: string; nama_kantor: string }[];
    variantTitle?: string;
};

const fmt = (n: number) => 'Rp ' + (n ?? 0).toLocaleString('id-ID');

export default function PencairanBerjangka({ data, filters, kantors, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Laporan Pencairan Simpanan Berjangka'} />
            <PageHeader
                title={variantTitle || 'Laporan Pencairan Simpanan Berjangka'}
                description="Pencairan simpanan berjangka pada periode tertentu."
                icon={ArrowRightLeft}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.simpanan-berjangka.pencairan-berjangka"
                        filters={filters}
                        kantors={kantors}
                        showKantor
                        showDateRange
                        printRoute="superadmin.laporan-cs.simpanan-berjangka.pencairan-berjangka.cetak"
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Transaksi</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>No Deposito</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama Anggota</TableHead>
                                <TableHead>Produk</TableHead>
                                <TableHead className="text-right">Pokok</TableHead>
                                <TableHead className="text-right">Bunga</TableHead>
                                <TableHead className="text-right">Pajak</TableHead>
                                <TableHead className="text-right">Penalti</TableHead>
                                <TableHead className="text-right">Diterima</TableHead>
                                <TableHead>Kantor</TableHead>
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
                                    <TableCell className="font-mono">{item.no_deposito}</TableCell>
                                    <TableCell>{item.no_anggota}</TableCell>
                                    <TableCell>{item.nama_anggota}</TableCell>
                                    <TableCell>{item.produk}</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.nominal_pokok)}</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.nominal_bunga)}</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.nominal_pajak)}</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.nominal_penalti)}</TableCell>
                                    <TableCell className="text-right font-semibold tabular-nums">{fmt(item.nominal_diterima)}</TableCell>
                                    <TableCell>{item.kantor}</TableCell>
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
