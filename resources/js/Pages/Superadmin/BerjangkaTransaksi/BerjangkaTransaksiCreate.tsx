import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import type { LucideIcon } from 'lucide-react';

interface Anggota { id: number; no_anggota: string; nama: string; }
interface Deposito { id: number; no_deposito: string; anggota_id: number; nominal: string; jangka_waktu: string; }
interface Kantor { id: number; kode: string; nama_kantor: string; }

interface Props {
    anggotas: Anggota[];
    depositos: Deposito[];
    kantors: Kantor[];
    config: {
        label: string;
        routeIndex: string;
        storeRoute: string;
        icon: LucideIcon;
        fields: string[];
    };
}

export default function BerjangkaTransaksiCreate({ anggotas, depositos, kantors, config }: Props) {
    const Icon = config.icon;
    const [form, setForm] = useState<Record<string, string>>({
        tgl_transaksi: new Date().toISOString().split('T')[0],
        anggota_id: '', deposito_id: '', nominal: '', keterangan: '', kantor_id: '', status: 'draft',
    });
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [submitting, setSubmitting] = useState(false);

    const filteredDepositos = depositos.filter(d => d.anggota_id === Number(form.anggota_id));

    const handleSubmit = () => {
        setSubmitting(true);
        router.post(route(config.storeRoute), form, {
            onError: (e) => { setErrors(e); setSubmitting(false); },
            onFinish: () => setSubmitting(false),
        });
    };

    const set = (key: string, val: string) => setForm(f => ({ ...f, [key]: val }));

    return (
        <AuthenticatedLayout>
            <Head title={'Tambah ' + config.label} />
            <PageHeader title={'Tambah ' + config.label} description="Isi form transaksi baru." icon={Icon} />

            <Card>
                <CardHeader><CardTitle>Data Transaksi</CardTitle></CardHeader>
                <CardContent className="space-y-4">
                    <div className="grid gap-4 sm:grid-cols-2">
                        <div className="space-y-2">
                            <Label>Tanggal Transaksi</Label>
                            <Input type="date" value={form.tgl_transaksi} onChange={e => set('tgl_transaksi', e.target.value)} />
                            {errors.tgl_transaksi && <p className="text-sm text-red-500">{errors.tgl_transaksi}</p>}
                        </div>
                        <div className="space-y-2">
                            <Label>Anggota</Label>
                            <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" value={form.anggota_id} onChange={e => { set('anggota_id', e.target.value); set('deposito_id', ''); }}>
                                <option value="">Pilih Anggota</option>
                                {anggotas.map(a => <option key={a.id} value={a.id}>{a.no_anggota} — {a.nama}</option>)}
                            </select>
                            {errors.anggota_id && <p className="text-sm text-red-500">{errors.anggota_id}</p>}
                        </div>
                        <div className="space-y-2">
                            <Label>Deposito</Label>
                            <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" value={form.deposito_id} onChange={e => set('deposito_id', e.target.value)}>
                                <option value="">Pilih Deposito</option>
                                {filteredDepositos.map(d => <option key={d.id} value={d.id}>{d.no_deposito} — Rp {Number(d.nominal).toLocaleString('id-ID')}</option>)}
                            </select>
                            {errors.deposito_id && <p className="text-sm text-red-500">{errors.deposito_id}</p>}
                        </div>
                        {config.fields.includes('nominal') && (
                            <div className="space-y-2">
                                <Label>Nominal</Label>
                                <Input type="number" value={form.nominal} onChange={e => set('nominal', e.target.value)} placeholder="0" />
                                {errors.nominal && <p className="text-sm text-red-500">{errors.nominal}</p>}
                            </div>
                        )}
                        <div className="space-y-2">
                            <Label>Kantor</Label>
                            <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" value={form.kantor_id} onChange={e => set('kantor_id', e.target.value)}>
                                <option value="">Pilih Kantor</option>
                                {kantors.map(k => <option key={k.id} value={k.id}>{k.nama_kantor}</option>)}
                            </select>
                            {errors.kantor_id && <p className="text-sm text-red-500">{errors.kantor_id}</p>}
                        </div>
                        <div className="space-y-2">
                            <Label>Status</Label>
                            <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" value={form.status} onChange={e => set('status', e.target.value)}>
                                <option value="draft">Draft</option>
                                <option value="posted">Posted</option>
                                <option value="batal">Batal</option>
                            </select>
                        </div>
                    </div>
                    <div className="space-y-2">
                        <Label>Keterangan</Label>
                        <Input value={form.keterangan} onChange={e => set('keterangan', e.target.value)} placeholder="Opsional..." />
                    </div>
                    <div className="flex gap-2 pt-2">
                        <Button onClick={handleSubmit} disabled={submitting} className="bg-brand-600 hover:bg-brand-500">
                            {submitting ? 'Menyimpan...' : 'Simpan'}
                        </Button>
                        <Button variant="outline" asChild>
                            <Link href={route(config.routeIndex)}>Batal</Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
