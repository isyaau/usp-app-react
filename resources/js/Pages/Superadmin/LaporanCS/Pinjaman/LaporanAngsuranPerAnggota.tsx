import { Head } from '@inertiajs/react';
import { UserCheck } from 'lucide-react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { ReportFilterBar } from '@/Components/ReportFilterBar';
import { Card } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import type { Paginated } from '@/types/models';

interface Props {
    data: Paginated<any>;
    filters: Record<string, string>;
    kelompoks?: Array<{ id: number; kode: string; nama: string }>;
    variantTitle: string;
}

export default function LaporanAngsuranPerAnggota({ data, filters, kelompoks, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Laporan Angsuran Per Anggota'} />
            <PageHeader title={variantTitle || 'Laporan Angsuran Per Anggota'} description="Laporan angsuran per anggota." icon={UserCheck} />
            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.pinjaman.angsuran-per-anggota"
                        filters={filters}
                        showDateRange={true}
                        kelompoks={kelompoks}
                        showKelompok={true}
                        printRoute="superadmin.laporan-cs.pinjaman.angsuran-per-anggota.cetak"
                    />
                </div>
                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-10">#</TableHead>
                                <TableHead>No Transaksi</TableHead>
                                <TableHead>Tgl</TableHead>
                                <TableHead>No Pinjaman</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead>Kelompok</TableHead>
                                <TableHead>Angsuran ke</TableHead>
                                <TableHead className="text-right">Pokok</TableHead>
                                <TableHead className="text-right">Bunga</TableHead>
                                <TableHead className="text-right">Total</TableHead>
                                <TableHead className="text-right">Denda</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={12} className="h-32 text-center text-muted-foreground">Tidak ada data.</TableCell>
                                </TableRow>
                            ) : (
                                data.data.map((item, i) => (
                                    <TableRow key={item.id}>
                                        <TableCell>{(data.current_page - 1) * data.per_page + i + 1}</TableCell>
                                        <TableCell>{item.no_transaksi}</TableCell>
                                        <TableCell>{new Date(item.tgl).toLocaleDateString('id-ID')}</TableCell>
                                        <TableCell>{item.no_pinjaman}</TableCell>
                                        <TableCell>{item.no_anggota}</TableCell>
                                        <TableCell>{item.nama}</TableCell>
                                        <TableCell>{item.kelompok}</TableCell>
                                        <TableCell>{item.angsuranke}</TableCell>
                                        <TableCell className="text-right">{Number(item.pokok).toLocaleString('id-ID')}</TableCell>
                                        <TableCell className="text-right">{Number(item.bunga).toLocaleString('id-ID')}</TableCell>
                                        <TableCell className="text-right">{Number(item.total).toLocaleString('id-ID')}</TableCell>
                                        <TableCell className="text-right">{Number(item.denda).toLocaleString('id-ID')}</TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </div>
                <div className="border-t px-5 pt-4">
                    <Pagination links={data.links} currentPage={data.current_page} lastPage={data.last_page} from={data.from} to={data.to} total={data.total} />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
