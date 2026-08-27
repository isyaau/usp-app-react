import { Head } from '@inertiajs/react';
import { User } from 'lucide-react';
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
    kantors?: Array<{ id: number; kode: string; nama_kantor: string }>;
    variantTitle: string;
}

export default function LaporanRekapNominatifMarketing({ data, filters, kantors, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Laporan Rekap Nominatif Marketing'} />
            <PageHeader title={variantTitle || 'Laporan Rekap Nominatif Marketing'} description="Laporan rekapitulasi nominatif berdasarkan marketing." icon={User} />
            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.pinjaman.rekap-marketing"
                        filters={filters}
                        showDateRange={true}
                        showKantor={true}
                        kantors={kantors}
                        printRoute="superadmin.laporan-cs.pinjaman.rekap-marketing.cetak"
                    />
                </div>
                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-10">#</TableHead>
                                <TableHead>No Pinjaman</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead>Jenis</TableHead>
                                <TableHead className="text-right">Plafon</TableHead>
                                <TableHead className="text-right">Bunga</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Kantor</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={10} className="h-32 text-center text-muted-foreground">Tidak ada data.</TableCell>
                                </TableRow>
                            ) : (
                                data.data.map((item, i) => (
                                    <TableRow key={item.id}>
                                        <TableCell>{(data.current_page - 1) * data.per_page + i + 1}</TableCell>
                                        <TableCell>{item.no_pinjaman}</TableCell>
                                        <TableCell>{new Date(item.tanggal).toLocaleDateString('id-ID')}</TableCell>
                                        <TableCell>{item.no_anggota}</TableCell>
                                        <TableCell>{item.nama}</TableCell>
                                        <TableCell>{item.jenis}</TableCell>
                                        <TableCell className="text-right">{Number(item.plafon).toLocaleString('id-ID')}</TableCell>
                                        <TableCell className="text-right">{Number(item.bunga).toLocaleString('id-ID')}</TableCell>
                                        <TableCell>{item.status}</TableCell>
                                        <TableCell>{item.kantor}</TableCell>
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
