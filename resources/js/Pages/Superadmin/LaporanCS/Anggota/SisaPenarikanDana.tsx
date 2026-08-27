import { Printer, Wallet } from 'lucide-react';
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
    kantor_id?: string;
}

interface KelompokOption {
    id: number;
    kode: string;
    nama: string;
}

interface KantorOption {
    id: number;
    kode: string;
    nama_kantor: string;
}

interface AnggotaSisaRow extends AnggotaRow {
    total_setor: number;
    total_tarik: number;
    sisa_saldo: number;
}

interface Props {
    data: Paginated<AnggotaSisaRow>;
    filters: Filters;
    kelompoks: KelompokOption[];
    kantors: KantorOption[];
    variantTitle: string;
}

const PRINT_ROUTE = 'superadmin.laporan-cs.anggota.sisa-penarikan.cetak';

const fmt = (v: string | number) => new Intl.NumberFormat('id-ID').format(Number(v));

export default function SisaPenarikanDana({ data, filters, kelompoks, kantors, variantTitle }: Props) {
    const handlePrint = () => {
        const params: Record<string, string> = {};
        Object.entries(filters).forEach(([k, v]) => {
            if (v !== '' && v != null) params[k] = String(v);
        });
        window.open(route(PRINT_ROUTE, params), '_blank');
    };

    return (
        <AuthenticatedLayout>
            <Head title="Sisa Penarikan Dana" />

            <PageHeader
                title="Sisa Penarikan Dana"
                description={variantTitle || 'Sisa saldo penarikan dana per anggota.'}
                icon={Wallet}
            >
                <Button variant="outline" onClick={handlePrint}>
                    <Printer className="size-4" />
                    Cetak
                </Button>
            </PageHeader>

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.anggota.sisa-penarikan"
                        filters={filters}
                        kelompoks={kelompoks}
                        kantors={kantors}
                        showKelompok
                        showKantor
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
                                <TableHead className="text-right">Total Setor</TableHead>
                                <TableHead className="text-right">Total Tarik</TableHead>
                                <TableHead className="text-right">Sisa Saldo</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={7} className="h-32 text-center text-muted-foreground">
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
                                    <TableCell className="text-right">{fmt(item.total_setor)}</TableCell>
                                    <TableCell className="text-right">{fmt(item.total_tarik)}</TableCell>
                                    <TableCell
                                        className={
                                            item.sisa_saldo < 0
                                                ? 'text-right font-medium text-red-600'
                                                : 'text-right font-medium'
                                        }
                                    >
                                        {fmt(item.sisa_saldo)}
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
