import { Head } from '@inertiajs/react';
import { Wallet } from 'lucide-react';
import { PageProps } from '@/types';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { Pagination } from '@/Components/Pagination';
import { ReportFilterBar } from '@/Components/ReportFilterBar';
import { PageHeader } from '@/Components/PageHeader';

type Row = {
    id: number;
    no_rekening: string;
    no_anggota: string;
    nama_anggota: string;
    jenis_simpanan: string;
    nominal_setor: number;
    aktif: boolean;
    marketing: string;
    kantor: string;
};

type Props = PageProps & {
    data: { data: Row[]; from: number | null; to: number | null; total: number };
    filters: any;
    kantors: { id: number; kode: string; nama_kantor: string }[];
    marketings: { id: number; kode: string; nama: string }[];
    variantTitle?: string;
};

const fmt = (n: number) => 'Rp ' + (n ?? 0).toLocaleString('id-ID');

export default function SimpananMarketing({ data, filters, kantors, marketings, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Laporan Simpanan Marketing'} />
            <PageHeader title={variantTitle || 'Laporan Simpanan Marketing'} description="Simpanan berdasarkan marketing." icon={Wallet} />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.marketing.simpanan-marketing"
                        filters={filters}
                        kantors={kantors}
                        marketings={marketings}
                        showMarketing
                        showKantor
                        printRoute="superadmin.laporan-cs.marketing.simpanan-marketing.cetak"
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
                                <TableHead className="text-right">Nominal Setor</TableHead>
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
                                    <TableCell className="text-muted-foreground">{data.from !== null ? data.from + i : i + 1}</TableCell>
                                    <TableCell className="font-mono">{item.no_rekening}</TableCell>
                                    <TableCell>{item.no_anggota}</TableCell>
                                    <TableCell>{item.nama_anggota}</TableCell>
                                    <TableCell>{item.jenis_simpanan}</TableCell>
                                    <TableCell className="text-right tabular-nums">{fmt(item.nominal_setor)}</TableCell>
                                    <TableCell>
                                        {item.aktif ? (
                                            <span className="rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700">Aktif</span>
                                        ) : (
                                            <span className="rounded-md bg-muted px-2 py-0.5 text-xs font-medium text-muted-foreground">Tidak</span>
                                        )}
                                    </TableCell>
                                    <TableCell>{item.marketing}</TableCell>
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
