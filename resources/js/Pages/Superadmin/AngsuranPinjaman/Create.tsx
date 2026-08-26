import { router, useForm } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { LoaderCircle } from 'lucide-react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Textarea } from '@/Components/ui/textarea';

interface AnggotaOption { id: number; no_anggota: string; nama: string; }
interface KantorOption { id: number; kode: string; nama_kantor: string; }
interface PinjamanOption { id: number; no_pinjaman: string; plafon: number; }

interface Props {
    anggotas: AnggotaOption[];
    kantors: KantorOption[];
}

export default function Create({ anggotas, kantors }: Props) {
    const [pinjamanList, setPinjamanList] = useState<PinjamanOption[]>([]);
    const { data, setData, post, processing, errors } = useForm({
        tgl_transaksi: new Date().toISOString().split('T')[0],
        anggota_id: '',
        pinjaman_id: '',
        angsuran_ke: '1',
        nominal_pokok: '',
        nominal_bunga: '',
        total_angsuran: '',
        denda: '0',
        keterangan: '',
        kantor_id: '',
        status: 'draft' as const,
    });

    useEffect(() => {
        if (data.anggota_id) {
            fetch(route('superadmin.transaksi-pinjaman.pinjaman-by-anggota', data.anggota_id))
                .then(r => r.json())
                .then(setPinjamanList)
                .catch(() => setPinjamanList([]));
        }
    }, [data.anggota_id]);

    useEffect(() => {
        const pokok = parseFloat(data.nominal_pokok) || 0;
        const bunga = parseFloat(data.nominal_bunga) || 0;
        setData('total_angsuran', String(pokok + bunga));
    }, [data.nominal_pokok, data.nominal_bunga]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('superadmin.transaksi-pinjaman.angsuran-pinjaman.store'));
    };

    return (
        <AuthenticatedLayout>
            <PageHeader title="Tambah Angsuran Pinjaman" />
            <div className="max-w-3xl mx-auto p-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Form Angsuran Pinjaman</CardTitle>
                        <CardDescription>Isi data angsuran pinjaman yang akan dicatat.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="space-y-6">
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

                            <div className="space-y-2">
                                <Label>Anggota</Label>
                                <Select value={data.anggota_id} onValueChange={v => { setData('anggota_id', v); setData('pinjaman_id', ''); }}>
                                    <SelectTrigger><SelectValue placeholder="Pilih anggota" /></SelectTrigger>
                                    <SelectContent>
                                        {anggotas.map(a => <SelectItem key={a.id} value={String(a.id)}>{a.no_anggota} - {a.nama}</SelectItem>)}
                                    </SelectContent>
                                </Select>
                                {errors.anggota_id && <p className="text-sm text-red-500">{errors.anggota_id}</p>}
                            </div>

                            <div className="space-y-2">
                                <Label>Pinjaman</Label>
                                <Select value={data.pinjaman_id} onValueChange={v => setData('pinjaman_id', v)} disabled={!data.anggota_id}>
                                    <SelectTrigger><SelectValue placeholder="Pilih pinjaman" /></SelectTrigger>
                                    <SelectContent>
                                        {pinjamanList.map(p => <SelectItem key={p.id} value={String(p.id)}>{p.no_pinjaman} (Rp {Number(p.plafon).toLocaleString('id-ID')})</SelectItem>)}
                                    </SelectContent>
                                </Select>
                                {errors.pinjaman_id && <p className="text-sm text-red-500">{errors.pinjaman_id}</p>}
                            </div>

                            <div className="grid gap-4 sm:grid-cols-3">
                                <div className="space-y-2">
                                    <Label>Angsuran Ke</Label>
                                    <Input type="number" min="1" value={data.angsuran_ke} onChange={e => setData('angsuran_ke', e.target.value)} />
                                    {errors.angsuran_ke && <p className="text-sm text-red-500">{errors.angsuran_ke}</p>}
                                </div>
                                <div className="space-y-2">
                                    <Label>Nominal Pokok</Label>
                                    <Input type="number" min="0" value={data.nominal_pokok} onChange={e => setData('nominal_pokok', e.target.value)} />
                                    {errors.nominal_pokok && <p className="text-sm text-red-500">{errors.nominal_pokok}</p>}
                                </div>
                                <div className="space-y-2">
                                    <Label>Nominal Bunga</Label>
                                    <Input type="number" min="0" value={data.nominal_bunga} onChange={e => setData('nominal_bunga', e.target.value)} />
                                    {errors.nominal_bunga && <p className="text-sm text-red-500">{errors.nominal_bunga}</p>}
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

                            <div className="flex justify-end gap-3">
                                <Button type="button" variant="outline" onClick={() => router.get(route('superadmin.transaksi-pinjaman.angsuran-pinjaman'))}>Batal</Button>
                                <Button type="submit" disabled={processing}>
                                    {processing && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}
                                    Simpan
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}