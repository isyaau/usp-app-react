import { useMemo, useState } from 'react';
import { Link, useForm} from '@inertiajs/react';
import { LoaderCircle, Plus, Trash2, X } from 'lucide-react';

import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
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
import type { AccountMini } from '@/types/models';
import {
    JENIS_SIMPANAN_LABELS,
    RUMUS_BUNGA_OPTIONS,
    type SimpananKodeOption,
    type SimpananProdukRow,
} from '@/types/simpanan';

interface FormShape {
    produk: {
        kode: string;
        nama: string;
        account_id: string;
        minimum: string;
        mengendap: string;
        bunga_id: string;
        jenis_bunga: string; // "1" flat | "2" bertingkat
        account_bunga: string;
        rumus_bunga: string;
        bulan: boolean;
        biaya_id: string;
        biaya: string;
        account_biaya: string;
        pajak_id: string;
        pajak: string;
        account_pajak: string;
        saldo_pajak: boolean;
        android: string;
        nominal_android: string;
        account_android: string;
        nominal: string;
        jenis: string;
        setor_id: string;
        tarik_id: string;
        insentif: string;
        saham: boolean;
    };
    bunga_flat: string;
    tingkat: Array<{ minimal: string; maksimal: string; bunga: string }>;
    kode_ids: number[];
}

const emptyTingkat = () => ({ minimal: '', maksimal: '', bunga: '' });

interface Props {
    initial?: SimpananProdukRow | null;
    accounts: AccountMini[];
    kodes: SimpananKodeOption[];
    submitUrl: string;
    submitMethod?: 'post' | 'put';
    processingLabel: string;
}

/**
 * Form Produk Simpanan bersama untuk Create & Edit.
 * Mereplikasi perilaku Livewire lama: bunga flat/bertingkat dengan
 * baris otomatis, dan pemilihan kode transaksi lewat modal checkbox.
 */
