import { Head, router, useForm } from '@inertiajs/react';
import { LoaderCircle, CreditCard } from 'lucide-react';
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
        tgl_transaksi: transaksi.tgl_transaksi?.split('T')[0] || transaksi.tgl_transaksi,
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
            <Head title={`Edit Angsuran — ${transaksi.no_transaksi}`} />
            <PageHeader
                title={`Edit Angsuran — ${transaksi.no_transaksi}`}
                description={`Pinjaman: ${transaksi.pinjaman.no_pinjaman} (${transaksi.pinjaman.anggota.nama})`}
                icon={CreditCard}
                backHref={route('superadmin.transaksi-pinjaman.angsuran-pinjaman')}
            />
            <Card className="max-w-3xl">
                <CardHeader>
                    <CardTitle>Edit Angsuran Pinjaman</CardTitle>
                    <CardDescription>Perbarui data angsuran pinjaman.</CardDescription>
                </CardHeader>
                <CardContent>
                    <form onSubmit={submit} className="space-y-4">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label>Tanggal Transaksi</Label>
                                <Input type="date" value={data.tgl_transaksi} onChange={e => setData('tgl_transaksi', e.target.value)} />
                                {errors.tgl_transaksi && <p className="text-sm text-red-500">{errors.tgl_transaksi}</p>}
                            </div>
                            <div className="space-y-2">
                                <Label>Kantor</Label>
                                <Select value={data.kantor_id} onValueChange={v => setData('kantor_id', v)}>
                                    <SelectTrigger><SelectValue placeholder="Pilih kantor" /></SelectTrigger>
                                    <SelectContent>
                                        {kantors.map(k => <SelectItem key={k.id} value={String(k.id)}>{k.nama_kantor}</SelectItem>)}
                                    </SelectContent>
                                </Select>
                                {errors.kantor_id && <p className="text-sm text-red-500">{errors.kantor_id}</p>}
                            </div>
                        </div>
                        <div className="grid gap-4 sm:grid-cols-3">
                            <div className="space-y-2">
                                <Label>Angsuran Ke</Label>
                                <Input type="number" min="1" value={data.angsuran_ke} onChange={e => setData('angsuran_ke', e.target.value)} />
                            </div>
                            <div className="space-y-2">
                                <Label>Nominal Pokok</Label>
                                <Input type="number" min="0" value={data.nominal_pokok} onChange={e => setData('nominal_pokok', e.target.value)} />
                            </div>
                            <div className="space-y-2">
                                <Label>Nominal Bunga</Label>
                                <Input type="number" min="0" value={data.nominal_bunga} onChange={e => setData('nominal_bunga', e.target.value)} />
                            </div>
                        </div>
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label>Total Angsuran</Label>
                                <Input type="number" value={data.total_angsuran} readOnly className="bg-muted" />
                            </div>
                            <div className="space-y-2">
                                <Label>Denda</Label>
                                <Input type="number" min="0" value={data.denda} onChange={e => setData('denda', e.target.value)} />
                            </div>
                        </div>
                        <div className="space-y-2">
                            <Label>Keterangan</Label>
                            <Textarea value={data.keterangan} onChange={e => setData('keterangan', e.target.value)} rows={3} />
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
                        <div className="flex gap-2 pt-2">
                            <Button type="submit" disabled={processing} className="bg-brand-600 hover:bg-brand-500">
                                {processing && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}
                                Update
                            </Button>
                            <Button type="button" variant="outline" onClick={() => router.get(route('superadmin.transaksi-pinjaman.angsuran-pinjaman'))}>
                                Batal
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}