import { useState } from 'react';
import { Link, useForm} from '@inertiajs/react';
import { FunctionSquare, LoaderCircle, Plus, Trash2 } from 'lucide-react';

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
import type { PinjamanProdukRow } from '@/types/pinjaman';

export const LIST_ANGSURAN = [
    'Anuitas',
    'Flat',
    'Flat Efektif',
    'Pokok Tetap',
    'Bagi Hasil Menurun',
];

export const KOLEKTABILITAS_LABELS = [
    'Lancar',
    'Kurang Lancar',
    'Diragukan',
    'Macet',
];

const LIST_OPERATOR_RUMUS = ['+', '-', '*', '/', '(', ')', '<', '>', '<=', '>=', '='] as const;

interface AccountOption extends AccountMini {}

interface FormShape {
    produk: {
        kode: string;
        nama: string;
        account_id: string;
        bunga: string;
        account_bunga: string;
        ditangguhkan: boolean;
        account_ditangguhkan: string;
        kas: string;
        account_bank: string;
        insentif: string;
        simpanan: boolean;
        swp_cair: boolean;
        swp_angsur: boolean;
        swp_persen: boolean;
        nominal_simpanan: string;
        simpanan_pokok: boolean;
        nominal_simpanan_pokok: string;
        toleransi: string;
        angsuran: string;
    };
    kolektabilitas: Array<{ kualitas_id: number; keterangan: string }>;
    komponen: Array<{
        nama: string;
        nominal: string;
        persen: boolean;
        account_id: string;
        cair: boolean;
        angsuran: boolean;
        penalti: boolean;
        rumus_c: string;
        rumus_a: string;
        rumus_p: string;
    }>;
}

function emptyKomponen(): FormShape['komponen'][number] {
    return {
        nama: '',
        nominal: '',
        persen: false,
        account_id: '',
        cair: false,
        angsuran: false,
        penalti: false,
        rumus_c: '',
        rumus_a: '',
        rumus_p: '',
    };
}

function defaultKolektabilitas(
    existing?: PinjamanProdukRow['kolektabilitas'],
): FormShape['kolektabilitas'] {
    return KOLEKTABILITAS_LABELS.map((_, i) => ({
        kualitas_id: i + 1,
        keterangan:
            existing?.find((k) => Number(k.kualitas_id) === i + 1)?.keterangan ?? '',
    }));
}

interface Props {
    initial?: PinjamanProdukRow | null;
    accounts: AccountOption[];
    parameters: Array<{ id: number; nama: string }>;
    submitUrl: string;
    submitMethod?: 'post' | 'put';
    processingLabel: string;
}

function AccountCombobox({
    label,
    value,
    accounts = [],
    onChange,
    error,
    required = false,
    placeholder = 'Pilih Akun',
}: {
    label: string;
    value: string;
    accounts: AccountOption[];
    onChange: (v: string) => void;
    error?: string;
    required?: boolean;
    placeholder?: string;
}) {
    const [open, setOpen] = useState(false);
    const [q, setQ] = useState('');
    const selected = accounts.find((a) => String(a.id) === value);
    const filtered = accounts.filter((a) =>
        `${a.no_account} ${a.nama}`.toLowerCase().includes(q.toLowerCase()),
    );

    return (
        <div className="space-y-2">
            <Label>
                {label} {required && <span className="text-brand-600">*</span>}
            </Label>
            <button
                type="button"
                onClick={() => {
                    setQ('');
                    setOpen(true);
                }}
                className="flex h-10 w-full items-center justify-between gap-2 rounded-md border border-input bg-transparent px-3 text-sm shadow-xs outline-none transition-colors hover:bg-muted/50 focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]"
            >
                {selected ? (
                    <span className="truncate text-foreground">
                        <span className="font-mono text-xs">{selected.no_account}</span> —{' '}
                        {selected.nama}
                    </span>
                ) : (
                    <span className="text-muted-foreground">-- {placeholder} --</span>
                )}
            </button>
            {error && <p className="text-sm text-brand-600">{error}</p>}

            <Dialog open={open} onOpenChange={setOpen}>
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>Pilih {label}</DialogTitle>
                    </DialogHeader>
                    <Input
                        autoFocus
                        value={q}
                        onChange={(e) => setQ(e.target.value)}
                        placeholder="Cari nomor akun / nama…"
                    />
                    <div className="max-h-64 space-y-1 overflow-y-auto">
                        {filtered.length === 0 ? (
                            <p className="px-1 py-2 text-sm text-muted-foreground">
                                Tidak ada akun yang cocok.
                            </p>
                        ) : (
                            filtered.map((a) => (
                                <button
                                    key={a.id}
                                    type="button"
                                    onClick={() => {
                                        onChange(String(a.id));
                                        setOpen(false);
                                    }}
                                    className={`flex w-full items-center gap-2 rounded px-2 py-1.5 text-left text-sm transition hover:bg-brand-600/10 ${
                                        value === String(a.id)
                                            ? 'bg-brand-600/10 font-medium'
                                            : ''
                                    }`}
                                >
                                    <span className="font-mono text-xs">{a.no_account}</span> —{' '}
                                    {a.nama}
                                </button>
                            ))
                        )}
                    </div>
                </DialogContent>
            </Dialog>
        </div>
    );
}

