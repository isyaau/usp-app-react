import { Head } from '@inertiajs/react';
import { Users } from 'lucide-react';

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

interface NamaPeminjamRow {
    id: number;
    no_anggota: string;
    nama: string;
    kelompok: string;
    kantor: string;
    jumlah_pinjaman: number;
    total_plafon: string | number;
}

interface Props {
    data: Paginated<NamaPeminjamRow>;
    filters: Record<string, string>;
    kelompoks?: Array<{ id: number; kode: string; nama: string }>;
    kantors?: Array<{ id: number; kode: string; nama_kantor: string }>;
    variantTitle: string;
}

export default function DaftarNamaPeminjam({ data, filters, kelompoks, kantors, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Daftar Nama Peminjam'} />

            <PageHeader
                title={variantTitle || 'Daftar Nama Peminjam'}
                description="Daftar nama peminjam beserta jumlah pinjaman."
                icon={Users}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.pinjaman.daftar-nama-peminjam"
                        filters={filters}
                        kelompoks={kelompoks}
                        kantors={kantors}
                        showKelompok
                        showKantor
                        printRoute="superadmin.laporan-cs.pinjaman.daftar-nama-peminjam.cetak"
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead>Kelompok</TableHead>
                                <TableHead>Kantor</TableHead>
                                <TableHead>Jumlah Pinjaman</TableHead>
                                <TableHead>Total Plafon</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={7} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data peminjam.
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
                                            {item.no_anggota}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{item.nama}</span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.kelompok ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.kantor ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.jumlah_pinjaman}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {Number(item.total_plafon).toLocaleString('id-ID')}
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
