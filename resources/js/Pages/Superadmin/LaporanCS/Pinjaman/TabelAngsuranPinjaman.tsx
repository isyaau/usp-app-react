import { Head } from '@inertiajs/react';
import { Table } from 'lucide-react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { ReportFilterBar } from '@/Components/ReportFilterBar';
import { Card } from '@/Components/ui/card';
import { Table as UiTable, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import type { Paginated } from '@/types/models';

interface Props {
    data: Paginated<any>;
    filters: Record<string, string>;
    kantors?: Array<{ id: number; kode: string; nama_kantor: string }>;
    jenisList?: Array<{ id: number; kode: string; nama: string }>;
    variantTitle: string;
}

export default function TabelAngsuranPinjaman({ data, filters, kantors, jenisList, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Tabel Angsuran Pinjaman'} />
            <PageHeader title={variantTitle || 'Tabel Angsuran Pinjaman'} description="Tabel angsuran pinjaman anggota." icon={Table} />
            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.pinjaman.tabel-angsuran"
                        filters={filters}
                        showKantor
                        kantors={kantors}
                        printRoute="superadmin.laporan-cs.pinjaman.tabel-angsuran.cetak"
                    />
                </div>
                <div className="px-5 overflow-x-auto">
                    <UiTable>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-10">#</TableHead>
                                <TableHead>No Pinjaman</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead>Jenis</TableHead>
                                <TableHead className="text-right">Plafon</TableHead>
                                <TableHead className="text-right">Angsuran/bulan</TableHead>
                                <TableHead className="text-right">Bunga</TableHead>
                                <TableHead>Jangka Waktu</TableHead>
                                <TableHead>Angsuran ke</TableHead>
                                <TableHead>Kantor</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={11} className="h-32 text-center text-muted-foreground">Tidak ada data.</TableCell>
                                </TableRow>
                            ) : (
                                data.data.map((item, i) => (
                                    <TableRow key={item.id}>
                                        <TableCell>{(data.current_page - 1) * data.per_page + i + 1}</TableCell>
                                        <TableCell>{item.no_pinjaman}</TableCell>
                                        <TableCell>{item.no_anggota}</TableCell>
                                        <TableCell>{item.nama}</TableCell>
                                        <TableCell>{item.jenis}</TableCell>
                                        <TableCell className="text-right">{Number(item.plafon).toLocaleString('id-ID')}</TableCell>
                                        <TableCell className="text-right">{Number(item.angsuran_per_bulan).toLocaleString('id-ID')}</TableCell>
                                        <TableCell className="text-right">{item.bunga}</TableCell>
                                        <TableCell>{item.jangka_waktu}</TableCell>
                                        <TableCell>{item.angsuranke}</TableCell>
                                        <TableCell>{item.kantor}</TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </UiTable>
                </div>
                <div className="border-t px-5 pt-4">
                    <Pagination links={data.links} currentPage={data.current_page} lastPage={data.last_page} from={data.from} to={data.to} total={data.total} />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
