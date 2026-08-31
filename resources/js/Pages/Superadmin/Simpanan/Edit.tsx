import { Link, Head, useForm} from '@inertiajs/react';
import { LoaderCircle, Wallet } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { SignaturePanel } from '@/Components/SignaturePanel';
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
import type {
    SimpananDetail,
    SimpananFormValues,
    SimpananJenisOption,
} from '@/types/models';

interface AnggotaOption {
    id: number;
    no_anggota: string;
    nama: string;
}

interface MarketingOption {
    id: number;
    nama: string;
}

interface KantorOption {
    id: number;
    nama_kantor: string;
}

interface Props {
    simpananData: SimpananDetail;
    existingSignatureUrl: string | null;
    jenisOptions: SimpananJenisOption[];
    marketingOptions: MarketingOption[];
    kantorOptions: KantorOption[];
    anggotaOptions: AnggotaOption[];
}

export default function SimpananEdit({
    simpananData: s,
    existingSignatureUrl,
    jenisOptions,
    marketingOptions,
    kantorOptions,
    anggotaOptions,
}: Props) {
    const form = useForm<SimpananFormValues & { signature_base64?: string }>({
        tanggal: s.tanggal ?? '',
        no_rekening: s.no_rekening,
        anggota_id: String(s.anggota_id),
        jenis_id: String(s.jenis_id),
        marketing_id: String(s.marketing_id),
        qq: s.qq ?? '',
        bunga: s.bunga ?? '',
        nominal_setor: s.nominal_setor ?? '',
        aktif: s.aktif === '1',
        sms: s.sms === '1',
        blokir_simpanan: s.blokir_simpanan === '1',
        blokir_nominal: s.blokir_nominal === '1',
        nominal_blokir: s.nominal_blokir ?? '',
        blokir_tgl: s.blokir_tgl === '1',
        tgl_blokir: s.tgl_blokir ?? '',
        kantor_id: s.kantor_id ? String(s.kantor_id) : '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.submit('put', route('superadmin.simpanan.update', s.id), {
            forceFormData: true,
            preserveScroll: true,
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Edit ${s.no_rekening}`} />

            <PageHeader
                title="Edit Simpanan"
                description={s.no_rekening}
                icon={Wallet}
                backHref={route('superadmin.simpanan')}
            />

            <form onSubmit={submit} className="max-w-5xl">
                <div className="grid gap-5 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Data Rekening</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-5">
                            <div className="grid gap-5 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="no_rekening">
                                        No. Rekening{' '}
                                        <span className="text-brand-600">*</span>
                                    </Label>
                                    <Input
                                        id="no_rekening"
                                        value={form.data.no_rekening}
                                        onChange={(e) =>
                                            form.setData('no_rekening', e.target.value)
                                        }
                                        className="font-mono"
                                    />
                                    {form.errors.no_rekening && (
                                        <p className="text-sm text-brand-600">
                                            {form.errors.no_rekening}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="tanggal">Tanggal</Label>
                                    <Input
                                        id="tanggal"
                                        type="date"
                                        value={form.data.tanggal}
                                        onChange={(e) =>
                                            form.setData('tanggal', e.target.value)
                                        }
                                    />
                                </div>
                            </div>

                            <div className="grid gap-5 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label>
                                        Produk Simpanan{' '}
                                        <span className="text-brand-600">*</span>
                                    </Label>
                                    <Select
                                        value={form.data.jenis_id || undefined}
                                        onValueChange={(v) => form.setData('jenis_id', v)}
                                    >
                                        <SelectTrigger
                                            className="w-full"
                                            aria-label="Pilih Produk Simpanan"
                                        >
                                            <SelectValue placeholder="-- Pilih Produk --" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {jenisOptions.map((j) => (
                                                <SelectItem key={j.id} value={String(j.id)}>
                                                    <span className="flex flex-col gap-0.5 py-0.5">
                                                        <span className="flex items-center gap-2">
                                                            <span className="font-mono font-semibold text-brand-700 dark:text-brand-300">
                                                                {j.kode}
                                                            </span>
                                                            <span>{j.nama}</span>
                                                        </span>
                                                        <span className="flex flex-wrap gap-x-3 text-[11px] text-muted-foreground">
                                                            <span>Akun: <span className="font-mono">{j.account_no ?? '—'}</span></span>
                                                            <span>Min: <span className="font-mono">Rp {Number(j.minimum ?? 0).toLocaleString('id-ID')}</span></span>
                                                            <span>Mengendap: <span className="font-mono">{j.mengendap ?? 0} bln</span></span>
                                                        </span>
                                                    </span>
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="bunga">Bagi Hasil / Tahun (%)</Label>
                                    <Input
                                        id="bunga"
                                        value={form.data.bunga}
                                        onChange={(e) => form.setData('bunga', e.target.value)}
                                        inputMode="decimal"
                                    />
                                </div>
                            </div>

                            <div className="grid gap-5 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="qq">QQ</Label>
                                    <Input
                                        id="qq"
                                        value={form.data.qq}
                                        onChange={(e) => form.setData('qq', e.target.value)}
                                    />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="nominal_setor">Nominal Setoran Awal</Label>
                                    <Input
                                        id="nominal_setor"
                                        value={form.data.nominal_setor}
                                        onChange={(e) =>
                                            form.setData('nominal_setor', e.target.value)
                                        }
                                        inputMode="numeric"
                                    />
                                </div>
                            </div>

                            <div className="grid gap-5 sm:grid-cols-3">
                                <div className="space-y-2 sm:col-span-2">
                                    <Label>
                                        Anggota <span className="text-brand-600">*</span>
                                    </Label>
                                    <Select
                                        value={form.data.anggota_id || undefined}
                                        onValueChange={(v) => form.setData('anggota_id', v)}
                                    >
                                        <SelectTrigger
                                            className="w-full"
                                            aria-label="Pilih Anggota"
                                        >
                                            <SelectValue placeholder="-- Pilih Anggota --" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {anggotaOptions.map((a) => (
                                                <SelectItem key={a.id} value={String(a.id)}>
                                                    <span className="font-mono text-xs">
                                                        {a.no_anggota}
                                                    </span>
                                                    {' · '}
                                                    {a.nama}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {/* Rekening terpilih mungkin milik anggota nonaktif */}
                                    {!anggotaOptions.some(
                                        (a) => String(a.id) === form.data.anggota_id,
                                    ) && (
                                        <p className="text-xs text-muted-foreground">
                                            ID anggota saat ini: {s.anggota?.no_anggota} ·{' '}
                                            {s.anggota?.nama}
                                        </p>
                                    )}
                                </div>

                                <label className="flex h-fit cursor-pointer items-center justify-between rounded-lg border bg-card px-4 py-2.5 transition hover:bg-muted/50">
                                    <span className="text-sm font-medium">Aktif</span>
                                    <Switch
                                        checked={form.data.aktif}
                                        onCheckedChange={(v) => form.setData('aktif', v)}
                                        aria-label="Rekening aktif"
                                    />
                                </label>
                            </div>

                            <div className="grid gap-5 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label>Marketing</Label>
                                    <Select
                                        value={form.data.marketing_id || undefined}
                                        onValueChange={(v) =>
                                            form.setData('marketing_id', v)
                                        }
                                    >
                                        <SelectTrigger
                                            className="w-full"
                                            aria-label="Pilih Marketing"
                                        >
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

                                <div className="space-y-2">
                                    <Label>Kantor</Label>
                                    <Select
                                        value={form.data.kantor_id || undefined}
                                        onValueChange={(v) => form.setData('kantor_id', v)}
                                    >
                                        <SelectTrigger
                                            className="w-full"
                                            aria-label="Pilih Kantor"
                                        >
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
                                </div>
                            </div>

                            <label className="flex w-fit cursor-pointer items-center justify-between rounded-lg border bg-card px-4 py-2.5 transition hover:bg-muted/50">
                                <span className="mr-4 text-sm font-medium">
                                    Notifikasi SMS
                                </span>
                                <Switch
                                    checked={form.data.sms}
                                    onCheckedChange={(v) => form.setData('sms', v)}
                                    aria-label="Notifikasi SMS"
                                />
                            </label>
                        </CardContent>
                    </Card>

                    <div className="space-y-5">
                        <Card>
                            <CardHeader>
                                <CardTitle>Tanda Tangan</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <SignaturePanel
                                    existingUrl={existingSignatureUrl}
                                    onChange={(dataUrl) =>
                                        form.setData('signature_base64', dataUrl ?? '')
                                    }
                                />
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Blokir</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="grid gap-4 sm:grid-cols-2">
                                    <label className="flex cursor-pointer items-center justify-between rounded-lg border bg-card px-4 py-2.5 transition hover:bg-muted/50">
                                        <span className="text-sm font-medium">
                                            Blokir Simpanan
                                        </span>
                                        <Switch
                                            checked={form.data.blokir_simpanan}
                                            onCheckedChange={(v) =>
                                                form.setData('blokir_simpanan', v)
                                            }
                                            aria-label="Blokir simpanan"
                                        />
                                    </label>

                                    <div className="space-y-2">
                                        <div className="flex items-center gap-2">
                                            <Switch
                                                checked={form.data.blokir_nominal}
                                                onCheckedChange={(v) =>
                                                    form.setData('blokir_nominal', v)
                                                }
                                                aria-label="Blokir nominal aktif"
                                            />
                                            <Label htmlFor="nominal_blokir">
                                                Blokir Nominal
                                            </Label>
                                        </div>
                                        <Input
                                            id="nominal_blokir"
                                            value={form.data.nominal_blokir}
                                            disabled={!form.data.blokir_nominal}
                                            onChange={(e) =>
                                                form.setData('nominal_blokir', e.target.value)
                                            }
                                            inputMode="numeric"
                                        />
                                    </div>
                                </div>

                                <div className="max-w-xs space-y-2">
                                    <div className="flex items-center gap-2">
                                        <Switch
                                            checked={form.data.blokir_tgl}
                                            onCheckedChange={(v) =>
                                                form.setData('blokir_tgl', v)
                                            }
                                            aria-label="Blokir tanggal aktif"
                                        />
                                        <Label htmlFor="tgl_blokir">Blokir s/d Tanggal</Label>
                                    </div>
                                    <Input
                                        id="tgl_blokir"
                                        type="date"
                                        value={form.data.tgl_blokir}
                                        disabled={!form.data.blokir_tgl}
                                        onChange={(e) =>
                                            form.setData('tgl_blokir', e.target.value)
                                        }
                                    />
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <div className="mt-5 flex items-center justify-end gap-3">
                    <Button variant="outline" asChild>
                        <Link href={route('superadmin.simpanan')}>Kembali</Link>
                    </Button>
                    <Button
                        type="submit"
                        disabled={form.processing}
                        className="bg-brand-600 hover:bg-brand-500"
                    >
                        {form.processing && <LoaderCircle className="animate-spin" />}
                        Perbarui Rekening
                    </Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
