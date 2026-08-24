import { useMemo } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { CalendarClock, LoaderCircle } from 'lucide-react';

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
import type { DepositoDetail } from '@/types/models';
import { LIST_PEMBAYARAN, emptyDepositoForm, type DepositoFormOptions } from './form';

interface Props extends DepositoFormOptions {
    berjangkaData: DepositoDetail;
}

export default function BerjangkaEdit({
    berjangkaData: b,
    anggotaOptions,
    produkOptions,
    marketingOptions,
    kantorOptions,
    accountOptions,
    simpananOptions,
}: Props) {
    const form = useForm({
        ...emptyDepositoForm(b.tanggal),
        anggota_id: String(b.anggota_id),
        jenis_id: String(b.jenis_id),
        marketing_id: b.marketing_id ? String(b.marketing_id) : '',
        qq: b.qq ?? '',
        jangka_waktu: b.jangka_waktu,
        bunga: b.bunga,
        nominal: b.nominal,
        otomatis: b.otomatis === '1',
        bayar_bunga: b.bayar_bunga,
        diawal: b.diawal,
        bunga_accrual: b.bunga_accrual === '1',
        account_bungaaccrual: b.account_bungaaccrual ?? '',
        tabunganbunga_id: b.tabunganbunga_id ? String(b.tabunganbunga_id) : '',
        tabungantempo_id: b.tabungantempo_id ? String(b.tabungantempo_id) : '',
        bayar_jatuhtempo: b.bayar_jatuhtempo,
        blokir: b.blokir === '1',
        kantor_id: String(b.kantor_id),
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.put(route('superadmin.simpanan-berjangka.update', b.id));
    };

    const jatuhTempo = useMemo(() => {
        if (!form.data.tanggal || !form.data.jangka_waktu) return null;
        const d = new Date(form.data.tanggal);
        d.setMonth(d.getMonth() + Number(form.data.jangka_waktu));
        return d.toISOString().slice(0, 10);
    }, [form.data.tanggal, form.data.jangka_waktu]);

    const simpananSelect = (
        field: 'tabunganbunga_id' | 'tabungantempo_id',
        placeholder: string,
        disabled = false,
    ) => (
        <Select
            value={form.data[field] || undefined}
            onValueChange={(v) => form.setData(field, v)}
            disabled={disabled}
        >
            <SelectTrigger className="w-full" aria-label={placeholder}>
                <SelectValue placeholder={`-- ${placeholder} --`} />
            </SelectTrigger>
            <SelectContent>
                {/* Pastikan rekening terpilih tetap ada di daftar */}
                {[...simpananOptions]
                    .sort((x, y) =>
                        String(form.data[field]) === String(y.id)
                            ? 1
                            : String(form.data[field]) === String(x.id)
                              ? -1
                              : 0,
                    )
                    .map((s) => (
                        <SelectItem key={s.id} value={String(s.id)}>
                            <span className="font-mono text-xs">{s.no_rekening}</span>
                            {s.anggota ? ` · ${s.anggota.nama}` : ''}
                        </SelectItem>
                    ))}
            </SelectContent>
        </Select>
    );

    return (
        <AuthenticatedLayout>
            <Head title={`Edit ${b.no_deposito}`} />

            <PageHeader
                title="Edit Simpanan Berjangka"
                description={b.no_deposito}
                icon={CalendarClock}
                backHref={route('superadmin.simpanan-berjangka')}
            />

            <form onSubmit={submit} className="max-w-4xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Data Deposito</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-5">
                        <div className="grid gap-5 sm:grid-cols-3">
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
                                <Label>No. DepositO</Label>
                                <Input value={b.no_deposito} readOnly disabled className="font-mono" />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="qq">QQ</Label>
                                <Input
                                    id="qq"
                                    value={form.data.qq}
                                    onChange={(e) => form.setData('qq', e.target.value)}
                                />
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
                                                <span className="font-mono text-xs">{a.no_anggota}</span>
                                                {' · '}
                                                {a.nama}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {form.errors.anggota_id && (
                                    <p className="text-sm text-brand-600">{form.errors.anggota_id}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label>Marketing</Label>
                                <Select
                                    value={form.data.marketing_id || undefined}
                                    onValueChange={(v) => form.setData('marketing_id', v)}
                                >
                                    <SelectTrigger className="w-full" aria-label="Pilih Marketing">
                                        <SelectValue placeholder="-- Pilih Marketing --" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {marketingOptions.map((m) => (
                                            <SelectItem key={m.id} value={String(m.id)}>
                                                {m.nama}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div className="grid gap-5 sm:grid-cols-4">
                            <div className="space-y-2">
                                <Label>
                                    Produk <span className="text-brand-600">*</span>
                                </Label>
                                <Select
                                    value={form.data.jenis_id || undefined}
                                    onValueChange={(v) => {
                                        const p = produkOptions.find((x) => String(x.id) === v);
                                        form.setData((data) => ({
                                            ...data,
                                            jenis_id: v,
                                            jangka_waktu: p?.jangka_waktu ?? data.jangka_waktu,
                                            bunga: p?.bunga ?? data.bunga,
                                        }));
                                    }}
                                >
                                    <SelectTrigger className="w-full" aria-label="Pilih Produk">
                                        <SelectValue placeholder="-- Pilih Produk --" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {produkOptions.map((p) => (
                                            <SelectItem key={p.id} value={String(p.id)}>
                                                {p.nama}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {form.errors.jenis_id && (
                                    <p className="text-sm text-brand-600">{form.errors.jenis_id}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="jangka_waktu">Jangka Waktu (bln)</Label>
                                <Input
                                    id="jangka_waktu"
                                    value={form.data.jangka_waktu}
                                    onChange={(e) => form.setData('jangka_waktu', e.target.value)}
                                    inputMode="numeric"
                                />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="bunga">Bunga (%)</Label>
                                <Input
                                    id="bunga"
                                    value={form.data.bunga}
                                    onChange={(e) => form.setData('bunga', e.target.value)}
                                    inputMode="decimal"
                                />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="nominal">
                                    Nominal <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="nominal"
                                    value={form.data.nominal}
                                    onChange={(e) => form.setData('nominal', e.target.value)}
                                    inputMode="numeric"
                                />
                                {form.errors.nominal && (
                                    <p className="text-sm text-brand-600">{form.errors.nominal}</p>
                                )}
                            </div>
                        </div>

                        <div className="grid gap-5 sm:grid-cols-3">
                            <label className="flex cursor-pointer items-center justify-between rounded-lg border bg-card px-4 py-2.5 transition hover:bg-muted/50">
                                <span className="text-sm font-medium">Perpanjangan Otomatis</span>
                                <Switch
                                    checked={form.data.otomatis}
                                    onCheckedChange={(v) => form.setData('otomatis', v)}
                                    aria-label="Perpanjangan otomatis"
                                />
                            </label>

                            <label className="flex cursor-pointer items-center justify-between rounded-lg border bg-card px-4 py-2.5 transition hover:bg-muted/50">
                                <span className="text-sm font-medium">Blokir Rekening</span>
                                <Switch
                                    checked={form.data.blokir}
                                    onCheckedChange={(v) => form.setData('blokir', v)}
                                    aria-label="Blokir rekening"
                                />
                            </label>

                            <div className="flex items-end gap-2 text-sm text-muted-foreground">
                                {jatuhTempo && (
                                    <span>
                                        Jatuh tempo:{' '}
                                        <strong className="text-foreground">{jatuhTempo}</strong>
                                    </span>
                                )}
                            </div>
                        </div>

                        <div className="max-w-md space-y-2">
                            <Label>
                                Kantor <span className="text-brand-600">*</span>
                            </Label>
                            <Select
                                value={form.data.kantor_id || undefined}
                                onValueChange={(v) => form.setData('kantor_id', v)}
                            >
                                <SelectTrigger className="w-full" aria-label="Pilih Kantor">
                                    <SelectValue placeholder="-- Pilih Kantor --" />
                                </SelectTrigger>
                                <SelectContent>
                                    {kantorOptions.map((k) => (
                                        <SelectItem key={k.id} value={String(k.id)}>
                                            {k.nama_kantor}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            {form.errors.kantor_id && (
                                <p className="text-sm text-brand-600">{form.errors.kantor_id}</p>
                            )}
                        </div>
                    </CardContent>
                </Card>

                <Card className="mt-5">
                    <CardHeader>
                        <CardTitle>Pembayaran Bunga &amp; Jatuh Tempo</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-5">
                        <div className="grid gap-5 lg:grid-cols-3">
                            <div className="space-y-2">
                                <Label>Jenis Pembayaran Bunga</Label>
                                <Select
                                    value={form.data.bayar_bunga}
                                    onValueChange={(v) => form.setData('bayar_bunga', v)}
                                >
                                    <SelectTrigger className="w-full" aria-label="Jenis pembayaran bunga">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="1">A.R.O.</SelectItem>
                                        <SelectItem value="2">Diambil Sendiri</SelectItem>
                                        <SelectItem value="3">Transfer ke No. Simpanan</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div className="space-y-2">
                                <Label>Cara Pembayaran (diawal)</Label>
                                <Select
                                    value={form.data.diawal}
                                    onValueChange={(v) => form.setData('diawal', v)}
                                >
                                    <SelectTrigger className="w-full" aria-label="Cara pembayaran">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {Object.entries(LIST_PEMBAYARAN).map(([v, label]) => (
                                            <SelectItem key={v} value={v}>
                                                {label}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>

                            <div className="space-y-2">
                                <Label>No. Simpanan Tujuan Bunga</Label>
                                {simpananSelect(
                                    'tabunganbunga_id',
                                    'Pilih Simpanan',
                                    form.data.bayar_bunga !== '3',
                                )}
                            </div>
                        </div>

                        <div className="grid gap-5 lg:grid-cols-3">
                            <div className="space-y-2">
                                <Label>Pembayaran Jatuh Tempo</Label>
                                <Select
                                    value={form.data.bayar_jatuhtempo}
                                    onValueChange={(v) => form.setData('bayar_jatuhtempo', v)}
                                >
                                    <SelectTrigger className="w-full" aria-label="Pembayaran jatuh tempo">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="1">Diambil Sendiri</SelectItem>
                                        <SelectItem value="2">Transfer ke No. Simpanan</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <div className="space-y-2">
                                <Label>No. Simpanan Tujuan Jatuh Tempo</Label>
                                {simpananSelect(
                                    'tabungantempo_id',
                                    'Pilih Simpanan',
                                    form.data.bayar_jatuhtempo !== '2',
                                )}
                            </div>

                            <label className="flex h-fit cursor-pointer items-center justify-between rounded-lg border bg-card px-4 py-2.5 transition hover:bg-muted/50">
                                <span className="text-sm font-medium">Bunga Accrual</span>
                                <Switch
                                    checked={form.data.bunga_accrual}
                                    onCheckedChange={(v) => form.setData('bunga_accrual', v)}
                                    aria-label="Bunga accrual"
                                />
                            </label>
                        </div>

                        {form.data.bunga_accrual && (
                            <div className="max-w-md space-y-2">
                                <Label>Account Bunga Accrual</Label>
                                <Select
                                    value={form.data.account_bungaaccrual || undefined}
                                    onValueChange={(v) => form.setData('account_bungaaccrual', v)}
                                >
                                    <SelectTrigger className="w-full" aria-label="Account bunga accrual">
                                        <SelectValue placeholder="-- Pilih Account --" />
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
                            </div>
                        )}
                    </CardContent>
                </Card>

                <div className="mt-5 flex items-center justify-end gap-3">
                    <Button variant="outline" asChild>
                        <a href={route('superadmin.simpanan-berjangka')}>Kembali</a>
                    </Button>
                    <Button
                        type="submit"
                        disabled={form.processing}
                        className="bg-brand-600 hover:bg-brand-500"
                    >
                        {form.processing && <LoaderCircle className="animate-spin" />}
                        Perbarui Deposito
                    </Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
