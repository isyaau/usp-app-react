import { Head, router, useForm } from '@inertiajs/react';
import { FileWarning, LoaderCircle } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { Textarea } from '@/Components/ui/textarea';

interface Transaksi {
    id: number;
    no_transaksi: string;
    tgl_transaksi: string;
    tahap: 'SP-1' | 'SP-2' | 'SP-3';
    isi: string | null;
    status: string;
    kantor_id: number;
    pinjaman: { no_pinjaman: string; anggota: { nama: string } };
}

interface Props {
    transaksi: Transaksi;
    kantors: { id: number; kode: string; nama_kantor: string }[];
}

export default function Edit({ transaksi, kantors }: Props) {
    const root = 'superadmin.pinjaman.surat-peringatan';
    const { data, setData, put, processing, errors } = useForm({
        tgl_transaksi: transaksi.tgl_transaksi?.split('T')[0] || transaksi.tgl_transaksi,
        tahap: transaksi.tahap,
        isi: transaksi.isi ?? '',
        kantor_id: String(transaksi.kantor_id),
        status: transaksi.status as 'draft' | 'posted' | 'batal',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route(`${root}.update`, transaksi.id));
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Edit Surat Peringatan — ${transaksi.no_transaksi}`} />
            <PageHeader
                title={`Edit Surat Peringatan — ${transaksi.no_transaksi}`}
                description={`Pinjaman: ${transaksi.pinjaman.no_pinjaman} (${transaksi.pinjaman.anggota.nama})`}
                icon={FileWarning}
                backHref={route(root)}
            />
            <Card className="max-w-3xl">
                <CardHeader>
                    <CardTitle>Edit Surat Peringatan</CardTitle>
                    <CardDescription>Perbarui data surat peringatan.</CardDescription>
                </CardHeader>
                <CardContent>
                    <form onSubmit={submit} className="space-y-4">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label>Tanggal Surat</Label>
                                <Input type="date" value={data.tgl_transaksi} onChange={(e) => setData('tgl_transaksi', e.target.value)} />
                                {errors.tgl_transaksi && <p className="text-sm text-red-500">{errors.tgl_transaksi}</p>}
                            </div>
                            <div className="space-y-2">
                                <Label>Kantor</Label>
                                <Select value={data.kantor_id} onValueChange={(v) => setData('kantor_id', v)}>
                                    <SelectTrigger><SelectValue placeholder="Pilih kantor" /></SelectTrigger>
                                    <SelectContent>
                                        {kantors.map((k) => <SelectItem key={k.id} value={String(k.id)}>{k.nama_kantor}</SelectItem>)}
                                    </SelectContent>
                                </Select>
                                {errors.kantor_id && <p className="text-sm text-red-500">{errors.kantor_id}</p>}
                            </div>
                        </div>

                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label>Tahap</Label>
                                <Select value={data.tahap} onValueChange={(v) => setData('tahap', v as any)}>
                                    <SelectTrigger><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="SP-1">SP-1 (Peringatan Pertama)</SelectItem>
                                        <SelectItem value="SP-2">SP-2 (Peringatan Kedua)</SelectItem>
                                        <SelectItem value="SP-3">SP-3 (Peringatan Terakhir)</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-2">
                                <Label>Status</Label>
                                <Select value={data.status} onValueChange={(v) => setData('status', v as any)}>
                                    <SelectTrigger><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="draft">Draft</SelectItem>
                                        <SelectItem value="posted">Posted</SelectItem>
                                        <SelectItem value="batal">Batal</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div className="space-y-2">
                            <Label>Isi Surat</Label>
                            <Textarea value={data.isi} onChange={(e) => setData('isi', e.target.value)} rows={4} />
                            {errors.isi && <p className="text-sm text-red-500">{errors.isi}</p>}
                        </div>

                        <div className="flex gap-2 pt-2">
                            <Button type="submit" disabled={processing} className="bg-brand-600 hover:bg-brand-500">
                                {processing && <LoaderCircle className="mr-2 h-4 w-4 animate-spin" />}
                                Update
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