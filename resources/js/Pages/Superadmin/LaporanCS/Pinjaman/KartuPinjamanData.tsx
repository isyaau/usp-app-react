import { Head } from '@inertiajs/react';
import { FileText, Printer } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { ReportFilterBar } from '@/Components/ReportFilterBar';
import { Button } from '@/Components/ui/button';
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

interface KartuDataRow {
    id: number;
    no_anggota: string;
    nama: string;
    no_pinjaman: string;
    jenis: string;
    plafon: string | number;
    bunga: string | number;
    angsuran_bulan: string | number;
    jangka_waktu: string | number;
    angsuranke: string | number;
    status: string | number;
}

interface Props {
    data: Paginated<KartuDataRow>;
    filters: Record<string, string>;
    kelompoks?: Array<{ id: number; kode: string; nama: string }>;
    kantors?: Array<{ id: number; kode: string; nama_kantor: string }>;
    variantTitle: string;
}

export default function KartuPinjamanData({ data, filters, kelompoks, kantors, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Kartu Pinjaman Data'} />

            <PageHeader
                title={variantTitle || 'Kartu Pinjaman Data'}
                description="Detail data kartu pinjaman anggota."
                icon={FileText}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.pinjaman.kartu-data"
                        filters={filters}
                        kelompoks={kelompoks}
                        kantors={kantors}
                        showKelompok
                        showKantor
                        printRoute="superadmin.laporan-cs.pinjaman.kartu-data.cetak"
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama</TableHead>
                                <TableHead>No Pinjaman</TableHead>
                                <TableHead>Jenis</TableHead>
                                <TableHead>Plafon</TableHead>
                                <TableHead>Bunga</TableHead>
                                <TableHead>Angsuran/bulan</TableHead>
                                <TableHead>Jangka Waktu</TableHead>
                                <TableHead>Angsuran ke</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead className="w-24">Aksi</TableHead>
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
                                            {item.no_anggota}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{item.nama}</span>
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.no_pinjaman}
                                        </span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.jenis ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {Number(item.plafon).toLocaleString('id-ID')}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.bunga}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {Number(item.angsuran_bulan).toLocaleString('id-ID')}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.jangka_waktu}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.angsuranke}
                                    </TableCell>
                                    <TableCell>
                                        <span className={`rounded-md px-2 py-0.5 text-xs font-medium ${item.status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-muted text-muted-foreground'}`}>
                                            {item.status === 'aktif' ? 'Aktif' : 'Lunas'}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            onClick={() =>
                                                window.open(
                                                    route('superadmin.laporan-cs.pinjaman.kartu-data.cetak', item.id),
                                                    '_blank',
                                                )
                                            }
                                        >
                                            <Printer className="size-4" />
                                            Cetak
                                        </Button>
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
