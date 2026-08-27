import { Head } from '@inertiajs/react';
import { ShieldCheck } from 'lucide-react';

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
import type { AnggotaRow, Paginated } from '@/types/models';

interface PengurusRow extends AnggotaRow {
    pengurus_jabatan: string | null;
    tgl_pengurus_diangkat: string | null;
}

interface Props {
    data: Paginated<PengurusRow>;
    filters: Record<string, string>;
    kantors: Array<{ id: number; kode: string; nama_kantor: string }>;
    variantTitle: string;
}

export default function DaftarPengurus({ data, filters, kantors, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Daftar Pengurus'} />

            <PageHeader
                title={variantTitle || 'Daftar Pengurus'}
                description="Daftar pengurus koperasi."
                icon={ShieldCheck}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.anggota.pengurus"
                        filters={filters}
                        kantors={kantors}
                        showKantor
                        printRoute="superadmin.laporan-cs.anggota.pengurus.cetak"
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
                                <TableHead>Jabatan</TableHead>
                                <TableHead>Tgl Diangkat</TableHead>
                                <TableHead>Telepon</TableHead>
                                <TableHead>Email</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={8} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data pengurus.
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
                                        {item.kelompok?.nama ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.pengurus_jabatan ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.tgl_pengurus_diangkat ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.telepon ?? item.no_hp ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.email ?? '—'}
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
