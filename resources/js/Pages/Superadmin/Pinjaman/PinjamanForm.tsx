import { useEffect, useMemo, useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import {
    Calculator,
    FileText,
    HandCoins,
    LoaderCircle,
    Plus,
    Printer,
    RotateCcw,
    Search,
    ShieldCheck,
    Trash2,
    UserRound,
    Users,
    Wallet,
    X,
} from 'lucide-react';

import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
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
    PinjamanAnggotaRow,
    PinjamanEditRow,
    PinjamanTransaksiValues,
} from '@/types/models';

interface Props {
    initial?: PinjamanEditRow | null;
    pinjamanId?: number;
    anggotaOptions: PinjamanAnggotaRow[];
    jenisOptions: { id: number; nama: string; angsuran: string | null; bunga: string }[];
    marketingOptions: { id: number; kode: string; nama: string }[];
    accountOptions: { id: number; no_account: string; nama: string }[];
    jaminanTypes: { id: number; nama: string; details: { id: number; detail: string }[] }[];
    simpananOptions: { id: number; no_rekening: string; anggota_id: string; anggota?: { id: number; no_anggota: string; nama: string } | null }[];
    kodeTarikanOptions: { id: number; kode: string; nama: string }[];
    sektorOptions: { id: number; nama: string }[];
    bayarPokokPerOptions: string[];
    suratOptions: { id: number; nama: string }[];
    satuanOptions: { value: string; label: string }[];
    nomorOtomatis?: string;
    submitUrl: string;
    submitMethod?: 'post' | 'put';
    processingLabel: string;
}

const TAB_LIST = [
    { id: 'pinjaman', label: 'Pinjaman', icon: HandCoins },
    { id: 'biaya', label: 'Biaya', icon: Wallet },
    { id: 'jaminan', label: 'Jaminan', icon: ShieldCheck },
    { id: 'saksi', label: 'Saksi', icon: Users },
    { id: 'surat', label: 'Surat', icon: FileText },
    { id: 'penjamin', label: 'Penjamin', icon: UserRound },
] as const;

type TabId = (typeof TAB_LIST)[number]['id'];

const rupiah = (v: string | number | null | undefined) =>
    `Rp ${Number(v ?? 0).toLocaleString('id-ID')}`;

const SURAT_TOKENS =
    '{{no_pinjaman}} {{tanggal}} {{nama_anggota}} {{no_anggota}} {{plafon}} {{jangka_waktu}} {{angsuran}}';

const emptyValues = (): PinjamanTransaksiValues => ({
    tanggal: new Date().toISOString().slice(0, 10),
    no_pinjaman: '',
    anggota_id: '',
    anggota: null,
    jenis_id: '',
    jaminan_id: '',
    marketing_id: '',
    sektor_id: '',
    jenis_angsuran: '',
    swp: '',
    spp: '',
    plafon: '',
    bunga: '',
    jangka_waktu: '',
    satuan: '',
    bayar_pokok_per: '',
    pembayaran: 'manual',
    jatuh_tempo: '',
    angsuran: '',
    manual: '0',
    tabungan_id: '',
    kode_id: '',
    kode_koreksi: '',
    cair_simpanan: false,
    sms: false,
    rekening_koran: false,
    aktif: true,
    biaya: [],
    jaminan: [],
    saksi: [],
    surat: [],
    penjamin: [],
});

