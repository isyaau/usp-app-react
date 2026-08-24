import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle, Package } from 'lucide-react';

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
import type { AccountMini } from '@/types/models';

interface AccountOption extends AccountMini {}

interface FormValues {
    kode: string;
    nama: string;
    account_id: string;
    jangka_waktu: string;
    bunga: string;
    account_bunga: string;
    rumus_bunga: string;
    penalti: string;
    account_penalti: string;
    pajak: string;
    account_pajak: string;
    saldo_pajak: string;
    insentif: string;
}

interface Props {
    accountOptions: AccountOption[];
}

const RUMUS_BUNGA_OPTIONS = [
    { value: '1', label: 'Saldo Harian' },
    { value: '2', label: 'Saldo Rata-rata' },
    { value: '3', label: 'Saldo Terendah' },
];

export default function ProdukForm({ accountOptions }: Props) {
    const form = useForm<FormValues>({
        kode: '',
        nama: '',
        account_id: '',
        jangka_waktu: '',
        bunga: '',
        account_bunga: '',
        rumus_bunga: '',
        penalti: '',
        account_penalti: '',
        pajak: '',
        account_pajak: '',
        saldo_pajak: '',
        insentif: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(route('superadmin.simpanan-berjangka.produk.store'));
    };

    const accountSelect = (
        field: keyof FormValues,
        placeholder: string,
        required = false,
    ) => (
        <Select
            value={form.data[field] || undefined}
            onValueChange={(v) => form.setData(field, v)}
        >
            <SelectTrigger className="w-full" aria-label={placeholder}>
                <SelectValue placeholder={`-- ${placeholder} --`} />
            </SelectTrigger>
            <SelectContent>
                {accountOptions.map((a) => (
                    <SelectItem key={a.id} value={String(a.id)}>
                        <span className="font-mono text-xs">{a.no_account}</span>
                        {' · '}
                        {a.nama}
                    </SelectItem>
                ))}
            </SelectContent>
        </Select>
    );

    return (
        <AuthenticatedLayout>
            <Head title="Tambah Produk Berjangka" />

            <PageHeader
                title="Tambah Produk Berjangka"
                description="Buat produk deposito baru beserta parameternya."
                icon={Package}
                backHref={route('superadmin.simpanan-berjangka.produk')}
            />

            <form onSubmit={submit} className="max-w-3xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Data Produk</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-5">
                        <div className="grid gap-5 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label htmlFor="kode">
                                    Kode <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="kode"
                                    value={form.data.kode}
                                    onChange={(e) => form.setData('kode', e.target.value)}
                                    placeholder="DJ-001"
                                    className="font-mono"
                                />
                                {form.errors.kode && (
                                    <p className="text-sm text-brand-600">{form.errors.kode}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="nama">
                                    Nama Produk <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="nama"
                                    value={form.data.nama}
                                    onChange={(e) => form.setData('nama', e.target.value)}
                                    placeholder="Deposito Reguler"
                                />
                                {form.errors.nama && (
                                    <p className="text-sm text-brand-600">{form.errors.nama}</p>
                                )}
                            </div>
                        </div>

                        <div className="grid gap-5 sm:grid-cols-3">
                            <div className="space-y-2">
                                <Label htmlFor="jangka_waktu">Jangka Waktu (bulan)</Label>
                                <Input
                                    id="jangka_waktu"
                                    value={form.data.jangka_waktu}
                                    onChange={(e) => form.setData('jangka_waktu', e.target.value)}
                                    inputMode="numeric"
                                    placeholder="12"
                                />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="bunga">Bunga (%)</Label>
                                <Input
                                    id="bunga"
                                    value={form.data.bunga}
                                    onChange={(e) => form.setData('bunga', e.target.value)}
                                    inputMode="decimal"
                                    placeholder="5"
                                />
                            </div>

                            <div className="space-y-2">
                                <Label>Rumus Bunga</Label>
                                <Select
                                    value={form.data.rumus_bunga || undefined}
                                    onValueChange={(v) => form.setData('rumus_bunga', v)}
                                >
                                    <SelectTrigger className="w-full" aria-label="Rumus Bunga">
                                        <SelectValue placeholder="-- Pilih --" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {RUMUS_BUNGA_OPTIONS.map((r) => (
                                            <SelectItem key={r.value} value={r.value}>
                                                {r.label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div className="space-y-2">
                            <Label>
                                Account Utama <span className="text-brand-600">*</span>
                            </Label>
                            {accountSelect('account_id', 'Pilih Account', true)}
                            {form.errors.account_id && (
                                <p className="text-sm text-brand-600">{form.errors.account_id}</p>
                            )}
                        </div>
                    </CardContent>
                </Card>

                <Card className="mt-5">
                    <CardHeader>
                        <CardTitle>Bunga, Penalti &amp; Pajak</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-5">
                        <div className="grid gap-5 sm:grid-cols-3">
                            <div className="space-y-2">
                                <Label>Account Bunga</Label>
                                {accountSelect('account_bunga', 'Pilih Account')}
                            </div>
                            <div className="space-y-2">
                                <Label>Account Penalti</Label>
                                {accountSelect('account_penalti', 'Pilih Account')}
                            </div>
                            <div className="space-y-2">
                                <Label>Account Pajak</Label>
                                {accountSelect('account_pajak', 'Pilih Account')}
                            </div>
                        </div>

                        <div className="grid gap-5 sm:grid-cols-4">
                            <div className="space-y-2">
                                <Label htmlFor="penalti">Penalti (%)</Label>
                                <Input
                                    id="penalti"
                                    value={form.data.penalti}
                                    onChange={(e) => form.setData('penalti', e.target.value)}
                                    inputMode="decimal"
                                    placeholder="2"
                                />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="pajak">Pajak (%)</Label>
                                <Input
                                    id="pajak"
                                    value={form.data.pajak}
                                    onChange={(e) => form.setData('pajak', e.target.value)}
                                    inputMode="decimal"
                                    placeholder="10"
                                />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="saldo_pajak">Saldo Pajak</Label>
                                <Input
                                    id="saldo_pajak"
                                    value={form.data.saldo_pajak}
                                    onChange={(e) => form.setData('saldo_pajak', e.target.value)}
                                    inputMode="decimal"
                                    placeholder="0"
                                />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="insentif">Insentif</Label>
                                <Input
                                    id="insentif"
                                    value={form.data.insentif}
                                    onChange={(e) => form.setData('insentif', e.target.value)}
                                    inputMode="decimal"
                                    placeholder="0"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div className="mt-5 flex items-center justify-end gap-3">
                    <Button variant="outline" asChild>
                        <a href={route('superadmin.simpanan-berjangka.produk')}>Kembali</a>
                    </Button>
                    <Button
                        type="submit"
                        disabled={form.processing}
                        className="bg-brand-600 hover:bg-brand-500"
                    >
                        {form.processing && <LoaderCircle className="animate-spin" />}
                        Simpan Produk
                    </Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
