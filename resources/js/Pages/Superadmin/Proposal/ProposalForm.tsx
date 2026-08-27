import { useEffect, useMemo, useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import {
    Calculator,
    Coins,
    LoaderCircle,
    Printer,
    RotateCcw,
    Search,
    Trash2,
    UserRound,
    X,
} from 'lucide-react';

import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Textarea } from '@/Components/ui/textarea';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Switch } from '@/Components/ui/switch';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import { LookupModal } from '@/Components/LookupModal';
import { ConfirmDelete } from '@/Components/ConfirmDelete';
import { cn } from '@/lib/utils';
import { calculateLoan, type LoanSatuan } from '@/lib/loanCalc';
import type {
    LoanCostComponentRow,
    PinjamanAccountRow,
    PinjamanAnggotaRow,
    PinjamanMarketingRow,
    ProposalCostItem,
    ProposalEditRow,
    ProposalProdukRow,
    ProposalValues,
} from '@/types/models';

interface Props {
    initial?: ProposalEditRow | null;
    proposalId?: number;
    anggotaOptions: PinjamanAnggotaRow[];
    produkOptions: ProposalProdukRow[];
    marketingOptions: PinjamanMarketingRow[];
    accountOptions: PinjamanAccountRow[];
    costComponents: LoanCostComponentRow[];
    satuanOptions: { value: string; label: string }[];
    metodeOptions: string[];
    bayarPokokPerOptions: string[];
    noBuktiOtomatis?: string;
    submitUrl: string;
    submitMethod?: 'post' | 'put';
    processingLabel: string;
}

const rupiah = (v: string | number | null | undefined) =>
    `Rp ${Number(v ?? 0).toLocaleString('id-ID')}`;

const emptyCost = (): ProposalCostItem => ({
    component_id: '',
    nama: '',
    nominal: '',
    persen: false,
    account_id: '',
    is_deducted_from_disbursement: true,
});

const emptyValues = (): ProposalValues => ({
    tanggal: new Date().toISOString().slice(0, 10),
    no_bukti: '',
    anggota_id: '',
    anggota: null,
    jenis_id: '',
    marketing_id: '',
    plafon: '',
    bunga: '',
    jangka_waktu: '',
    satuan: '',
    bayar_pokok_per: '',
    pembayaran: 'per-jangka',
    setiap_saat: false,
    jenis_angsuran: '',
    penggunaan_kredit: '',
    jaminan: '',
    biaya: [],
});

