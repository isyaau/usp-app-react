import { useMemo, useState } from 'react';
import { Link, Head, useForm} from '@inertiajs/react';
import { CalendarClock, LoaderCircle, Plus, Trash2 } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
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
import type { RencanaFormValues, RekeningOption } from '@/types/models';

interface KantorOption {
    id: number;
    nama_kantor: string;
}

interface Props {
    kantorOptions: KantorOption[];
    rekeningOptions: RekeningOption[];
}

const SATUAN_OPTIONS = [
    { value: 'hari', label: 'Hari' },
    { value: 'bulan', label: 'Bulan' },
    { value: 'tahun', label: 'Tahun' },
];

export default function SimpananRencanaCreate({
    kantorOptions,
    rekeningOptions,
}: Props) {
    const form = useForm<RencanaFormValues>({
        tanggal_mulai: new Date().toISOString().slice(0, 10),
        tanggal_jatuhtempo: '',
        no_bukti: '',
        jangka_waktu: '',
        satuan: '',
        nominal: '',
        bunga: '',
        keterangan: '',
        kantor_id: '',
        simpanan_ids: [],
    });

    // Modal pemilih rekening (pola modalPilihSimpanan di blade lama).
    const [modalOpen, setModalOpen] = useState(false);
    const [checked, setChecked] = useState<number[]>([]);
    const [query, setQuery] = useState('');

    const terpilih = useMemo(
        () => rekeningOptions.filter((r) => form.data.simpanan_ids.includes(r.id)),
        [form.data.simpanan_ids, rekeningOptions],
    );

    const tersedia = useMemo(() => {
        const q = query.toLowerCase();
        return rekeningOptions.filter(
            (r) =>
                !form.data.simpanan_ids.includes(r.id) &&
                (q === '' ||
                    r.no_rekening.toLowerCase().includes(q) ||
                    r.jenis_nama?.toLowerCase().includes(q) ||
                    r.anggota_nama?.toLowerCase().includes(q)),
        );
    }, [rekeningOptions, form.data.simpanan_ids, query]);

    const tambahkanTerpilih = () => {
        form.setData('simpanan_ids', [
            ...new Set([...form.data.simpanan_ids, ...checked]),
        ]);
        setChecked([]);
        setModalOpen(false);
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(route('superadmin.simpanan.rencana.store'), {
            preserveScroll: true,
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Tambah Simpanan Rencana" />

            <PageHeader
                title="Tambah Simpanan Rencana"
                description="Buat rencana simpanan dan pilih rekening yang terlibat."
                icon={CalendarClock}
                backHref={route('superadmin.simpanan.rencana')}
            />

            <form onSubmit={submit} className="max-w-5xl">
                <div className="grid gap-5 lg:grid-cols-2">
                    {/* Kolom kiri: data rencana */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Data Rencana</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-5">
                            <div className="grid gap-5 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="tanggal_mulai">
                                        Tanggal Mulai <span className="text-brand-600">*</span>
                                    </Label>
                                    <Input
                                        id="tanggal_mulai"
                                        type="date"
                                        value={form.data.tanggal_mulai}
                                        onChange={(e) =>
                                            form.setData('tanggal_mulai', e.target.value)
                                        }
                                    />
                                    {form.errors.tanggal_mulai && (
                                        <p className="text-sm text-brand-600">
                                            {form.errors.tanggal_mulai}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="tanggal_jatuhtempo">
                                        Tanggal Jatuh Tempo{' '}
                                        <span className="text-brand-600">*</span>
                                    </Label>
                                    <Input
                                        id="tanggal_jatuhtempo"
                                        type="date"
                                        value={form.data.tanggal_jatuhtempo}
                                        onChange={(e) =>
                                            form.setData('tanggal_jatuhtempo', e.target.value)
                                        }
                                    />
                                    {form.errors.tanggal_jatuhtempo && (
                                        <p className="text-sm text-brand-600">
                                            {form.errors.tanggal_jatuhtempo}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="grid gap-5 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="no_bukti">
                                        No Bukti <span className="text-brand-600">*</span>
                                    </Label>
                                    <Input
                                        id="no_bukti"
                                        value={form.data.no_bukti}
                                        onChange={(e) => form.setData('no_bukti', e.target.value)}
                                        className="font-mono"
                                        placeholder="RNC-0001"
                                    />
                                    {form.errors.no_bukti && (
                                        <p className="text-sm text-brand-600">
                                            {form.errors.no_bukti}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="kantor_id">
                                        Kantor <span className="text-brand-600">*</span>
                                    </Label>
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
                                    {form.errors.kantor_id && (
                                        <p className="text-sm text-brand-600">
                                            {form.errors.kantor_id}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="grid gap-5 sm:grid-cols-3">
                                <div className="space-y-2">
                                    <Label htmlFor="jangka_waktu">
                                        Jangka Waktu <span className="text-brand-600">*</span>
                                    </Label>
                                    <Input
                                        id="jangka_waktu"
                                        value={form.data.jangka_waktu}
                                        onChange={(e) =>
                                            form.setData('jangka_waktu', e.target.value)
                                        }
                                        inputMode="numeric"
                                    />
                                    {form.errors.jangka_waktu && (
                                        <p className="text-sm text-brand-600">
                                            {form.errors.jangka_waktu}
                                        </p>
                                    )}
                                </div>

                                <div className="space-y-2">
                                    <Label>
                                        Satuan <span className="text-brand-600">*</span>
                                    </Label>
                                    <Select
                                        value={form.data.satuan || undefined}
                                        onValueChange={(v) => form.setData('satuan', v)}
                                    >
                                        <SelectTrigger
                                            className="w-full"
                                            aria-label="Pilih Satuan"
                                        >
                                            <SelectValue placeholder="-- Pilih --" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {SATUAN_OPTIONS.map((s) => (
                                                <SelectItem key={s.value} value={s.value}>
                                                    {s.label}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                    {form.errors.satuan && (
                                        <p className="text-sm text-brand-600">
                                            {form.errors.satuan}
                                        </p>
                                    )}
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
                                        placeholder="500000"
                                    />
                                    {form.errors.nominal && (
                                        <p className="text-sm text-brand-600">
                                            {form.errors.nominal}
                                        </p>
                                    )}
                                </div>
                            </div>

                            <div className="max-w-xs space-y-2">
                                <Label htmlFor="bunga">Bagi Hasil (%)</Label>
                                <Input
                                    id="bunga"
                                    value={form.data.bunga}
                                    onChange={(e) => form.setData('bunga', e.target.value)}
                                    inputMode="decimal"
                                />
                                {form.errors.bunga && (
                                    <p className="text-sm text-brand-600">{form.errors.bunga}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="keterangan">Keterangan</Label>
                                <Input
                                    id="keterangan"
                                    value={form.data.keterangan}
                                    onChange={(e) => form.setData('keterangan', e.target.value)}
                                />
                            </div>
                        </CardContent>
                    </Card>

                    {/* Kolom kanan: rekening terlibat */}
                    <Card>
                        <CardHeader>
                            <CardTitle>Rekening Terlibat</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-4">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => setModalOpen(true)}
                            >
                                <Plus />
                                Pilih Rekening
                            </Button>

                            {form.errors.simpanan_ids && (
                                <p className="text-sm text-brand-600">
                                    {form.errors.simpanan_ids}
                                </p>
                            )}

                            {terpilih.length === 0 ? (
                                <p className="rounded-lg border border-dashed px-4 py-8 text-center text-sm text-muted-foreground">
                                    Belum ada rekening dipilih.
                                </p>
                            ) : (
                                <ul className="divide-y rounded-lg border">
                                    {terpilih.map((r) => (
                                        <li key={r.id} className="flex items-center justify-between gap-3 px-3 py-2">
                                            <div>
                                                <span className="rounded bg-muted px-1.5 py-0.5 font-mono text-xs">
                                                    {r.no_rekening}
                                                </span>
                                                <span className="ml-2 text-sm">
                                                    {r.anggota_nama ?? '—'}
                                                </span>
                                                <span className="block text-xs text-muted-foreground">
                                                    {r.jenis_nama ?? ''}
                                                </span>
                                            </div>
                                            <Button
                                                type="button"
                                                variant="ghost"
                                                size="icon"
                                                className="size-8"
                                                onClick={() =>
                                                    form.setData(
                                                        'simpanan_ids',
                                                        form.data.simpanan_ids.filter(
                                                            (x) => x !== r.id,
                                                        ),
                                                    )
                                                }
                                                aria-label={`Hapus ${r.no_rekening}`}
                                            >
                                                <Trash2 className="size-4" />
                                            </Button>
                                        </li>
                                    ))}
                                </ul>
                            )}
                        </CardContent>
                    </Card>
                </div>

                <div className="mt-5 flex items-center justify-end gap-3">
                    <Button variant="outline" asChild>
                        <Link href={route('superadmin.simpanan.rencana')}>Kembali</Link>
                    </Button>
                    <Button
                        type="submit"
                        disabled={form.processing}
                        className="bg-brand-600 hover:bg-brand-500"
                    >
                        {form.processing && <LoaderCircle className="animate-spin" />}
                        Simpan Rencana
                    </Button>
                </div>
            </form>

            {/* ===== Modal Pilih Rekening ===== */}
            <Dialog open={modalOpen} onOpenChange={setModalOpen}>
                <DialogContent className="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle>Pilih Rekening Simpanan</DialogTitle>
                    </DialogHeader>
                    <Input
                        value={query}
                        onChange={(e) => setQuery(e.target.value)}
                        placeholder="Cari no. rekening / anggota / produk…"
                    />
                    <div className="max-h-80 space-y-1 overflow-y-auto">
                        {tersedia.length === 0 && (
                            <p className="py-6 text-center text-sm text-muted-foreground">
                                Tidak ada rekening yang cocok.
                            </p>
                        )}
                        {tersedia.map((r) => (
                            <label
                                key={r.id}
                                className="flex cursor-pointer items-center gap-3 rounded-md px-2 py-1.5 text-sm transition hover:bg-muted"
                            >
                                <input
                                    type="checkbox"
                                    checked={checked.includes(r.id)}
                                    onChange={(e) =>
                                        setChecked((c) =>
                                            e.target.checked
                                                ? [...c, r.id]
                                                : c.filter((x) => x !== r.id),
                                        )
                                    }
                                    className="size-4 accent-[var(--color-brand-600)]"
                                />
                                <span className="font-mono text-xs text-muted-foreground">
                                    {r.no_rekening}
                                </span>
                                {r.anggota_nama ?? '—'}
                                <span className="ml-auto text-xs text-muted-foreground">
                                    {r.jenis_nama ?? ''}
                                </span>
                            </label>
                        ))}
                    </div>
                    <DialogFooter className="gap-2 sm:justify-between">
                        <span className="text-sm text-muted-foreground">
                            {checked.length} dipilih
                        </span>
                        <div className="flex gap-2">
                            <Button
                                type="button"
                                variant="outline"
                                onClick={() => setModalOpen(false)}
                            >
                                Batal
                            </Button>
                            <Button
                                type="button"
                                onClick={tambahkanTerpilih}
                                disabled={checked.length === 0}
                                className="bg-brand-600 hover:bg-brand-500"
                            >
                                Tambahkan ({checked.length})
                            </Button>
                        </div>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </AuthenticatedLayout>
    );
}