export function PinjamanForm({
    initial,
    pinjamanId,
    anggotaOptions,
    jenisOptions,
    marketingOptions,
    accountOptions,
    jaminanTypes,
    simpananOptions,
    kodeTarikanOptions,
    sektorOptions,
    bayarPokokPerOptions,
    suratOptions,
    satuanOptions,
    nomorOtomatis,
    submitUrl,
    submitMethod = 'post',
    processingLabel,
}: Props) {
    const form = useForm<PinjamanTransaksiValues>(() => {
        if (!initial) {
            const v = emptyValues();
            if (nomorOtomatis) v.no_pinjaman = nomorOtomatis;
            return v;
        }
        return {
            tanggal: initial.tanggal || new Date().toISOString().slice(0, 10),
            no_pinjaman: initial.no_pinjaman ?? '',
            anggota_id: String(initial.anggota_id),
            anggota: initial.anggota ?? null,
            jenis_id: String(initial.jenis_id),
            jaminan_id: String(initial.jaminan_id ?? ''),
            marketing_id: String(initial.marketing_id ?? ''),
            sektor_id: String(initial.sektor_id ?? ''),
            jenis_angsuran: initial.angsuran ?? '',
            swp: initial.swp_id ?? '',
            spp: initial.spp_id ?? '',
            plafon: initial.plafon ?? '',
            bunga: initial.bunga ?? '',
            jangka_waktu: initial.jangka_waktu ?? '',
            satuan: initial.satuan ?? '',
            bayar_pokok_per: initial.bayar_pokok_per ?? '',
            pembayaran: initial.pembayaran || 'manual',
            jatuh_tempo: initial.jatuh_tempo ?? '',
            angsuran: initial.nominal_angsuran ?? '',
            manual: initial.manual ?? '0',
            tabungan_id: String(initial.tabungan_id ?? ''),
            kode_id: String(initial.kode_id ?? ''),
            kode_koreksi: initial.kode_koreksi ?? '',
            cair_simpanan: initial.cair_simpanan === '1',
            sms: initial.sms === '1',
            rekening_koran: initial.rekening_koran === '1',
            aktif: initial.aktif !== '0',
            biaya: (initial.biaya ?? []).map((b) => ({ ...b, persen: b.persen === '1' || b.persen === true })),
            jaminan: initial.jaminan ?? [],
            saksi: initial.saksi ?? [],
            surat: initial.surat ?? [],
            penjamin: initial.penjamin ?? [],
        };
    });

    const [activeTab, setActiveTab] = useState<TabId>('pinjaman');

    // Modal lookup state.
    const [anggotaOpen, setAnggotaOpen] = useState(false);
    const [marketingOpen, setMarketingOpen] = useState(false);
    const [swpOpen, setSwpOpen] = useState(false);
    const [sppOpen, setSppOpen] = useState(false);
    const [tabunganOpen, setTabunganOpen] = useState(false);
    const [kodeOpen, setKodeOpen] = useState(false);
    const [saksiLookup, setSaksiLookup] = useState<number | null>(null);
    const [penjaminLookup, setPenjaminLookup] = useState<number | null>(null);

    const selectedProduk = jenisOptions.find(
        (j) => String(j.id) === form.data.jenis_id,
    );

    const terpilihAnggota = anggotaOptions.find(
        (a) => String(a.id) === form.data.anggota_id,
    );

    // Recalculate angsuran + jatuh tempo secara live (mirror service).
    useEffect(() => {
        const plafon = parseFloat(form.data.plafon);
        const jangka = parseInt(form.data.jangka_waktu, 10);
        if (!isNaN(plafon) && plafon > 0 && !isNaN(jangka) && jangka > 0 && form.data.satuan) {
            const hasil = calculateLoan({
                plafon,
                bunga: parseFloat(form.data.bunga) || 0,
                jangka_waktu: jangka,
                satuan: form.data.satuan as LoanSatuan,
                metode: form.data.jenis_angsuran || selectedProduk?.angsuran,
            });
            form.setData((d) => ({ ...d, angsuran: String(hasil.nominal_angsuran) }));
        } else {
            form.setData((d) => ({ ...d, angsuran: '' }));
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [form.data.plafon, form.data.bunga, form.data.jangka_waktu, form.data.satuan, form.data.jenis_angsuran, selectedProduk?.angsuran]);

    useEffect(() => {
        if (form.data.tanggal && form.data.jangka_waktu && form.data.satuan) {
            form.setData((d) => ({ ...d, jatuh_tempo: hitungJatuhTempo(d) }));
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [form.data.tanggal, form.data.jangka_waktu, form.data.satuan]);

    // Saat produk dipilih, ambil metode angsuran + prefill bunga default.
    useEffect(() => {
        if (selectedProduk) {
            const upd: Partial<PinjamanTransaksiValues> = {};
            if (selectedProduk.angsuran) upd.jenis_angsuran = selectedProduk.angsuran;
            if (!form.data.bunga && selectedProduk.bunga) upd.bunga = selectedProduk.bunga;
            form.setData((d) => ({ ...d, ...upd }));
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [form.data.jenis_id]);

    // Saat jenis jaminan diganti, isi baris jaminan sesuai detail tipe.
    useEffect(() => {
        const tipe = jaminanTypes.find((j) => String(j.id) === form.data.jaminan_id);
        if (tipe && tipe.details.length) {
            form.setData((d) => ({
                ...d,
                jaminan: tipe.details.map((det) => ({
                    nama: det.detail,
                    keterangan: '',
                    nominal: '',
                })),
            }));
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [form.data.jaminan_id]);

    const setAnggota = (a: PinjamanAnggotaRow) => {
        form.setData((d) => ({
            ...d,
            anggota_id: String(a.id),
            anggota: {
                id: a.id,
                no_anggota: a.no_anggota,
                nama: a.nama,
                alamat: a.alamat ?? '',
                no_identitas: a.no_identitas ?? '',
                telepon: a.telepon ?? '',
            },
        }));
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form[submitMethod === 'put' ? 'put' : 'post'](submitUrl, { preserveScroll: true });
    };

    const setRow = <K extends 'biaya' | 'jaminan' | 'saksi' | 'surat' | 'penjamin'>(
        key: K,
        index: number,
        patch: Partial<PinjamanTransaksiValues[K][number]>,
    ) => {
        form.setData((d) => {
            const rows = [...(d[key] as any[])];
            rows[index] = { ...rows[index], ...patch } as any;
            return { ...d, [key]: rows } as PinjamanTransaksiValues;
        });
    };

    const addRow = <K extends 'biaya' | 'jaminan' | 'saksi' | 'surat' | 'penjamin'>(
        key: K,
        row: PinjamanTransaksiValues[K][number],
    ) => {
        form.setData((d) => ({ ...d, [key]: [...(d[key] as any[]), row] } as PinjamanTransaksiValues));
    };

    const removeRow = <K extends 'biaya' | 'jaminan' | 'saksi' | 'surat' | 'penjamin'>(
        key: K,
        index: number,
    ) => {
        form.setData((d) => {
            const rows = [...(d[key] as any[])];
            rows.splice(index, 1);
            return { ...d, [key]: rows } as PinjamanTransaksiValues;
        });
    };

    const totalBiaya = useMemo(() => {
        const plafon = parseFloat(form.data.plafon) || 0;
        return form.data.biaya.reduce((sum, b) => {
            const nominal = parseFloat(b.nominal) || 0;
            return sum + (b.persen ? (plafon * nominal) / 100 : nominal);
        }, 0);
    }, [form.data.biaya, form.data.plafon]);

    const totalJaminan = useMemo(
        () =>
            form.data.jaminan.reduce(
                (s, j) => s + (parseFloat(j.nominal) || 0),
                0,
            ),
        [form.data.jaminan],
    );

    const totalPencairan = (parseFloat(form.data.plafon) || 0) - totalBiaya;

    return (
        <form onSubmit={submit} className="w-full space-y-5">
            {/* ===== Tabs ===== */}
            <div className="flex flex-wrap gap-2">
                {TAB_LIST.map((tab) => {
                    const Icon = tab.icon;
                    return (
                        <button
                            key={tab.id}
                            type="button"
                            onClick={() => setActiveTab(tab.id)}
                            className={cn(
                                'inline-flex items-center gap-2 rounded-lg border px-4 py-2 text-sm font-medium transition',
                                activeTab === tab.id
                                    ? 'border-brand-600 bg-brand-600 text-white'
                                    : 'border-input bg-card text-muted-foreground hover:bg-muted/50',
                            )}
                        >
                            <Icon className="size-4" />
                            {tab.label}
                        </button>
                    );
                })}
            </div>

            {/* ===== TAB 1: PINJAMAN ===== */}
            {activeTab === 'pinjaman' && (
                <div className="space-y-5">
                    {/* Informasi Dasar */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi Dasar</CardTitle>
                        </CardHeader>
                        <CardContent className="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                            <div className="space-y-2">
                                <Label htmlFor="tanggal">Tanggal <span className="text-brand-600">*</span></Label>
                                <Input id="tanggal" type="date" value={form.data.tanggal}
                                    onChange={(e) => form.setData('tanggal', e.target.value)} />
                                {form.errors.tanggal && <Err>{form.errors.tanggal}</Err>}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="no_pinjaman">No. Pinjaman <span className="text-brand-600">*</span></Label>
                                <Input id="no_pinjaman" className="font-mono" value={form.data.no_pinjaman}
                                    onChange={(e) => form.setData('no_pinjaman', e.target.value)} />
                                {form.errors.no_pinjaman && <Err>{form.errors.no_pinjaman}</Err>}
                            </div>
                            <div className="space-y-2">
                                <Label>No. Anggota <span className="text-brand-600">*</span></Label>
                                <div className="flex gap-2">
                                    <Input
                                        value={
                                            form.data.anggota
                                                ? `${form.data.anggota.no_anggota} — ${form.data.anggota.nama}`
                                                : ''
                                        }
                                        placeholder="Cari anggota…"
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
                            <div className="space-y-2">
                                <Label htmlFor="nama_anggota">Nama Anggota</Label>
                                <Input id="nama_anggota" value={form.data.anggota?.nama ?? ''} readOnly />
                            </div>
                            <div className="space-y-2">
                                <Label>Produk Pinjaman <span className="text-brand-600">*</span></Label>
                                <Select value={form.data.jenis_id || undefined}
                                    onValueChange={(v) => form.setData('jenis_id', v)}>
                                    <SelectTrigger><SelectValue placeholder="-- Pilih Produk --" /></SelectTrigger>
                                    <SelectContent>
                                        {jenisOptions.map((j) => (
                                            <SelectItem key={j.id} value={String(j.id)}>{j.nama}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {form.errors.jenis_id && <Err>{form.errors.jenis_id}</Err>}
                            </div>
                            <div className="space-y-2">
                                <Label>Jaminan</Label>
                                <Select value={form.data.jaminan_id || undefined}
                                    onValueChange={(v) => form.setData('jaminan_id', v)}>
                                    <SelectTrigger><SelectValue placeholder="-- Pilih Jaminan --" /></SelectTrigger>
                                    <SelectContent>
                                        {jaminanTypes.map((j) => (
                                            <SelectItem key={j.id} value={String(j.id)}>{j.nama}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Informasi Angsuran */}
                    <Card>
                        <CardHeader><CardTitle>Informasi Angsuran</CardTitle></CardHeader>
                        <CardContent className="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
                            <div className="space-y-2">
                                <Label>{selectedProduk?.angsuran ?? 'Metode Angsuran'}</Label>
                                <Input value={form.data.jenis_angsuran || selectedProduk?.angsuran || ''} readOnly />
                            </div>
                            <div className="space-y-2">
                                <Label>Jenis Angsuran</Label>
                                <Select value={form.data.jenis_angsuran || undefined}
                                    onValueChange={(v) => form.setData('jenis_angsuran', v)}>
                                    <SelectTrigger><SelectValue placeholder="-- Metode --" /></SelectTrigger>
                                    <SelectContent>
                                        {['Anuitas', 'Flat', 'Flat Efektif', 'Pokok Tetap', 'Bagi Hasil Menurun'].map((m) => (
                                            <SelectItem key={m} value={m}>{m}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-2">
                                <Label>SWP</Label>
                                <div className="flex gap-2">
                                    <Input value={form.data.swp} placeholder="0"
                                        onChange={(e) => form.setData('swp', e.target.value)} />
                                    <Button type="button" variant="outline" size="icon" onClick={() => setSwpOpen(true)}><Search /></Button>
                                </div>
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="plafon">Plafon <span className="text-brand-600">*</span></Label>
                                <Input id="plafon" value={form.data.plafon} inputMode="decimal"
                                    onChange={(e) => form.setData('plafon', e.target.value)} placeholder="0" />
                                {form.errors.plafon && <Err>{form.errors.plafon}</Err>}
                            </div>
                            <div className="space-y-2">
                                <Label>SPP</Label>
                                <div className="flex gap-2">
                                    <Input value={form.data.spp} placeholder="0"
                                        onChange={(e) => form.setData('spp', e.target.value)} />
                                    <Button type="button" variant="outline" size="icon" onClick={() => setSppOpen(true)}><Search /></Button>
                                </div>
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="bunga">Bagi Hasil / Tahun (%) <span className="text-brand-600">*</span></Label>
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
                            <div className="space-y-2">
                                <Label htmlFor="jatuh_tempo">Jatuh Tempo</Label>
                                <Input id="jatuh_tempo" type="date" readOnly value={form.data.jatuh_tempo} />
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="angsuran">Angsuran</Label>
                                <div className="relative">
                                    <Input id="angsuran" readOnly value={form.data.angsuran} className="font-mono pr-16" />
                                    <Calculator className="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Pembayaran Angsuran */}
                    <Card>
                        <CardHeader><CardTitle>Pembayaran Angsuran</CardTitle>
                            <p className="text-sm text-muted-foreground">Pilih metode pembayaran angsuran.</p>
                        </CardHeader>
                        <CardContent className="space-y-5">
                            <div className="flex flex-wrap gap-6">
                                {(['manual', 'otomatis'] as const).map((m) => (
                                    <label key={m} className="flex cursor-pointer items-center gap-2">
                                        <input
                                            type="radio"
                                            name="pembayaran"
                                            checked={form.data.pembayaran === m}
                                            onChange={() => form.setData('pembayaran', m)}
                                            className="size-4 accent-brand-600"
                                        />
                                        <span className="text-sm capitalize">{m}</span>
                                    </label>
                                ))}
                            </div>
                            {form.data.pembayaran === 'manual' && (
                                <div className="grid gap-5 sm:grid-cols-3">
                                    <div className="space-y-2">
                                        <Label>No. Simpanan</Label>
                                        <div className="flex gap-2">
                                            <Input value={form.data.tabungan_id} readOnly placeholder="Pilih simpanan"
                                                onClick={() => setTabunganOpen(true)} className="cursor-pointer" />
                                            <Button type="button" variant="outline" size="icon" onClick={() => setTabunganOpen(true)}><Search /></Button>
                                        </div>
                                    </div>
                                    <div className="space-y-2">
                                        <Label>Kode Tarikan</Label>
                                        <div className="flex gap-2">
                                            <Input value={form.data.kode_id} readOnly placeholder="Pilih kode"
                                                onClick={() => setKodeOpen(true)} className="cursor-pointer" />
                                            <Button type="button" variant="outline" size="icon" onClick={() => setKodeOpen(true)}><Search /></Button>
                                        </div>
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="kode_koreksi">Koreksi Debet</Label>
                                        <Input id="kode_koreksi" value={form.data.kode_koreksi}
                                            onChange={(e) => form.setData('kode_koreksi', e.target.value)} />
                                    </div>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    {/* Informasi Tambahan */}
                    <Card>
                        <CardHeader><CardTitle>Informasi Tambahan</CardTitle></CardHeader>
                        <CardContent className="grid gap-5 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label>Marketing</Label>
                                <div className="flex gap-2">
                                    <Input
                                        value={
                                            marketingOptions.find((m) => String(m.id) === form.data.marketing_id)
                                                ? `${marketingOptions.find((m) => String(m.id) === form.data.marketing_id)!.kode} — ${marketingOptions.find((m) => String(m.id) === form.data.marketing_id)!.nama}`
                                                : ''
                                        }
                                        readOnly placeholder="Cari marketing…" onClick={() => setMarketingOpen(true)} className="cursor-pointer" />
                                    <Button type="button" variant="outline" size="icon" onClick={() => setMarketingOpen(true)}><Search /></Button>
                                </div>
                            </div>
                            <div className="space-y-2">
                                <Label>Sektor</Label>
                                <Select value={form.data.sektor_id || undefined}
                                    onValueChange={(v) => form.setData('sektor_id', v)}>
                                    <SelectTrigger><SelectValue placeholder="-- Pilih Sektor --" /></SelectTrigger>
                                    <SelectContent>
                                        {sektorOptions.map((s) => (
                                            <SelectItem key={s.id} value={String(s.id)}>{s.nama}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Checkbox status */}
                    <Card>
                        <CardContent className="grid gap-4 sm:grid-cols-4">
                            {([['cair_simpanan', 'Pencairan ke Simpanan'], ['sms', 'SMS'], ['rekening_koran', 'Rekening Koran'], ['aktif', 'Aktif']] as const).map(([key, label]) => (
                                <label key={key} className="flex cursor-pointer items-center justify-between gap-3">
                                    <span className="text-sm">{label}</span>
                                    <Switch
                                        checked={form.data[key]}
                                        onCheckedChange={(v) => form.setData(key, v)}
                                        aria-label={label}
                                    />
                                </label>
                            ))}
                        </CardContent>
                    </Card>
                </div>
            )}

            {/* ===== TAB 2: BIAYA ===== */}
            {activeTab === 'biaya' && (
                <Card>
                    <CardHeader className="flex-row items-center justify-between space-y-0">
                        <CardTitle>Biaya Pinjaman</CardTitle>
                        <Button type="button" variant="outline" onClick={() => addRow('biaya', { nama: '', nominal: '', persen: false, account_id: '' })}>
                            <Plus /> Tambah Komponen
                        </Button>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="overflow-x-auto rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Komponen Pinjaman</TableHead>
                                        <TableHead>Nominal</TableHead>
                                        <TableHead>%</TableHead>
                                        <TableHead>No. Account</TableHead>
                                        <TableHead className="w-12" />
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {form.data.biaya.length === 0 && (
                                        <TableRow><TableCell colSpan={5} className="h-20 text-center text-muted-foreground">Belum ada komponen biaya.</TableCell></TableRow>
                                    )}
                                    {form.data.biaya.map((b, i) => (
                                        <TableRow key={i}>
                                            <TableCell>
                                                <Input value={b.nama} placeholder="Nama komponen"
                                                    onChange={(e) => setRow('biaya', i, { nama: e.target.value })} />
                                            </TableCell>
                                            <TableCell className="w-40">
                                                <Input value={b.nominal} inputMode="decimal" placeholder="0"
                                                    onChange={(e) => setRow('biaya', i, { nominal: e.target.value })} />
                                            </TableCell>
                                            <TableCell className="w-16">
                                                <Switch checked={b.persen}
                                                    onCheckedChange={(v) => setRow('biaya', i, { persen: v })}
                                                    aria-label="Persen" />
                                            </TableCell>
                                            <TableCell className="w-52">
                                                <Select value={b.account_id || undefined}
                                                    onValueChange={(v) => setRow('biaya', i, { account_id: v })}>
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
                                                <Button type="button" variant="ghost" size="icon" onClick={() => removeRow('biaya', i)}>
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
                                <span className="text-muted-foreground">Total Pencairan: </span>
                                <span className="font-mono font-semibold">{rupiah(totalPencairan)}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* ===== TAB 3: JAMINAN ===== */}
            {activeTab === 'jaminan' && (
                <Card>
                    <CardHeader className="flex-row items-center justify-between space-y-0">
                        <CardTitle>Data Jaminan</CardTitle>
                        <Button type="button" variant="outline" onClick={() => addRow('jaminan', { nama: '', keterangan: '', nominal: '' })}>
                            <Plus /> Tambah Baris
                        </Button>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="overflow-x-auto rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Detail Jaminan</TableHead>
                                        <TableHead>Keterangan</TableHead>
                                        <TableHead>Nilai Jaminan</TableHead>
                                        <TableHead className="w-12" />
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {form.data.jaminan.length === 0 && (
                                        <TableRow><TableCell colSpan={4} className="h-20 text-center text-muted-foreground">
                                            Pilih jenis jaminan di tab Pinjaman untuk mengisi otomatis, atau tambah baris manual.
                                        </TableCell></TableRow>
                                    )}
                                    {form.data.jaminan.map((j, i) => (
                                        <TableRow key={i}>
                                            <TableCell className="w-56">
                                                <Input value={j.nama}
                                                    onChange={(e) => setRow('jaminan', i, { nama: e.target.value })} />
                                            </TableCell>
                                            <TableCell>
                                                <Input value={j.keterangan}
                                                    onChange={(e) => setRow('jaminan', i, { keterangan: e.target.value })} />
                                            </TableCell>
                                            <TableCell className="w-44">
                                                <Input value={j.nominal} inputMode="decimal"
                                                    onChange={(e) => setRow('jaminan', i, { nominal: e.target.value })} />
                                            </TableCell>
                                            <TableCell>
                                                <Button type="button" variant="ghost" size="icon" onClick={() => removeRow('jaminan', i)}>
                                                    <Trash2 className="text-brand-600" />
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </div>
                        <div className="flex justify-end rounded-lg border bg-muted/40 px-4 py-3 text-sm">
                            <span className="text-muted-foreground">Total Nilai Jaminan: </span>
                            <span className="ml-2 font-mono font-semibold">{rupiah(totalJaminan)}</span>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* ===== TAB 4: SAKSI ===== */}
            {activeTab === 'saksi' && (
                <Card>
                    <CardHeader className="flex-row items-center justify-between space-y-0">
                        <CardTitle>Data Saksi</CardTitle>
                        <Button type="button" variant="outline" onClick={() => addRow('saksi', { nama: '', tempat_lahir: '', tgl_lahir: '', no_ktp: '', alamat: '', pekerjaan_id: '' })}>
                            <Plus /> Tambah Saksi
                        </Button>
                    </CardHeader>
                    <CardContent>
                        <div className="flex gap-2">
                            <Button type="button" variant="outline" onClick={() => setSaksiLookup(0)}>
                                <Search /> Pilih dari Anggota
                            </Button>
                        </div>
                        {form.data.saksi.length > 0 && (
                            <div className="mt-4 space-y-3">
                                {form.data.saksi.map((s, i) => (
                                    <div key={i} className="grid gap-3 rounded-lg border p-3 sm:grid-cols-3">
                                        <div className="flex gap-2">
                                            <Input value={s.nama} placeholder="Nama saksi"
                                                onChange={(e) => setRow('saksi', i, { nama: e.target.value })} />
                                            <Button type="button" variant="outline" size="icon" onClick={() => setSaksiLookup(i)}><Search /></Button>
                                        </div>
                                        <Input value={s.no_ktp} placeholder="No. KTP"
                                            onChange={(e) => setRow('saksi', i, { no_ktp: e.target.value })} />
                                        <Input value={s.alamat} placeholder="Alamat"
                                            onChange={(e) => setRow('saksi', i, { alamat: e.target.value })} />
                                        <div className="flex items-center gap-2 sm:col-span-3">
                                            <Button type="button" variant="ghost" size="icon" onClick={() => removeRow('saksi', i)}>
                                                <Trash2 className="text-brand-600" />
                                            </Button>
                                            <span className="text-xs text-muted-foreground">Saksi #{i + 1}</span>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>
            )}

            {/* ===== TAB 5: SURAT ===== */}
            {activeTab === 'surat' && (
                <Card>
                    <CardHeader className="flex-row items-center justify-between space-y-0">
                        <CardTitle>Surat Pinjaman</CardTitle>
                        <Button type="button" variant="outline" onClick={() => addRow('surat', { surat: '', surat_id: '', keterangan: '' })}>
                            <Plus /> Tambah Surat
                        </Button>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="rounded-lg border bg-muted/40 p-4 text-sm text-muted-foreground">
                            Template surat otomatis memakai placeholder berikut.
                            <code className="mt-1 block font-mono text-xs text-foreground">
                                {SURAT_TOKENS}
                            </code>
                        </div>
                        <div className="overflow-x-auto rounded-md border">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Surat Pinjaman</TableHead>
                                        <TableHead>Keterangan</TableHead>
                                        <TableHead className="w-24">Tampil</TableHead>
                                        <TableHead className="w-12" />
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {form.data.surat.length === 0 && (
                                        <TableRow><TableCell colSpan={4} className="h-20 text-center text-muted-foreground">Belum ada surat.</TableCell></TableRow>
                                    )}
                                    {form.data.surat.map((s, i) => (
                                        <TableRow key={i}>
                                            <TableCell className="w-72">
                                                <Select value={s.surat_id || undefined}
                                                    onValueChange={(v) => {
                                                        const opt = suratOptions.find((o) => String(o.id) === v);
                                                        setRow('surat', i, { surat_id: v, surat: opt?.nama ?? '' });
                                                    }}>
                                                    <SelectTrigger><SelectValue placeholder="-- Pilih Surat --" /></SelectTrigger>
                                                    <SelectContent>
                                                        {suratOptions.map((o) => (
                                                            <SelectItem key={o.id} value={String(o.id)}>{o.nama}</SelectItem>
                                                        ))}
                                                    </SelectContent>
                                                </Select>
                                            </TableCell>
                                            <TableCell>
                                                <Input value={s.keterangan}
                                                    onChange={(e) => setRow('surat', i, { keterangan: e.target.value })} />
                                            </TableCell>
                                            <TableCell>
                                                <Button type="button" variant="outline" size="sm">
                                                    <Printer /> Tampil
                                                </Button>
                                            </TableCell>
                                            <TableCell>
                                                <Button type="button" variant="ghost" size="icon" onClick={() => removeRow('surat', i)}>
                                                    <Trash2 className="text-brand-600" />
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* ===== TAB 6: PENJAMIN ===== */}
            {activeTab === 'penjamin' && (
                <Card>
                    <CardHeader className="flex-row items-center justify-between space-y-0">
                        <CardTitle>Penjamin</CardTitle>
                        <Button type="button" variant="outline" onClick={() => addRow('penjamin', { nama: '', alamat: '', no_ktp: '', hubungan: '', ibu: '', telepon: '' })}>
                            <Plus /> Tambah Penjamin
                        </Button>
                    </CardHeader>
                    <CardContent>
                        <div className="flex gap-2">
                            <Button type="button" variant="outline" onClick={() => setPenjaminLookup(0)}>
                                <Search /> Pilih dari Anggota
                            </Button>
                        </div>
                        {form.data.penjamin.length > 0 && (
                            <div className="mt-4 space-y-3">
                                {form.data.penjamin.map((p, i) => (
                                    <div key={i} className="grid gap-3 rounded-lg border p-3 sm:grid-cols-3">
                                        <div className="flex gap-2">
                                            <Input value={p.nama} placeholder="Nama penjamin"
                                                onChange={(e) => setRow('penjamin', i, { nama: e.target.value })} />
                                            <Button type="button" variant="outline" size="icon" onClick={() => setPenjaminLookup(i)}><Search /></Button>
                                        </div>
                                        <Input value={p.hubungan} placeholder="Hubungan"
                                            onChange={(e) => setRow('penjamin', i, { hubungan: e.target.value })} />
                                        <Input value={p.no_ktp} placeholder="No. Identitas"
                                            onChange={(e) => setRow('penjamin', i, { no_ktp: e.target.value })} />
                                        <Input value={p.alamat} placeholder="Alamat" className="sm:col-span-3"
                                            onChange={(e) => setRow('penjamin', i, { alamat: e.target.value })} />
                                        <Input value={p.telepon} placeholder="No. Telepon"
                                            onChange={(e) => setRow('penjamin', i, { telepon: e.target.value })} />
                                        <div className="flex items-center gap-2 sm:col-span-2">
                                            <Button type="button" variant="ghost" size="icon" onClick={() => removeRow('penjamin', i)}>
                                                <Trash2 className="text-brand-600" />
                                            </Button>
                                            <span className="text-xs text-muted-foreground">Penjamin #{i + 1}</span>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>
            )}

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
                    {pinjamanId && (
                        <ConfirmDelete
                            routeName="superadmin.pinjaman.pinjaman.destroy"
                            id={pinjamanId}
                            label={form.data.no_pinjaman}
                        />
                    )}
                    <Button type="button" variant="outline" asChild>
                        <Link href={route('superadmin.pinjaman.pinjaman')}>
                            <X /> Kembali
                        </Link>
                    </Button>
                </div>
            </div>

            {/* ===== Lookup modals ===== */}
            <LookupModal<PinjamanAnggotaRow>
                open={anggotaOpen}
                onOpenChange={setAnggotaOpen}
                title="Pilih Anggota"
                columns={[
                    { key: 'no_anggota', header: 'No. Anggota', render: (a) => <span className="font-mono text-xs">{a.no_anggota}</span> },
                    { key: 'nama', header: 'Nama' },
                    { key: 'alamat', header: 'Alamat' },
                    { key: 'status', header: 'Status' },
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

            <LookupModal
                open={swpOpen}
                onOpenChange={setSwpOpen}
                title="Pilih SWP"
                columns={[{ key: 'nama', header: 'Nama' }]}
                rows={[
                    { id: 1, nama: `Simpanan Wajib Pinjaman — ${selectedProduk?.bunga ?? '0'}` },
                ]}
                onSelect={(r) => form.setData('swp', String((selectedProduk as any)?.nominal_simpanan ?? ''))}
            />

            <LookupModal
                open={sppOpen}
                onOpenChange={setSppOpen}
                title="Pilih SPP"
                columns={[{ key: 'nama', header: 'Nama' }]}
                rows={[
                    { id: 1, nama: `Simpanan Pokok Pinjaman` },
                ]}
                onSelect={() => form.setData('spp', String((selectedProduk as any)?.nominal_simpanan_pokok ?? ''))}
            />

            <LookupModal<{ id: number; no_rekening: string; anggota?: { nama: string } | null }>
                open={tabunganOpen}
                onOpenChange={setTabunganOpen}
                title="Pilih No. Simpanan"
                columns={[
                    { key: 'no_rekening', header: 'No. Rekening', render: (r) => <span className="font-mono text-xs">{r.no_rekening}</span> },
                    { key: 'anggota', header: 'Anggota', render: (r) => r.anggota?.nama ?? '' },
                ]}
                rows={simpananOptions}
                onSelect={(r) => form.setData('tabungan_id', String(r.id))}
                getSearchText={(r) => `${r.no_rekening} ${r.anggota?.nama ?? ''}`}
                searchPlaceholder="Cari no. rekening…"
            />

            <LookupModal
                open={kodeOpen}
                onOpenChange={setKodeOpen}
                title="Pilih Kode Tarikan"
                columns={[
                    { key: 'kode', header: 'Kode', render: (r: any) => <span className="font-mono text-xs">{r.kode}</span> },
                    { key: 'nama', header: 'Nama' },
                ]}
                rows={kodeTarikanOptions}
                onSelect={(r) => form.setData('kode_id', String(r.id))}
                getSearchText={(r) => `${r.kode} ${r.nama}`}
                searchPlaceholder="Cari kode…"
            />

            <LookupModal<PinjamanAnggotaRow>
                open={saksiLookup !== null}
                onOpenChange={(o) => {
                    if (!o) setSaksiLookup(null);
                }}
                title="Pilih Saksi dari Anggota"
                columns={[
                    { key: 'no_anggota', header: 'No. Anggota', render: (a) => <span className="font-mono text-xs">{a.no_anggota}</span> },
                    { key: 'nama', header: 'Nama' },
                ]}
                rows={anggotaOptions}
                onSelect={(a) => {
                    if (saksiLookup !== null) {
                        if (saksiLookup >= form.data.saksi.length) {
                            addRow('saksi', { nama: a.nama, tempat_lahir: '', tgl_lahir: '', no_ktp: a.no_identitas ?? '', alamat: a.alamat ?? '', pekerjaan_id: '' });
                        } else {
                            setRow('saksi', saksiLookup, { nama: a.nama, no_ktp: a.no_identitas ?? '', alamat: a.alamat ?? '' });
                        }
                        setSaksiLookup(null);
                    }
                }}
                getSearchText={(a) => `${a.no_anggota} ${a.nama}`}
                searchPlaceholder="Cari anggota…"
            />

            <LookupModal<PinjamanAnggotaRow>
                open={penjaminLookup !== null}
                onOpenChange={(o) => {
                    if (!o) setPenjaminLookup(null);
                }}
                title="Pilih Penjamin dari Anggota"
                columns={[
                    { key: 'no_anggota', header: 'No. Anggota', render: (a) => <span className="font-mono text-xs">{a.no_anggota}</span> },
                    { key: 'nama', header: 'Nama' },
                ]}
                rows={anggotaOptions}
                onSelect={(a) => {
                    if (penjaminLookup !== null) {
                        if (penjaminLookup >= form.data.penjamin.length) {
                            addRow('penjamin', { nama: a.nama, alamat: a.alamat ?? '', no_ktp: a.no_identitas ?? '', hubungan: '', ibu: '', telepon: a.telepon ?? '' });
                        } else {
                            setRow('penjamin', penjaminLookup, { nama: a.nama, alamat: a.alamat ?? '', no_ktp: a.no_identitas ?? '', telepon: a.telepon ?? '' });
                        }
                        setPenjaminLookup(null);
                    }
                }}
                getSearchText={(a) => `${a.no_anggota} ${a.nama}`}
                searchPlaceholder="Cari anggota…"
            />
        </form>
    );
}

function Err({ children }: { children: React.ReactNode }) {
    return <p className="text-sm text-brand-600">{children}</p>;
}

function hitungJatuhTempo(d: Pick<PinjamanTransaksiValues, 'tanggal' | 'jangka_waktu' | 'satuan'>): string {
    if (!d.tanggal || !d.jangka_waktu || !d.satuan) return '';
    const date = new Date(d.tanggal);
    const jangka = parseInt(d.jangka_waktu, 10) || 0;
    if (d.satuan === 'hari') date.setDate(date.getDate() + jangka);
    else if (d.satuan === 'minggu') date.setDate(date.getDate() + jangka * 7);
    else if (d.satuan === 'tahun') date.setFullYear(date.getFullYear() + jangka);
    else date.setMonth(date.getMonth() + jangka);
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const dd = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${dd}`;
}
