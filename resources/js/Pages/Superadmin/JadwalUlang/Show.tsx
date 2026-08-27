import { Head, Link } from '@inertiajs/react';
import { CalendarClock, HandCoins, Printer } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import type { JadwalUlangDetailRow } from '@/types/models';

interface SubstituteRow {
    id: number;
    nama: string;
    [key: string]: any;
}

interface ShowRow {
    id: number;
    no_transaksi: string;
    no_pinjaman_lama: string | null;
    no_pinjaman: string | null;
    tgl_transaksi: string;
    plafon: string;
    sisa_pokok: string;
    bunga: string;
    jangka_waktu: string;
    satuan: string;
    metode: string;
    nominal_angsuran: string;
    total_bunga: string;
    status: string;
    keterangan: string | null;
    pinjaman?: { id: number; no_pinjaman: string; anggota?: { id: number; no_anggota: string; nama: string } | null } | null;
    details?: JadwalUlangDetailRow[];
    biaya?: SubstituteRow[];
    jaminan?: SubstituteRow[];
    saksi?: SubstituteRow[];
    surat?: SubstituteRow[];
    penjamin?: SubstituteRow[];
}

interface Props {
    transaksi: ShowRow;
}

const rupiah = (v: string | number | null) =>
    `Rp ${Number(v ?? 0).toLocaleString('id-ID')}`;

const statusBadge: Record<string, string> = {
    draft: 'border-amber-500/40 bg-amber-500/10 text-amber-600 dark:text-amber-400',
    posted: 'border-emerald-500/40 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
    batal: 'border-muted-foreground/30 bg-muted text-muted-foreground',
};

