import { useState } from 'react';
import { useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';

import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import type { SimpananMini } from '@/types/models';

export interface AnggotaOption {
    id: number;
    no_anggota: string;
    nama: string;
}

export interface KantorOption {
    id: number;
    kode: string;
    nama_kantor: string;
}

export interface KodeTransaksiOption {
    id: number;
    kode: string;
    nama: string;
}

interface BaseProps {
    /** URL endpoint JSON rekening per anggota (dari controller). */
    simpananUrl?: string;
    anggotas: AnggotaOption[];
    kantors: KantorOption[];
    kodeTransaksis: KodeTransaksiOption[];
}

interface FormValues {
    tgl_transaksi: string;
    anggota_id: string;
    simpanan_id: string;
    simpanan_ke_id: string;
    kode_transaksi_id: string;
    nominal: string;
    keterangan: string;
    kantor_id: string;
    status: 'draft' | 'posted' | 'batal';
}

const EMPTY: FormValues = {
    tgl_transaksi: new Date().toISOString().slice(0, 10),
    anggota_id: '',
    simpanan_id: '',
    simpanan_ke_id: '',
    kode_transaksi_id: '',
    nominal: '',
    keterangan: '',
    kantor_id: '',
    status: 'draft',
};

/**
 * Field bersama untuk form transaksi simpanan:
 * dropdown bertingkat anggota → rekening (fetch JSON), kode transaksi,
 * nominal, kantor, status.
 */
export function useTransaksiForm(initial?: Partial<FormValues>) {
    return useForm<FormValues>({ ...EMPTY, ...initial });
}

export function TransaksiFormFields({
    form,
    simpananUrl = '',
    anggotas,
    kantors,
    kodeTransaksis,
    initialRekenings,
    pemindahbukuan = false,
    extra,
}: BaseProps & {
    form: ReturnType<typeof useTransaksiForm>;
    /** Daftar rekening awal (untuk halaman Edit agar nilai terpilih tampil). */
    initialRekenings?: SimpananMini[];
    /** Mode pemindahbukuan: rekening pertama = asal + tambahan rekening tujuan. */
    pemindahbukuan?: boolean;
    /** Field tambahan spesifik modul (mis. nominal bunga). */
    extra?: React.ReactNode;
}) {
    const [rekenings, setRekenings] = useState<SimpananMini[]>(
        initialRekenings ?? [],
    );
    const [loadingRekening, setLoadingRekening] = useState(false);

    const pilihAnggota = (anggotaId: string) => {
        form.setData('anggota_id', anggotaId);
        form.setData('simpanan_id', '');
        form.setData('simpanan_ke_id', '');
        setRekenings([]);

        if (!anggotaId || !simpananUrl) return;

        setLoadingRekening(true);
        fetch(`${simpananUrl}/${anggotaId}`)
            .then((res) => (res.ok ? res.json() : []))
            .then((data: SimpananMini[]) => setRekenings(data))
            .finally(() => setLoadingRekening(false));
    };

    const labelAsal = pemindahbukuan ? 'Rekening Asal' : 'Rekening Simpanan';
    const opsiTujuan = rekenings.filter(
        (r) => String(r.id) !== form.data.simpanan_id,
    );

    const selectClass =
        'border-input h-9 w-full rounded-md border bg-card px-3 text-sm shadow-xs outline-none focus-visible:border-brand-500 disabled:cursor-not-allowed disabled:opacity-50';

    return (
        <div className="space-y-5">
            {/* Tanggal & Status */}
            <div className="grid gap-5 sm:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="tgl_transaksi">
                        Tanggal Transaksi{' '}
                        <span className="text-brand-600">*</span>
                    </Label>
                    <Input
                        id="tgl_transaksi"
                        type="date"
                        value={form.data.tgl_transaksi}
                        onChange={(e) =>
                            form.setData('tgl_transaksi', e.target.value)
                        }
                    />
                    {form.errors.tgl_transaksi && (
                        <p className="text-destructive text-sm">
                            {form.errors.tgl_transaksi}
                        </p>
                    )}
                </div>
                <div className="space-y-2">
                    <Label>
                        Status <span className="text-brand-600">*</span>
                    </Label>
                    <Select
                        value={form.data.status}
                        onValueChange={(v) =>
                            form.setData('status', v as FormValues['status'])
                        }
                    >
                        <SelectTrigger className="w-full">
                            <SelectValue placeholder="Pilih status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="draft">Draft</SelectItem>
                            <SelectItem value="posted">Posted</SelectItem>
                            <SelectItem value="batal">Batal</SelectItem>
                        </SelectContent>
                    </Select>
                    {form.errors.status && (
                        <p className="text-destructive text-sm">
                            {form.errors.status}
                        </p>
                    )}
                </div>
            </div>

            {/* Anggota → Rekening */}
            <div className="grid gap-5 sm:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="anggota_id">
                        Anggota <span className="text-brand-600">*</span>
                    </Label>
                    <select
                        id="anggota_id"
                        value={form.data.anggota_id}
                        onChange={(e) => pilihAnggota(e.target.value)}
                        className={selectClass}
                    >
                        <option value="">— Pilih Anggota —</option>
                        {anggotas.map((a) => (
                            <option key={a.id} value={a.id}>
                                {a.no_anggota} · {a.nama}
                            </option>
                        ))}
                    </select>
                    {form.errors.anggota_id && (
                        <p className="text-destructive text-sm">
                            {form.errors.anggota_id}
                        </p>
                    )}
                </div>
                <div className="space-y-2">
                    <Label htmlFor="simpanan_id">
                        {labelAsal} <span className="text-brand-600">*</span>
                    </Label>
                    <select
                        id="simpanan_id"
                        value={form.data.simpanan_id}
                        disabled={!form.data.anggota_id || loadingRekening}
                        onChange={(e) =>
                            form.setData('simpanan_id', e.target.value)
                        }
                        className={selectClass}
                    >
                        <option value="">
                            {loadingRekening
                                ? 'Memuat rekening…'
                                : form.data.anggota_id
                                  ? '— Pilih Rekening —'
                                  : '— Pilih anggota dahulu —'}
                        </option>
                        {rekenings.map((r) => (
                            <option key={r.id} value={r.id}>
                                {r.no_rekening}
                                {r.jenis ? ` · ${r.jenis}` : ''}
                            </option>
                        ))}
                    </select>
                    {form.errors.simpanan_id && (
                        <p className="text-destructive text-sm">
                            {form.errors.simpanan_id}
                        </p>
                    )}
                </div>
            </div>

            {/* Rekening tujuan (khusus pemindahbukuan) */}
            {pemindahbukuan && (
                <div className="grid gap-5 sm:grid-cols-2">
                    <div className="space-y-2">
                        <Label htmlFor="simpanan_ke_id">
                            Rekening Tujuan{' '}
                            <span className="text-brand-600">*</span>
                        </Label>
                        <select
                            id="simpanan_ke_id"
                            value={form.data.simpanan_ke_id}
                            disabled={
                                !form.data.anggota_id || loadingRekening
                            }
                            onChange={(e) =>
                                form.setData('simpanan_ke_id', e.target.value)
                            }
                            className={selectClass}
                        >
                            <option value="">
                                {loadingRekening
                                    ? 'Memuat rekening…'
                                    : form.data.anggota_id
                                      ? '— Pilih Rekening Tujuan —'
                                      : '— Pilih anggota dahulu —'}
                            </option>
                            {opsiTujuan.map((r) => (
                                <option key={r.id} value={r.id}>
                                    {r.no_rekening}
                                    {r.jenis ? ` · ${r.jenis}` : ''}
                                </option>
                            ))}
                        </select>
                        {form.errors.simpanan_ke_id && (
                            <p className="text-destructive text-sm">
                                {form.errors.simpanan_ke_id}
                            </p>
                        )}
                    </div>
                </div>
            )}

            {extra}

            {/* Kode transaksi & nominal */}
            <div className="grid gap-5 sm:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="kode_transaksi_id">
                        Kode Transaksi <span className="text-brand-600">*</span>
                    </Label>
                    <select
                        id="kode_transaksi_id"
                        value={form.data.kode_transaksi_id}
                        onChange={(e) =>
                            form.setData('kode_transaksi_id', e.target.value)
                        }
                        className={selectClass}
                    >
                        <option value="">— Pilih Kode —</option>
                        {kodeTransaksis.map((k) => (
                            <option key={k.id} value={k.id}>
                                {k.kode} · {k.nama}
                            </option>
                        ))}
                    </select>
                    {form.errors.kode_transaksi_id && (
                        <p className="text-destructive text-sm">
                            {form.errors.kode_transaksi_id}
                        </p>
                    )}
                </div>
                <div className="space-y-2">
                    <Label htmlFor="nominal">
                        Nominal <span className="text-brand-600">*</span>
                    </Label>
                    <Input
                        id="nominal"
                        type="number"
                        min="0"
                        step="0.01"
                        value={form.data.nominal}
                        onChange={(e) => form.setData('nominal', e.target.value)}
                        placeholder="0"
                        className="font-mono"
                    />
                    {form.errors.nominal && (
                        <p className="text-destructive text-sm">
                            {form.errors.nominal}
                        </p>
                    )}
                </div>
            </div>

            {/* Kantor & keterangan */}
            <div className="grid gap-5 sm:grid-cols-2">
                <div className="space-y-2">
                    <Label htmlFor="kantor_id">
                        Kantor <span className="text-brand-600">*</span>
                    </Label>
                    <select
                        id="kantor_id"
                        value={form.data.kantor_id}
                        onChange={(e) =>
                            form.setData('kantor_id', e.target.value)
                        }
                        className={selectClass}
                    >
                        <option value="">— Pilih Kantor —</option>
                        {kantors.map((k) => (
                            <option key={k.id} value={k.id}>
                                {k.kode} · {k.nama_kantor}
                            </option>
                        ))}
                    </select>
                    {form.errors.kantor_id && (
                        <p className="text-destructive text-sm">
                            {form.errors.kantor_id}
                        </p>
                    )}
                </div>
                <div className="space-y-2">
                    <Label htmlFor="keterangan">Keterangan</Label>
                    <Input
                        id="keterangan"
                        value={form.data.keterangan}
                        onChange={(e) =>
                            form.setData('keterangan', e.target.value)
                        }
                        placeholder="Catatan tambahan (opsional)"
                    />
                </div>
            </div>

            <SubmitBar form={form} />
        </div>
    );
}

/** Tombol submit + indikator proses. */
export function SubmitBar({
    form,
}: {
    form: ReturnType<typeof useTransaksiForm>;
}) {
    return (
        <div className="flex items-center gap-3 pt-2">
            <Button
                id="btn-submit"
                type="submit"
                disabled={form.processing}
                className="bg-brand-600 hover:bg-brand-500"
            >
                {form.processing && <LoaderCircle className="animate-spin" />}
                Simpan
            </Button>
        </div>
    );
}

/** Kartu pembungkus form dengan header standar. */
export function FormCard({
    title,
    description,
    children,
}: {
    title: string;
    description: string;
    children: React.ReactNode;
}) {
    return (
        <Card className="max-w-3xl">
            <CardHeader>
                <CardTitle>{title}</CardTitle>
                <CardDescription>{description}</CardDescription>
            </CardHeader>
            <CardContent>{children}</CardContent>
        </Card>
    );
}
