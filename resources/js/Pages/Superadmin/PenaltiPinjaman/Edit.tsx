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
    nominal_penalti: number; denda: number; keterangan: string | null;
    status: string; kantor_id: number;
    pinjaman: { no_pinjaman: string; anggota: { nama: string } };
}

interface Props { transaksi: Transaksi; kantors: { id: number; nama_kantor: string }[]; }

export default function Edit({ transaksi, kantors }: Props) {
    const { data, setData, put, processing, errors } = useForm({
        tgl_transaksi: transaksi.tgl_transaksi,
        nominal_penalti: String(transaksi.nominal_penalti),
        denda: String(transaksi.denda),
        keterangan: transaksi.keterangan ?? '',
        kantor_id: String(transaksi.kantor_id),
        status: transaksi.status as 'draft' | 'posted' | 'batal',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('superadmin.transaksi-pinjaman.penalti-pinjaman.update', transaksi.id));
    };

    return (
        <AuthenticatedLayout>
            <PageHeader title={`Edit Penalti - ${transaksi.no_transaksi}`} />
            <div className="max-w-3xl mx-auto p-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Edit Penalti Pinjaman</CardTitle>
                        <CardDescription>Pinjaman: {transaksi.pinjaman.no_pinjaman} ({transaksi.pinjaman.anggota.nama})</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="space-y-6">
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2"><Label>Tanggal</Label><Input type="date" value={data.tgl_transaksi} onChange={e => setData('tgl_transaksi', e.target.value)} /></div>
                                <div className="space-y-2"><Label>Status</Label>
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
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2"><Label>Nominal Penalti</Label><Input type="number" value={data.nominal_penalti} onChange={e => setData('nominal_penalti', e.target.value)} /></div>
                                <div className="space-y-2"><Label>Denda</Label><Input type="number" value={data.denda} onChange={e => setData('denda', e.target.value)} /></div>
                            </div>
                            <div className="space-y-2"><Label>Keterangan</Label><Textarea value={data.keterangan} onChange={e => setData('keterangan', e.target.value)} rows={3} /></div>
                            <div className="flex justify-end gap-3">
                                <Button type="button" variant="outline" onClick={() => router.get(route('superadmin.transaksi-pinjaman.penalti-pinjaman'))}>Batal</Button>
                                <Button type="submit" disabled={processing}>{processing && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}Update</Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}