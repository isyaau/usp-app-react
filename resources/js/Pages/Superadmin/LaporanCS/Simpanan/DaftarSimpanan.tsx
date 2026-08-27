import { Head } from '@inertiajs/react';
import { List } from 'lucide-react';

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

interface DaftarRow {
    id: number;
    no_rekening: string;
    no_anggota: string;
    nama_anggota: string;
    jenis_simpanan: string;
    nominal_setor: string | number;
    status: string;
    marketing: string;
    kantor: string;
}

interface Props {
    data: Paginated<DaftarRow>;
    filters: Record<string, string>;
    kantors?: Array<{ id: number; kode: string; nama_kantor: string }>;
    jenisList?: Array<{ id: number; kode: string; nama: string }>;
    variantTitle: string;
}

export default function DaftarSimpanan({ data, filters, kantors, jenisList, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Daftar Simpanan'} />

            <PageHeader
                title={variantTitle || 'Daftar Simpanan'}
                description="Daftar seluruh simpanan anggota."
                icon={List}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.simpanan.daftar-simpanan"
                        filters={filters}
                        kantors={kantors}
                        jenisList={jenisList}
                        showKantor
                        showJenis
                        printRoute="superadmin.laporan-cs.simpanan.daftar-simpanan.cetak"
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Rekening</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama Anggota</TableHead>
                                <TableHead>Jenis Simpanan</TableHead>
                                <TableHead>Nominal Setor</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Marketing</TableHead>
                                <TableHead>Kantor</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={9} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data.
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
                                            {item.no_rekening}
                                        </span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.no_anggota}
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{item.nama_anggota}</span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.jenis_simpanan ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {Number(item.nominal_setor).toLocaleString('id-ID')}
                                    </TableCell>
                                    <TableCell>
                                        <span className={`rounded-md px-2 py-0.5 text-xs font-medium ${item.status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-muted text-muted-foreground'}`}>
                                            {item.status ?? '—'}
                                        </span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.marketing ?? '—'}
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
