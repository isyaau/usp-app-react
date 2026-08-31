import { useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import { LoaderCircle, Minus, Plus, Search } from 'lucide-react';

import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import type { AccountMini } from '@/types/models';
import {
    JENIS_SIMPANAN_LABELS,
    type SimpananKodeOption,
    type SimpananProdukRow,
} from '@/types/simpanan';

/* ======================================================================
   Komponen pencarian akun & kode transaksi (field "text + tombol cari")
   ====================================================================== */

function AccountPicker({
    label,
    value,
    onChange,
    accounts,
    error,
    optional,
    placeholder = '-- Pilih Akun --',
    id,
}: {
    label: string;
    value: string;
    onChange: (v: string) => void;
    accounts: AccountMini[];
    error?: string;
    optional?: boolean;
    placeholder?: string;
    id?: string;
}) {
    const [open, setOpen] = useState(false);
    const [q, setQ] = useState('');
    const selected = accounts.find((a) => String(a.id) === value);
    const filtered = accounts.filter(
        (a) => a.no_account.toLowerCase().includes(q.toLowerCase()) || a.nama.toLowerCase().includes(q.toLowerCase()),
    );

    return (
        <div className="space-y-2">
            <Label htmlFor={id}>
                {label}
                {!optional && <span className="text-brand-600"> *</span>}
            </Label>
            <div className="flex gap-2">
                <Input
                    id={id}
                    readOnly
                    value={selected ? `${selected.no_account} — ${selected.nama}` : ''}
                    placeholder={placeholder}
                    className="cursor-pointer"
                    onClick={() => setOpen(true)}
                />
                <Button type="button" variant="outline" size="icon" onClick={() => setOpen(true)} aria-label="Cari akun">
                    <Search className="size-4" />
                </Button>
            </div>
            {error && <p className="text-sm text-brand-600">{error}</p>}

            <Dialog open={open} onOpenChange={(o) => { setOpen(o); setQ(''); }}>
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>Cari Akun — {label}</DialogTitle>
                    </DialogHeader>
                    <div className="space-y-3">
                        <Input
                            value={q}
                            onChange={(e) => setQ(e.target.value)}
                            placeholder="Cari no account / nama..."
                            autoFocus
                        />
                        <div className="max-h-72 space-y-1 overflow-y-auto">
                            {filtered.length === 0 && (
                                <p className="py-6 text-center text-sm text-muted-foreground">Tidak ada akun cocok.</p>
                            )}
                            {filtered.map((a) => (
                                <button
                                    key={a.id}
                                    type="button"
                                    onClick={() => {
                                        onChange(String(a.id));
                                        setOpen(false);
                                    }}
                                    className={`flex w-full items-center justify-between rounded-md px-2 py-1.5 text-sm transition hover:bg-muted ${
                                        String(a.id) === value ? 'bg-brand-50' : ''
                                    }`}
                                >
                                    <span className="font-mono text-xs text-brand-700">{a.no_account}</span>
                                    <span className="text-muted-foreground">{a.nama}</span>
                                </button>
                            ))}
                        </div>
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    );
}

function KodePicker({
    label,
    value,
    onChange,
    kodes,
    error,
    optional,
    placeholder = '-- Pilih Kode --',
    id,
}: {
    label: string;
    value: string;
    onChange: (v: string) => void;
    kodes: SimpananKodeOption[];
    error?: string;
    optional?: boolean;
    placeholder?: string;
    id?: string;
}) {
    const [open, setOpen] = useState(false);
    const [q, setQ] = useState('');
    const selected = kodes.find((k) => String(k.id) === value);
    const filtered = kodes.filter(
        (k) => k.kode.toLowerCase().includes(q.toLowerCase()) || k.nama.toLowerCase().includes(q.toLowerCase()),
    );

    return (
        <div className="space-y-2">
            <Label htmlFor={id}>
                {label}
                {!optional && <span className="text-brand-600"> *</span>}
            </Label>
            <div className="flex gap-2">
                <Input
                    id={id}
                    readOnly
                    value={selected ? `${selected.kode} — ${selected.nama}` : ''}
                    placeholder={placeholder}
                    className="cursor-pointer"
                    onClick={() => setOpen(true)}
                />
                <Button type="button" variant="outline" size="icon" onClick={() => setOpen(true)} aria-label="Cari kode">
                    <Search className="size-4" />
                </Button>
            </div>
            {error && <p className="text-sm text-brand-600">{error}</p>}

            <Dialog open={open} onOpenChange={(o) => { setOpen(o); setQ(''); }}>
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>Cari Kode — {label}</DialogTitle>
                    </DialogHeader>
                    <div className="space-y-3">
                        <Input
                            value={q}
                            onChange={(e) => setQ(e.target.value)}
                            placeholder="Cari kode / nama..."
                            autoFocus
                        />
                        <div className="max-h-72 space-y-1 overflow-y-auto">
                            {filtered.length === 0 && (
                                <p className="py-6 text-center text-sm text-muted-foreground">Tidak ada kode cocok.</p>
                            )}
                            {filtered.map((k) => (
                                <button
                                    key={k.id}
                                    type="button"
                                    onClick={() => {
                                        onChange(String(k.id));
                                        setOpen(false);
                                    }}
                                    className={`flex w-full items-center justify-between rounded-md px-2 py-1.5 text-sm transition hover:bg-muted ${
                                        String(k.id) === value ? 'bg-brand-50' : ''
                                    }`}
                                >
                                    <span className="font-mono text-xs text-brand-700">{k.kode}</span>
                                    <span className="text-muted-foreground">{k.nama}</span>
                                </button>
                            ))}
                        </div>
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    );
}

/* ======================================================================
   Tipe data form
   ====================================================================== */

interface KodeRow {
    id: number;
    account_debet: string;
    account_kredit: string;
}

interface FormShape {
    produk: {
        kode: string;
        nama: string;
        account_id: string;
        minimum: string;
        mengendap: string;
        harga_saham: string;
        nominal: string;
        insentif: string;
        setor_id: string;
        tarik_id: string;
        jenis: string;
        jenis_bunga: string;
        bunga_id: string;
        account_bunga: string;
        rumus_bunga: string;
        bulan: boolean;
        bunga: string;
        biaya_id: string;
        biaya: string;
        account_biaya: string;
        pajak_id: string;
        pajak: string;
        account_pajak: string;
        pajak_saldo: string;
        android: string;
        nominal_android: string;
        account_android: string;
        update_bagi_hasil: boolean;
    };
    bunga_flat: string;
    tingkat: Array<{ minimal: string; maksimal: string; bunga: string }>;
    kode_rows: KodeRow[];
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
            harga_saham: initial?.harga_saham != null ? String(initial.harga_saham) : '',
            nominal: initial?.nominal != null ? String(initial.nominal) : '',
            insentif: initial?.insentif != null ? String(initial.insentif) : '',
            setor_id: initial?.setor_id ? String(initial.setor_id) : '',
            tarik_id: initial?.tarik_id ? String(initial.tarik_id) : '',
            jenis: initial ? String(initial.jenis) : '',
            jenis_bunga: initial ? String(initial.jenis_bunga ?? 1) : '1',
            bunga_id: initial?.bunga_id ? String(initial.bunga_id) : '',
            account_bunga: initial?.account_bunga ? String(initial.account_bunga) : '',
            rumus_bunga: initial?.rumus_bunga ? String(initial.rumus_bunga) : '2',
            bulan: Boolean(initial?.bulan),
            bunga: initial?.bunga != null ? String(initial.bunga) : '',
            biaya_id: initial?.biaya_id ? String(initial.biaya_id) : '',
            biaya: initial?.biaya != null ? String(initial.biaya) : '',
            account_biaya: initial?.account_biaya ? String(initial.account_biaya) : '',
            pajak_id: initial?.pajak_id ? String(initial.pajak_id) : '',
            pajak: initial?.pajak != null ? String(initial.pajak) : '',
            account_pajak: initial?.account_pajak ? String(initial.account_pajak) : '',
            pajak_saldo: initial?.pajak_saldo != null ? String(initial.pajak_saldo) : '',
            android: initial?.android ? String(initial.android) : '',
            nominal_android: initial?.nominal_android != null ? String(initial.nominal_android) : '',
            account_android: initial?.account_android ? String(initial.account_android) : '',
            update_bagi_hasil: Boolean(initial?.update_bagi_hasil),
        },
        bunga_flat:
            initial && initial.jenis_bunga === 1 && initial.bunga != null ? String(initial.bunga) : '',
        tingkat:
            initial?.tingkat && initial.tingkat.length > 0
                ? initial.tingkat.map((t) => ({
                      minimal: t.minimal != null ? String(t.minimal) : '',
                      maksimal: t.maksimal != null ? String(t.maksimal) : '',
                      bunga: t.bunga != null ? String(t.bunga) : '',
                  }))
                : [emptyTingkat()],
        kode_rows: (initial?.simpanan_kodes ?? []).map((k) => ({
            id: k.id,
            account_debet: k.account_debet ? String(k.account_debet) : '',
            account_kredit: k.account_kredit ? String(k.account_kredit) : '',
        })),
    });

    const setProduk = <K extends keyof FormShape['produk']>(key: K, value: FormShape['produk'][K]) =>
        form.setData('produk', { ...form.data.produk, [key]: value });

    const err = (path: string): string | undefined => {
        const parts = path.split('.');
        let node: unknown = form.errors;
        for (const p of parts) {
            if (node == null || typeof node !== 'object') return undefined;
            node = (node as Record<string, unknown>)[p];
        }
        return typeof node === 'string' ? node : undefined;
    };

    const isBertingkat = form.data.produk.jenis_bunga === '2';
    const isSaham = form.data.produk.jenis === '5';
    const isSaldoTerendah = form.data.produk.rumus_bunga === '1';

    const setTingkat = (idx: number, patch: Partial<FormShape['tingkat'][number]>) => {
        const next = form.data.tingkat.map((t, i) => (i === idx ? { ...t, ...patch } : t));
        const last = next[next.length - 1];
        if (idx === next.length - 1 && last.minimal !== '' && last.maksimal !== '' && last.bunga !== '') {
            next.push(emptyTingkat());
        }
        form.setData('tingkat', next);
    };

    const removeTingkat = (idx: number) => {
        const next = form.data.tingkat.filter((_, i) => i !== idx);
        form.setData('tingkat', next.length ? next : [emptyTingkat()]);
    };

    /* ---------- pemilihan kode transaksi (tabel) ---------- */
    const [txOpen, setTxOpen] = useState(false);
    const [txQ, setTxQ] = useState('');
    const [txSel, setTxSel] = useState<number[]>([]);

    const linkedIds = form.data.kode_rows.map((r) => r.id);
    const txAvailable = kodes.filter((k) => !linkedIds.includes(k.id));
    const txFiltered = txAvailable.filter(
        (k) => k.kode.toLowerCase().includes(txQ.toLowerCase()) || k.nama.toLowerCase().includes(txQ.toLowerCase()),
    );

    const addRows = (ids: number[]) => {
        const merged = [...form.data.kode_rows];
        ids.forEach((id) => {
            if (!merged.some((r) => r.id === id)) {
                const k = kodes.find((o) => o.id === id);
                merged.push({
                    id,
                    account_debet: k?.account_debet ? String(k.account_debet) : '',
                    account_kredit: k?.account_kredit ? String(k.account_kredit) : '',
                });
            }
        });
        form.setData('kode_rows', merged);
        setTxSel([]);
        setTxOpen(false);
    };

    const removeRows = () => {
        form.setData('kode_rows', form.data.kode_rows.filter((r) => !txSel.includes(r.id)));
        setTxSel([]);
    };

    const setRowAccount = (idx: number, key: 'account_debet' | 'account_kredit', value: string) => {
        form.setData(
            'kode_rows',
            form.data.kode_rows.map((r, i) => (i === idx ? { ...r, [key]: value } : r)),
        );
    };

    const rowKode = (id: number) => kodes.find((k) => k.id === id);
    const rowAccountNo = (id: string | null | undefined) =>
        id ? accounts.find((a) => String(a.id) === id) : undefined;

    /* ---------- radio ---------- */
    const RadioGroup = ({
        name,
        options,
        value,
        onChange,
        cols = 2,
    }: {
        name: string;
        options: Array<{ value: string; label: string }>;
        value: string;
        onChange: (v: string) => void;
        cols?: number;
    }) => (
        <div className={`grid gap-2`} style={{ gridTemplateColumns: `repeat(${cols}, minmax(0, 1fr))` }}>
            {options.map((o) => (
                <label
                    key={o.value}
                    className={`flex cursor-pointer items-center gap-2 rounded-lg border px-3 py-2 text-sm transition hover:bg-muted/50 ${
                        value === o.value ? 'border-brand-600 bg-brand-50' : 'bg-card'
                    }`}
                >
                    <input
                        type="radio"
                        name={name}
                        value={o.value}
                        checked={value === o.value}
                        onChange={() => onChange(o.value)}
                        className="size-4 accent-[var(--color-brand-600)]"
                    />
                    {o.label}
                </label>
            ))}
        </div>
    );

    return (
        <form
            onSubmit={(e) => {
                e.preventDefault();
                if (submitMethod === 'put') form.put(submitUrl);
                else form.post(submitUrl);
            }}
            className="space-y-5"
        >
            {/* ===== 1. Produk ===== */}
            <Card>
                <CardHeader>
                    <CardTitle>Produk</CardTitle>
                </CardHeader>
                <CardContent className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <div className="space-y-2">
                        <Label htmlFor="sp-kode">
                            Kode <span className="text-brand-600">*</span>
                        </Label>
                        <Input
                            id="sp-kode"
                            value={form.data.produk.kode}
                            onChange={(e) => setProduk('kode', e.target.value)}
                            className="font-mono"
                            placeholder="TAB001"
                        />
                        {err('produk.kode') && <p className="text-sm text-brand-600">{err('produk.kode')}</p>}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="sp-nama">
                            Nama <span className="text-brand-600">*</span>
                        </Label>
                        <Input
                            id="sp-nama"
                            value={form.data.produk.nama}
                            onChange={(e) => setProduk('nama', e.target.value)}
                            placeholder="Tabungan Umum"
                        />
                        {err('produk.nama') && <p className="text-sm text-brand-600">{err('produk.nama')}</p>}
                    </div>

                    <AccountPicker
                        label="No. Account"
                        value={form.data.produk.account_id}
                        onChange={(v) => setProduk('account_id', v)}
                        accounts={accounts}
                        error={err('produk.account_id')}
                    />

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
                        <Label htmlFor="sp-mengendap">Mengendap (Bulan)</Label>
                        <Input
                            id="sp-mengendap"
                            type="number"
                            min="0"
                            value={form.data.produk.mengendap}
                            onChange={(e) => setProduk('mengendap', e.target.value)}
                        />
                    </div>
                </CardContent>
            </Card>

            {/* ===== 2. Jenis ===== */}
            <Card>
                <CardHeader>
                    <CardTitle>Jenis</CardTitle>
                </CardHeader>
                <CardContent className="space-y-5">
                    <RadioGroup
                        name="produk-jenis"
                        value={form.data.produk.jenis}
                        onChange={(v) => {
                            setProduk('jenis', v);
                            if (v !== '5') setProduk('harga_saham', '');
                        }}
                        options={Object.entries(JENIS_SIMPANAN_LABELS).map(([val, label]) => ({ value: val, label }))}
                        cols={4}
                    />
                    {err('produk.jenis') && <p className="text-sm text-brand-600">{err('produk.jenis')}</p>}

                    {isSaham && (
                        <div className="max-w-xs space-y-2">
                            <Label htmlFor="sp-harga-saham">Harga Saham</Label>
                            <Input
                                id="sp-harga-saham"
                                type="number"
                                min="0"
                                value={form.data.produk.harga_saham}
                                onChange={(e) => setProduk('harga_saham', e.target.value)}
                            />
                        </div>
                    )}

                    <div className="grid gap-5 rounded-lg border bg-muted/30 p-4 sm:grid-cols-2 lg:grid-cols-4">
                        <KodePicker
                            label="Kode Setoran"
                            value={form.data.produk.setor_id}
                            onChange={(v) => setProduk('setor_id', v)}
                            kodes={kodes}
                        />
                        <KodePicker
                            label="Kode Tarikan"
                            value={form.data.produk.tarik_id}
                            onChange={(v) => setProduk('tarik_id', v)}
                            kodes={kodes}
                        />
                        <div className="space-y-2">
                            <Label htmlFor="sp-nominal">Nil. Setoran</Label>
                            <Input
                                id="sp-nominal"
                                type="number"
                                min="0"
                                value={form.data.produk.nominal}
                                onChange={(e) => setProduk('nominal', e.target.value)}
                            />
                        </div>
                        <div className="space-y-2">
                            <Label htmlFor="sp-insentif">Insentif Mkt. (%)</Label>
                            <Input
                                id="sp-insentif"
                                type="number"
                                step="0.01"
                                min="0"
                                value={form.data.produk.insentif}
                                onChange={(e) => setProduk('insentif', e.target.value)}
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* ===== 3. Bagi Hasil ===== */}
            <Card>
                <CardHeader>
                    <CardTitle>Bagi Hasil</CardTitle>
                </CardHeader>
                <CardContent className="space-y-5">
                    <div className="grid gap-5 sm:grid-cols-3">
                        <KodePicker
                            label="Kode"
                            value={form.data.produk.bunga_id}
                            onChange={(v) => setProduk('bunga_id', v)}
                            kodes={kodes}
                            error={err('produk.bunga_id')}
                        />
                        {!isBertingkat ? (
                            <div className="space-y-2">
                                <Label htmlFor="sp-bunga-flat">
                                    B. Hasil/Tahun (%) <span className="text-brand-600">*</span>
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
                            <div className="sm:col-span-2">
                                <Label>Tabel B. Hasil Bertingkat</Label>
                                <div className="mt-2 overflow-x-auto rounded-lg border">
                                    <table className="w-full min-w-[420px] text-sm">
                                        <thead>
                                            <tr className="border-b bg-muted/50 text-left text-muted-foreground">
                                                <th className="px-3 py-2 font-medium">Saldo Min (Rp)</th>
                                                <th className="px-3 py-2 font-medium">Saldo Maks (Rp)</th>
                                                <th className="px-3 py-2 font-medium">B. Hasil (%)</th>
                                                <th className="w-12 px-3 py-2" />
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
                                                            <Minus className="size-4" />
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
                            </div>
                        )}
                        <AccountPicker
                            label="No. Account"
                            value={form.data.produk.account_bunga}
                            onChange={(v) => setProduk('account_bunga', v)}
                            accounts={accounts}
                            optional
                        />
                    </div>

                    <div className="grid gap-5 sm:grid-cols-2">
                        <div className="space-y-2">
                            <Label>Rumus</Label>
                            <RadioGroup
                                name="rumus-bunga"
                                value={form.data.produk.rumus_bunga}
                                onChange={(v) => {
                                    setProduk('rumus_bunga', v);
                                    if (v !== '1') setProduk('bulan', false);
                                }}
                                options={[
                                    { value: '1', label: 'Saldo Terendah' },
                                    { value: '2', label: 'Saldo Harian' },
                                    { value: '3', label: 'Saldo Rata-rata' },
                                ]}
                                cols={3}
                            />
                        </div>

                        {isSaldoTerendah && (
                            <label className="flex cursor-pointer items-center gap-2 rounded-lg border bg-card px-4 py-3 text-sm transition hover:bg-muted/50 self-end">
                                <input
                                    type="checkbox"
                                    checked={form.data.produk.bulan}
                                    onChange={(e) => setProduk('bulan', e.target.checked)}
                                    className="size-4 accent-[var(--color-brand-600)]"
                                />
                                1 Bulan
                            </label>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label>Pola</Label>
                        <RadioGroup
                            name="jenis-bunga"
                            value={form.data.produk.jenis_bunga}
                            onChange={(v) =>
                                form.setData((d) => ({
                                    ...d,
                                    produk: { ...d.produk, jenis_bunga: v },
                                    tingkat:
                                        v === '1'
                                            ? d.tingkat
                                            : [...d.tingkat, emptyTingkat()].filter(
                                                  (t, i, arr) => i === arr.length - 1 || t.minimal !== '' || t.maksimal !== '',
                                              ),
                                }))
                            }
                            options={[
                                { value: '1', label: 'Tidak Bertingkat' },
                                { value: '2', label: 'Bertingkat' },
                            ]}
                            cols={2}
                        />
                    </div>
                </CardContent>
            </Card>

            {/* ===== 4. Biaya Administrasi ===== */}
            <Card>
                <CardHeader>
                    <CardTitle>Biaya Administrasi</CardTitle>
                </CardHeader>
                <CardContent className="grid gap-5 sm:grid-cols-3">
                    <KodePicker
                        label="Kode"
                        value={form.data.produk.biaya_id}
                        onChange={(v) => setProduk('biaya_id', v)}
                        kodes={kodes}
                    />
                    <div className="space-y-2">
                        <Label htmlFor="sp-biaya">Biaya Adm.</Label>
                        <Input
                            id="sp-biaya"
                            type="number"
                            min="0"
                            value={form.data.produk.biaya}
                            onChange={(e) => setProduk('biaya', e.target.value)}
                        />
                    </div>
                    <AccountPicker
                        label="No. Account"
                        value={form.data.produk.account_biaya}
                        onChange={(v) => setProduk('account_biaya', v)}
                        accounts={accounts}
                        optional
                    />
                </CardContent>
            </Card>

            {/* ===== 5. Pajak ===== */}
            <Card>
                <CardHeader>
                    <CardTitle>Pajak</CardTitle>
                </CardHeader>
                <CardContent className="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <KodePicker
                        label="Kode"
                        value={form.data.produk.pajak_id}
                        onChange={(v) => setProduk('pajak_id', v)}
                        kodes={kodes}
                    />
                    <div className="space-y-2">
                        <Label htmlFor="sp-pajak">Pajak (%)</Label>
                        <Input
                            id="sp-pajak"
                            type="number"
                            step="0.01"
                            min="0"
                            value={form.data.produk.pajak}
                            onChange={(e) => setProduk('pajak', e.target.value)}
                        />
                    </div>
                    <AccountPicker
                        label="No. Account"
                        value={form.data.produk.account_pajak}
                        onChange={(v) => setProduk('account_pajak', v)}
                        accounts={accounts}
                        optional
                    />
                    <div className="space-y-2">
                        <Label htmlFor="sp-pajak-saldo">Saldo Minimum</Label>
                        <Input
                            id="sp-pajak-saldo"
                            type="number"
                            min="0"
                            value={form.data.produk.pajak_saldo}
                            onChange={(e) => setProduk('pajak_saldo', e.target.value)}
                        />
                    </div>
                </CardContent>
            </Card>

            {/* ===== 6. Biaya Android ===== */}
            <Card>
                <CardHeader>
                    <CardTitle>Biaya Android</CardTitle>
                </CardHeader>
                <CardContent className="grid gap-5 sm:grid-cols-3">
                    <KodePicker
                        label="Kode"
                        value={form.data.produk.android}
                        onChange={(v) => setProduk('android', v)}
                        kodes={kodes}
                    />
                    <div className="space-y-2">
                        <Label htmlFor="sp-android">Biaya Android</Label>
                        <Input
                            id="sp-android"
                            type="number"
                            min="0"
                            value={form.data.produk.nominal_android}
                            onChange={(e) => setProduk('nominal_android', e.target.value)}
                        />
                    </div>
                    <AccountPicker
                        label="No. Account"
                        value={form.data.produk.account_android}
                        onChange={(v) => setProduk('account_android', v)}
                        accounts={accounts}
                        optional
                    />
                </CardContent>
            </Card>

            {/* ===== 7. Tabel Transaksi ===== */}
            <Card>
                <CardHeader className="flex-row items-center justify-between space-y-0">
                    <CardTitle>Transaksi</CardTitle>
                    <div className="flex gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            onClick={() => setTxOpen(true)}
                            disabled={txAvailable.length === 0}
                        >
                            <Plus />
                            Tambah
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            onClick={removeRows}
                            disabled={txSel.length === 0}
                            className="text-destructive hover:text-destructive"
                        >
                            <Minus />
                            Hapus
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    {form.data.kode_rows.length === 0 ? (
                        <p className="rounded-lg bg-muted px-3 py-6 text-center text-sm text-muted-foreground">
                            Belum ada transaksi. Klik + Tambah untuk menambahkan.
                        </p>
                    ) : (
                        <div className="overflow-x-auto rounded-lg border">
                            <table className="w-full min-w-[640px] text-sm">
                                <thead>
                                    <tr className="border-b bg-muted/50 text-left text-muted-foreground">
                                        <th className="w-10 px-3 py-2" />
                                        <th className="px-3 py-2 font-medium">Kode</th>
                                        <th className="px-3 py-2 font-medium">Nama Transaksi</th>
                                        <th className="px-3 py-2 font-medium">Acc. Debet</th>
                                        <th className="px-3 py-2 font-medium">Acc. Kredit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {form.data.kode_rows.map((row, i) => {
                                        const k = rowKode(row.id);
                                        const db = rowAccountNo(row.account_debet);
                                        const cr = rowAccountNo(row.account_kredit);
                                        const selected = txSel.includes(row.id);
                                        return (
                                            <tr key={row.id} className="border-b last:border-b-0">
                                                <td className="px-3 py-2">
                                                    <input
                                                        type="checkbox"
                                                        checked={selected}
                                                        onChange={(e) =>
                                                            setTxSel((s) =>
                                                                e.target.checked
                                                                    ? [...s, row.id]
                                                                    : s.filter((x) => x !== row.id),
                                                            )
                                                        }
                                                        className="size-4 accent-[var(--color-brand-600)]"
                                                    />
                                                </td>
                                                <td className="px-3 py-2 font-mono text-xs text-brand-700">
                                                    {k?.kode ?? '—'}
                                                </td>
                                                <td className="px-3 py-2">{k?.nama ?? '—'}</td>
                                                <td className="px-3 py-2">
                                                    <select
                                                        value={row.account_debet}
                                                        onChange={(e) => setRowAccount(i, 'account_debet', e.target.value)}
                                                        className="w-full rounded-md border bg-card px-2 py-1.5 text-sm"
                                                    >
                                                        <option value="">-- --</option>
                                                        {accounts.map((a) => (
                                                            <option key={a.id} value={String(a.id)}>
                                                                {a.no_account} — {a.nama}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </td>
                                                <td className="px-3 py-2">
                                                    <select
                                                        value={row.account_kredit}
                                                        onChange={(e) => setRowAccount(i, 'account_kredit', e.target.value)}
                                                        className="w-full rounded-md border bg-card px-2 py-1.5 text-sm"
                                                    >
                                                        <option value="">-- --</option>
                                                        {accounts.map((a) => (
                                                            <option key={a.id} value={String(a.id)}>
                                                                {a.no_account} — {a.nama}
                                                            </option>
                                                        ))}
                                                    </select>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    )}
                    {(err('kode_rows.0.id') || err('kode_rows.0.account_debet') || err('kode_rows.0.account_kredit')) && (
                        <p className="mt-1 text-sm text-brand-600">Periksa kembali data transaksi.</p>
                    )}
                </CardContent>
            </Card>

            {/* ===== 8. Checkbox Update Bagi Hasil ===== */}
            <label className="flex cursor-pointer items-center gap-2 rounded-lg border bg-card px-4 py-3 text-sm transition hover:bg-muted/50">
                <input
                    type="checkbox"
                    checked={form.data.produk.update_bagi_hasil}
                    onChange={(e) => setProduk('update_bagi_hasil', e.target.checked)}
                    className="size-4 accent-[var(--color-brand-600)]"
                />
                Update Bagi Hasil ke Semua Simpanan
            </label>

            {/* ===== ===== Modal Tambah Kode Transaksi ===== */}
            <Dialog
                open={txOpen}
                onOpenChange={(o) => {
                    setTxOpen(o);
                    setTxQ('');
                    setTxSel([]);
                }}
            >
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>Tambah Transaksi</DialogTitle>
                    </DialogHeader>
                    <div className="space-y-3">
                        <Input
                            value={txQ}
                            onChange={(e) => setTxQ(e.target.value)}
                            placeholder="Cari kode / nama transaksi..."
                            autoFocus
                        />
                        <div className="max-h-72 space-y-1 overflow-y-auto">
                            {txFiltered.length === 0 && (
                                <p className="py-6 text-center text-sm text-muted-foreground">
                                    Tidak ada kode tersedia.
                                </p>
                            )}
                            {txFiltered.map((k) => (
                                <label
                                    key={k.id}
                                    className="flex cursor-pointer items-center gap-3 rounded-md px-2 py-1.5 text-sm transition hover:bg-muted"
                                >
                                    <input
                                        type="checkbox"
                                        checked={txSel.includes(k.id)}
                                        onChange={(e) =>
                                            setTxSel((s) =>
                                                e.target.checked ? [...s, k.id] : s.filter((x) => x !== k.id),
                                            )
                                        }
                                        className="size-4 accent-[var(--color-brand-600)]"
                                    />
                                    <span className="font-mono text-xs text-brand-700">{k.kode}</span>
                                    {k.nama}
                                </label>
                            ))}
                        </div>
                    </div>
                    <div className="flex justify-end gap-2">
                        <Button type="button" variant="outline" onClick={() => setTxOpen(false)}>
                            Batal
                        </Button>
                        <Button
                            type="button"
                            onClick={() => addRows(txSel)}
                            disabled={txSel.length === 0}
                            className="bg-brand-600 hover:bg-brand-500"
                        >
                            Tambahkan ({txSel.length})
                        </Button>
                    </div>
                </DialogContent>
            </Dialog>

            {/* ===== 9. Aksi ===== */}
            <div className="flex items-center justify-end gap-3">
                <Button variant="outline" asChild>
                    <Link href={route('superadmin.simpanan.produk-simpanan')}>Tutup</Link>
                </Button>
                <Button type="submit" disabled={form.processing} className="bg-brand-600 hover:bg-brand-500">
                    {form.processing && <LoaderCircle className="animate-spin" />}
                    {processingLabel}
                </Button>
            </div>
        </form>
    );
}
