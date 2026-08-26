import { Head, Link } from '@inertiajs/react';
import { Receipt } from 'lucide-react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';

const STATUS: Record<string, string> = { draft: 'bg-amber-500/90', posted: 'bg-emerald-600', batal: 'bg-rose-600' };
const fmtRp = (v: number | string | undefined) => 'Rp ' + Number(v ?? 0).toLocaleString('id-ID');

export default function Show({ transaksi: t }: { transaksi: any }) {
    const base = 'superadmin.laporan.laporan-kas-harian';
    return (
        <AuthenticatedLayout>
            <Head title="Detail Laporan Kas Harian" />
            <PageHeader title="Detail Laporan Kas Harian" description="Informasi laporan kas harian." icon={Receipt}>
                <Button variant="outline" asChild><Link href={route(base + '.edit', t.id)}>Edit</Link></Button>
                <Button variant="outline" asChild><Link href={route(base)}>Kembali</Link></Button>
            </PageHeader>
            <Card className="max-w-3xl">
                <CardHeader><CardTitle>Informasi Laporan</CardTitle></CardHeader>
                <CardContent className="grid gap-3 sm:grid-cols-2">
                    <div><p className="text-xs text-muted-foreground">No. Laporan</p><p className="font-mono font-bold">{t.no_laporan}</p></div>
                    <div><p className="text-xs text-muted-foreground">Tanggal</p><p>{t.tgl_laporan ? new Date(t.tgl_laporan).toLocaleDateString('id-ID') : '-'}</p></div>
                    <div><p className="text-xs text-muted-foreground">Saldo Awal</p><p>{fmtRp(t.saldo_awal)}</p></div>
                    <div><p className="text-xs text-muted-foreground">Total Pemasukan</p><p className="text-emerald-600">{fmtRp(t.total_pemasukan)}</p></div>
                    <div><p className="text-xs text-muted-foreground">Total Pengeluaran</p><p className="text-rose-600">{fmtRp(t.total_pengeluaran)}</p></div>
                    <div><p className="text-xs text-muted-foreground">Saldo Akhir</p><p className="font-bold">{fmtRp(t.saldo_akhir)}</p></div>
                    <div><p className="text-xs text-muted-foreground">Status</p><Badge className={STATUS[t.status] ?? ''}>{t.status}</Badge></div>
                    <div><p className="text-xs text-muted-foreground">User</p><p>{t.user?.nama ?? '-'}</p></div>
                    <div><p className="text-xs text-muted-foreground">Kantor</p><p>{t.kantor?.nama_kantor ?? '-'}</p></div>
                    <div className="sm:col-span-2"><p className="text-xs text-muted-foreground">Keterangan</p><p>{t.keterangan || '-'}</p></div>
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
