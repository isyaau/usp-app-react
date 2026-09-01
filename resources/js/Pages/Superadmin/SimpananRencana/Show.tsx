import { Head, Link } from '@inertiajs/react';
import { CalendarClock, Pencil, Printer, Wallet } from 'lucide-react';

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

interface RekeningRow {
    id: number;
    no_rekening: string;
    anggota?: { id: number; no_anggota: string; nama: string } | null;
    jenis_simpanan?: { id: number; nama: string } | null;
}

interface Props {
    rencanaData: RencanaRow & {
        kantor_id: number | string;
        user?: { id: number; nama: string } | null;
    };
    rekeningList: RekeningRow[];
}

function InfoRow({ label, value }: { label: string; value: React.ReactNode }) {
    return (
        <div className="flex flex-col gap-1 py-2.5 sm:flex-row sm:items-center">
            <span className="w-48 shrink-0 text-sm font-medium text-muted-foreground">
                {label}
            </span>
            <span className="text-sm">{value ?? '—'}</span>
        </div>
    );
}

const rupiah = (v: string | number | null) =>
    `Rp ${Number(v ?? 0).toLocaleString('id-ID')}`;

export default function SimpananRencanaShow({ rencanaData: r, rekeningList }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${r.no_bukti}`} />

            <PageHeader
                title="Detail Simpanan Rencana"
                description={`No. ${r.no_bukti}`}
                icon={CalendarClock}
                backHref={route('superadmin.simpanan.rencana')}
            />

            <div className="grid max-w-5xl gap-5 lg:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center justify-between gap-2">
                            <span className="flex items-center gap-2">
                                <CalendarClock className="size-4 text-brand-600" />
                                {r.no_bukti}
                            </span>
                            <Badge variant="secondary">{rekeningList.length} rekening</Badge>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <InfoRow label="Tanggal Mulai" value={r.tanggal_mulai} />
                        <InfoRow label="Tanggal Jatuh Tempo" value={r.tanggal_jatuhtempo} />
                        <InfoRow
                            label="Jangka Waktu"
                            value={`${r.jangka_waktu} ${r.satuan}`}
                        />
                        <InfoRow label="Nominal Target" value={rupiah(r.nominal)} />
                        <InfoRow label="Bagi Hasil / Tahun" value={r.bunga ? `${r.bunga}%` : '—'} />
                        <InfoRow label="Kantor" value={r.kantor?.nama_kantor} />
                        <InfoRow label="Dibuat Oleh" value={r.user?.nama} />
                        <InfoRow label="Keterangan" value={r.keterangan} />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle className="text-base">Rekening Terlibat</CardTitle>
                    </CardHeader>
                    <CardContent>
                        {rekeningList.length === 0 ? (
                            <p className="rounded-lg border border-dashed px-4 py-8 text-center text-sm text-muted-foreground">
                                Tidak ada rekening yang terlibat.
                            </p>
                        ) : (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>No. Rekening</TableHead>
                                        <TableHead>Anggota</TableHead>
                                        <TableHead>Produk</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {rekeningList.map((rek) => (
                                        <TableRow key={rek.id}>
                                            <TableCell>
                                                <span className="flex items-center gap-2 font-mono text-xs">
                                                    <Wallet className="size-3.5 text-muted-foreground" />
                                                    {rek.no_rekening}
                                                </span>
                                            </TableCell>
                                            <TableCell>
                                                {rek.anggota ? (
                                                    <>
                                                        <span className="font-medium">
                                                            {rek.anggota.nama}
                                                        </span>{' '}
                                                        <span className="font-mono text-xs text-muted-foreground">
                                                            ({rek.anggota.no_anggota})
                                                        </span>
                                                    </>
                                                ) : (
                                                    '—'
                                                )}
                                            </TableCell>
                                            <TableCell className="text-muted-foreground">
                                                {rek.jenis_simpanan?.nama ?? '—'}
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        )}
                    </CardContent>
                </Card>
            </div>

            <div className="mt-5 flex max-w-5xl items-center justify-end gap-3">
                <Button variant="outline" asChild>
                    <a
                        href={route('superadmin.simpanan.rencana.cetak', r.id)}
                        target="_blank"
                        rel="noreferrer"
                    >
                        <Printer />
                        Cetak PDF
                    </a>
                </Button>
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route('superadmin.simpanan.rencana.edit', r.id)}>
                        <Pencil />
                        Edit Rencana
                    </Link>
                </Button>
            </div>
        </AuthenticatedLayout>
    );
}
