import { Link, Head } from '@inertiajs/react';
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
    const STATUS_COLOR = { draft: 'bg-amber-500', posted: 'bg-emerald-600', batal: 'bg-rose-600' } as Record<string, string>;

    return (
        <AuthenticatedLayout>
            <Head title={`Angsuran ${t.no_transaksi}`} />
            <PageHeader title={`Detail Angsuran - ${t.no_transaksi}`} />
            <div className="max-w-3xl mx-auto p-6 space-y-6">
                <Card>
                    <CardHeader><CardTitle>Informasi Transaksi</CardTitle></CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div><Label>No. Transaksi</Label><p className="font-mono">{t.no_transaksi}</p></div>
                            <div><Label>Status</Label><Badge className={STATUS_COLOR[t.status] ?? ''}>{t.status}</Badge></div>
                            <div><Label>Tanggal</Label><p>{new Date(t.tgl_transaksi).toLocaleDateString('id-ID')}</p></div>
                            <div><Label>Angsuran Ke</Label><p>{t.angsuran_ke}</p></div>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader><CardTitle>Detail Pinjaman</CardTitle></CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div><Label>No. Pinjaman</Label><p className="font-mono">{t.pinjaman.no_pinjaman}</p></div>
                            <div><Label>Anggota</Label><p>{t.pinjaman.anggota.no_anggota} - {t.pinjaman.anggota.nama}</p></div>
                        </div>
                        <div className="grid gap-4 sm:grid-cols-3">
                            <div><Label>Pokok</Label><p className="font-mono">Rp {Number(t.nominal_pokok).toLocaleString('id-ID')}</p></div>
                            <div><Label>Bunga</Label><p className="font-mono">Rp {Number(t.nominal_bunga).toLocaleString('id-ID')}</p></div>
                            <div><Label>Total</Label><p className="font-mono text-lg font-bold">Rp {Number(t.total_angsuran).toLocaleString('id-ID')}</p></div>
                        </div>
                        {Number(t.denda) > 0 && (
                            <div><Label>Denda</Label><p className="font-mono text-red-600">Rp {Number(t.denda).toLocaleString('id-ID')}</p></div>
                        )}
                        {t.keterangan && <div><Label>Keterangan</Label><p>{t.keterangan}</p></div>}
                    </CardContent>
                </Card>
                <div className="flex gap-3">
                    <Button variant="outline" asChild><Link href={route('superadmin.transaksi-pinjaman.angsuran-pinjaman')}>Kembali</Link></Button>
                    <Button asChild><Link href={route('superadmin.transaksi-pinjaman.angsuran-pinjaman.edit', t.id)}>Edit</Link></Button>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

function Label({ children }: { children: React.ReactNode }) {
    return <span className="text-sm text-muted-foreground">{children}</span>;
}