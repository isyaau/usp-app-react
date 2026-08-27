import { AlertTriangle, Printer } from 'lucide-react';
import { Head } from '@inertiajs/react';

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
import type { AnggotaRow, Paginated } from '@/types/models';

interface Filters {
    search?: string;
    kelompok_id?: string;
}

interface KelompokOption {
    id: number;
    kode: string;
    nama: string;
}

interface PinjamanAktif {
    id: number;
    no_pinjaman: string;
    plafon: string | number;
    nominal_angsuran: string | number;
    angsuranke: string;
    jangka_waktu: string;
    aktif: string | number;
    jenisPinjaman?: { id: number; nama: string } | null;
}

interface AnggotaHutangRow extends AnggotaRow {
    pinjaman_aktif: PinjamanAktif[];
    total_hutang: number;
    total_angsuran_bulan: number;
}

interface Props {
    data: Paginated<AnggotaHutangRow>;
    filters: Filters;
    kelompoks: KelompokOption[];
    variantTitle: string;
}

const PRINT_ROUTE = 'superadmin.laporan-cs.anggota.hutang-kewajiban.cetak';

const fmt = (v: string | number) => new Intl.NumberFormat('id-ID').format(Number(v));

export default function HutangKewajiban({ data, filters, kelompoks, variantTitle }: Props) {
    const handlePrint = () => {
        const params: Record<string, string> = {};
        Object.entries(filters).forEach(([k, v]) => {
            if (v !== '' && v != null) params[k] = String(v);
        });
        window.open(route(PRINT_ROUTE, params), '_blank');
    };

    return (
        <AuthenticatedLayout>
            <Head title="Hutang & Kewajiban Anggota" />

            <PageHeader
                title="Hutang & Kewajiban Anggota"
                description={variantTitle || 'Daftar hutang dan kewajiban angsuran per anggota.'}
                icon={AlertTriangle}
            >
                <Button variant="outline" onClick={handlePrint}>
                    <Printer className="size-4" />
                    Cetak
                </Button>
            </PageHeader>

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.anggota.hutang-kewajiban"
                        filters={filters}
                        kelompoks={kelompoks}
                        showKelompok
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
                                <TableHead className="text-right">Total Hutang</TableHead>
                                <TableHead className="text-right">Angsuran/Bulan</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={6} className="h-32 text-center text-muted-foreground">
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
                                        <span className="font-mono text-xs">{item.no_anggota}</span>
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{item.nama}</span>
                                    </TableCell>
                                    <TableCell>{item.kelompok?.nama ?? '—'}</TableCell>
                                    <TableCell className="text-right font-medium">
                                        {fmt(item.total_hutang)}
                                    </TableCell>
                                    <TableCell className="text-right">{fmt(item.total_angsuran_bulan)}</TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>

                <div className="flex flex-col gap-4 border-t px-5 pt-4">
                    {data.data
                        .filter((item) => item.pinjaman_aktif.length > 0)
                        .map((item) => (
                            <div key={item.id} className="rounded-lg border">
                                <div className="border-b px-4 py-2.5">
                                    <span className="font-semibold">{item.nama}</span>
                                    <span className="ml-2 font-mono text-xs text-muted-foreground">
                                        {item.no_anggota}
                                    </span>
                                </div>
                                <div className="px-4 py-3">
                                    <div className="overflow-x-auto">
                                        <Table>
                                            <TableHeader>
                                                <TableRow>
                                                    <TableHead>No Pinjaman</TableHead>
                                                    <TableHead>Produk</TableHead>
                                                    <TableHead className="text-right">Plafon</TableHead>
                                                    <TableHead className="text-right">Angsuran</TableHead>
                                                    <TableHead>Jangka Waktu</TableHead>
                                                    <TableHead className="text-right">Angsuran ke</TableHead>
                                                </TableRow>
                                            </TableHeader>
                                            <TableBody>
                                                {item.pinjaman_aktif.map((p) => (
                                                    <TableRow key={p.id}>
                                                        <TableCell className="font-mono text-xs">{p.no_pinjaman}</TableCell>
                                                        <TableCell>{p.jenisPinjaman?.nama ?? '—'}</TableCell>
                                                        <TableCell className="text-right">{fmt(p.plafon)}</TableCell>
                                                        <TableCell className="text-right">{fmt(p.nominal_angsuran)}</TableCell>
                                                        <TableCell>{p.jangka_waktu}</TableCell>
                                                        <TableCell className="text-right">{p.angsuranke}</TableCell>
                                                    </TableRow>
                                                ))}
                                            </TableBody>
                                        </Table>
                                    </div>
                                </div>
                            </div>
                        ))}
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
