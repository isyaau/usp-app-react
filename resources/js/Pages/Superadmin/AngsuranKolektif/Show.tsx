import { Link, Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';

interface Detail {
    id: number;
    angsuran_ke: number;
    nominal_pokok: number;
    nominal_bunga: number;
    total_angsuran: number;
    setoran_simpanan: number | null;
    denda: number;
    pinjaman: { no_pinjaman: string; anggota: { nama: string; no_anggota: string } };
}

interface Transaksi {
    id: number; no_transaksi: string; tgl_transaksi: string;
    jenis: string; metode_pembayaran: string;
    nominal_total: number; jumlah_anggota: number;
    keterangan: string | null; status: string;
    kelompok: { nama: string };
    user: { nama: string };
    kantor: { nama_kantor: string };
    details: Detail[];
    variant_title?: string;
}

interface Props { transaksi: Transaksi; }

export default function Show({ transaksi: t }: Props) {
    const SC: Record<string, string> = { draft: 'bg-amber-500', posted: 'bg-emerald-600', batal: 'bg-rose-600' };
    const JL: Record<string, string> = { angsuran: 'Angsuran', penalti: 'Penalti', angsuran_dan_setoran: 'Angsuran & Setoran' };
    const ML: Record<string, string> = { tunai: 'Tunai', debet_simpanan: 'Debet Simpanan', bank: 'Bank', custom: 'Custom' };
    const title = t.variant_title ?? 'Angsuran Kolektif';

    return (
        <AuthenticatedLayout>
            <Head title={`${title} ${t.no_transaksi}`} />
            <PageHeader title={`Detail ${title} - ${t.no_transaksi}`} />
            <div className="max-w-5xl mx-auto p-6 space-y-6">
                <Card>
                    <CardHeader><CardTitle>Informasi Transaksi</CardTitle></CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid gap-4 sm:grid-cols-3">
                            <div><p className="text-sm text-muted-foreground">No. Transaksi</p><p className="font-mono">{t.no_transaksi}</p></div>
                            <div><p className="text-sm text-muted-foreground">Status</p><Badge className={SC[t.status] ?? ''}>{t.status}</Badge></div>
                            <div><p className="text-sm text-muted-foreground">Tanggal</p><p>{new Date(t.tgl_transaksi).toLocaleDateString('id-ID')}</p></div>
                            <div><p className="text-sm text-muted-foreground">Kelompok</p><p>{t.kelompok.nama}</p></div>
                            <div><p className="text-sm text-muted-foreground">Jenis</p><Badge variant="outline">{JL[t.jenis] ?? t.jenis}</Badge></div>
                            <div><p className="text-sm text-muted-foreground">Metode</p><Badge variant="secondary">{ML[t.metode_pembayaran] ?? t.metode_pembayaran}</Badge></div>
                            <div><p className="text-sm text-muted-foreground">Total</p><p className="font-mono text-lg font-bold">Rp {Number(t.nominal_total).toLocaleString('id-ID')}</p></div>
                            <div><p className="text-sm text-muted-foreground">Jumlah Anggota</p><p>{t.jumlah_anggota}</p></div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle>Detail per Anggota ({t.details.length})</CardTitle></CardHeader>
                    <CardContent>
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead><tr className="border-b">
                                    <th className="p-2 text-left">#</th>
                                    <th className="p-2 text-left">Anggota</th>
                                    <th className="p-2 text-left">Pinjaman</th>
                                    <th className="p-2 text-left">Ke</th>
                                    <th className="p-2 text-right">Pokok</th>
                                    <th className="p-2 text-right">Bunga</th>
                                    <th className="p-2 text-right">Total</th>
                                    {t.metode_pembayaran === 'debet_simpanan' && <th className="p-2 text-right">Set. Simpanan</th>}
                                </tr></thead>
                                <tbody>
                                    {t.details.map((d, i) => (
                                        <tr key={d.id} className="border-b hover:bg-muted/50">
                                            <td className="p-2">{i + 1}</td>
                                            <td className="p-2">{d.pinjaman.anggota.no_anggota} - {d.pinjaman.anggota.nama}</td>
                                            <td className="p-2 font-mono text-xs">{d.pinjaman.no_pinjaman}</td>
                                            <td className="p-2">{d.angsuran_ke}</td>
                                            <td className="p-2 text-right font-mono">Rp {Number(d.nominal_pokok).toLocaleString('id-ID')}</td>
                                            <td className="p-2 text-right font-mono">Rp {Number(d.nominal_bunga).toLocaleString('id-ID')}</td>
                                            <td className="p-2 text-right font-mono font-bold">Rp {Number(d.total_angsuran).toLocaleString('id-ID')}</td>
                                            {t.metode_pembayaran === 'debet_simpanan' && <td className="p-2 text-right font-mono">{d.setoran_simpanan ? `Rp ${Number(d.setoran_simpanan).toLocaleString('id-ID')}` : '-'}</td>}
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>

                <div className="flex gap-3">
                    <Button variant="outline" asChild><Link href={route(t.variant_title ? `superadmin.transaksi-pinjaman.${t.variant_title}` : 'superadmin.transaksi-pinjaman.angsuran-kolektif')}>Kembali</Link></Button>
                    <Button asChild><Link href={route(`superadmin.transaksi-pinjaman.angsuran-kolektif.edit`, t.id)}>Edit</Link></Button>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}