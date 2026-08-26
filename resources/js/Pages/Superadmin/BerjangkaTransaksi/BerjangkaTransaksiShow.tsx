import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { CalendarClock } from 'lucide-react';

interface Props {
    transaksi: any;
    variantTitle: string;
    config?: { label?: string; routeIndex: string; icon?: any };
}

const STATUS: Record<string, string> = {
    draft: 'bg-amber-500/90',
    posted: 'bg-emerald-600',
    batal: 'bg-rose-600',
};

function Rp(v: number | string | undefined) {
    return 'Rp ' + Number(v ?? 0).toLocaleString('id-ID');
}

export default function BerjangkaTransaksiShow({ transaksi: t, variantTitle, config }: Props) {
    const Icon = config?.icon || CalendarClock;
    const routeBase = config?.routeIndex || (() => {
        if (t.no_transaksi?.startsWith('SB')) return 'superadmin.transaksi-simpanan-berjangka.setoran-simpanan-berjangka';
        if (t.no_transaksi?.startsWith('PC')) return 'superadmin.transaksi-simpanan-berjangka.pencairan-simpanan-berjangka';
        if (t.no_transaksi?.startsWith('DT')) return 'superadmin.transaksi-titipan.penarikan-dana-titipan';
        return 'superadmin.transaksi-simpanan-berjangka.penalti-simpanan-berjangka';
    })();

    return (
        <AuthenticatedLayout>
            <Head title={(variantTitle || config?.label || 'Transaksi') + ' — Detail'} />

            <PageHeader title={variantTitle || config?.label || 'Transaksi'} description="Detail transaksi." icon={Icon}>
                <Button variant="outline" asChild>
                    <Link href={route(routeBase + '.edit', t.id)}>Edit</Link>
                </Button>
                <Button variant="outline" asChild>
                    <Link href={route(routeBase)}>Kembali</Link>
                </Button>
            </PageHeader>

            <Card>
                <CardHeader><CardTitle>Informasi Transaksi</CardTitle></CardHeader>
                <CardContent className="grid gap-3 sm:grid-cols-2">
                    <div><p className="text-xs text-muted-foreground">No. Transaksi</p><p className="font-mono font-bold">{t.no_transaksi}</p></div>
                    <div><p className="text-xs text-muted-foreground">Tanggal</p><p>{new Date(t.tgl_transaksi).toLocaleDateString('id-ID')}</p></div>
                    <div><p className="text-xs text-muted-foreground">Anggota</p><p>{t.anggota?.nama ?? '-'} ({t.anggota?.no_anggota ?? ''})</p></div>
                    <div><p className="text-xs text-muted-foreground">Deposito</p><p>{t.deposito?.no_deposito ?? '-'}</p></div>
                    {t.nominal !== undefined && <div><p className="text-xs text-muted-foreground">Nominal</p><p className="font-bold">{Rp(t.nominal)}</p></div>}
                    {t.nominal_pokok !== undefined && <div><p className="text-xs text-muted-foreground">Nominal Pokok</p><p className="font-bold">{Rp(t.nominal_pokok)}</p></div>}
                    {t.nominal_bunga !== undefined && <div><p className="text-xs text-muted-foreground">Bunga</p><p>{Rp(t.nominal_bunga)}</p></div>}
                    {t.nominal_pajak !== undefined && <div><p className="text-xs text-muted-foreground">Pajak</p><p>{Rp(t.nominal_pajak)}</p></div>}
                    {t.nominal_penalti !== undefined && <div><p className="text-xs text-muted-foreground">Penalti</p><p>{Rp(t.nominal_penalti)}</p></div>}
                    {t.nominal_penarikan !== undefined && <div><p className="text-xs text-muted-foreground">Nominal Penarikan</p><p className="font-bold">{Rp(t.nominal_penarikan)}</p></div>}
                    {t.nominal_diterima !== undefined && <div><p className="text-xs text-muted-foreground">Diterima</p><p className="font-bold text-emerald-600">{Rp(t.nominal_diterima)}</p></div>}
                    {t.total_penalti !== undefined && <div><p className="text-xs text-muted-foreground">Total Penalti</p><p className="font-bold text-rose-600">{Rp(t.total_penalti)}</p></div>}
                    <div><p className="text-xs text-muted-foreground">Status</p><Badge className={STATUS[t.status] ?? ''}>{t.status}</Badge></div>
                    <div><p className="text-xs text-muted-foreground">User</p><p>{t.user?.nama ?? '-'}</p></div>
                    <div><p className="text-xs text-muted-foreground">Kantor</p><p>{t.kantor?.nama_kantor ?? '-'}</p></div>
                    <div className="sm:col-span-2"><p className="text-xs text-muted-foreground">Keterangan</p><p>{t.keterangan || '-'}</p></div>
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
