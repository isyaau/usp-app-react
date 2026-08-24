import { Link, Head, useForm} from '@inertiajs/react';
import { Banknote, LoaderCircle, Wallet } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import type { PinjamanFormValues, PinjamanJenisOptionLite, PinjamanAnggotaOption } from '@/types/models';

interface Props {
    anggotaOptions: PinjamanAnggotaOption[];
    jenisOptions: PinjamanJenisOptionLite[];
}

const SATUAN_OPTIONS = [
    { value: 'hari', label: 'Hari' },
    { value: 'bulan', label: 'Bulan' },
    { value: 'tahun', label: 'Tahun' },
];

export default function PinjamanCreate({ anggotaOptions, jenisOptions }: Props) {
    const form = useForm<PinjamanFormValues>({
        tanggal: new Date().toISOString().slice(0, 10),
        no_pinjaman: '',
        anggota_id: '',
        jenis_id: '',
        plafon: '',
        bunga: '',
        jangka_waktu: '',
        satuan: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(route('superadmin.pinjaman.pinjaman.store'), {
            preserveScroll: true,
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Tambah Data Pinjaman" />

            <PageHeader
                title="Tambah Data Pinjaman"
                description="Buat rekening pinjaman baru untuk anggota."
                icon={Banknote}
                backHref={route('superadmin.pinjaman.pinjaman')}
            />

            <form onSubmit={submit} className="max-w-3xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Data Pinjaman</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-5">
                        <div className="grid gap-5 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label htmlFor="tanggal">
                                    Tanggal <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="tanggal"
                                    type="date"
                                    value={form.data.tanggal}
                                    onChange={(e) => form.setData('tanggal', e.target.value)}
                                />
                                {form.errors.tanggal && (
                                    <p className="text-sm text-brand-600">{form.errors.tanggal}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="no_pinjaman">
                                    No. Pinjaman <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="no_pinjaman"
                                    value={form.data.no_pinjaman}
                                    onChange={(e) => form.setData('no_pinjaman', e.target.value)}
                                    className="font-mono"
                                    placeholder="PJ-0001"
                                />
                                {form.errors.no_pinjaman && (
                                    <p className="text-sm text-brand-600">
                                        {form.errors.no_pinjaman}
                                    </p>
                                )}
                            </div>
                        </div>

                        <div className="grid gap-5 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label>
                                    Anggota <span className="text-brand-600">*</span>
                                </Label>
                                <Select
                                    value={form.data.anggota_id || undefined}
                                    onValueChange={(v) => form.setData('anggota_id', v)}
                                >
                                    <SelectTrigger className="w-full" aria-label="Pilih Anggota">
                                        <SelectValue placeholder="-- Pilih Anggota --" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {anggotaOptions.map((a) => (
                                            <SelectItem key={a.id} value={String(a.id)}>
                                                <span className="font-mono text-xs">
                                                    {a.no_anggota}
                                                </span>{' '}
                                                — {a.nama}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {form.errors.anggota_id && (
                                    <p className="text-sm text-brand-600">
                                        {form.errors.anggota_id}
                                    </p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label>
                                    Produk Pinjaman <span className="text-brand-600">*</span>
                                </Label>
                                <Select
                                    value={form.data.jenis_id || undefined}
                                    onValueChange={(v) => form.setData('jenis_id', v)}
                                >
                                    <SelectTrigger className="w-full" aria-label="Pilih Produk">
                                        <SelectValue placeholder="-- Pilih Produk --" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {jenisOptions.map((j) => (
                                            <SelectItem key={j.id} value={String(j.id)}>
                                                {j.nama}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {form.errors.jenis_id && (
                                    <p className="text-sm text-brand-600">{form.errors.jenis_id}</p>
                                )}
                            </div>
                        </div>

                        <div className="grid gap-5 sm:grid-cols-3">
                            <div className="space-y-2">
                                <Label htmlFor="plafon">
                                    Plafon <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="plafon"
                                    value={form.data.plafon}
                                    onChange={(e) => form.setData('plafon', e.target.value)}
                                    inputMode="numeric"
                                    placeholder="10000000"
                                />
                                {form.errors.plafon && (
                                    <p className="text-sm text-brand-600">{form.errors.plafon}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="bunga">Bunga (%)</Label>
                                <Input
                                    id="bunga"
                                    value={form.data.bunga}
                                    onChange={(e) => form.setData('bunga', e.target.value)}
                                    inputMode="decimal"
                                />
                                {form.errors.bunga && (
                                    <p className="text-sm text-brand-600">{form.errors.bunga}</p>
                                )}
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <div className="space-y-2">
                                    <Label htmlFor="jangka_waktu">
                                        Jangka Waktu <span className="text-brand-600">*</span>
                                    </Label>
                                    <Input
                                        id="jangka_waktu"
                                        value={form.data.jangka_waktu}
                                        onChange={(e) =>
                                            form.setData('jangka_waktu', e.target.value)
                                        }
                                        inputMode="numeric"
                                    />
                                    {form.errors.jangka_waktu && (
                                        <p className="text-sm text-brand-600">
                                            {form.errors.jangka_waktu}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label>
                                        Satuan <span className="text-brand-600">*</span>
                                    </Label>
                                    <Select
                                        value={form.data.satuan || undefined}
                                        onValueChange={(v) => form.setData('satuan', v)}
                                    >
                                        <SelectTrigger className="w-full" aria-label="Pilih Satuan">
                                            <SelectValue placeholder="--" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {SATUAN_OPTIONS.map((s) => (
                                                <SelectItem key={s.value} value={s.value}>
                                                    {s.label}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {form.errors.satuan && (
                                        <p className="text-sm text-brand-600">
                                            {form.errors.satuan}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>

                        <div className="rounded-lg bg-muted/60 p-4 text-sm text-muted-foreground">
                            <Wallet className="mr-2 inline size-4 align-text-bottom" />
                            Field lanjutan (jaminan, marketing, SWP, kode transaksi) mengikuti
                            nilai default dan bisa dilengkapi setelah modul pendukung tersedia.
                        </div>
                    </CardContent>
                </Card>

                <div className="mt-5 flex items-center justify-end gap-3">
                    <Button variant="outline" asChild>
                        <Link href={route('superadmin.pinjaman.pinjaman')}>Kembali</Link>
                    </Button>
                    <Button
                        type="submit"
                        disabled={form.processing}
                        className="bg-brand-600 hover:bg-brand-500"
                    >
                        {form.processing && <LoaderCircle className="animate-spin" />}
                        Simpan Pinjaman
                    </Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
