import { Link, Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';

interface Transaksi {
    id: number; no_transaksi: string; tgl_transaksi: string;
    nominal_penalti: number; denda: number; keterangan: string | null;
    status: string;
    pinjaman: { no_pinjaman: string; anggota: { nama: string; no_anggota: string } };
    user: { name: string };
    kantor: { nama_kantor: string };
}

interface Props { transaksi: Transaksi; }

export default function Show({ transaksi: t }: Props) {
    const SC = { draft: 'bg-amber-500', posted: 'bg-emerald-600', batal: 'bg-rose-600' } as Record<string, string>;

    return (
        <AuthenticatedLayout>
            <Head title={`Penalti ${t.no_transaksi}`} />
            <PageHeader title={`Detail Penalti - ${t.no_transaksi}`} />
            <div className="max-w-3xl mx-auto p-6 space-y-6">
                <Card>
                    <CardHeader><CardTitle>Informasi Transaksi</CardTitle></CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div><p className="text-sm text-muted-foreground">No. Transaksi</p><p className="font-mono">{t.no_transaksi}</p></div>
                            <div><p className="text-sm text-muted-foreground">Status</p><Badge className={SC[t.status] ?? ''}>{t.status}</Badge></div>
                            <div><p className="text-sm text-muted-foreground">Tanggal</p><p>{new Date(t.tgl_transaksi).toLocaleDateString('id-ID')}</p></div>
                            <div><p className="text-sm text-muted-foreground">Pinjaman</p><p className="font-mono">{t.pinjaman.no_pinjaman}</p></div>
                            <div><p className="text-sm text-muted-foreground">Anggota</p><p>{t.pinjaman.anggota.no_anggota} - {t.pinjaman.anggota.nama}</p></div>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader><CardTitle>Detail Penalti</CardTitle></CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div><p className="text-sm text-muted-foreground">Nominal Penalti</p><p className="font-mono text-lg font-bold text-red-600">Rp {Number(t.nominal_penalti).toLocaleString('id-ID')}</p></div>
                            {Number(t.denda) > 0 && <div><p className="text-sm text-muted-foreground">Denda Tambahan</p><p className="font-mono text-red-600">Rp {Number(t.denda).toLocaleString('id-ID')}</p></div>}
                        </div>
                        {t.keterangan && <div><p className="text-sm text-muted-foreground">Keterangan</p><p>{t.keterangan}</p></div>}
                    </CardContent>
                </Card>
                <div className="flex gap-3">
                    <Button variant="outline" asChild><Link href={route('superadmin.transaksi-pinjaman.penalti-pinjaman')}>Kembali</Link></Button>
                    <Button asChild><Link href={route('superadmin.transaksi-pinjaman.penalti-pinjaman.edit', t.id)}>Edit</Link></Button>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}