/**
 * Form Produk Pinjaman bersama untuk halaman Create & Edit.
 * Mereplikasi seluruh perilaku form Livewire lama termasuk
 * baris komponen otomatis dan modal rumus.
 */
export function PinjamanProdukForm({
    initial,
    accounts,
    parameters,
    submitUrl,
    submitMethod = 'post',
    processingLabel,
}: Props) {
    const form = useForm<FormShape>({
        produk: {
            kode: initial?.kode ? String(initial.kode) : '',
            nama: initial?.nama ?? '',
            account_id: initial?.account_id ? String(initial.account_id) : '',
            bunga: initial?.bunga != null ? String(initial.bunga) : '',
            account_bunga: initial?.account_bunga ? String(initial.account_bunga) : '',
            ditangguhkan: Boolean(initial?.ditangguhkan),
            account_ditangguhkan: initial?.account_ditangguhkan
                ? String(initial.account_ditangguhkan)
                : '',
            kas: initial?.kas != null ? String(initial.kas) : '',
            account_bank: initial?.account_bank ? String(initial.account_bank) : '',
            insentif: initial?.insentif != null ? String(initial.insentif) : '',
            simpanan: Boolean(initial?.simpanan),
            swp_cair: Boolean(initial?.swp_cair),
            swp_angsur: Boolean(initial?.swp_angsur),
            swp_persen: Boolean(initial?.swp_persen),
            nominal_simpanan:
                initial?.nominal_simpanan != null ? String(initial.nominal_simpanan) : '',
            simpanan_pokok: Boolean(initial?.simpanan_pokok),
            nominal_simpanan_pokok:
                initial?.nominal_simpanan_pokok != null
                    ? String(initial.nominal_simpanan_pokok)
                    : '',
            toleransi: initial?.toleransi != null ? String(initial.toleransi) : '',
            angsuran: initial?.angsuran ?? '',
        },
        kolektabilitas: defaultKolektabilitas(initial?.kolektabilitas),
        komponen: [
            ...(initial?.komponen ?? []).map((c) => ({
                nama: c.nama,
                nominal: c.nominal != null ? String(c.nominal) : '',
                persen: Boolean(c.persen),
                account_id: String(c.account_id),
                cair: Boolean(c.cair),
                angsuran: Boolean(c.angsuran),
                penalti: Boolean(c.penalti),
                rumus_c: c.rumus_c ?? '',
                rumus_a: c.rumus_a ?? '',
                rumus_p: c.rumus_p ?? '',
            })),
            emptyKomponen(),
        ],
    });

    // Modal rumus generik (dipakai kolektabilitas & komponen)
    const [rumusTarget, setRumusTarget] = useState<string | null>(null);
    const [rumusValue, setRumusValue] = useState('');

    const openRumus = (path: string, current: string) => {
        setRumusTarget(path);
        setRumusValue(current);
    };

    const saveRumus = () => {
        if (!rumusTarget) return;
        // path format: "kolektabilitas.0.keterangan" atau "komponen.1.rumus_c"
        const [section, indexStr, field] = rumusTarget.split('.');
        const idx = Number(indexStr);
        if (section === 'kolektabilitas') {
            form.setData((data) => ({
                ...data,
                kolektabilitas: data.kolektabilitas.map((k, i) =>
                    i === idx ? { ...k, keterangan: rumusValue.trim() } : k,
                ),
            }));
        } else if (field === 'rumus_c' || field === 'rumus_a' || field === 'rumus_p') {
            form.setData((data) => ({
                ...data,
                komponen: data.komponen.map((c, i) =>
                    i === idx ? { ...c, [field]: rumusValue.trim() } : c,
                ),
            }));
        }
        setRumusTarget(null);
    };

    const setKomponen = (idx: number, patch: Partial<FormShape['komponen'][number]>) => {
        const isNamaBarisTerakhir =
            'nama' in patch && idx === form.data.komponen.length - 1 && Boolean(patch.nama?.trim());

        form.setData((data) => ({
            ...data,
            komponen: data.komponen.map((c, i) => (i === idx ? { ...c, ...patch } : c)),
            // Baris terakhir terisi → tambah baris kosong baru (perilaku Livewire lama)
            ...(isNamaBarisTerakhir ? { komponen: [...data.komponen, emptyKomponen()] as FormShape['komponen'] } : {}),
        }));
    };

    const removeKomponen = (idx: number) => {
        form.setData((data) => {
            const next = data.komponen.filter((_, i) => i !== idx);
            return { ...data, komponen: next.length ? next : [emptyKomponen()] };
        });
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        const options = {
            forceFormData: false as const,
        };
        if (submitMethod === 'put') {
            form.put(submitUrl, options);
        } else {
            form.post(submitUrl, options);
        }
    };

    const err = (path: string): string | undefined => {
        const parts = path.split('.');
        let node: unknown = form.errors;
        for (const p of parts) {
            if (node == null || typeof node !== 'object') return undefined;
            node = (node as Record<string, unknown>)[p];
        }
        return typeof node === 'string' ? node : undefined;
    };

    return (
        <form onSubmit={submit} className="space-y-5">
            {/* ===== Informasi Produk ===== */}
            <Card>
                <CardHeader>
                    <CardTitle>Produk</CardTitle>
                </CardHeader>
                <CardContent className="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    <div className="space-y-2">
                        <Label htmlFor="kode">
                            Kode <span className="text-brand-600">*</span>
                        </Label>
                        <Input
                            id="kode"
                            value={form.data.produk.kode}
                            onChange={(e) =>
                                form.setData('produk', { ...form.data.produk, kode: e.target.value })
                            }
                            className="font-mono"
                            placeholder="PRD-001"
                        />
                        {err('produk.kode') && (
                            <p className="text-sm text-brand-600">{err('produk.kode')}</p>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="nama">
                            Nama Produk <span className="text-brand-600">*</span>
                        </Label>
                        <Input
                            id="nama"
                            value={form.data.produk.nama}
                            onChange={(e) =>
                                form.setData('produk', { ...form.data.produk, nama: e.target.value })
                            }
                            placeholder="Pinjaman Modal Usaha"
                        />
                        {err('produk.nama') && (
                            <p className="text-sm text-brand-600">{err('produk.nama')}</p>
                        )}
                    </div>

                    <AccountCombobox
                        label="No Account"
                        required
                        accounts={accounts}
                        value={form.data.produk.account_id}
                        onChange={(v) =>
                            form.setData('produk', { ...form.data.produk, account_id: v })
                        }
                        error={err('produk.account_id')}
                    />

                    <div className="space-y-2">
                        <Label htmlFor="insentif">
                            Insentif Marketing (%) <span className="text-brand-600">*</span>
                        </Label>
                        <Input
                            id="insentif"
                            type="number"
                            step="0.01"
                            min="0"
                            value={form.data.produk.insentif}
                            onChange={(e) =>
                                form.setData('produk', {
                                    ...form.data.produk,
                                    insentif: e.target.value,
                                })
                            }
                        />
                        {err('produk.insentif') && (
                            <p className="text-sm text-brand-600">{err('produk.insentif')}</p>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="toleransi">
                            Toleransi Tunggakan (Hari) <span className="text-brand-600">*</span>
                        </Label>
                        <Input
                            id="toleransi"
                            type="number"
                            min="0"
                            value={form.data.produk.toleransi}
                            onChange={(e) =>
                                form.setData('produk', {
                                    ...form.data.produk,
                                    toleransi: e.target.value,
                                })
                            }
                        />
                        {err('produk.toleransi') && (
                            <p className="text-sm text-brand-600">{err('produk.toleransi')}</p>
                        )}
                    </div>

                    <div className="space-y-2">
                        <Label>
                            Metode Angsuran <span className="text-brand-600">*</span>
                        </Label>
                        <Select
                            value={form.data.produk.angsuran || undefined}
                            onValueChange={(v) =>
                                form.setData('produk', { ...form.data.produk, angsuran: v })
                            }
                        >
                            <SelectTrigger className="w-full" aria-label="Metode Angsuran">
                                <SelectValue placeholder="-- Pilih Metode --" />
                            </SelectTrigger>
                            <SelectContent>
                                {LIST_ANGSURAN.map((m) => (
                                    <SelectItem key={m} value={m}>
                                        {m}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        {err('produk.angsuran') && (
                            <p className="text-sm text-brand-600">{err('produk.angsuran')}</p>
                        )}
                    </div>

                    <label className="flex cursor-pointer items-center justify-between rounded-lg border bg-card px-4 py-2.5 transition hover:bg-muted/50 sm:col-span-2">
                        <span className="text-sm font-medium">
                            Simpanan Wajib
                        </span>
                        <Switch
                            checked={form.data.produk.simpanan}
                            onCheckedChange={(v) => {
                                form.setData((data) => ({
                                    ...data,
                                    produk: {
                                        ...data.produk,
                                        simpanan: v,
                                        // reset dependensi saat dimatikan (perilaku updatedIsAktif lama)
                                        ...(v
                                            ? {}
                                            : {
                                                  swp_cair: false,
                                                  swp_angsur: false,
                                                  swp_persen: false,
                                                  nominal_simpanan: '',
                                              }),
                                    },
                                }));
                            }}
                            aria-label="Wajib simpanan aktif"
                        />
                    </label>

                    {form.data.produk.simpanan && (
                        <div className="grid gap-4 rounded-lg border bg-muted/30 p-4 sm:col-span-2 xl:col-span-3 xl:grid-cols-3">
                            {(
                                [
                                    ['swp_cair', 'Pencairan'],
                                    ['swp_angsur', 'Angsuran'],
                                    ['swp_persen', 'Persen'],
                                ] as const
                            ).map(([key, label]) => (
                                <label key={key} className="flex cursor-pointer items-center justify-between gap-3 rounded-md bg-card px-3 py-2">
                                    <span className="text-sm">{label}</span>
                                    <Switch
                                        checked={form.data.produk[key]}
                                        onCheckedChange={(v) =>
                                            form.setData('produk', { ...form.data.produk, [key]: v })
                                        }
                                        aria-label={label}
                                    />
                                </label>
                            ))}
                            <div className="space-y-2 sm:col-span-2 lg:col-span-3">
                                <Label htmlFor="nominal_simpanan">Nominal Simpanan Wajib</Label>
                                <Input
                                    id="nominal_simpanan"
                                    type="number"
                                    min="0"
                                    value={form.data.produk.nominal_simpanan}
                                    onChange={(e) =>
                                        form.setData('produk', {
                                            ...form.data.produk,
                                            nominal_simpanan: e.target.value,
                                        })
                                    }
                                    placeholder="Contoh: 500000"
                                />
                            </div>
                        </div>
                    )}

                    <label className="flex cursor-pointer items-center justify-between rounded-lg border bg-card px-4 py-2.5 transition hover:bg-muted/50 sm:col-span-2">
                        <span className="text-sm font-medium">Simpanan Pokok</span>
                        <Switch
                            checked={form.data.produk.simpanan_pokok}
                            onCheckedChange={(v) =>
                                form.setData('produk', { ...form.data.produk, simpanan_pokok: v })
                            }
                            aria-label="Simpanan pokok aktif"
                        />
                    </label>

                    {form.data.produk.simpanan_pokok && (
                        <div className="space-y-2 sm:col-span-2">
                            <Label htmlFor="nominal_simpanan_pokok">Nominal Simpanan Pokok</Label>
                            <Input
                                id="nominal_simpanan_pokok"
                                type="number"
                                min="0"
                                value={form.data.produk.nominal_simpanan_pokok}
                                onChange={(e) =>
                                    form.setData('produk', {
                                        ...form.data.produk,
                                        nominal_simpanan_pokok: e.target.value,
                                    })
                                }
                            />
                        </div>
                    )}
                </CardContent>
            </Card>

            {/* ===== Bagi Hasil & Kolektabilitas ===== */}
            <div className="grid gap-5 lg:grid-cols-2">
            {/* ===== Bagi Hasil ===== */}
            <Card>
                <CardHeader>
                    <CardTitle>Bagi Hasil</CardTitle>
                </CardHeader>
                <CardContent className="grid gap-5 sm:grid-cols-2">
                    <div className="space-y-2">
                        <Label htmlFor="bunga">
                            Bagi Hasil (%) <span className="text-brand-600">*</span>
                        </Label>
                        <Input
                            id="bunga"
                            type="number"
                            step="0.01"
                            min="0"
                            value={form.data.produk.bunga}
                            onChange={(e) =>
                                form.setData('produk', { ...form.data.produk, bunga: e.target.value })
                            }
                        />
                        {err('produk.bunga') && (
                            <p className="text-sm text-brand-600">{err('produk.bunga')}</p>
                        )}
                    </div>

                    <AccountCombobox
                        label="No Account"
                        accounts={accounts}
                        value={form.data.produk.account_bunga}
                        onChange={(v) =>
                            form.setData('produk', { ...form.data.produk, account_bunga: v })
                        }
                        error={err('produk.account_bunga')}
                    />

                    <label className="flex cursor-pointer items-center justify-between rounded-lg border bg-card px-4 py-2.5 transition hover:bg-muted/50">
                        <span className="text-sm font-medium">Bagi Hasil</span>
                        <Switch
                            checked={form.data.produk.ditangguhkan}
                            onCheckedChange={(v) =>
                                form.setData('produk', { ...form.data.produk, ditangguhkan: v })
                            }
                            aria-label="Bagi hasil aktif"
                        />
                    </label>

                    {form.data.produk.ditangguhkan && (
                        <AccountCombobox
                            label="No Account Bagi Hasil"
                            accounts={accounts}
                            value={form.data.produk.account_ditangguhkan}
                            onChange={(v) =>
                                form.setData('produk', {
                                    ...form.data.produk,
                                    account_ditangguhkan: v,
                                })
                            }
                            error={err('produk.account_ditangguhkan')}
                        />
                    )}

                    <div className="space-y-2">
                        <Label htmlFor="kas">Kas di Kantor (Rp)</Label>
                        <Input
                            id="kas"
                            type="number"
                            min="0"
                            value={form.data.produk.kas}
                            onChange={(e) =>
                                form.setData('produk', { ...form.data.produk, kas: e.target.value })
                            }
                        />
                    </div>

                    <AccountCombobox
                        label="Akun Bank"
                        accounts={accounts}
                        value={form.data.produk.account_bank}
                        onChange={(v) =>
                            form.setData('produk', { ...form.data.produk, account_bank: v })
                        }
                        error={err('produk.account_bank')}
                    />
                </CardContent>
            </Card>

            {/* ===== Kolektabilitas ===== */}
            <Card>
                <CardHeader>
                    <CardTitle>Kolektabilitas</CardTitle>
                </CardHeader>
                <CardContent className="space-y-3">
                    {form.data.kolektabilitas.map((k, i) => (
                        <div key={k.kualitas_id} className="flex items-end gap-3">
                            <span className="w-28 shrink-0 pb-2.5 text-sm font-medium">
                                {KOLEKTABILITAS_LABELS[i]}
                            </span>
                            <div className="relative flex-1">
                                <Input
                                    value={k.keterangan}
                                    placeholder="Rumus kolektabilitas…"
                                    onClick={() => openRumus(`kolektabilitas.${i}.keterangan`, k.keterangan)}
                                    className="cursor-pointer pr-10"
                                />
                                <button
                                    type="button"
                                    onClick={() => openRumus(`kolektabilitas.${i}.keterangan`, k.keterangan)}
                                    className="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground transition hover:text-brand-600"
                                    aria-label={`Edit rumus ${KOLEKTABILITAS_LABELS[i]}`}
                                >
                                    <FunctionSquare className="size-4" />
                                </button>
                            </div>
                        </div>
                    ))}
                </CardContent>
            </Card>
            </div>

            {/* ===== Komponen ===== */}
            <Card>
                <CardHeader>
                    <CardTitle>Komponen Biaya</CardTitle>
                </CardHeader>
                <CardContent className="overflow-x-auto">
                    <table className="w-full min-w-[1150px] text-sm">
                        <thead>
                            <tr className="border-b text-left text-muted-foreground">
                                <th className="w-44 pb-2 font-medium">Nama</th>
                                <th className="w-28 pb-2 font-medium">Nominal</th>
                                <th className="w-14 pb-2 text-center font-medium">%</th>
                                <th className="pb-2 font-medium">Akun</th>
                                <th className="w-12 pb-2 text-center font-medium">C</th>
                                <th className="w-12 pb-2 text-center font-medium">A</th>
                                <th className="w-12 pb-2 text-center font-medium">P</th>
                                <th className="w-[150px] pb-2 font-medium">Rumus Cair</th>
                                <th className="w-[150px] pb-2 font-medium">Rumus Angsuran</th>
                                <th className="w-[150px] pb-2 font-medium">Rumus Penalti</th>
                                <th className="w-12 pb-2" />
                            </tr>
                        </thead>
                        <tbody>
                            {form.data.komponen.map((c, i) => (
                                <tr key={i} className="border-b last:border-b-0">
                                    <td className="py-2 pr-2">
                                        <Input
                                            value={c.nama}
                                            onChange={(e) => setKomponen(i, { nama: e.target.value })}
                                            placeholder="Biaya Administrasi"
                                        />
                                        {err(`komponen.${i}.nama`) && (
                                            <p className="mt-1 text-xs text-brand-600">
                                                {err(`komponen.${i}.nama`)}
                                            </p>
                                        )}
                                    </td>
                                    <td className="py-2 pr-2">
                                        <Input
                                            type="number"
                                            min="0"
                                            value={c.nominal}
                                            onChange={(e) => setKomponen(i, { nominal: e.target.value })}
                                            disabled={c.persen}
                                            placeholder={c.persen ? '%' : '0'}
                                        />
                                    </td>
                                    <td className="py-2 pr-2 text-center">
                                        <input
                                            type="checkbox"
                                            checked={c.persen}
                                            onChange={(e) => setKomponen(i, { persen: e.target.checked })}
                                            className="size-4 accent-[var(--color-brand-600)]"
                                            aria-label="Persen"
                                        />
                                    </td>
                                    <td className="py-2 pr-2">
                                        <Select
                                            value={String(c.account_id) || undefined}
                                            onValueChange={(v) => setKomponen(i, { account_id: v })}
                                        >
                                            <SelectTrigger className="w-full min-w-44" aria-label={`Akun komponen ${i + 1}`}>
                                                <SelectValue placeholder="-- Pilih --" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {accounts.map((a) => (
                                                    <SelectItem key={a.id} value={String(a.id)}>
                                                        <span className="font-mono text-xs">{a.no_account}</span> —{' '}
                                                        {a.nama}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        {err(`komponen.${i}.account_id`) && (
                                            <p className="mt-1 text-xs text-brand-600">
                                                {err(`komponen.${i}.account_id`)}
                                            </p>
                                        )}
                                    </td>
                                    {(['cair', 'angsuran', 'penalti'] as const).map((f) => (
                                        <td key={f} className="py-2 pr-2 text-center">
                                            <input
                                                type="checkbox"
                                                checked={c[f]}
                                                onChange={(e) => setKomponen(i, { [f]: e.target.checked })}
                                                className="size-4 accent-[var(--color-brand-600)]"
                                                aria-label={f}
                                            />
                                        </td>
                                    ))}
                                    {(['rumus_c', 'rumus_a', 'rumus_p'] as const).map((f) => (
                                        <td key={f} className="py-2 pr-2">
                                            <Input
                                                value={c[f]}
                                                placeholder={'JT > 3'}
                                                onClick={() => openRumus(`komponen.${i}.${f}`, c[f])}
                                                readOnly
                                                className="cursor-pointer font-mono text-xs"
                                            />
                                        </td>
                                    ))}
                                    <td className="py-2">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            className="size-8 text-muted-foreground hover:text-destructive"
                                            onClick={() => removeKomponen(i)}
                                            aria-label={`Hapus komponen ${i + 1}`}
                                        >
                                            <Trash2 className="size-4" />
                                        </Button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>

                    <p className="mt-2 flex items-center gap-1.5 text-xs text-muted-foreground">
                        <Plus className="size-3" />
                        Klik kolom Rumus Cair / Angsuran / Penalti untuk membuka editor rumus. Isi
                        kolom Nama pada baris terakhir untuk menambah baris komponen baru.
                    </p>
                </CardContent>
            </Card>

            {/* ===== Modal Rumus ===== */}
            <Dialog open={rumusTarget !== null} onOpenChange={(o) => !o && setRumusTarget(null)}>
                <DialogContent className="sm:max-w-md">
                    <DialogHeader>
                        <DialogTitle>Edit Rumus</DialogTitle>
                    </DialogHeader>
                    <textarea
                        rows={3}
                        value={rumusValue}
                        onChange={(e) => setRumusValue(e.target.value)}
                        className="border-input focus-visible:border-ring focus-visible:ring-ring/50 w-full rounded-md border bg-transparent px-3 py-2 font-mono text-sm shadow-xs outline-none focus-visible:ring-[3px]"
                        placeholder="Bangun rumus dengan token di bawah…"
                    />
                    <div className="space-y-3">
                        <div>
                            <p className="mb-1.5 text-xs font-medium text-muted-foreground">
                                Token Rumus
                            </p>
                            <div className="max-h-56 space-y-1 overflow-y-auto rounded-md border bg-muted/30 p-2">
                                {parameters.length === 0 ? (
                                    <p className="px-1.5 py-1 text-[11px] text-muted-foreground">
                                        Belum ada parameter token. Jalankan seeder Parameter.
                                    </p>
                                ) : (
                                    parameters.map((p) => (
                                        <button
                                            key={p.id}
                                            type="button"
                                            onClick={() =>
                                                setRumusValue((v) =>
                                                    v === '' ? p.nama : `${v} ${p.nama}`,
                                                )
                                            }
                                            className="flex w-full items-center gap-2 rounded px-1.5 py-1 text-left transition hover:bg-brand-600/10"
                                            title={`Sisipkan ${p.nama}`}
                                        >
                                            <span className="inline-flex max-w-full truncate rounded border bg-card px-1.5 py-0.5 font-mono text-xs text-brand-700 dark:text-brand-300">
                                                {p.nama}
                                            </span>
                                        </button>
                                    ))
                                )}
                            </div>
                        </div>

                        <div>
                            <p className="mb-1.5 text-xs font-medium text-muted-foreground">
                                Operator Matematika
                            </p>
                            <div className="flex flex-wrap gap-1.5">
                                {LIST_OPERATOR_RUMUS.map((op) => (
                                    <button
                                        key={op}
                                        type="button"
                                        onClick={() =>
                                            setRumusValue((v) => (v === '' ? op : `${v} ${op}`))
                                        }
                                        title={`Sisipkan operator ${op}`}
                                        className="rounded-md border bg-card px-2.5 py-1 font-mono text-xs font-semibold text-brand-700 transition hover:bg-brand-600/10 dark:text-brand-300"
                                    >
                                        {op}
                                    </button>
                                ))}
                            </div>
                        </div>
                    </div>
                    <DialogFooter className="gap-2">
                        <Button type="button" variant="outline" onClick={() => setRumusValue('')}>
                            Bersihkan
                        </Button>
                        <Button
                            type="button"
                            onClick={saveRumus}
                            className="bg-brand-600 hover:bg-brand-500"
                        >
                            Simpan Rumus
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            {/* ===== Aksi ===== */}
            <div className="flex items-center justify-end gap-3">
                <Button variant="outline" asChild>
                    <Link href={route('superadmin.pinjaman.produk')}>Kembali</Link>
                </Button>
                <Button
                    type="submit"
                    disabled={form.processing}
                    className="bg-brand-600 hover:bg-brand-500"
                >
                    {form.processing && <LoaderCircle className="animate-spin" />}
                    {processingLabel}
                </Button>
            </div>
        </form>
    );
}
