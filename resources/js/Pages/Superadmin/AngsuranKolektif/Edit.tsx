import { router, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Textarea } from '@/Components/ui/textarea';
import { Badge } from '@/Components/ui/badge';

interface Transaksi {
    id: number; no_transaksi: string; tgl_transaksi: string;
    jenis: string; metode_pembayaran: string;
    nominal_total: number; jumlah_anggota: number;
    keterangan: string | null; status: string; kantor_id: number;
    kelompok: { nama: string };
    variant_title?: string;
}

interface Props {
    transaksi: Transaksi;
    kantors: { id: number; nama_kantor: string }[];
}

export default function Edit({ transaksi, kantors }: Props) {
    const title = transaksi.variant_title ?? 'Angsuran Kolektif';

    const { data, setData, put, processing } = useForm({
        tgl_transaksi: transaksi.tgl_transaksi,
        kantor_id: String(transaksi.kantor_id),
        keterangan: transaksi.keterangan ?? '',
        status: transaksi.status as 'draft' | 'posted' | 'batal',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('superadmin.transaksi-pinjaman.angsuran-kolektif.update', transaksi.id));
    };

    const JL: Record<string, string> = { angsuran: 'Angsuran', penalti: 'Penalti', angsuran_dan_setoran: 'Angsuran & Setoran' };
    const ML: Record<string, string> = { tunai: 'Tunai', debet_simpanan: 'Debet Simpanan', bank: 'Bank', custom: 'Custom' };

    return (
        <AuthenticatedLayout>
            <PageHeader title={`Edit ${title} - ${transaksi.no_transaksi}`} />
            <div className="max-w-3xl mx-auto p-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Edit {title}</CardTitle>
                        <CardDescription>
                            Kelompok: {transaksi.kelompok.nama} |
                            <Badge variant="outline" className="ml-2">{JL[transaksi.jenis] ?? transaksi.jenis}</Badge>
                            <Badge variant="secondary" className="ml-2">{ML[transaksi.metode_pembayaran] ?? transaksi.metode_pembayaran}</Badge>
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="space-y-6">
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label>Tanggal Transaksi</Label>
                                    <Input type="date" value={data.tgl_transaksi} onChange={e => setData('tgl_transaksi', e.target.value)} />
                                </div>
                                <div className="space-y-2">
                                    <Label>Kantor</Label>
                                    <Select value={data.kantor_id} onValueChange={v => setData('kantor_id', v)}>
                                        <SelectTrigger><SelectValue /></SelectTrigger>
                                        <SelectContent>
                                            {kantors.map(k => <SelectItem key={k.id} value={String(k.id)}>{k.nama_kantor}</SelectItem>)}
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label>Status</Label>
                                    <Select value={data.status} onValueChange={v => setData('status', v as any)}>
                                        <SelectTrigger><SelectValue /></SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="draft">Draft</SelectItem>
                                            <SelectItem value="posted">Posted</SelectItem>
                                            <SelectItem value="batal">Batal</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div className="space-y-2">
                                    <Label>Nominal Total</Label>
                                    <Input type="text" value={`Rp ${Number(transaksi.nominal_total).toLocaleString('id-ID')}`} readOnly className="bg-muted font-mono" />
                                </div>
                            </div>
                            <div className="space-y-2">
                                <Label>Keterangan</Label>
                                <Textarea value={data.keterangan} onChange={e => setData('keterangan', e.target.value)} rows={3} />
                            </div>
                            <div className="flex justify-end gap-3">
                                <Button type="button" variant="outline" onClick={() => router.get(route('superadmin.transaksi-pinjaman.angsuran-kolektif'))}>Batal</Button>
                                <Button type="submit" disabled={processing}>
                                    {processing && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}
                                    Update
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}