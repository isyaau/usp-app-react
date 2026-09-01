import { Link, Head } from '@inertiajs/react';
import { Calculator, FileDown } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import type { RencanaRow } from '@/types/models';

interface SimulasiJadwalItem {
    ke: number;
    setoran: number;
    bunga: number;
    total_setor: number;
    saldo: number;
}

interface SimulasiHasil {
    nominal: number;
    bunga_tahun: number;
    jumlah_periode: number;
    satuan_periode: string;
    setoran_pokok: number;
    total_bunga: number;
    saldo_akhir: number;
    jadwal: SimulasiJadwalItem[];
}

interface Props {
    rencanaData: RencanaRow;
    hasil: SimulasiHasil;
}

const rupiah = (v: number) => `Rp ${v.toLocaleString('id-ID')}`;

export default function SimpananRencanaSimulasi({ rencanaData: r, hasil }: Props) {
    const satuanLabel = hasil.satuan_periode;

    return (
        <AuthenticatedLayout>
            <Head title={`Simulasi ${r.no_bukti}`} />

            <PageHeader
                title="Simulasi Setoran Rencana"
                description={`Proyeksi setoran menuju target ${rupiah(hasil.nominal)} untuk ${r.no_bukti}.`}
                icon={Calculator}
                backHref={route('superadmin.simpanan.rencana')}
            />

            <div className="max-w-5xl space-y-4">
                <Card>
                    <CardHeader className="pb-3">
                        <CardTitle className="text-base">Parameter Rencana</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="grid gap-x-6 gap-y-2 text-sm sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <span className="text-muted-foreground">No. Bukti</span>
                                <p className="font-mono font-medium">{r.no_bukti}</p>
                            </div>
                            <div>
                                <span className="text-muted-foreground">Target Nominal</span>
                                <p className="font-medium">{rupiah(hasil.nominal)}</p>
                            </div>
                            <div>
                                <span className="text-muted-foreground">Jangka Waktu</span>
                                <p className="font-medium">
                                    {r.jangka_waktu} {r.satuan}
                                </p>
                            </div>
                            <div>
                                <span className="text-muted-foreground">Bagi Hasil / Tahun</span>
                                <p className="font-medium">{hasil.bunga_tahun}%</p>
                            </div>
                            <div>
                                <span className="text-muted-foreground">Periode Setoran</span>
                                <p className="font-medium">{hasil.jumlah_periode} {satuanLabel}</p>
                            </div>
                            <div>
                                <span className="text-muted-foreground">Setoran Pokok / Periode</span>
                                <p className="font-mono font-medium">{rupiah(hasil.setoran_pokok)}</p>
                            </div>
                            <div>
                                <span className="text-muted-foreground">Total Bagi Hasil</span>
                                <p className="font-medium">{rupiah(hasil.total_bunga)}</p>
                            </div>
                            <div>
                                <span className="text-muted-foreground">Total Saldo Akhir</span>
                                <p className="font-medium text-brand-600">{rupiah(hasil.saldo_akhir)}</p>
                            </div>
                        </div>

                        <div className="mt-4 flex flex-wrap gap-2">
                            <Button asChild className="gap-2">
                                <a
                                    href={route('superadmin.simpanan.rencana.cetak-simulasi', r.id)}
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    <FileDown className="size-4" />
                                    Cetak PDF
                                </a>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader className="flex-row items-center justify-between">
                        <CardTitle className="text-base">Tabel Jadwal Setoran</CardTitle>
                        <Badge variant="outline">{hasil.jadwal.length} periode</Badge>
                    </CardHeader>
                    <CardContent className="max-h-96 overflow-auto">
                        <Table className="rounded-md border">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Periode</TableHead>
                                    <TableHead className="text-right">Setoran</TableHead>
                                    <TableHead className="text-right">Bagi Hasil</TableHead>
                                    <TableHead className="text-right">Total Setor</TableHead>
                                    <TableHead className="text-right">Saldo Akhir</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {hasil.jadwal.map((row) => (
                                    <TableRow key={row.ke}>
                                        <TableCell>{row.ke}</TableCell>
                                        <TableCell className="text-right font-mono">
                                            {rupiah(row.setoran)}
                                        </TableCell>
                                        <TableCell className="text-right font-mono">
                                            {rupiah(row.bunga)}
                                        </TableCell>
                                        <TableCell className="text-right font-mono">
                                            {rupiah(row.total_setor)}
                                        </TableCell>
                                        <TableCell className="text-right font-mono">
                                            {rupiah(row.saldo)}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <div className="flex justify-end">
                    <Button variant="outline" asChild>
                        <Link href={route('superadmin.simpanan.rencana')}>
                            Kembali ke Data Rencana
                        </Link>
                    </Button>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
