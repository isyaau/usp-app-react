import { Head } from '@inertiajs/react';
import { Eye } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import type {
    PenutupanSimpananRow,
    PemindahbukuanSimpananRow,
    TransaksiSimpananRow,
} from '@/types/models';

export type DetailTransaksi =
    | TransaksiSimpananRow
    | PenutupanSimpananRow
    | PemindahbukuanSimpananRow;

interface Props {
    transaksiData: DetailTransaksi & {
        user?: { id: number; nama: string } | null;
        simpanan?: { id: number; no_rekening: string; jenis_simpanan?: { nama: string } | null } | null;
        simpananDari?: { id: number; no_rekening: string; jenis_simpanan?: { nama: string } | null } | null;
        simpananKe?: { id: number; no_rekening: string; jenis_simpanan?: { nama: string } | null } | null;
    };
    /** Route kembali ke daftar (opsional). */
    backHref?: string;
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

/**
 * Halaman detail transaksi simpanan.
 * Dipakai bersama oleh modul Setoran/Tarikan/Penutupan/Pemindahbukuan.
 */
export default function TransaksiShow({ transaksiData: t, backHref }: Props) {
    const rupiah = (v: string | number) =>
        `Rp ${Number(v ?? 0).toLocaleString('id-ID')}`;

    const rekening = (r: { no_rekening: string; jenis_simpanan?: { nama: string } | null }) =>
        r.jenis_simpanan ? `${r.no_rekening} · ${r.jenis_simpanan.nama}` : r.no_rekening;

    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${t.no_transaksi}`} />

            <PageHeader
                title="Detail Transaksi"
                description={`Nomor ${t.no_transaksi}`}
                icon={Eye}
                backHref={backHref}
            />

            <Card className="max-w-2xl">
                <CardHeader>
                    <CardTitle>{t.no_transaksi}</CardTitle>
                </CardHeader>
                <CardContent>
                    <Separator className="mb-2" />
                    <InfoRow label="Tanggal Transaksi" value={t.tgl_transaksi} />
                    <InfoRow
                        label="Anggota"
                        value={
                            t.anggota
                                ? `${t.anggota.no_anggota} · ${t.anggota.nama}`
                                : '—'
                        }
                    />

                    {'simpananDari' in t && t.simpananDari ? (
                        <>
                            <InfoRow label="Rekening Asal" value={rekening(t.simpananDari)} />
                            <InfoRow
                                label="Rekening Tujuan"
                                value={t.simpananKe ? rekening(t.simpananKe) : '—'}
                            />
                        </>
                    ) : (
                        <InfoRow
                            label="Rekening Simpanan"
                            value={t.simpanan ? rekening(t.simpanan) : '—'}
                        />
                    )}

                    <InfoRow
                        label="Kode Transaksi"
                        value={
                            t.kodeTransaksi
                                ? `${t.kodeTransaksi.kode} · ${t.kodeTransaksi.nama}`
                                : '—'
                        }
                    />
                    <InfoRow
                        label={
                            'nominal_bunga' in t ? 'Nominal Pokok' : 'Nominal'
                        }
                        value={
                            <span className="font-semibold tabular-nums">
                                {rupiah(t.nominal)}
                            </span>
                        }
                    />
                    {'nominal_bunga' in t && (
                        <InfoRow
                            label="Nominal Bunga"
                            value={
                                <span className="font-semibold tabular-nums">
                                    {rupiah(t.nominal_bunga)}
                                </span>
                            }
                        />
                    )}
                    <InfoRow label="Status" value={t.status.toUpperCase()} />
                    <InfoRow label="Kantor" value={t.kantor?.nama_kantor ?? '—'} />
                    <InfoRow label="Dicatat Oleh" value={t.user?.nama ?? '—'} />
                    <InfoRow label="Keterangan" value={t.keterangan ?? '—'} />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
