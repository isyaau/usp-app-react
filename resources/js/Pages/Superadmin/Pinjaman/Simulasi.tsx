import { useMemo, useState } from 'react';
import { Link, Head } from '@inertiajs/react';
import { Calculator, FileDown } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import {
    calculateLoan,
    type LoanMethod,
    type LoanSatuan,
} from '@/lib/loanCalc';

const METODE: LoanMethod[] = ['Anuitas', 'Flat', 'Flat Efektif', 'Pokok Tetap', 'Bagi Hasil Menurun'];

const rupiah = (v: number) => `Rp ${v.toLocaleString('id-ID')}`;

export default function Simulasi() {
    const [plafon, setPlafon] = useState('5000000');
    const [bunga, setBunga] = useState('12');
    const [jangkaWaktu, setJangkaWaktu] = useState('12');
    const [satuan, setSatuan] = useState<LoanSatuan>('bulan');
    const [metode, setMetode] = useState<LoanMethod>('Flat');

    const hasil = useMemo(
        () =>
            calculateLoan({
                plafon: Number(plafon) || 0,
                bunga: Number(bunga) || 0,
                jangka_waktu: Number(jangkaWaktu) || 0,
                satuan,
                metode,
            }),
        [plafon, bunga, jangkaWaktu, satuan, metode],
    );

    const cetakQuery = () =>
        new URLSearchParams({
            plafon,
            bunga,
            jangka_waktu: jangkaWaktu,
            satuan,
            metode,
        }).toString();

    const totalBayar = hasil.nominal_angsuran * hasil.jumlah_periode;

    return (
        <AuthenticatedLayout>
            <Head title="Simulasi Angsuran Pinjaman" />

            <PageHeader
                title="Simulasi Angsuran Pinjaman"
                description="Hitung nominal angsuran dan lihat tabel jadwal pembayaran."
                icon={Calculator}
                backHref={route('superadmin.pinjaman.pinjaman')}
            />

            <div className="max-w-5xl space-y-4">
                <Card>
                    <CardHeader>
                        <CardTitle className="text-base">Parameter Pinjaman</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                            <div className="space-y-1.5">
                                <Label className="text-xs text-muted-foreground">Plafon (Rp)</Label>
                                <Input
                                    type="number"
                                    min="0"
                                    value={plafon}
                                    onChange={(e) => setPlafon(e.target.value)}
                                    placeholder="0"
                                />
                            </div>
                            <div className="space-y-1.5">
                                <Label className="text-xs text-muted-foreground">Bagi Hasil / Tahun (%)</Label>
                                <Input
                                    type="number"
                                    min="0"
                                    max="100"
                                    value={bunga}
                                    onChange={(e) => setBunga(e.target.value)}
                                    placeholder="0"
                                />
                            </div>
                            <div className="space-y-1.5">
                                <Label className="text-xs text-muted-foreground">Jangka Waktu</Label>
                                <Input
                                    type="number"
                                    min="1"
                                    value={jangkaWaktu}
                                    onChange={(e) => setJangkaWaktu(e.target.value)}
                                    placeholder="0"
                                />
                            </div>
                            <div className="space-y-1.5">
                                <Label className="text-xs text-muted-foreground">Satuan</Label>
                                <Select value={satuan} onValueChange={(v) => setSatuan(v as LoanSatuan)}>
                                    <SelectTrigger>
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {(['hari', 'minggu', 'bulan', 'tahun'] as LoanSatuan[]).map((s) => (
                                            <SelectItem key={s} value={s}>
                                                {s.charAt(0).toUpperCase() + s.slice(1)}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-1.5">
                                <Label className="text-xs text-muted-foreground">Metode</Label>
                                <Select value={metode} onValueChange={(v) => setMetode(v as LoanMethod)}>
                                    <SelectTrigger>
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {METODE.map((m) => (
                                            <SelectItem key={m} value={m}>
                                                {m}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div className="mt-4 flex flex-wrap gap-2">
                            <Button asChild className="gap-2">
                                <a
                                    href={`${route('superadmin.pinjaman.pinjaman.simulasi-cetak')}?${cetakQuery()}`}
                                >
                                    <FileDown className="size-4" />
                                    Cetak PDF
                                </a>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <div className="grid gap-4 sm:grid-cols-3">
                    <Card>
                        <CardHeader className="pb-1">
                            <CardTitle className="text-xs font-medium text-muted-foreground">
                                Nominal Angsuran / {satuan}
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="text-2xl font-bold text-brand-600">
                            {rupiah(hasil.nominal_angsuran)}
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-1">
                            <CardTitle className="text-xs font-medium text-muted-foreground">Total Bunga</CardTitle>
                        </CardHeader>
                        <CardContent className="text-2xl font-bold">{rupiah(hasil.total_bunga)}</CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="pb-1">
                            <CardTitle className="text-xs font-medium text-muted-foreground">
                                Total Pembayaran ({hasil.jumlah_periode} periode, {metode})
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="text-2xl font-bold">{rupiah(totalBayar)}</CardContent>
                    </Card>
                </div>

                <Card>
                    <CardHeader className="flex-row items-center justify-between">
                        <CardTitle className="text-base">Tabel Jadwal Angsuran</CardTitle>
                        <Badge variant="outline">{hasil.jadwal.length} periode</Badge>
                    </CardHeader>
                    <CardContent className="max-h-96 overflow-auto">
                        <Table className="rounded-md border">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Ke</TableHead>
                                    <TableHead className="text-right">Pokok</TableHead>
                                    <TableHead className="text-right">Bunga</TableHead>
                                    <TableHead className="text-right">Angsuran</TableHead>
                                    <TableHead className="text-right">Sisa Pokok</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {hasil.jadwal.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={5} className="h-24 text-center text-muted-foreground">
                                            Isi parameter di atas lalu tekan Hitung Angsuran.
                                        </TableCell>
                                    </TableRow>
                                )}
                                {hasil.jadwal.map((row) => (
                                    <TableRow key={row.ke}>
                                        <TableCell>{row.ke}</TableCell>
                                        <TableCell className="text-right font-mono">{rupiah(row.pokok)}</TableCell>
                                        <TableCell className="text-right font-mono">{rupiah(row.bunga)}</TableCell>
                                        <TableCell className="text-right font-mono">{rupiah(row.angsuran)}</TableCell>
                                        <TableCell className="text-right font-mono">{rupiah(row.sisa)}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                <div className="flex justify-end">
                    <Button variant="outline" asChild>
                        <Link href={route('superadmin.pinjaman.pinjaman')}>Kembali ke Data Pinjaman</Link>
                    </Button>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}