import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { Receipt } from 'lucide-react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';

interface Kantor { id: number; kode: string; nama_kantor: string; }
interface Props { transaksi: any; kantors: Kantor[]; }

export default function Edit({ transaksi: t, kantors }: Props) {
    const [form, setForm] = useState({
        tgl_laporan: t.tgl_laporan?.split('T')[0] || '',
        saldo_awal: String(t.saldo_awal ?? 0), total_pemasukan: String(t.total_pemasukan ?? 0),
        total_pengeluaran: String(t.total_pengeluaran ?? 0), saldo_akhir: String(t.saldo_akhir ?? 0),
        keterangan: t.keterangan ?? '', kantor_id: String(t.kantor_id ?? ''), status: t.status ?? 'draft',
    });
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [submitting, setSubmitting] = useState(false);

    const set = (k: string, v: string) => setForm(f => {
        const next = { ...f, [k]: v };
        if (['saldo_awal', 'total_pemasukan', 'total_pengeluaran'].includes(k)) {
            const awal = parseFloat(next.saldo_awal) || 0;
            const masuk = parseFloat(next.total_pemasukan) || 0;
            const keluar = parseFloat(next.total_pengeluaran) || 0;
            next.saldo_akhir = String(awal + masuk - keluar);
        }
        return next;
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        setSubmitting(true);
        router.put(route('superadmin.laporan.laporan-kas-harian.update', t.id), form, {
            onError: (e) => { setErrors(e); setSubmitting(false); },
            onFinish: () => setSubmitting(false),
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Edit Laporan Kas Harian" />
            <PageHeader title="Edit Laporan Kas Harian" description="Ubah data laporan kas harian." icon={Receipt} />
            <Card className="max-w-3xl">
                <CardHeader><CardTitle>Edit Laporan Kas Harian</CardTitle></CardHeader>
                <CardContent>
                    <form onSubmit={submit} className="space-y-4">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label>Tanggal Laporan</Label>
                                <Input type="date" value={form.tgl_laporan} onChange={e => set('tgl_laporan', e.target.value)} />
                                {errors.tgl_laporan && <p className="text-sm text-red-500">{errors.tgl_laporan}</p>}
                            </div>
                            <div className="space-y-2">
                                <Label>Kantor</Label>
                                <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" value={form.kantor_id} onChange={e => set('kantor_id', e.target.value)}>
                                    <option value="">Pilih Kantor</option>
                                    {kantors.map(k => <option key={k.id} value={k.id}>{k.nama_kantor}</option>)}
                                </select>
                                {errors.kantor_id && <p className="text-sm text-red-500">{errors.kantor_id}</p>}
                            </div>
                        </div>
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label>Saldo Awal</Label>
                                <Input type="number" min="0" value={form.saldo_awal} onChange={e => set('saldo_awal', e.target.value)} />
                            </div>
                            <div className="space-y-2">
                                <Label>Total Pemasukan</Label>
                                <Input type="number" min="0" value={form.total_pemasukan} onChange={e => set('total_pemasukan', e.target.value)} />
                            </div>
                            <div className="space-y-2">
                                <Label>Total Pengeluaran</Label>
                                <Input type="number" min="0" value={form.total_pengeluaran} onChange={e => set('total_pengeluaran', e.target.value)} />
                            </div>
                            <div className="space-y-2">
                                <Label>Saldo Akhir</Label>
                                <Input type="number" value={form.saldo_akhir} readOnly className="bg-muted" />
                            </div>
                        </div>
                        <div className="space-y-2">
                            <Label>Keterangan</Label>
                            <Input value={form.keterangan} onChange={e => set('keterangan', e.target.value)} />
                        </div>
                        <div className="space-y-2">
                            <Label>Status</Label>
                            <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" value={form.status} onChange={e => set('status', e.target.value)}>
                                <option value="draft">Draft</option>
                                <option value="posted">Posted</option>
                                <option value="batal">Batal</option>
                            </select>
                        </div>
                        <div className="flex gap-2 pt-2">
                            <Button type="submit" disabled={submitting} className="bg-brand-600 hover:bg-brand-500">
                                {submitting ? 'Menyimpan...' : 'Update'}
                            </Button>
                            <Button variant="outline" asChild><Link href={route('superadmin.laporan.laporan-kas-harian')}>Batal</Link></Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
