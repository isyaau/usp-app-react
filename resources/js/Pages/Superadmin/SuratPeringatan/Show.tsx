import { Link, Head } from '@inertiajs/react';
import { FileWarning } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';

interface Transaksi {
    id: number;
    no_transaksi: string;
    tgl_transaksi: string;
    tahap: string;
    isi: string | null;
    status: string;
    pinjaman: { no_pinjaman: string; plafon: number; anggota: { nama: string; no_anggota: string } };
    user: { nama: string };
    kantor: { nama_kantor: string };
}

interface Props { transaksi: Transaksi; }

export default function Show({ transaksi: t }: Props) {
    const root = 'superadmin.pinjaman.surat-peringatan';
    const SC = { draft: 'bg-amber-500', posted: 'bg-emerald-600', batal: 'bg-rose-600' } as Record<string, string>;
    const TAHAP = { 'SP-1': 'bg-sky-600', 'SP-2': 'bg-orange-600', 'SP-3': 'bg-red-700' } as Record<string, string>;

    return (
        <AuthenticatedLayout>
            <Head title={`Detail Surat Peringatan — ${t.no_transaksi}`} />
            <PageHeader
                title={`Detail Surat Peringatan — ${t.no_transaksi}`}
                description="Informasi detail surat peringatan atas keterlambatan angsuran."
                icon={FileWarning}
                backHref={route(root)}
            >
                <Button variant="outline" onClick={() => window.open(route(`${root}.cetak`, t.id), '_blank')}>
                    Cetak PDF
                </Button>
                <Button variant="outline" asChild><Link href={route(`${root}.edit`, t.id)}>Edit</Link></Button>
            </PageHeader>
            <div className="max-w-3xl space-y-4">
                <Card>
                    <CardHeader><CardTitle>Informasi Transaksi</CardTitle></CardHeader>
                    <CardContent className="grid gap-3 sm:grid-cols-2">
                        <div><p className="text-xs text-muted-foreground">No. Surat</p><p className="font-mono font-bold">{t.no_transaksi}</p></div>
                        <div><p className="text-xs text-muted-foreground">Tanggal</p><p>{new Date(t.tgl_transaksi).toLocaleDateString('id-ID')}</p></div>
                        <div><p className="text-xs text-muted-foreground">Tahap</p><Badge className={TAHAP[t.tahap] ?? ''}>{t.tahap}</Badge></div>
                        <div><p className="text-xs text-muted-foreground">Status</p><Badge className={SC[t.status] ?? ''}>{t.status}</Badge></div>
                        <div><p className="text-xs text-muted-foreground">Anggota</p><p>{t.pinjaman.anggota.no_anggota} — {t.pinjaman.anggota.nama}</p></div>
                        <div><p className="text-xs text-muted-foreground">Pinjaman</p><p className="font-mono">{t.pinjaman.no_pinjaman}</p></div>
                        <div><p className="text-xs text-muted-foreground">Plafon</p><p className="font-mono">Rp {Number(t.pinjaman.plafon).toLocaleString('id-ID')}</p></div>
                        <div><p className="text-xs text-muted-foreground">Kantor</p><p>{t.kantor.nama_kantor}</p></div>
                        <div><p className="text-xs text-muted-foreground">Dicatat oleh</p><p>{t.user.nama}</p></div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader><CardTitle>Isi Surat</CardTitle></CardHeader>
                    <CardContent>
                        <p className="whitespace-pre-wrap">{t.isi || '-'}</p>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}