import { Printer, FileText } from 'lucide-react';
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

interface SimpananItem {
    id: number;
    no_rekening: string;
    aktif: string | number;
    nominal_setor: string | number | null;
    jenis_simpanan?: { id: number; kode: string | null; nama: string | null } | null;
}

interface PinjamanItem {
    id: number;
    no_pinjaman: string;
    plafon: string | number;
    bunga: string | number;
    jangka_waktu: string;
    angsuranke: string;
    aktif: string | number;
    jenisPinjaman?: { id: number; nama: string } | null;
}

interface AnggotaDetailRow extends AnggotaRow {
    simpanan: SimpananItem[];
    pinjaman: PinjamanItem[];
}

interface Props {
    data: Paginated<AnggotaDetailRow>;
    filters: Filters;
    kelompoks: KelompokOption[];
    variantTitle: string;
}

const PRINT_ROUTE = 'superadmin.laporan-cs.anggota.simpan-pinjam-detail.cetak';

const fmt = (v: string | number) => new Intl.NumberFormat('id-ID').format(Number(v));

export default function SimpanPinjamDetail({ data, filters, kelompoks, variantTitle }: Props) {
    const handlePrint = () => {
        const params: Record<string, string> = {};
        Object.entries(filters).forEach(([k, v]) => {
            if (v !== '' && v != null) params[k] = String(v);
        });
        window.open(route(PRINT_ROUTE, params), '_blank');
    };

    return (
        <AuthenticatedLayout>
            <Head title="Simpan & Pinjam Detail" />

            <PageHeader
                title="Simpan & Pinjam Detail"
                description={variantTitle || 'Detail rekening simpanan dan pinjaman per anggota.'}
                icon={FileText}
            >
                <Button variant="outline" onClick={handlePrint}>
                    <Printer className="size-4" />
                    Cetak
                </Button>
            </PageHeader>

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.anggota.simpan-pinjam-detail"
                        filters={filters}
                        kelompoks={kelompoks}
                        showKelompok
                    />
                </div>

                <div className="flex flex-col gap-4 px-5">
                    {data.data.length === 0 && (
                        <p className="py-16 text-center text-muted-foreground">Tidak ada data anggota.</p>
                    )}
                    {data.data.map((item, i) => (
                        <div key={item.id} className="rounded-lg border">
                            <div className="flex flex-wrap items-center justify-between gap-2 border-b px-4 py-3">
                                <div className="flex items-center gap-2">
                                    <span className="text-muted-foreground">
                                        {data.from !== null ? data.from + i : i + 1}.
                                    </span>
                                    <span className="font-semibold">{item.nama}</span>
                                    <span className="font-mono text-xs text-muted-foreground">
                                        {item.no_anggota}
                                    </span>
                                </div>
                                <span className="text-xs text-muted-foreground">
                                    {item.kelompok?.nama ?? '—'}
                                </span>
                            </div>

                            <div className="px-4 py-3">
                                <h3 className="mb-2 text-sm font-medium text-muted-foreground">Simpanan</h3>
                                <div className="overflow-x-auto">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>No Rekening</TableHead>
                                                <TableHead>Jenis</TableHead>
                                                <TableHead className="text-right">Nominal Setor</TableHead>
                                                <TableHead>Status</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {item.simpanan.length === 0 && (
                                                <TableRow>
                                                    <TableCell colSpan={4} className="text-center text-muted-foreground">
                                                        Tidak ada simpanan.
                                                    </TableCell>
                                                </TableRow>
                                            )}
                                            {item.simpanan.map((s) => (
                                                <TableRow key={s.id}>
                                                    <TableCell className="font-mono text-xs">{s.no_rekening}</TableCell>
                                                    <TableCell>{s.jenis_simpanan?.nama ?? '—'}</TableCell>
                                                    <TableCell className="text-right">
                                                        {s.nominal_setor != null ? fmt(s.nominal_setor) : '—'}
                                                    </TableCell>
                                                    <TableCell>
                                                        <span className="rounded-md bg-muted px-2 py-0.5 text-xs font-medium text-muted-foreground">
                                                            {Number(s.aktif) ? 'Aktif' : 'Nonaktif'}
                                                        </span>
                                                    </TableCell>
                                                </TableRow>
                                            ))}
                                        </TableBody>
                                    </Table>
                                </div>
                            </div>

                            <div className="border-t px-4 py-3">
                                <h3 className="mb-2 text-sm font-medium text-muted-foreground">Pinjaman</h3>
                                <div className="overflow-x-auto">
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>No Pinjaman</TableHead>
                                                <TableHead>Produk</TableHead>
                                                <TableHead className="text-right">Plafon</TableHead>
                                                <TableHead className="text-right">Bunga</TableHead>
                                                <TableHead>Jangka Waktu</TableHead>
                                                <TableHead className="text-right">Angsuran ke</TableHead>
                                                <TableHead>Status</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {item.pinjaman.length === 0 && (
                                                <TableRow>
                                                    <TableCell colSpan={7} className="text-center text-muted-foreground">
                                                        Tidak ada pinjaman.
                                                    </TableCell>
                                                </TableRow>
                                            )}
                                            {item.pinjaman.map((p) => (
                                                <TableRow key={p.id}>
                                                    <TableCell className="font-mono text-xs">{p.no_pinjaman}</TableCell>
                                                    <TableCell>{p.jenisPinjaman?.nama ?? '—'}</TableCell>
                                                    <TableCell className="text-right">{fmt(p.plafon)}</TableCell>
                                                    <TableCell className="text-right">{fmt(p.bunga)}</TableCell>
                                                    <TableCell>{p.jangka_waktu}</TableCell>
                                                    <TableCell className="text-right">{p.angsuranke}</TableCell>
                                                    <TableCell>
                                                        <span className="rounded-md bg-muted px-2 py-0.5 text-xs font-medium text-muted-foreground">
                                                            {Number(p.aktif) ? 'Aktif' : 'Nonaktif'}
                                                        </span>
                                                    </TableCell>
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
