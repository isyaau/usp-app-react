import { Link, Head } from '@inertiajs/react';
import { CreditCard } from 'lucide-react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';

interface Transaksi {
    id: number; no_transaksi: string; tgl_transaksi: string;
    angsuran_ke: number; nominal_pokok: number; nominal_bunga: number;
    total_angsuran: number; denda: number; keterangan: string | null;
    status: string; created_at: string;
    pinjaman: { no_pinjaman: string; anggota: { nama: string; no_anggota: string } };
    user: { name: string };
    kantor: { nama_kantor: string };
}

interface Props { transaksi: Transaksi; }

export default function Show({ transaksi: t }: Props) {
    const SC = { draft: 'bg-amber-500', posted: 'bg-emerald-600', batal: 'bg-rose-600' } as Record<string, string>;
    const base = 'superadmin.transaksi-pinjaman.angsuran-pinjaman';

    return (
        <AuthenticatedLayout>
            <Head title={`Detail Angsuran — ${t.no_transaksi}`} />
            <PageHeader
                title={`Detail Angsuran — ${t.no_transaksi}`}
                description="Informasi detail angsuran pinjaman."
                icon={CreditCard}
                backHref={route(base)}
            >
                <Button variant="outline" asChild><Link href={route(base + '.edit', t.id)}>Edit</Link></Button>
            </PageHeader>
            <div className="max-w-3xl space-y-4">
                <Card>
                    <CardHeader><CardTitle>Informasi Transaksi</CardTitle></CardHeader>
                    <CardContent className="grid gap-3 sm:grid-cols-2">
                        <div><p className="text-xs text-muted-foreground">No. Transaksi</p><p className="font-mono font-bold">{t.no_transaksi}</p></div>
                        <div><p className="text-xs text-muted-foreground">Status</p><Badge className={SC[t.status] ?? ''}>{t.status}</Badge></div>
                        <div><p className="text-xs text-muted-foreground">Tanggal</p><p>{new Date(t.tgl_transaksi).toLocaleDateString('id-ID')}</p></div>
                        <div><p className="text-xs text-muted-foreground">Angsuran Ke</p><p>{t.angsuran_ke}</p></div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader><CardTitle>Detail Pinjaman</CardTitle></CardHeader>
                    <CardContent className="grid gap-3 sm:grid-cols-2">
                        <div><p className="text-xs text-muted-foreground">No. Pinjaman</p><p className="font-mono">{t.pinjaman.no_pinjaman}</p></div>
                        <div><p className="text-xs text-muted-foreground">Anggota</p><p>{t.pinjaman.anggota.no_anggota} — {t.pinjaman.anggota.nama}</p></div>
                        <div><p className="text-xs text-muted-foreground">Pokok</p><p className="font-mono">Rp {Number(t.nominal_pokok).toLocaleString('id-ID')}</p></div>
                        <div><p className="text-xs text-muted-foreground">Bunga</p><p className="font-mono">Rp {Number(t.nominal_bunga).toLocaleString('id-ID')}</p></div>
                        <div><p className="text-xs text-muted-foreground">Total</p><p className="font-mono text-lg font-bold">Rp {Number(t.total_angsuran).toLocaleString('id-ID')}</p></div>
                        {Number(t.denda) > 0 && <div><p className="text-xs text-muted-foreground">Denda</p><p className="font-mono text-red-600">Rp {Number(t.denda).toLocaleString('id-ID')}</p></div>}
                        <div className="sm:col-span-2"><p className="text-xs text-muted-foreground">Keterangan</p><p>{t.keterangan || '-'}</p></div>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}