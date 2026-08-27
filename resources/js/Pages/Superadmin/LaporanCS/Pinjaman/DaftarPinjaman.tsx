import { Head } from '@inertiajs/react';
import { FileText } from 'lucide-react';

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

interface PinjamanRow {
    id: number;
    no_pinjaman: string;
    tanggal: string;
    no_anggota: string;
    nama_anggota: string;
    jenis_pinjaman: string;
    plafon: string | number;
    bunga: string | number;
    jangka_waktu: string | number;
    angsuranke: string | number;
    kantor: string;
    status: string | number;
}

interface Props {
    data: Paginated<PinjamanRow>;
    filters: Record<string, string>;
    kantors?: Array<{ id: number; kode: string; nama_kantor: string }>;
    jenisList?: Array<{ id: number; kode: string; nama: string }>;
    variantTitle: string;
}

export default function DaftarPinjaman({ data, filters, kantors, jenisList, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Daftar Pinjaman'} />

            <PageHeader
                title={variantTitle || 'Daftar Pinjaman'}
                description="Daftar seluruh pinjaman anggota."
                icon={FileText}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.pinjaman.daftar-pinjaman"
                        filters={filters}
                        kantors={kantors}
                        showKantor
                        printRoute="superadmin.laporan-cs.pinjaman.daftar-pinjaman.cetak"
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Pinjaman</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama Anggota</TableHead>
                                <TableHead>Jenis Pinjaman</TableHead>
                                <TableHead>Plafon</TableHead>
                                <TableHead>Bunga (%)</TableHead>
                                <TableHead>Jangka Waktu</TableHead>
                                <TableHead>Angsuran ke</TableHead>
                                <TableHead>Kantor</TableHead>
                                <TableHead>Status</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={12} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data pinjaman.
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
                                            {item.no_pinjaman}
                                        </span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.tanggal ? new Date(item.tanggal).toLocaleDateString('id-ID') : '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.no_anggota}
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{item.nama_anggota}</span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.jenis_pinjaman ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {Number(item.plafon).toLocaleString('id-ID')}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.bunga}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.jangka_waktu}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.angsuranke}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.kantor ?? '—'}
                                    </TableCell>
                                    <TableCell>
                                        <span className={`rounded-md px-2 py-0.5 text-xs font-medium ${item.status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-muted text-muted-foreground'}`}>
                                            {item.status === 'aktif' ? 'Aktif' : 'Lunas'}
                                        </span>
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
