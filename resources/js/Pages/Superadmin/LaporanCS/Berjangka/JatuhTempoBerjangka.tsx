import { Head } from '@inertiajs/react';
import { CalendarClock } from 'lucide-react';
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
    tanggal_jatuh_tempo: string;
    no_anggota: string;
    nama_anggota: string;
    produk: string;
    jangka_waktu: string;
    bunga: string;
    nominal: number;
    kantor: string;
};

type Props = PageProps & {
    data: { data: Row[]; from: number | null; to: number | null; total: number };
    filters: any;
    kantors: { id: number; kode: string; nama_kantor: string }[];
    variantTitle?: string;
};

const fmt = (n: number) => 'Rp ' + (n ?? 0).toLocaleString('id-ID');

export default function JatuhTempoBerjangka({ data, filters, kantors, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Laporan Simpanan Berjangka Jatuh Tempo'} />
            <PageHeader
                title={variantTitle || 'Laporan Simpanan Berjangka Jatuh Tempo'}
                description="Simpanan berjangka yang akan/telah jatuh tempo."
                icon={CalendarClock}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.simpanan-berjangka.jatuh-tempo-berjangka"
                        filters={filters}
                        kantors={kantors}
                        showKantor
                        printRoute="superadmin.laporan-cs.simpanan-berjangka.jatuh-tempo-berjangka.cetak"
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Deposito</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>Jatuh Tempo</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama Anggota</TableHead>
                                <TableHead>Produk</TableHead>
                                <TableHead>Jangka Waktu</TableHead>
                                <TableHead className="text-right">Bunga</TableHead>
                                <TableHead className="text-right">Nominal</TableHead>
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
                                    <TableCell>{item.tanggal_jatuh_tempo}</TableCell>
                                    <TableCell>{item.no_anggota}</TableCell>
                                    <TableCell>{item.nama_anggota}</TableCell>
                                    <TableCell>{item.produk}</TableCell>
                                    <TableCell>{item.jangka_waktu}</TableCell>
                                    <TableCell className="text-right tabular-nums">{item.bunga}%</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.nominal)}</TableCell>
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
