import { Link, Head } from '@inertiajs/react';
import { Wallet } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';

interface Transaksi {
    id: number;
    no_transaksi: string;
    tgl_transaksi: string;
    sisa_pokok: number;
    keterangan: string | null;
    status: string;
    pinjaman: { no_pinjaman: string; plafon: number; anggota: { nama: string; no_anggota: string } };
    user: { nama: string };
    kantor: { nama_kantor: string };
}

interface Props { transaksi: Transaksi; }

export default function Show({ transaksi: t }: Props) {
    const root = 'superadmin.pinjaman.penghapusan';
    const SC = { draft: 'bg-amber-500', posted: 'bg-emerald-600', batal: 'bg-rose-600' } as Record<string, string>;

    return (
        <AuthenticatedLayout>
            <Head title={`Detail Penghapusan — ${t.no_transaksi}`} />
            <PageHeader
                title={`Detail Penghapusan — ${t.no_transaksi}`}
                description="Informasi detail penghapusan (hapus buku) pinjaman."
                icon={Wallet}
                backHref={route(root)}
            >
                <Button variant="outline" asChild><Link href={route(`${root}.edit`, t.id)}>Edit</Link></Button>
            </PageHeader>
            <div className="max-w-3xl space-y-4">
                <Card>
                    <CardHeader><CardTitle>Informasi Transaksi</CardTitle></CardHeader>
                    <CardContent className="grid gap-3 sm:grid-cols-2">
                        <div><p className="text-xs text-muted-foreground">No. Transaksi</p><p className="font-mono font-bold">{t.no_transaksi}</p></div>
                        <div><p className="text-xs text-muted-foreground">Status</p><Badge className={SC[t.status] ?? ''}>{t.status}</Badge></div>
                        <div><p className="text-xs text-muted-foreground">Tanggal</p><p>{new Date(t.tgl_transaksi).toLocaleDateString('id-ID')}</p></div>
                        <div><p className="text-xs text-muted-foreground">Kantor</p><p>{t.kantor.nama_kantor}</p></div>
                        <div><p className="text-xs text-muted-foreground">Anggota</p><p>{t.pinjaman.anggota.no_anggota} — {t.pinjaman.anggota.nama}</p></div>
                        <div><p className="text-xs text-muted-foreground">Pinjaman</p><p className="font-mono">{t.pinjaman.no_pinjaman}</p></div>
                        <div><p className="text-xs text-muted-foreground">Plafon</p><p className="font-mono">Rp {Number(t.pinjaman.plafon).toLocaleString('id-ID')}</p></div>
                        <div><p className="text-xs text-muted-foreground">Dicatat oleh</p><p>{t.user.nama}</p></div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader><CardTitle>Detail Penghapusan</CardTitle></CardHeader>
                    <CardContent className="grid gap-3 sm:grid-cols-2">
                        <div><p className="text-xs text-muted-foreground">Sisa Pokok Dihapus</p><p className="font-mono text-lg font-bold text-red-600">Rp {Number(t.sisa_pokok).toLocaleString('id-ID')}</p></div>
                        <div className="sm:col-span-2"><p className="text-xs text-muted-foreground">Alasan / Keterangan</p><p>{t.keterangan || '-'}</p></div>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}