export function SimpananProdukForm({
    initial,
    accounts,
    kodes,
    submitUrl,
    submitMethod = 'post',
    processingLabel,
}: Props) {
    const form = useForm<FormShape>({
        produk: {
            kode: initial?.kode ?? '',
            nama: initial?.nama ?? '',
            account_id: initial?.account_id ? String(initial.account_id) : '',
            minimum: initial?.minimum != null ? String(initial.minimum) : '',
            mengendap: initial?.mengendap != null ? String(initial.mengendap) : '',
            bunga_id: initial?.bunga_id ? String(initial.bunga_id) : '',
            jenis_bunga: initial ? String(initial.jenis_bunga ?? 1) : '1',
            account_bunga: initial?.account_bunga ? String(initial.account_bunga) : '',
            rumus_bunga: initial?.rumus_bunga ? String(initial.rumus_bunga) : '',
            bulan: Boolean(initial?.bulan),
            biaya_id: initial?.biaya_id ? String(initial.biaya_id) : '',
            biaya: initial?.biaya != null ? String(initial.biaya) : '',
            account_biaya: initial?.account_biaya ? String(initial.account_biaya) : '',
            pajak_id: initial?.pajak_id ? String(initial.pajak_id) : '',
            pajak: initial?.pajak != null ? String(initial.pajak) : '',
            account_pajak: initial?.account_pajak ? String(initial.account_pajak) : '',
            saldo_pajak: Boolean(initial?.saldo_pajak),
            android: initial?.android ? String(initial.android) : '',
            nominal_android:
                initial?.nominal_android != null ? String(initial.nominal_android) : '',
            account_android: initial?.account_android ? String(initial.account_android) : '',
            nominal: initial?.nominal != null ? String(initial.nominal) : '',
            jenis: initial ? String(initial.jenis) : '',
            setor_id: initial?.setor_id ? String(initial.setor_id) : '',
            tarik_id: initial?.tarik_id ? String(initial.tarik_id) : '',
            insentif: initial?.insentif != null ? String(initial.insentif) : '',
            saham: Boolean(initial?.saham),
        },
        bunga_flat: initial && initial.jenis_bunga === 1 && initial.bunga != null ? String(initial.bunga) : '',
        tingkat:
            initial?.tingkat && initial.tingkat.length > 0
                ? [
                      ...initial.tingkat.map((t) => ({
                          minimal: t.minimal != null ? String(t.minimal) : '',
                          maksimal: t.maksimal != null ? String(t.maksimal) : '',
                          bunga: t.bunga != null ? String(t.bunga) : '',
                      })),
                  ]
                : [emptyTingkat()],
        kode_ids: (initial?.simpananKodes ?? [])
            .map((k) => kodes.find((o) => o.kode === k.kode)?.id)
            .filter((id): id is number => id != null),
    });

    const setProduk = <K extends keyof FormShape['produk']>(key: K, value: FormShape['produk'][K]) =>
        form.setData('produk', { ...form.data.produk, [key]: value });

    const isBertingkat = form.data.produk.jenis_bunga === '2';

    const setTingkat = (idx: number, patch: Partial<FormShape['tingkat'][number]>) => {
        const next = form.data.tingkat.map((t, i) => (i === idx ? { ...t, ...patch } : t));
        // Baris terakhir lengkap → tambah baris baru (perilaku Livewire lama)
        const last = next[next.length - 1];
        if (
            idx === next.length - 1 &&
            last.minimal !== '' &&
            last.maksimal !== '' &&
            last.bunga !== ''
        ) {
            next.push(emptyTingkat());
        }
        form.setData('tingkat', next);
    };

    const removeTingkat = (idx: number) => {
        const next = form.data.tingkat.filter((_, i) => i !== idx);
        form.setData('tingkat', next.length ? next : [emptyTingkat()]);
    };

    // Modal pilih kode transaksi
    const [modalOpen, setModalOpen] = useState(false);
    const [checked, setChecked] = useState<number[]>([]);
    const allChecked = checked.length > 0 && checked.length === kodes.length;

    const toggleAll = () => setChecked(allChecked ? [] : kodes.map((k) => k.id));

    const addSelected = () => {
        form.setData(
            'kode_ids',
            Array.from(new Set([...form.data.kode_ids, ...checked])),
        );
        setChecked([]);
        setModalOpen(false);
    };

    const availableOptions = useMemo(
        () => kodes.filter((k) => !form.data.kode_ids.includes(k.id)),
        [form.data.kode_ids, kodes],
    );

    const err = (path: string): string | undefined => {
        const parts = path.split('.');
        let node: unknown = form.errors;
        for (const p of parts) {
            if (node == null || typeof node !== 'object') return undefined;
            node = (node as Record<string, unknown>)[p];
        }
        return typeof node === 'string' ? node : undefined;
    };

    const AccountField = ({
        label,
        value,
        onChange,
        error,
        optional,
    }: {
        label: string;
        value: string;
        onChange: (v: string) => void;
        error?: string;
        optional?: boolean;
    }) => (
        <div className="space-y-2">
            <Label>
                {label}
                {!optional && <span className="text-brand-600"> *</span>}
            </Label>
            <Select value={value || undefined} onValueChange={onChange}>
                <SelectTrigger className="w-full" aria-label={label}>
                    <SelectValue placeholder="-- Pilih Akun --" />
                </SelectTrigger>
                <SelectContent>
                    {accounts.map((a) => (
                        <SelectItem key={a.id} value={String(a.id)}>
                            <span className="font-mono text-xs">{a.no_account}</span> — {a.nama}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
            {error && <p className="text-sm text-brand-600">{error}</p>}
        </div>
    );

    const KodeField = ({
        label,
        value,
        onChange,
        error,
    }: {
        label: string;
        value: string;
        onChange: (v: string) => void;
        error?: string;
    }) => (
        <div className="space-y-2">
            <Label>{label}</Label>
            <Select value={value || undefined} onValueChange={onChange}>
                <SelectTrigger className="w-full" aria-label={label}>
                    <SelectValue placeholder="-- Pilih Kode --" />
                </SelectTrigger>
                <SelectContent>
                    {kodes.map((k) => (
                        <SelectItem key={k.id} value={String(k.id)}>
                            <span className="font-mono text-xs">{k.kode}</span> — {k.nama}
                        </SelectItem>
                    ))}
                </SelectContent>
            </Select>
            {error && <p className="text-sm text-brand-600">{error}</p>}
        </div>
    );

    return (
        <form onSubmit={(e) => {
            e.preventDefault();
            if (submitMethod === 'put') form.put(submitUrl);
            else form.post(submitUrl);
        }} className="space-y-5">
            {/* ===== Informasi Produk ===== */}
            <Card>
                <CardHeader>
                    <CardTitle>Informasi Produk</CardTitle>
                </CardHeader>
                <CardContent className="grid gap-5 sm:grid-cols-2">
                    <div className="space-y-2">
                        <Label htmlFor="sp-kode">
                            Kode <span className="text-brand-600">*</span>
                        </Label>
                        <Input
                            id="sp-kode"
                            value={form.data.produk.kode}
                            onChange={(e) => setProduk('kode', e.target.value)}
                            className="font-mono"
                            placeholder="SP-001"
                        />
                        {err('produk.kode') && <p className="text-sm text-brand-600">{err('produk.kode')}</p>}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="sp-nama">
                            Nama Produk <span className="text-brand-600">*</span>
                        </Label>
                        <Input
                            id="sp-nama"
                            value={form.data.produk.nama}
                            onChange={(e) => setProduk('nama', e.target.value)}
                            placeholder="Simpanan Pokok"
                        />
                        {err('produk.nama') && <p className="text-sm text-brand-600">{err('produk.nama')}</p>}
                    </div>

                    <AccountField
                        label="Akun Simpanan"
                        value={form.data.produk.account_id}
                        onChange={(v) => setProduk('account_id', v)}
                        error={err('produk.account_id')}
                    />

                    <div className="space-y-2">
                        <Label>
                            Jenis Simpanan <span className="text-brand-600">*</span>
                        </Label>
                        <Select value={form.data.produk.jenis || undefined} onValueChange={(v) => setProduk('jenis', v)}>
                            <SelectTrigger className="w-full" aria-label="Jenis Simpanan">
                                <SelectValue placeholder="-- Pilih Jenis --" />
                            </SelectTrigger>
                            <SelectContent>
                                {Object.entries(JENIS_SIMPANAN_LABELS).map(([val, label]) => (
                                    <SelectItem key={val} value={val}>
                                        {label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        {err('produk.jenis') && <p className="text-sm text-brand-600">{err('produk.jenis')}</p>}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="sp-minimum">Saldo Minimum</Label>
                        <Input
                            id="sp-minimum"
                            type="number"
                            min="0"
                            value={form.data.produk.minimum}
                            onChange={(e) => setProduk('minimum', e.target.value)}
                        />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="sp-mengendap">Saldo Mengendap</Label>
                        <Input
                            id="sp-mengendap"
                            type="number"
                            min="0"
                            value={form.data.produk.mengendap}
                            onChange={(e) => setProduk('mengendap', e.target.value)}
                        />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="sp-nominal">Nominal Setoran Awal</Label>
                        <Input
                            id="sp-nominal"
                            type="number"
                            min="0"
                            value={form.data.produk.nominal}
                            onChange={(e) => setProduk('nominal', e.target.value)}
                        />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="sp-insentif">Insentif (%)</Label>
                        <Input
                            id="sp-insentif"
                            type="number"
                            step="0.01"
                            min="0"
                            value={form.data.produk.insentif}
                            onChange={(e) => setProduk('insentif', e.target.value)}
                        />
                    </div>

                    <label className="flex cursor-pointer items-center justify-between rounded-lg border bg-card px-4 py-2.5 transition hover:bg-muted/50 sm:col-span-2">
                        <span className="text-sm font-medium">Simpanan Saham</span>
                        <Switch
                            checked={form.data.produk.saham}
                            onCheckedChange={(v) => setProduk('saham', v)}
                            aria-label="Simpanan saham"
                        />
                    </label>
                </CardContent>
            </Card>

            {/* ===== Pengaturan Bunga ===== */}
            <Card>
                <CardHeader>
                    <CardTitle>Pengaturan Bunga</CardTitle>
                </CardHeader>
                <CardContent className="space-y-5">
                    <div className="grid gap-5 sm:grid-cols-3">
                        <div className="space-y-2">
                            <Label>Jenis Bunga</Label>
                            <Select
                                value={form.data.produk.jenis_bunga}
                                onValueChange={(v) => {
                                    form.setData((d) => ({
                                        ...d,
                                        produk: { ...d.produk, jenis_bunga: v },
                                        tingkat: v === '1' ? d.tingkat : [...d.tingkat, emptyTingkat()].filter(
                                            (t, i, arr) => i === arr.length - 1 || t.minimal !== '' || t.maksimal !== '',
                                        ),
                                    }));
                                }}
                            >
                                <SelectTrigger className="w-full" aria-label="Jenis Bunga">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="1">Flat</SelectItem>
                                    <SelectItem value="2">Bertingkat</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <KodeField
                            label="Kode Bunga"
                            value={form.data.produk.bunga_id}
                            onChange={(v) => setProduk('bunga_id', v)}
                            error={err('produk.bunga_id')}
                        />

                        <AccountField
                            label="Akun Bunga"
                            value={form.data.produk.account_bunga}
                            onChange={(v) => setProduk('account_bunga', v)}
                            error={err('produk.account_bunga')}
                            optional
                        />
                    </div>

                    {!isBertingkat ? (
                        <div className="max-w-xs space-y-2">
                            <Label htmlFor="sp-bunga-flat">
                                Bunga Flat (%) <span className="text-brand-600">*</span>
                            </Label>
                            <Input
                                id="sp-bunga-flat"
                                type="number"
                                step="0.01"
                                min="0"
                                value={form.data.bunga_flat}
                                onChange={(e) => form.setData('bunga_flat', e.target.value)}
                            />
                            {err('bunga_flat') && (
                                <p className="text-sm text-brand-600">{err('bunga_flat')}</p>
                            )}
                        </div>
                    ) : (
                        <div className="overflow-x-auto rounded-lg border">
                            <table className="w-full min-w-[520px] text-sm">
                                <thead>
                                    <tr className="border-b bg-muted/50 text-left text-muted-foreground">
                                        <th className="px-3 py-2 font-medium">Minimal (Rp)</th>
                                        <th className="px-3 py-2 font-medium">Maksimal (Rp)</th>
                                        <th className="px-3 py-2 font-medium">Bunga (%)</th>
                                        <th className="px-3 py-2 w-12" />
                                    </tr>
                                </thead>
                                <tbody>
                                    {form.data.tingkat.map((t, i) => (
                                        <tr key={i} className="border-b last:border-b-0">
                                            <td className="px-3 py-2">
                                                <Input
                                                    type="number"
                                                    min="0"
                                                    value={t.minimal}
                                                    onChange={(e) => setTingkat(i, { minimal: e.target.value })}
                                                />
                                            </td>
                                            <td className="px-3 py-2">
                                                <Input
                                                    type="number"
                                                    min="0"
                                                    value={t.maksimal}
                                                    onChange={(e) => setTingkat(i, { maksimal: e.target.value })}
                                                />
                                            </td>
                                            <td className="px-3 py-2">
                                                <Input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    value={t.bunga}
                                                    onChange={(e) => setTingkat(i, { bunga: e.target.value })}
                                                />
                                                {err(`tingkat.${i}.bunga`) && (
                                                    <p className="mt-1 text-xs text-brand-600">
                                                        {err(`tingkat.${i}.bunga`)}
                                                    </p>
                                                )}
                                            </td>
                                            <td className="px-3 py-2">
                                                <Button
                                                    type="button"
                                                    variant="ghost"
                                                    size="icon"
                                                    className="size-8"
                                                    onClick={() => removeTingkat(i)}
                                                    aria-label={`Hapus tingkat ${i + 1}`}
                                                >
                                                    <Trash2 className="size-4" />
                                                </Button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                            <p className="px-3 py-2 text-xs text-muted-foreground">
                                Baris baru muncul otomatis saat baris terakhir terisi lengkap.
                            </p>
                        </div>
                    )}

                    <div className="grid gap-5 sm:grid-cols-3">
                        <div className="space-y-2">
                            <Label>Rumus Perhitungan Bunga</Label>
                            <Select
                                value={form.data.produk.rumus_bunga || undefined}
                                onValueChange={(v) => {
                                    setProduk('rumus_bunga', v);
                                    // Hanya rumus saldo terendah yang mendukung opsi satu bulan
                                    if (v !== '1') setProduk('bulan', false);
                                }}
                            >
                                <SelectTrigger className="w-full" aria-label="Rumus Bunga">
                                    <SelectValue placeholder="-- Pilih Rumus --" />
                                </SelectTrigger>
                                <SelectContent>
                                    {RUMUS_BUNGA_OPTIONS.map((r) => (
                                        <SelectItem key={r.value} value={String(r.value)}>
                                            {r.label}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

                        <label
                            className={`flex items-center justify-between rounded-lg border bg-card px-4 py-2.5 ${
                                form.data.produk.rumus_bunga === '1'
                                    ? 'cursor-pointer transition hover:bg-muted/50'
                                    : 'opacity-50'
                            }`}
                        >
                            <span className="text-sm font-medium">Hanya Bulan Berjalan</span>
                            <Switch
                                checked={form.data.produk.bulan}
                                disabled={form.data.produk.rumus_bunga !== '1'}
                                onCheckedChange={(v) => setProduk('bulan', v)}
                                aria-label="Hanya bulan berjalan"
                            />
                        </label>
                    </div>
                </CardContent>
            </Card>

            {/* ===== Biaya & Pajak ===== */}
            <Card>
                <CardHeader>
                    <CardTitle>Biaya, Pajak & Mobile</CardTitle>
                </CardHeader>
                <CardContent className="space-y-5">
                    <div className="grid gap-5 sm:grid-cols-3">
                        <KodeField
                            label="Kode Biaya Admin"
                            value={form.data.produk.biaya_id}
                            onChange={(v) => setProduk('biaya_id', v)}
                        />
                        <div className="space-y-2">
                            <Label htmlFor="sp-biaya">Nominal Biaya</Label>
                            <Input
                                id="sp-biaya"
                                type="number"
                                min="0"
                                value={form.data.produk.biaya}
                                onChange={(e) => setProduk('biaya', e.target.value)}
                            />
                        </div>
                        <AccountField
                            label="Akun Biaya"
                            value={form.data.produk.account_biaya}
                            onChange={(v) => setProduk('account_biaya', v)}
                            optional
                        />

                        <KodeField
                            label="Kode Pajak"
                            value={form.data.produk.pajak_id}
                            onChange={(v) => setProduk('pajak_id', v)}
                        />
                        <div className="space-y-2">
                            <Label htmlFor="sp-pajak">Tarif Pajak (%)</Label>
                            <Input
                                id="sp-pajak"
                                type="number"
                                step="0.01"
                                min="0"
                                value={form.data.produk.pajak}
                                onChange={(e) => setProduk('pajak', e.target.value)}
                            />
                        </div>
                        <AccountField
                            label="Akun Pajak"
                            value={form.data.produk.account_pajak}
                            onChange={(v) => setProduk('account_pajak', v)}
                            optional
                        />

                        <KodeField
                            label="Kode Transaksi Android"
                            value={form.data.produk.android}
                            onChange={(v) => setProduk('android', v)}
                        />
                        <div className="space-y-2">
                            <Label htmlFor="sp-android">Nominal Android</Label>
                            <Input
                                id="sp-android"
                                type="number"
                                min="0"
                                value={form.data.produk.nominal_android}
                                onChange={(e) => setProduk('nominal_android', e.target.value)}
                            />
                        </div>
                        <AccountField
                            label="Akun Android"
                            value={form.data.produk.account_android}
                            onChange={(v) => setProduk('account_android', v)}
                            optional
                        />
                    </div>

                    <div className="grid gap-4 sm:grid-cols-2">
                        <label className="flex cursor-pointer items-center justify-between rounded-lg border bg-card px-4 py-2.5 transition hover:bg-muted/50">
                            <span className="text-sm font-medium">Pajak dari Saldo</span>
                            <Switch
                                checked={form.data.produk.saldo_pajak}
                                onCheckedChange={(v) => setProduk('saldo_pajak', v)}
                                aria-label="Pajak dari saldo"
                            />
                        </label>

                        <KodeField
                            label="Kode Setoran"
                            value={form.data.produk.setor_id}
                            onChange={(v) => setProduk('setor_id', v)}
                        />
                        <KodeField
                            label="Kode Penarikan"
                            value={form.data.produk.tarik_id}
                            onChange={(v) => setProduk('tarik_id', v)}
                        />
                    </div>
                </CardContent>
            </Card>

            {/* ===== Kode Transaksi Terkait ===== */}
            <Card>
                <CardHeader className="flex-row items-center justify-between space-y-0">
                    <CardTitle>Kode Transaksi Terkait</CardTitle>
                    <Button type="button" variant="outline" size="sm" onClick={() => setModalOpen(true)}>
                        <Plus />
                        Tambah Kode
                    </Button>
                </CardHeader>
                <CardContent>
                    {form.data.kode_ids.length === 0 ? (
                        <p className="rounded-lg bg-muted px-3 py-6 text-center text-sm text-muted-foreground">
                            Belum ada kode transaksi dipilih.
                        </p>
                    ) : (
                        <div className="flex flex-wrap gap-2">
                            {form.data.kode_ids.map((id) => {
                                const k = kodes.find((o) => o.id === id);
                                if (!k) return null;
                                return (
                                    <Badge key={id} variant="secondary" className="gap-1.5 pr-1.5">
                                        <span className="font-mono text-[10px]">{k.kode}</span>
                                        {k.nama}
                                        <button
                                            type="button"
                                            onClick={() =>
                                                form.setData(
                                                    'kode_ids',
                                                    form.data.kode_ids.filter((x) => x !== id),
                                                )
                                            }
                                            className="rounded-full p-0.5 transition hover:bg-destructive/15 hover:text-destructive"
                                            aria-label={`Hapus ${k.nama}`}
                                        >
                                            <X className="size-3" />
                                        </button>
                                    </Badge>
                                );
                            })}
                        </div>
                    )}
                </CardContent>
            </Card>

            {/* ===== Modal Pilih Kode ===== */}
            <Dialog open={modalOpen} onOpenChange={setModalOpen}>
                <DialogContent className="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle>Pilih Kode Transaksi</DialogTitle>
                    </DialogHeader>
                    <div className="max-h-80 space-y-1 overflow-y-auto">
                        {availableOptions.length === 0 && (
                            <p className="py-6 text-center text-sm text-muted-foreground">
                                Semua kode sudah ditambahkan.
                            </p>
                        )}
                        {availableOptions.map((k) => (
                            <label
                                key={k.id}
                                className="flex cursor-pointer items-center gap-3 rounded-md px-2 py-1.5 text-sm transition hover:bg-muted"
                            >
                                <input
                                    type="checkbox"
                                    checked={checked.includes(k.id)}
                                    onChange={(e) =>
                                        setChecked((c) =>
                                            e.target.checked ? [...c, k.id] : c.filter((x) => x !== k.id),
                                        )
                                    }
                                    className="size-4 accent-[var(--color-brand-600)]"
                                />
                                <span className="font-mono text-xs text-muted-foreground">{k.kode}</span>
                                {k.nama}
                            </label>
                        ))}
                    </div>
                    <DialogFooter className="gap-2 sm:justify-between">
                        <label className="flex cursor-pointer items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                checked={allChecked}
                                onChange={toggleAll}
                                className="size-4 accent-[var(--color-brand-600)]"
                            />
                            Pilih semua tersedia
                        </label>
                        <div className="flex gap-2">
                            <Button type="button" variant="outline" onClick={() => setModalOpen(false)}>
                                Batal
                            </Button>
                            <Button
                                type="button"
                                onClick={addSelected}
                                disabled={checked.length === 0}
                                className="bg-brand-600 hover:bg-brand-500"
                            >
                                Tambahkan ({checked.length})
                            </Button>
                        </div>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            {/* ===== Aksi ===== */}
            <div className="flex items-center justify-end gap-3">
                <Button variant="outline" asChild>
                    <Link href={route('superadmin.simpanan.produk-simpanan')}>Kembali</Link>
                </Button>
                <Button type="submit" disabled={form.processing} className="bg-brand-600 hover:bg-brand-500">
                    {form.processing && <LoaderCircle className="animate-spin" />}
                    {processingLabel}
                </Button>
            </div>
        </form>
    );
}
