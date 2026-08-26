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

interface Transaksi {
    id: number; no_transaksi: string; tgl_transaksi: string;
    angsuran_ke: number; nominal_pokok: number; nominal_bunga: number;
    total_angsuran: number; denda: number; keterangan: string | null;
    status: string; kantor_id: number;
    pinjaman: { id: number; no_pinjaman: string; anggota: { nama: string } };
}

interface Props { transaksi: Transaksi; anggotas: { id: number; nama: string }[]; kantors: { id: number; nama_kantor: string }[]; }

export default function Edit({ transaksi, anggotas, kantors }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        tgl_transaksi: transaksi.tgl_transaksi,
        angsuran_ke: String(transaksi.angsuran_ke),
        nominal_pokok: String(transaksi.nominal_pokok),
        nominal_bunga: String(transaksi.nominal_bunga),
        total_angsuran: String(transaksi.total_angsuran),
        denda: String(transaksi.denda),
        keterangan: transaksi.keterangan ?? '',
        kantor_id: String(transaksi.kantor_id),
        status: transaksi.status as 'draft' | 'posted' | 'batal',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('superadmin.transaksi-pinjaman.angsuran-pinjaman.update', transaksi.id));
    };

    return (
        <AuthenticatedLayout>
            <PageHeader title={`Edit Angsuran - ${transaksi.no_transaksi}`} />
            <div className="max-w-3xl mx-auto p-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Edit Angsuran Pinjaman</CardTitle>
                        <CardDescription>Pinjaman: {transaksi.pinjaman.no_pinjaman} ({transaksi.pinjaman.anggota.nama})</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="space-y-6">
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label>Tanggal Transaksi</Label>
                                    <Input type="date" value={data.tgl_transaksi} onChange={e => setData('tgl_transaksi', e.target.value)} />
                                </div>
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
                            </div>
                            <div className="grid gap-4 sm:grid-cols-3">
                                <div className="space-y-2"><Label>Angsuran Ke</Label><Input type="number" value={data.angsuran_ke} onChange={e => setData('angsuran_ke', e.target.value)} /></div>
                                <div className="space-y-2"><Label>Nominal Pokok</Label><Input type="number" value={data.nominal_pokok} onChange={e => setData('nominal_pokok', e.target.value)} /></div>
                                <div className="space-y-2"><Label>Nominal Bunga</Label><Input type="number" value={data.nominal_bunga} onChange={e => setData('nominal_bunga', e.target.value)} /></div>
                            </div>
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2"><Label>Total Angsuran</Label><Input type="number" value={data.total_angsuran} onChange={e => setData('total_angsuran', e.target.value)} /></div>
                                <div className="space-y-2"><Label>Denda</Label><Input type="number" value={data.denda} onChange={e => setData('denda', e.target.value)} /></div>
                            </div>
                            <div className="space-y-2"><Label>Keterangan</Label><Textarea value={data.keterangan} onChange={e => setData('keterangan', e.target.value)} rows={3} /></div>
                            <div className="flex justify-end gap-3">
                                <Button type="button" variant="outline" onClick={() => router.get(route('superadmin.transaksi-pinjaman.angsuran-pinjaman'))}>Batal</Button>
                                <Button type="submit" disabled={processing}>{processing && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}Update</Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}