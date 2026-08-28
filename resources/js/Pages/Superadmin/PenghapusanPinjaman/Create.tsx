import { Head, router, useForm } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { LoaderCircle, Wallet } from 'lucide-react';

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
interface PinjamanOption { id: number; no_pinjaman: string; plafon: number; sisa_pokok: number; }

interface Props { anggotas: AnggotaOption[]; kantors: KantorOption[]; }

export default function Create({ anggotas, kantors }: Props) {
    const root = 'superadmin.pinjaman.penghapusan';
    const [pinjamanList, setPinjamanList] = useState<PinjamanOption[]>([]);
    const { data, setData, post, processing, errors } = useForm({
        tgl_transaksi: new Date().toISOString().split('T')[0],
        anggota_id: '',
        pinjaman_id: '',
        sisa_pokok: '',
        keterangan: '',
        kantor_id: '',
    });

    useEffect(() => {
        if (data.anggota_id) {
            fetch(route(`${root}.pinjaman-by-anggota`, data.anggota_id))
                .then((r) => r.json())
                .then(setPinjamanList)
                .catch(() => setPinjamanList([]));
        } else {
            setPinjamanList([]);
        }
    }, [data.anggota_id, root]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route(`${root}.store`));
    };

    return (
        <AuthenticatedLayout>
            <Head title="Tambah Penghapusan Pinjaman" />

            <PageHeader
                title="Tambah Penghapusan Pinjaman"
                description="Pilih pinjaman yang dihapus buku lalu isi sisa pokok yang dihapus."
                icon={Wallet}
                backHref={route(root)}
            />

            <Card className="max-w-3xl">
                <CardHeader>
                    <CardTitle>Form Penghapusan Pinjaman</CardTitle>
                    <CardDescription>Nomor transaksi digenerate otomatis oleh sistem.</CardDescription>
                </CardHeader>
                <CardContent>
                    <form onSubmit={submit} className="space-y-4">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label>Tanggal Transaksi <span className="text-red-500">*</span></Label>
                                <Input type="date" value={data.tgl_transaksi} onChange={(e) => setData('tgl_transaksi', e.target.value)} />
                                {errors.tgl_transaksi && <p className="text-sm text-red-500">{errors.tgl_transaksi}</p>}
                            </div>
                            <div className="space-y-2">
                                <Label>Kantor <span className="text-red-500">*</span></Label>
                                <Select value={data.kantor_id} onValueChange={(v) => setData('kantor_id', v)}>
                                    <SelectTrigger><SelectValue placeholder="Pilih kantor" /></SelectTrigger>
                                    <SelectContent>
                                        {kantors.map((k) => <SelectItem key={k.id} value={String(k.id)}>{k.nama_kantor}</SelectItem>)}
                                    </SelectContent>
                                </Select>
                                {errors.kantor_id && <p className="text-sm text-red-500">{errors.kantor_id}</p>}
                            </div>
                        </div>

                        <div className="space-y-2">
                            <Label>Anggota <span className="text-red-500">*</span></Label>
                            <Select value={data.anggota_id} onValueChange={(v) => { setData('anggota_id', v); setData('pinjaman_id', ''); setData('sisa_pokok', ''); }}>
                                <SelectTrigger><SelectValue placeholder="Pilih anggota" /></SelectTrigger>
                                <SelectContent>
                                    {anggotas.map((a) => <SelectItem key={a.id} value={String(a.id)}>{a.no_anggota} — {a.nama}</SelectItem>)}
                                </SelectContent>
                            </Select>
                            {errors.anggota_id && <p className="text-sm text-red-500">{errors.anggota_id}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label>Pinjaman Aktif <span className="text-red-500">*</span></Label>
                            <Select
                                value={data.pinjaman_id}
                                onValueChange={(v) => {
                                    setData('pinjaman_id', v);
                                    const p = pinjamanList.find((x) => String(x.id) === v);
                                    if (p) setData('sisa_pokok', String(p.sisa_pokok));
                                }}
                                disabled={!data.anggota_id}
                            >
                                <SelectTrigger><SelectValue placeholder="Pilih pinjaman" /></SelectTrigger>
                                <SelectContent>
                                    {pinjamanList.map((p) => (
                                        <SelectItem key={p.id} value={String(p.id)}>
                                            {p.no_pinjaman} — sisa Rp {Number(p.sisa_pokok).toLocaleString('id-ID')}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {errors.pinjaman_id && <p className="text-sm text-red-500">{errors.pinjaman_id}</p>}
                        </div>

                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label>Sisa Pokok Dihapus <span className="text-red-500">*</span></Label>
                                <Input
                                    type="number"
                                    min="0"
                                    step="any"
                                    value={data.sisa_pokok}
                                    onChange={(e) => setData('sisa_pokok', e.target.value)}
                                    placeholder="0"
                                />
                                {errors.sisa_pokok && <p className="text-sm text-red-500">{errors.sisa_pokok}</p>}
                            </div>
                        </div>

                        <div className="space-y-2">
                            <Label>Alasan / Keterangan</Label>
                            <Textarea value={data.keterangan} onChange={(e) => setData('keterangan', e.target.value)} placeholder="Catatan alasan penghapusan..." rows={3} />
                            {errors.keterangan && <p className="text-sm text-red-500">{errors.keterangan}</p>}
                        </div>

                        <div className="flex gap-2 pt-2">
                            <Button type="submit" disabled={processing} className="bg-brand-600 hover:bg-brand-500">
                                {processing && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}
                                Simpan
                            </Button>
                            <Button type="button" variant="outline" onClick={() => router.get(route(root))}>
                                Batal
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}