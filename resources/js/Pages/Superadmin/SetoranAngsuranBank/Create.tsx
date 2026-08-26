import { Head, router, useForm } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { LoaderCircle, Landmark } from 'lucide-react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Textarea } from '@/Components/ui/textarea';
import { Badge } from '@/Components/ui/badge';

interface KelompokOption { id: number; nama: string; }
interface KantorOption { id: number; kode: string; nama_kantor: string; }
interface PinjamanRow { id: number; no_pinjaman: string; plafon: number; angsuran_ke: number; angsuranke: string; anggota: { id: number; no_anggota: string; nama: string } | null; }

interface DetailRow {
    pinjaman_id: string;
    anggota_id: string;
    anggota_nama: string;
    angsuran_ke: string;
    nominal_pokok: string;
    nominal_bunga: string;
    total_angsuran: string;
    setoran_simpanan: string;
    denda: string;
    keterangan: string;
}

interface Props {
    kelompoks: KelompokOption[];
    kantors: KantorOption[];
}

export default function Create({ kelompoks, kantors }: Props) {
    const [pinjamanList, setPinjamanList] = useState<PinjamanRow[]>([]);
    const [details, setDetails] = useState<DetailRow[]>([]);

    const { data, setData, post, processing, errors } = useForm({
        tgl_transaksi: new Date().toISOString().split('T')[0],
        kelompok_id: '',
        jenis: 'angsuran_dan_setoran',
        metode_pembayaran: 'bank',
        nominal_total: '0',
        keterangan: '',
        kantor_id: '',
        status: 'draft' as string,
        details: [] as any[],
    });

    useEffect(() => {
        if (data.kelompok_id) {
            fetch(route('superadmin.transaksi-pinjaman.pinjaman-by-kelompok', data.kelompok_id))
                .then(r => r.json())
                .then((list: PinjamanRow[]) => {
                    setPinjamanList(list);
                    setDetails(list.map(p => ({
                        pinjaman_id: String(p.id),
                        anggota_id: p.anggota ? String(p.anggota.id) : '',
                        anggota_nama: p.anggota ? p.anggota.nama : '',
                        angsuran_ke: '1',
                        nominal_pokok: '',
                        nominal_bunga: '',
                        total_angsuran: '0',
                        setoran_simpanan: '',
                        denda: '0',
                        keterangan: '',
                    })));
                })
                .catch(() => { setPinjamanList([]); setDetails([]); });
        }
    }, [data.kelompok_id]);

    useEffect(() => {
        let total = 0;
        details.forEach(d => { total += parseFloat(d.total_angsuran) || 0; });
        setData('nominal_total', String(total));
        setData('details', details);
    }, [details]);

    const updateDetail = (idx: number, field: keyof DetailRow, value: string) => {
        setDetails(prev => {
            const copy = [...prev];
            copy[idx] = { ...copy[idx], [field]: value };
            if (field === 'nominal_pokok' || field === 'nominal_bunga') {
                const pokok = parseFloat(copy[idx].nominal_pokok) || 0;
                const bunga = parseFloat(copy[idx].nominal_bunga) || 0;
                copy[idx].total_angsuran = String(pokok + bunga);
            }
            return copy;
        });
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        setData('details', details);
        post(route('superadmin.transaksi-pinjaman.setoran-angsuran-bank.store'));
    };

    const jenisLabel = { angsuran: 'Angsuran', penalti: 'Penalti', angsuran_dan_setoran: 'Angsuran & Setoran' } as const;
    const metodeLabel = { tunai: 'Tunai', debet_simpanan: 'Debet Simpanan', bank: 'Bank', custom: 'Custom' } as const;

    const base = 'superadmin.transaksi-pinjaman.setoran-angsuran-bank';

    return (
        <AuthenticatedLayout>
            <Head title="Tambah Setoran Angsuran Bank" />
            <PageHeader
                title="Tambah Setoran Angsuran Bank"
                description="Isi data transaksi kolektif per kelompok."
                icon={Landmark}
                backHref={route(base)}
            />
            <form onSubmit={submit} className="max-w-5xl space-y-4">
                <Card>
                    <CardHeader>
                        <CardTitle>Form Setoran Angsuran Bank</CardTitle>
                        <CardDescription>Isi data transaksi kolektif per kelompok.</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label>Tanggal Transaksi <span className="text-red-500">*</span></Label>
                                    <Input type="date" value={data.tgl_transaksi} onChange={e => setData('tgl_transaksi', e.target.value)} />
                                    {errors.tgl_transaksi && <p className="text-sm text-red-500">{errors.tgl_transaksi}</p>}
                                </div>
                                <div className="space-y-2">
                                <Label>Kantor <span className="text-red-500">*</span></Label>
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
                                <Label>Kelompok <span className="text-red-500">*</span></Label>
                                    <Select value={data.kelompok_id} onValueChange={v => setData('kelompok_id', v)}>
                                        <SelectTrigger><SelectValue placeholder="Pilih kelompok" /></SelectTrigger>
                                        <SelectContent>
                                            {kelompoks.map(k => <SelectItem key={k.id} value={String(k.id)}>{k.nama}</SelectItem>)}
                                        </SelectContent>
                                    </Select>
                                    {errors.kelompok_id && <p className="text-sm text-red-500">{errors.kelompok_id}</p>}
                                </div>
                                <div className="space-y-2">
                                    <Label>Jenis</Label>
                                    <Select value={data.jenis} onValueChange={v => setData('jenis', v)}>
                                        <SelectTrigger><SelectValue /></SelectTrigger>
                                        <SelectContent>
                                            {Object.entries(jenisLabel).map(([k, v]) => <SelectItem key={k} value={k}>{v}</SelectItem>)}
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div className="space-y-2">
                                    <Label>Metode Pembayaran</Label>
                                    <Select value={data.metode_pembayaran} onValueChange={v => setData('metode_pembayaran', v)}>
                                        <SelectTrigger><SelectValue /></SelectTrigger>
                                        <SelectContent>
                                            {Object.entries(metodeLabel).map(([k, v]) => <SelectItem key={k} value={k}>{v}</SelectItem>)}
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                            <div className="space-y-2">
                                <Label>Keterangan</Label>
                                <Textarea value={data.keterangan} onChange={e => setData('keterangan', e.target.value)} rows={2} />
                            </div>
                        </CardContent>
                    </Card>

                    {details.length > 0 && (
                        <Card>
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <div>
                                        <CardTitle>Detail Anggota ({details.length})</CardTitle>
                                        <CardDescription>Input angsuran per anggota dalam kelompok.</CardDescription>
                                    </div>
                                    <Badge variant="outline" className="text-lg font-mono">
                                        Total: Rp {parseFloat(data.nominal_total || '0').toLocaleString('id-ID')}
                                    </Badge>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div className="overflow-x-auto">
                                    <table className="w-full text-sm">
                                        <thead>
                                            <tr className="border-b">
                                                <th className="p-2 text-left">No</th>
                                                <th className="p-2 text-left">Anggota</th>
                                                <th className="p-2 text-left">No. Pinjaman</th>
                                                <th className="p-2 text-left">Pokok</th>
                                                <th className="p-2 text-left">Bunga</th>
                                                <th className="p-2 text-left">Total</th>
                                                {data.metode_pembayaran === 'debet_simpanan' && <th className="p-2 text-left">Set. Simpanan</th>}
                                                <th className="p-2 text-left">Ket</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {details.map((d, i) => (
                                                <tr key={i} className="border-b hover:bg-muted/50">
                                                    <td className="p-2">{i + 1}</td>
                                                    <td className="p-2 whitespace-nowrap">{d.anggota_nama}</td>
                                                    <td className="p-2 font-mono text-xs">{pinjamanList.find(p => String(p.id) === d.pinjaman_id)?.no_pinjaman ?? '-'}</td>
                                                    <td className="p-2"><Input type="number" min="0" value={d.nominal_pokok} onChange={e => updateDetail(i, 'nominal_pokok', e.target.value)} className="h-8 w-28 text-xs" /></td>
                                                    <td className="p-2"><Input type="number" min="0" value={d.nominal_bunga} onChange={e => updateDetail(i, 'nominal_bunga', e.target.value)} className="h-8 w-28 text-xs" /></td>
                                                    <td className="p-2 font-mono text-xs font-bold">Rp {parseFloat(d.total_angsuran || '0').toLocaleString('id-ID')}</td>
                                                    {data.metode_pembayaran === 'debet_simpanan' && (
                                                        <td className="p-2"><Input type="number" min="0" value={d.setoran_simpanan} onChange={e => updateDetail(i, 'setoran_simpanan', e.target.value)} className="h-8 w-28 text-xs" /></td>
                                                    )}
                                                    <td className="p-2"><Input type="text" value={d.keterangan} onChange={e => updateDetail(i, 'keterangan', e.target.value)} className="h-8 w-24 text-xs" /></td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </CardContent>
                        </Card>
                    )}

                    <div className="flex gap-2 pt-2">
                        <Button type="submit" disabled={processing || details.length === 0} className="bg-brand-600 hover:bg-brand-500">
                            {processing && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}
                            Simpan Kolektif ({details.length} anggota)
                        </Button>
                        <Button type="button" variant="outline" onClick={() => router.get(route(base))}>
                            Batal
                        </Button>
                    </div>
                </form>
        </AuthenticatedLayout>
    );
}