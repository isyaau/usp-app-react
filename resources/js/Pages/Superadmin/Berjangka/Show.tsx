import { Head } from '@inertiajs/react';
import { CalendarClock, Eye } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Badge } from '@/Components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import type { DepositoDetail } from '@/types/models';
import { LIST_PEMBAYARAN } from './form';

interface Props {
    berjangkaData: DepositoDetail;
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

const BAYAR_BUNGA_LABELS: Record<string, string> = {
    '1': 'A.R.O.',
    '2': 'Diambil Sendiri',
    '3': 'Transfer ke No. Simpanan',
};

const BAYAR_JATUHTEMPO_LABELS: Record<string, string> = {
    '1': 'Diambil Sendiri',
    '2': 'Transfer ke No. Simpanan',
};

const rupiah = (v: string | number) => `Rp ${Number(v ?? 0).toLocaleString('id-ID')}`;

export default function BerjangkaShow({ berjangkaData: b }: Props) {
    const jatuhTempo = (() => {
        if (!b.tanggal || !b.jangka_waktu) return null;
        const d = new Date(b.tanggal);
        d.setMonth(d.getMonth() + Number(b.jangka_waktu));
        return d.toISOString().slice(0, 10);
    })();

    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${b.no_deposito}`} />

            <PageHeader
                title="Detail Simpanan Berjangka"
                description={`No. ${b.no_deposito}`}
                icon={Eye}
                backHref={route('superadmin.simpanan-berjangka')}
            />

            <div className="grid max-w-4xl gap-5 lg:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center justify-between gap-2">
                            <span className="flex items-center gap-2">
                                <CalendarClock className="size-4 text-brand-600" />
                                {b.no_deposito}
                            </span>
                            <Badge variant={b.blokir === '1' ? 'destructive' : 'success'}>
                                {b.blokir === '1' ? 'Diblokir' : 'Aktif'}
                            </Badge>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Separator className="mb-2" />
                        <InfoRow label="Tanggal" value={b.tanggal} />
                        <InfoRow
                            label="Anggota"
                            value={
                                b.anggota && (
                                    <>
                                        <span className="font-medium">{b.anggota.nama}</span>{' '}
                                        <span className="font-mono text-xs text-muted-foreground">
                                            ({b.anggota.no_anggota})
                                        </span>
                                    </>
                                )
                            }
                        />
                        <InfoRow label="QQ" value={b.qq} />
                        <InfoRow label="Marketing" value={b.marketing?.nama} />
                        <InfoRow label="Kantor" value={b.kantor?.nama_kantor} />

                        <Separator className="my-2" />
                        <InfoRow
                            label="Produk"
                            value={
                                b.produk && (
                                    <>
                                        <span className="font-medium">{b.produk.nama}</span>{' '}
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {b.produk.kode}
                                        </span>
                                    </>
                                )
                            }
                        />
                        <InfoRow label="Jangka Waktu" value={`${b.jangka_waktu} bulan`} />
                        <InfoRow label="Bunga" value={`${b.bunga}%`} />
                        <InfoRow label="Nominal" value={<strong>{rupiah(b.nominal)}</strong>} />
                        <InfoRow label="Jatuh Tempo (estimasi)" value={jatuhTempo} />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Pembayaran</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Separator className="mb-2" />
                        <InfoRow
                            label="Jenis Bayar Bunga"
                            value={
                                BAYAR_BUNGA_LABELS[b.bayar_bunga] ?? `Kode ${b.bayar_bunga}`
                            }
                        />
                        <InfoRow
                            label="Tujuan Transfer Bunga"
                            value={
                                b.tabunganBunga ? (
                                    <span className="font-mono text-xs">
                                        {b.tabunganBunga.no_rekening}
                                    </span>
                                ) : (
                                    '—'
                                )
                            }
                        />
                        <InfoRow
                            label="Cara Pembayaran"
                            value={LIST_PEMBAYARAN[b.diawal] ?? `Kode ${b.diawal}`}
                        />
                        <InfoRow
                            label="Perpanjangan Otomatis"
                            value={
                                <Badge variant={b.otomatis === '1' ? 'success' : 'secondary'}>
                                    {b.otomatis === '1' ? 'Ya (A.R.O.)' : 'Tidak'}
                                </Badge>
                            }
                        />
                        <Separator className="my-2" />
                        <InfoRow
                            label="Bayar Jatuh Tempo"
                            value={
                                BAYAR_JATUHTEMPO_LABELS[b.bayar_jatuhtempo] ??
                                `Kode ${b.bayar_jatuhtempo}`
                            }
                        />
                        <InfoRow
                            label="Tujuan Transfer Jatuh Tempo"
                            value={
                                b.tabunganTempo ? (
                                    <span className="font-mono text-xs">
                                        {b.tabunganTempo.no_rekening}
                                    </span>
                                ) : (
                                    '—'
                                )
                            }
                        />
                        <InfoRow
                            label="Bunga Accrual"
                            value={
                                <Badge variant={b.bunga_accrual === '1' ? 'success' : 'secondary'}>
                                    {b.bunga_accrual === '1' ? 'Aktif' : 'Nonaktif'}
                                </Badge>
                            }
                        />
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}
