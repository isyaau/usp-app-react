import { Head } from '@inertiajs/react';
import { Banknote } from 'lucide-react';
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

export default function LaporanPencairanPinjaman({ data, filters, kantors, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Laporan Pencairan Pinjaman'} />
            <PageHeader title={variantTitle || 'Laporan Pencairan Pinjaman'} description="Laporan pencairan pinjaman." icon={Banknote} />
            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.pinjaman.pencairan"
                        filters={filters}
                        showDateRange={true}
                        showKantor={true}
                        kantors={kantors}
                        printRoute="superadmin.laporan-cs.pinjaman.pencairan.cetak"
                    />
                </div>
                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-10">#</TableHead>
                                <TableHead>Tgl Cair</TableHead>
                                <TableHead>No Pinjaman</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead className="text-right">Nominal Cair</TableHead>
                                <TableHead className="text-right">Biaya Admin</TableHead>
                                <TableHead className="text-right">Potongan Simpanan</TableHead>
                                <TableHead className="text-right">Net</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Kantor</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 ? (
                                <TableRow>
                                    <TableCell colSpan={11} className="h-32 text-center text-muted-foreground">Tidak ada data.</TableCell>
                                </TableRow>
                            ) : (
                                data.data.map((item, i) => {
                                    const nominalCair = Number(item.nominal_cair);
                                    const biayaAdmin = Number(item.biaya_admin);
                                    const potonganSimpanan = Number(item.potongan_simpanan);
                                    const net = nominalCair - biayaAdmin - potonganSimpanan;
                                    return (
                                        <TableRow key={item.id}>
                                            <TableCell>{(data.current_page - 1) * data.per_page + i + 1}</TableCell>
                                            <TableCell>{new Date(item.tgl_cair).toLocaleDateString('id-ID')}</TableCell>
                                            <TableCell>{item.no_pinjaman}</TableCell>
                                            <TableCell>{item.no_anggota}</TableCell>
                                            <TableCell>{item.nama}</TableCell>
                                            <TableCell className="text-right">{nominalCair.toLocaleString('id-ID')}</TableCell>
                                            <TableCell className="text-right">{biayaAdmin.toLocaleString('id-ID')}</TableCell>
                                            <TableCell className="text-right">{potonganSimpanan.toLocaleString('id-ID')}</TableCell>
                                            <TableCell className="text-right">{net.toLocaleString('id-ID')}</TableCell>
                                            <TableCell>{item.status}</TableCell>
                                            <TableCell>{item.kantor}</TableCell>
                                        </TableRow>
                                    );
                                })
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