export default function Show({ transaksi }: Props) {
    const totalPokok = (transaksi.details ?? []).reduce((s, r) => s + (parseFloat(r.nominal_pokok) || 0), 0);
    const totalBunga = (transaksi.details ?? []).reduce((s, r) => s + (parseFloat(r.nominal_bunga) || 0), 0);

    const rows = (list?: SubstituteRow[]) => list ?? [];

    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${transaksi.no_transaksi}`} />

            <PageHeader
                title="Detail Jadwal Ulang Pinjaman"
                description={transaksi.no_transaksi}
                icon={CalendarClock}
                backHref={route('superadmin.pinjaman.jadwal-ulang')}
            >
                <Button variant="outline" onClick={() => window.print()}>
                    <Printer /> Cetak
                </Button>
            </PageHeader>

            <div className="space-y-5">
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-3">
                            Info Jadwal Ulang
                            <Badge variant="outline" className={statusBadge[transaksi.status] ?? ''}>{transaksi.status}</Badge>
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-x-8 gap-y-4 sm:grid-cols-2 lg:grid-cols-4">
                        <Info label="No. Transaksi" value={transaksi.no_transaksi} />
                        <Info label="No. Pinjaman Lama" value={transaksi.no_pinjaman_lama ?? '—'} mono highlighted />
                        <Info label="No. Pinjaman" value={transaksi.no_pinjaman || '—'} />
                        <Info label="Tanggal" value={transaksi.tgl_transaksi} />
                        <Info label="Anggota" value={transaksi.pinjaman?.anggota?.nama ?? '—'} />
                        <Info label="Plafon / Sisa Pokok" value={`${rupiah(transaksi.plafon)} / ${rupiah(transaksi.sisa_pokok)}`} />
                        <Info label="Bunga" value={`${transaksi.bunga}%`} />
                        <Info label="Jangka Waktu" value={`${transaksi.jangka_waktu} ${transaksi.satuan}`} />
                        <Info label="Metode" value={transaksi.metode} />
                        <Info label="Angsuran" value={rupiah(transaksi.nominal_angsuran)} bold />
                        <Info label="Total Bunga" value={rupiah(transaksi.total_bunga)} />
                        <Info label="Keterangan" value={transaksi.keterangan ?? '—'} />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader className="flex-row flex-wrap items-center justify-between space-y-0">
                        <CardTitle>Jadwal Angsuran Baru</CardTitle>
                        <span className="text-sm text-muted-foreground">{transaksi.details?.length ?? 0} periode</span>
                    </CardHeader>
                    <CardContent>
                        <div className="overflow-x-auto rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead className="w-16">Ke</TableHead>
                                        <TableHead>Pokok</TableHead>
                                        <TableHead>Bunga</TableHead>
                                        <TableHead>Total Angsuran</TableHead>
                                        <TableHead>Sisa Pokok</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {(transaksi.details?.length ?? 0) === 0 && (
                                        <TableRow><TableCell colSpan={5} className="h-24 text-center text-muted-foreground">Belum ada detail jadwal.</TableCell></TableRow>
                                    )}
                                    {(transaksi.details ?? []).map((r) => (
                                        <TableRow key={r.id}>
                                            <TableCell className="text-muted-foreground">{r.angsuran_ke}</TableCell>
                                            <TableCell className="font-mono">{rupiah(r.nominal_pokok)}</TableCell>
                                            <TableCell className="font-mono">{rupiah(r.nominal_bunga)}</TableCell>
                                            <TableCell className="font-mono">{rupiah(r.total_angsuran)}</TableCell>
                                            <TableCell className="font-mono">{rupiah(r.sisa_pokok)}</TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </div>
                        <div className="mt-4 flex flex-wrap items-center justify-end gap-6 rounded-lg border bg-muted/40 px-4 py-3 text-sm">
                            <span><span className="text-muted-foreground">Total Pokok: </span><span className="font-mono font-semibold">{rupiah(totalPokok)}</span></span>
                            <span><span className="text-muted-foreground">Total Bunga: </span><span className="font-mono font-semibold">{rupiah(totalBunga)}</span></span>
                        </div>
                    </CardContent>
                </Card>

                {rows(transaksi.penjamin).length > 0 && (
                    <DetailCard title="Penjamin" columns={['nama', 'hubungan', 'no_ktp', 'telepon']} data={transaksi.penjamin!} />
                )}

                {rows(transaksi.biaya).length > 0 && (
                    <DetailCard title="Biaya Pinjaman" columns={['nama', 'nominal']} data={transaksi.biaya!} moneyCols="nominal" />
                )}

                {rows(transaksi.jaminan).length > 0 && (
                    <DetailCard title="Jaminan" columns={['nama', 'keterangan', 'nominal']} data={transaksi.jaminan!} moneyCols="nominal" />
                )}

                {rows(transaksi.saksi).length > 0 && (
                    <DetailCard title="Saksi" columns={['nama', 'no_ktp', 'alamat']} data={transaksi.saksi!} />
                )}

                {rows(transaksi.surat).length > 0 && (
                    <DetailCard title="Surat" columns={['surat', 'keterangan']} data={transaksi.surat!} />
                )}

                <div className="flex gap-2">
                    <Button asChild variant="outline">
                        <Link href={route('superadmin.pinjaman.jadwal-ulang.edit', transaksi.id)}>Edit</Link>
                    </Button>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

function DetailCard({
    title,
    columns,
    data,
    moneyCols,
}: {
    title: string;
    columns?: string[];
    data: SubstituteRow[];
    moneyCols?: string;
}) {
    return (
        <Card>
            <CardHeader>
                <CardTitle className="flex items-center gap-2">
                    <HandCoins className="size-4" /> {title}
                </CardTitle>
            </CardHeader>
            <CardContent className="overflow-x-auto rounded-md border p-0">
                <Table>
                    <TableHeader>
                        <TableRow>
                            {(columns ?? ['nama']).map((c) => (
                                <TableHead key={c} className="capitalize">{c}</TableHead>
                            ))}
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {data.map((r) => (
                            <TableRow key={r.id}>
                                {(columns ?? ['nama']).map((c) => (
                                    <TableCell key={c} className="font-mono">
                                        {moneyCols === c ? rupiah(r[c]) : (r[c] ?? '')}
                                    </TableCell>
                                ))}
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    );
}

function Info({ label, value, mono, bold, highlighted }: { label: string; value: string; mono?: boolean; bold?: boolean; highlighted?: boolean }) {
    return (
        <div>
            <div className="text-xs uppercase tracking-wide text-muted-foreground">{label}</div>
            <div className={
                highlighted
                    ? 'mt-1 rounded-md bg-brand-50 px-2 py-1 font-mono text-lg font-semibold text-brand-600 dark:bg-brand-600/15 dark:text-brand-300'
                    : bold ? 'mt-1 font-mono text-lg font-semibold text-brand-600' : 'mt-1 font-medium'
            } style={mono ? { fontFamily: 'monospace' } : undefined}>{value}</div>
        </div>
    );
}
