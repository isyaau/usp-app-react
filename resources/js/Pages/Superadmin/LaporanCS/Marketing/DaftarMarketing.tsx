import { Head } from '@inertiajs/react';
import { Users } from 'lucide-react';
import { PageProps } from '@/types';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { Pagination } from '@/Components/Pagination';
import { ReportFilterBar } from '@/Components/ReportFilterBar';
import { PageHeader } from '@/Components/PageHeader';

type Row = {
    id: number;
    kode: string;
    nama: string;
    alamat: string;
    no_hp: string;
    aktif: boolean;
    kantor: string;
};

type Props = PageProps & {
    data: { data: Row[]; from: number | null; to: number | null; total: number };
    filters: any;
    kantors: { id: number; kode: string; nama_kantor: string }[];
    marketings: { id: number; kode: string; nama: string }[];
    variantTitle?: string;
};

export default function DaftarMarketing({ data, filters, kantors, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Daftar Marketing'} />
            <PageHeader title={variantTitle || 'Daftar Marketing'} description="Daftar seluruh marketing." icon={Users} />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.marketing.daftar-marketing"
                        filters={filters}
                        kantors={kantors}
                        showKantor
                        printRoute="superadmin.laporan-cs.marketing.daftar-marketing.cetak"
                    />
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>Kode</TableHead>
                                <TableHead>Nama Marketing</TableHead>
                                <TableHead>Alamat</TableHead>
                                <TableHead>No HP</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Kantor</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={7} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data.
                                    </TableCell>
                                </TableRow>
                            )}
                            {data.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">{data.from !== null ? data.from + i : i + 1}</TableCell>
                                    <TableCell className="font-mono">{item.kode}</TableCell>
                                    <TableCell className="font-medium">{item.nama}</TableCell>
                                    <TableCell>{item.alamat}</TableCell>
                                    <TableCell>{item.no_hp}</TableCell>
                                    <TableCell>
                                        {item.aktif ? (
                                            <span className="rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">Aktif</span>
                                        ) : (
                                            <span className="rounded-md bg-muted px-2 py-0.5 text-xs font-medium text-muted-foreground">Tidak Aktif</span>
                                        )}
                                    </TableCell>
                                    <TableCell>{item.kantor}</TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>

                {data.data.length > 0 && (
                    <div className="px-5">
                        <Pagination data={data} />
                    </div>
                )}
            </Card>
        </AuthenticatedLayout>
    );
}