export function ProposalForm({
    initial,
    proposalId,
    anggotaOptions,
    produkOptions,
    marketingOptions,
    accountOptions,
    costComponents,
    satuanOptions,
    metodeOptions,
    bayarPokokPerOptions,
    noBuktiOtomatis,
    submitUrl,
    submitMethod = 'post',
    processingLabel,
}: Props) {
    const form = useForm<ProposalValues>(() => {
        if (!initial) {
            const v = emptyValues();
            if (noBuktiOtomatis) v.no_bukti = noBuktiOtomatis;
            return v;
        }
        return {
            tanggal: initial.tanggal || new Date().toISOString().slice(0, 10),
            no_bukti: initial.no_bukti ?? '',
            anggota_id: String(initial.anggota_id),
            anggota: initial.anggota ?? null,
            jenis_id: String(initial.jenis_id),
            marketing_id: String(initial.marketing_id ?? ''),
            plafon: initial.plafon ?? '',
            bunga: initial.bunga ?? '',
            jangka_waktu: initial.jangka_waktu ?? '',
            satuan: initial.satuan ?? '',
            bayar_pokok_per: initial.bayar_pokok_per ?? '',
            pembayaran: initial.pembayaran || 'per-jangka',
            setiap_saat: initial.setiap_saat === '1',
            jenis_angsuran: initial.jenis_angsuran ?? '',
            penggunaan_kredit: initial.penggunaan_kredit ?? '',
            jaminan: initial.jaminan ?? '',
            biaya: (initial.biaya ?? []).map((b) => ({
                component_id: String((b as any).component_id ?? ''),
                nama: (b as any).nama ?? '',
                nominal: (b as any).nominal ?? '',
                persen: (b as any).persen === '1' || (b as any).persen === true,
                account_id: String((b as any).account_id ?? ''),
                is_deducted_from_disbursement:
                    (b as any).is_deducted_from_disbursement === '1' ||
                    (b as any).is_deducted_from_disbursement === true,
            })),
        };
    });

    const [anggotaOpen, setAnggotaOpen] = useState(false);
    const [marketingOpen, setMarketingOpen] = useState(false);

    const selectedProduk = produkOptions.find(
        (j) => String(j.id) === form.data.jenis_id,
    );

    const terpilihAnggota = anggotaOptions.find(
        (a) => String(a.id) === form.data.anggota_id,
    );

    // Live preview angsuran (mirror service).
    const angsuranPreview = useMemo(() => {
        const plafon = parseFloat(form.data.plafon);
        const jangka = parseInt(form.data.jangka_waktu, 10);
        if (
            isNaN(plafon) || plafon <= 0 ||
            isNaN(jangka) || jangka <= 0 ||
            !form.data.satuan
        ) {
            return null;
        }
        return calculateLoan({
            plafon,
            bunga: parseFloat(form.data.bunga) || 0,
            jangka_waktu: jangka,
            satuan: form.data.satuan as LoanSatuan,
            metode: form.data.jenis_angsuran || selectedProduk?.angsuran || undefined,
        });
    }, [form.data.plafon, form.data.bunga, form.data.jangka_waktu, form.data.satuan, form.data.jenis_angsuran, selectedProduk?.angsuran]);

    // Prefill metode + bunga default saat produk dipilih.
    useEffect(() => {
        if (selectedProduk) {
            const upd: Partial<ProposalValues> = {};
            if (selectedProduk.angsuran) upd.jenis_angsuran = selectedProduk.angsuran;
            if (!form.data.bunga && selectedProduk.bunga) upd.bunga = selectedProduk.bunga;
            form.setData((d) => ({ ...d, ...upd }));
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [form.data.jenis_id]);

    const setAnggota = (a: PinjamanAnggotaRow) => {
        form.setData((d) => ({
            ...d,
            anggota_id: String(a.id),
            anggota: a,
        }));
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form[submitMethod === 'put' ? 'put' : 'post'](submitUrl, { preserveScroll: true });
    };

    const setRow = (index: number, patch: Partial<ProposalCostItem>) => {
        form.setData((d) => {
            const rows = [...d.biaya];
            rows[index] = { ...rows[index], ...patch };
            return { ...d, biaya: rows };
        });
    };

    const addCost = (component?: LoanCostComponentRow) => {
        if (component) {
            form.setData((d) => ({
                ...d,
                biaya: [
                    ...d.biaya,
                    {
                        component_id: String(component.id),
                        nama: component.name,
                        nominal: component.calculation_type === 'percentage'
                            ? component.percentage
                            : component.amount,
                        persen: component.calculation_type === 'percentage',
                        account_id: String(component.account_id ?? ''),
                        is_deducted_from_disbursement:
                            component.is_deducted_from_disbursement === '1',
                    },
                ],
            }));
        } else {
            form.setData((d) => ({ ...d, biaya: [...d.biaya, emptyCost()] }));
        }
    };

    const removeCost = (index: number) => {
        form.setData((d) => {
            const rows = [...d.biaya];
            rows.splice(index, 1);
            return { ...d, biaya: rows };
        });
    };

    const plafon = parseFloat(form.data.plafon) || 0;

    const { totalBiaya, totalTerima } = useMemo(() => {
        let biaya = 0;
        let potongan = 0;
        for (const b of form.data.biaya) {
            const nominal = parseFloat(b.nominal) || 0;
            const amount = b.persen ? (plafon * nominal) / 100 : nominal;
            biaya += amount;
            if (b.is_deducted_from_disbursement) potongan += amount;
        }
        return {
            totalBiaya: biaya,
            totalTerima: plafon - potongan,
        };
    }, [form.data.biaya, plafon]);

    return (
        <form onSubmit={submit} className="w-full space-y-5">
            <div className="grid gap-5 lg:grid-cols-2">
                {/* ===== KOLOM KIRI: DATA PINJAMAN ===== */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Coins className="size-4" /> Data Pinjaman
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-5">
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label htmlFor="tanggal">Tanggal <span className="text-brand-600">*</span></Label>
                                <Input id="tanggal" type="date" value={form.data.tanggal}
                                    onChange={(e) => form.setData('tanggal', e.target.value)} />
                                {form.errors.tanggal && <Err>{form.errors.tanggal}</Err>}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="no_bukti">No. Bukti <span className="text-brand-600">*</span></Label>
                                <Input id="no_bukti" className="font-mono" value={form.data.no_bukti}
                                    onChange={(e) => form.setData('no_bukti', e.target.value)} />
                                {form.errors.no_bukti && <Err>{form.errors.no_bukti}</Err>}
                            </div>
                            <div className="space-y-2 sm:col-span-2">
                                <Label>Nama Debitur <span className="text-brand-600">*</span></Label>
                                <div className="flex gap-2">
                                    <Input
                                        value={form.data.anggota ? `${form.data.anggota.no_anggota} — ${form.data.anggota.nama}` : ''}
                                        placeholder="Cari debitur…"
                                        readOnly
                                        onClick={() => setAnggotaOpen(true)}
                                        className="cursor-pointer"
                                    />
                                    <Button type="button" variant="outline" size="icon" onClick={() => setAnggotaOpen(true)}>
                                        <Search />
                                    </Button>
                                </div>
                                {form.errors.anggota_id && <Err>{form.errors.anggota_id}</Err>}
                            </div>
                            <div className="space-y-2 sm:col-span-2">
                                <Label>Produk Pinjaman <span className="text-brand-600">*</span></Label>
                                <Select value={form.data.jenis_id || undefined}
                                    onValueChange={(v) => form.setData('jenis_id', v)}>
                                    <SelectTrigger><SelectValue placeholder="-- Pilih Produk --" /></SelectTrigger>
                                    <SelectContent>
                                        {produkOptions.map((p) => (
                                            <SelectItem key={p.id} value={String(p.id)}>{p.nama}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {form.errors.jenis_id && <Err>{form.errors.jenis_id}</Err>}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="plafon">Plafon <span className="text-brand-600">*</span></Label>
                                <Input id="plafon" value={form.data.plafon} inputMode="decimal"
                                    onChange={(e) => form.setData('plafon', e.target.value)} placeholder="0" />
                                {form.errors.plafon && <Err>{form.errors.plafon}</Err>}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="bunga">B. Hasil / Tahun (%) <span className="text-brand-600">*</span></Label>
                                <Input id="bunga" value={form.data.bunga} inputMode="decimal"
                                    onChange={(e) => form.setData('bunga', e.target.value)} placeholder="12" />
                                {form.errors.bunga && <Err>{form.errors.bunga}</Err>}
                            </div>
                            <div className="grid grid-cols-2 gap-3">
                                <div className="space-y-2">
                                    <Label htmlFor="jangka_waktu">Jangka Waktu <span className="text-brand-600">*</span></Label>
                                    <Input id="jangka_waktu" value={form.data.jangka_waktu} inputMode="numeric"
                                        onChange={(e) => form.setData('jangka_waktu', e.target.value)} />
                                    {form.errors.jangka_waktu && <Err>{form.errors.jangka_waktu}</Err>}
                                </div>
                                <div className="space-y-2">
                                    <Label>Satuan</Label>
                                    <Select value={form.data.satuan || undefined}
                                        onValueChange={(v) => form.setData('satuan', v)}>
                                        <SelectTrigger><SelectValue placeholder="--" /></SelectTrigger>
                                        <SelectContent>
                                            {satuanOptions.map((s) => (
                                                <SelectItem key={s.value} value={s.value}>{s.label}</SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {form.errors.satuan && <Err>{form.errors.satuan}</Err>}
                                </div>
                            </div>
                            <div className="space-y-2">
                                <Label>Bayar Pokok Per</Label>
                                <Select value={form.data.bayar_pokok_per || undefined}
                                    onValueChange={(v) => form.setData('bayar_pokok_per', v)}>
                                    <SelectTrigger><SelectValue placeholder="--" /></SelectTrigger>
                                    <SelectContent>
                                        {bayarPokokPerOptions.map((b) => (
                                            <SelectItem key={b} value={b}>{b} Bulan</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-2">
                                <Label>Jenis Pembayaran</Label>
                                <Select value={form.data.pembayaran || undefined}
                                    onValueChange={(v) => form.setData('pembayaran', v)}>
                                    <SelectTrigger><SelectValue placeholder="--" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="per-jangka">Per Jangka Waktu</SelectItem>
                                        <SelectItem value="per-bulan">Per Bulan</SelectItem>
                                        <SelectItem value="tunai">Tunai</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <label className="flex cursor-pointer items-center justify-between gap-3 rounded-lg border px-3 py-2 sm:col-span-2">
                                <span className="text-sm">Setiap Saat</span>
                                <Switch
                                    checked={form.data.setiap_saat}
                                    onCheckedChange={(v) => form.setData('setiap_saat', v)}
                                    aria-label="Setiap saat"
                                />
                            </label>
                            <div className="space-y-2">
                                <Label>Jenis Angsuran</Label>
                                <Select value={form.data.jenis_angsuran || undefined}
                                    onValueChange={(v) => form.setData('jenis_angsuran', v)}>
                                    <SelectTrigger><SelectValue placeholder="-- Metode --" /></SelectTrigger>
                                    <SelectContent>
                                        {metodeOptions.map((m) => (
                                            <SelectItem key={m} value={m}>{m}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="angsuran">Angsuran</Label>
                                <div className="relative">
                                    <Input id="angsuran" readOnly value={angsuranPreview ? String(angsuranPreview.nominal_angsuran) : ''}
                                        className="font-mono pr-16" placeholder="Otomatis" />
                                    <Calculator className="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* ===== KOLOM KANAN: INFORMASI DEBITUR + BIAYA ===== */}
                <div className="space-y-5">
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <UserRound className="size-4" /> Informasi Debitur
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-5">
                            <div className="space-y-2">
                                <Label htmlFor="penggunaan_kredit">Penggunaan Kredit</Label>
                                <Textarea id="penggunaan_kredit" value={form.data.penggunaan_kredit}
                                    onChange={(e) => form.setData('penggunaan_kredit', e.target.value)}
                                    placeholder="Modal usaha, renovasi, pendidikan, dsb." />
                            </div>
                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="jaminan">Jaminan</Label>
                                    <Input id="jaminan" value={form.data.jaminan}
                                        onChange={(e) => form.setData('jaminan', e.target.value)}
                                        placeholder="Emas, BPKB, Sertifikat, dll." />
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="no_hp">No. HP</Label>
                                    <Input id="no_hp" value={form.data.anggota?.no_hp ?? ''} readOnly />
                                </div>
                                <div className="space-y-2 sm:col-span-2">
                                    <Label>Marketing</Label>
                                    <div className="flex gap-2">
                                        <Input
                                            value={marketingOptions.find((m) => String(m.id) === form.data.marketing_id)
                                                ? `${marketingOptions.find((m) => String(m.id) === form.data.marketing_id)!.kode} — ${marketingOptions.find((m) => String(m.id) === form.data.marketing_id)!.nama}`
                                                : ''}
                                            readOnly placeholder="Cari marketing…" onClick={() => setMarketingOpen(true)} className="cursor-pointer" />
                                        <Button type="button" variant="outline" size="icon" onClick={() => setMarketingOpen(true)}><Search /></Button>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Tabel Biaya Pinjaman */}
                    <Card>
                        <CardHeader className="flex-row flex-wrap items-center justify-between space-y-0">
                            <CardTitle>Tabel Biaya Pinjaman</CardTitle>
                            <div className="flex flex-wrap gap-2">
                                <Select onValueChange={(v) => {
                                    const c = costComponents.find((c) => String(c.id) === v);
                                    addCost(c);
                                }}>
                                    <SelectTrigger><SelectValue placeholder="Tambah dari master" /></SelectTrigger>
                                    <SelectContent>
                                        {costComponents.map((c) => (
                                            <SelectItem key={c.id} value={String(c.id)}>{c.name}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <Button type="button" variant="outline" onClick={() => addCost()}>
                                    Tambah Baris
                                </Button>
                            </div>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <div className="overflow-x-auto rounded-md border">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Komponen Pinjaman</TableHead>
                                            <TableHead>Nominal</TableHead>
                                            <TableHead className="w-14">%</TableHead>
                                            <TableHead className="w-24">Potong Pencairan</TableHead>
                                            <TableHead>No. Account</TableHead>
                                            <TableHead className="w-12" />
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {form.data.biaya.length === 0 && (
                                            <TableRow><TableCell colSpan={6} className="h-20 text-center text-muted-foreground">
                                                Belum ada biaya. Tambah dari master komponen atau kosongkan.
                                            </TableCell></TableRow>
                                        )}
                                        {form.data.biaya.map((b, i) => (
                                            <TableRow key={i}>
                                                <TableCell className="min-w-52">
                                                    <Select value={b.component_id || undefined}
                                                        onValueChange={(v) => {
                                                            const c = costComponents.find((c) => String(c.id) === v);
                                                            setRow(i, {
                                                                component_id: v,
                                                                nama: c?.name ?? '',
                                                                nominal: c ? (c.calculation_type === 'percentage' ? c.percentage : c.amount) : '',
                                                                persen: c?.calculation_type === 'percentage',
                                                                account_id: String(c?.account_id ?? ''),
                                                                is_deducted_from_disbursement: c ? c.is_deducted_from_disbursement === '1' : true,
                                                            });
                                                        }}>
                                                        <SelectTrigger className="w-full"><SelectValue placeholder="-- Komponen --" /></SelectTrigger>
                                                        <SelectContent>
                                                            {costComponents.map((c) => (
                                                                <SelectItem key={c.id} value={String(c.id)}>{c.name}</SelectItem>
                                                            ))}
                                                        </SelectContent>
                                                    </Select>
                                                    <Input value={b.nama} placeholder="Atur manual nama"
                                                        className="mt-1" onChange={(e) => setRow(i, { nama: e.target.value })} />
                                                </TableCell>
                                                <TableCell className="w-36">
                                                    <Input value={b.nominal} inputMode="decimal" placeholder="0"
                                                        onChange={(e) => setRow(i, { nominal: e.target.value })} />
                                                </TableCell>
                                                <TableCell>
                                                    <Switch checked={b.persen}
                                                        onCheckedChange={(v) => setRow(i, { persen: v })}
                                                        aria-label="Persen" />
                                                </TableCell>
                                                <TableCell>
                                                    <Switch checked={b.is_deducted_from_disbursement}
                                                        onCheckedChange={(v) => setRow(i, { is_deducted_from_disbursement: v })}
                                                        aria-label="Potong pencairan" />
                                                </TableCell>
                                                <TableCell className="w-52">
                                                    <Select value={b.account_id || undefined}
                                                        onValueChange={(v) => setRow(i, { account_id: v })}>
                                                        <SelectTrigger className="w-full"><SelectValue placeholder="-- Akun --" /></SelectTrigger>
                                                        <SelectContent>
                                                            {accountOptions.map((a) => (
                                                                <SelectItem key={a.id} value={String(a.id)}>
                                                                    <span className="font-mono text-xs">{a.no_account}</span> — {a.nama}
                                                                </SelectItem>
                                                            ))}
                                                        </SelectContent>
                                                    </Select>
                                                </TableCell>
                                                <TableCell>
                                                    <Button type="button" variant="ghost" size="icon" onClick={() => removeCost(i)}>
                                                        <Trash2 className="text-brand-600" />
                                                    </Button>
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            </div>
                            <div className="flex flex-wrap items-center justify-end gap-6 rounded-lg border bg-muted/40 px-4 py-3 text-sm">
                                <div>
                                    <span className="text-muted-foreground">Total Biaya: </span>
                                    <span className="font-mono font-semibold">{rupiah(totalBiaya)}</span>
                                </div>
                                <div>
                                    <span className="text-muted-foreground">Total Terima: </span>
                                    <span className="font-mono font-semibold">{rupiah(totalTerima)}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            {/* ===== Tombol aksi ===== */}
            <div className="flex flex-wrap items-center justify-between gap-3 rounded-lg border bg-card p-4">
                <div className="flex flex-wrap items-center gap-2">
                    <Button type="submit" disabled={form.processing} className="bg-brand-600 hover:bg-brand-500">
                        {form.processing && <LoaderCircle className="animate-spin" />}
                        Simpan
                    </Button>
                    <Button type="button" variant="outline" onClick={() => form.reset()}>
                        <RotateCcw /> Reset
                    </Button>
                    <Button type="button" variant="outline" onClick={() => window.print()}>
                        <Printer /> Cetak
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        disabled={form.processing}
                        onClick={() => {
                            form[submitMethod === 'put' ? 'put' : 'post'](submitUrl, {
                                preserveScroll: true,
                                onSuccess: () => setTimeout(() => window.print(), 400),
                            });
                        }}
                    >
                        {form.processing && <LoaderCircle className="animate-spin" />}
                        Simpan &amp; Cetak
                    </Button>
                </div>
                <div className="flex flex-wrap items-center gap-2">
                    {proposalId && (
                        <ConfirmDelete
                            routeName="superadmin.pinjaman.proposal.destroy"
                            id={proposalId}
                            label={`${form.data.no_bukti} (${form.data.anggota?.nama ?? ''})`}
                        />
                    )}
                    <Button type="button" variant="outline" asChild>
                        <Link href={route('superadmin.pinjaman.proposal')}>
                            <X /> Tutup
                        </Link>
                    </Button>
                </div>
            </div>

            {/* ===== Lookup modals ===== */}
            <LookupModal<PinjamanAnggotaRow>
                open={anggotaOpen}
                onOpenChange={setAnggotaOpen}
                title="Pilih Debitur"
                columns={[
                    { key: 'no_anggota', header: 'No. Anggota', render: (a) => <span className="font-mono text-xs">{a.no_anggota}</span> },
                    { key: 'nama', header: 'Nama Debitur' },
                    { key: 'no_hp', header: 'No. HP' },
                    { key: 'alamat', header: 'Alamat' },
                ]}
                rows={anggotaOptions}
                onSelect={setAnggota}
                getSearchText={(a) => `${a.no_anggota} ${a.nama} ${a.alamat ?? ''}`}
                searchPlaceholder="Cari no. anggota / nama…"
            />

            <LookupModal
                open={marketingOpen}
                onOpenChange={setMarketingOpen}
                title="Pilih Marketing"
                columns={[
                    { key: 'kode', header: 'Kode', render: (m: any) => <span className="font-mono text-xs">{m.kode}</span> },
                    { key: 'nama', header: 'Nama' },
                ]}
                rows={marketingOptions}
                onSelect={(m) => form.setData('marketing_id', String(m.id))}
                getSearchText={(m) => `${m.kode} ${m.nama}`}
                searchPlaceholder="Cari marketing…"
            />
        </form>
    );
}

function Err({ children }: { children: React.ReactNode }) {
    return <p className="text-sm text-brand-600">{children}</p>;
}
