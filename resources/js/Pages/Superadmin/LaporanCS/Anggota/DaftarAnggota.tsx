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
import type { AnggotaRow, Paginated } from '@/types/models';

interface Props {
    data: Paginated<AnggotaRow>;
    filters: Record<string, string>;
    kelompoks: Array<{ id: number; kode: string; nama: string }>;
    kantors: Array<{ id: number; kode: string; nama_kantor: string }>;
    variantTitle: string;
}

export default function DaftarAnggota({ data, filters, kelompoks, kantors, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Daftar Anggota'} />

            <PageHeader
                title={variantTitle || 'Daftar Anggota'}
                description="Daftar seluruh anggota koperasi."
                icon={Users}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.anggota.daftar-anggota"
                        filters={filters}
                        kelompoks={kelompoks}
                        kantors={kantors}
                        showKelompok
                        showKantor
                        printRoute="superadmin.laporan-cs.anggota.daftar-anggota.cetak"
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
                                <TableHead>Alamat</TableHead>
                                <TableHead>Telepon</TableHead>
                                <TableHead>Email</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={8} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data anggota.
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
                                        {item.kantor?.nama_kantor ?? '—'}
                                    </TableCell>
                                    <TableCell className="max-w-48 truncate text-muted-foreground">
                                        {item.alamat ?? '—'}
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
