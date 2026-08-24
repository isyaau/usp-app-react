import { Link, useForm, Head} from '@inertiajs/react';
import { LoaderCircle, Pencil } from 'lucide-react';

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
import { Switch } from '@/Components/ui/switch';
import type { AccountMini, KodeFlag, SimpananKodeRow } from '@/types/models';

const FLAGS: Array<{ key: KodeFlag; label: string }> = [
    { key: 'setoran', label: 'Setoran' },
    { key: 'tarikan', label: 'Tarikan' },
    { key: 'transfer', label: 'Transfer' },
    { key: 'pokok', label: 'Simpanan Pokok' },
    { key: 'wajib', label: 'Simpanan Wajib' },
    { key: 'sukarela', label: 'Simpanan Sukarela' },
    { key: 'pinjaman', label: 'Pinjaman' },
    { key: 'saham', label: 'Saham' },
    { key: 'pokok_pinjaman', label: 'Pokok Pinjaman' },
    { key: 'rencana', label: 'Rencana' },
];

type FormValues = {
    kode: string;
    nama: string;
    account_debet: string;
    account_kredit: string;
    keterangan: string;
} & Record<KodeFlag, boolean>;

interface Props {
    kodeData: SimpananKodeRow;
    debetAccounts: AccountMini[];
    kreditAccounts: AccountMini[];
}

export default function KodetransaksiEdit({ kodeData, debetAccounts, kreditAccounts }: Props) {
    const form = useForm<FormValues>({
        kode: kodeData.kode,
        nama: kodeData.nama,
        account_debet: String(kodeData.account_debet),
        account_kredit: String(kodeData.account_kredit),
        keterangan: kodeData.keterangan ?? '',
        setoran: Boolean(kodeData.setoran),
        tarikan: Boolean(kodeData.tarikan),
        transfer: Boolean(kodeData.transfer),
        pokok: Boolean(kodeData.pokok),
        wajib: Boolean(kodeData.wajib),
        sukarela: Boolean(kodeData.sukarela),
        pinjaman: Boolean(kodeData.pinjaman),
        saham: Boolean(kodeData.saham),
        pokok_pinjaman: Boolean(kodeData.pokok_pinjaman),
        rencana: Boolean(kodeData.rencana),
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.put(route('superadmin.simpanan.kode-transaksi.update', kodeData.id));
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Edit ${kodeData.nama}`} />

            <PageHeader
                title="Edit Kode Transaksi"
                description={`Perbarui data ${kodeData.nama}.`}
                icon={Pencil}
                backHref={route('superadmin.simpanan.kode-transaksi')}
            />

            <form onSubmit={submit} className="max-w-3xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Data Kode Transaksi</CardTitle>
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
                                    className="font-mono"
                                />
                                {form.errors.kode && (
                                    <p className="text-sm text-brand-600">{form.errors.kode}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="nama">
                                    Nama <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="nama"
                                    value={form.data.nama}
                                    onChange={(e) => form.setData('nama', e.target.value)}
                                />
                                {form.errors.nama && (
                                    <p className="text-sm text-brand-600">{form.errors.nama}</p>
                                )}
                            </div>
                        </div>

                        <div className="grid gap-5 sm:grid-cols-2">
                            {(
                                [
                                    ['account_debet', 'Account Debet', debetAccounts],
                                    ['account_kredit', 'Account Kredit', kreditAccounts],
                                ] as const
                            ).map(([field, label, options]) => (
                                <div key={field} className="space-y-2">
                                    <Label>
                                        {label} <span className="text-brand-600">*</span>
                                    </Label>
                                    <Select
                                        value={form.data[field] || undefined}
                                        onValueChange={(v) => form.setData(field, v)}
                                    >
                                        <SelectTrigger className="w-full" aria-label={`Pilih ${label}`}>
                                            <SelectValue placeholder="-- Pilih Account --" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {options.map((a) => (
                                                <SelectItem key={a.id} value={String(a.id)}>
                                                    <span className="font-mono text-xs">{a.no_account}</span> —{' '}
                                                    {a.nama}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {form.errors[field] && (
                                        <p className="text-sm text-brand-600">{form.errors[field]}</p>
                                    )}
                                </div>
                            ))}
                        </div>

                        <fieldset className="space-y-3">
                            <legend className="text-sm font-medium">Jenis Transaksi (flag)</legend>
                            <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                {FLAGS.map(({ key, label }) => (
                                    <label
                                        key={key}
                                        className="flex cursor-pointer items-center justify-between rounded-lg border bg-card px-3 py-2 transition hover:bg-muted/50"
                                    >
                                        <span className="text-sm">{label}</span>
                                        <Switch
                                            checked={form.data[key]}
                                            onCheckedChange={(v) => form.setData(key, v)}
                                            aria-label={label}
                                        />
                                    </label>
                                ))}
                            </div>
                        </fieldset>

                        <div className="space-y-2">
                            <Label htmlFor="keterangan">Keterangan</Label>
                            <textarea
                                id="keterangan"
                                rows={2}
                                value={form.data.keterangan}
                                onChange={(e) => form.setData('keterangan', e.target.value)}
                                className="border-input focus-visible:border-ring focus-visible:ring-ring/50 flex w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px]"
                            />
                        </div>
                    </CardContent>
                </Card>

                <div className="mt-5 flex items-center justify-end gap-3">
                    <Button variant="outline" asChild>
                        <Link href={route('superadmin.simpanan.kode-transaksi')}>Kembali</Link>
                    </Button>
                    <Button
                        type="submit"
                        disabled={form.processing}
                        className="bg-brand-600 hover:bg-brand-500"
                    >
                        {form.processing && <LoaderCircle className="animate-spin" />}
                        Perbarui Kode Transaksi
                    </Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
