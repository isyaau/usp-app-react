import { Head } from '@inertiajs/react';
import { Receipt } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { ReportFilterBar } from '@/Components/ReportFilterBar';
import { Card } from '@/Components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import type { Paginated } from '@/types/models';

interface AngsuranRow {
    id: number;
    no_transaksi: string;
    tgl_transaksi: string;
    no_pinjaman: string;
    no_anggota: string;
    nama_anggota: string;
    angsuranke: string | number;
    pokok: string | number;
    bunga: string | number;
    total: string | number;
    denda: string | number;
    kantor: string;
}

interface Props {
    data: Paginated<AngsuranRow>;
    filters: Record<string, string>;
    kantors?: Array<{ id: number; kode: string; nama_kantor: string }>;
    variantTitle: string;
}

export default function LaporanAngsuranPinjaman({ data, filters, kantors, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Laporan Angsuran Pinjaman'} />

            <PageHeader
                title={variantTitle || 'Laporan Angsuran Pinjaman'}
                description="Laporan transaksi angsuran pinjaman anggota."
                icon={Receipt}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.pinjaman.angsuran"
                        filters={filters}
                        kantors={kantors}
                        showKantor
                        showDateRange
                        printRoute="superadmin.laporan-cs.pinjaman.angsuran.cetak"
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Transaksi</TableHead>
                                <TableHead>Tgl Transaksi</TableHead>
                                <TableHead>No Pinjaman</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama Anggota</TableHead>
                                <TableHead>Angsuran ke</TableHead>
                                <TableHead>Pokok</TableHead>
                                <TableHead>Bunga</TableHead>
                                <TableHead>Total</TableHead>
                                <TableHead>Denda</TableHead>
                                <TableHead>Kantor</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={12} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data angsuran.
                                    </TableCell>
                                </TableRow>
                            )}
                            {data.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {data.from !== null ? data.from + i : i + 1}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.no_transaksi}
                                        </span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.tgl_transaksi ? new Date(item.tgl_transaksi).toLocaleDateString('id-ID') : '—'}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.no_pinjaman}
                                        </span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.no_anggota}
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{item.nama_anggota}</span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.angsuranke}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {Number(item.pokok).toLocaleString('id-ID')}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {Number(item.bunga).toLocaleString('id-ID')}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {Number(item.total).toLocaleString('id-ID')}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {Number(item.denda).toLocaleString('id-ID')}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.kantor ?? '—'}
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>

                <div className="border-t px-5 pt-4">
                    <Pagination
                        links={data.links}
                        currentPage={data.current_page}
                        lastPage={data.last_page}
                        from={data.from}
                        to={data.to}
                        total={data.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
