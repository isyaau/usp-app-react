import { useMemo } from 'react';
import { useForm, Head } from '@inertiajs/react';
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
import type { AccGroupOption, AccHeaderRow } from '@/types/models';

function jenisOptions(groupName: string): Record<string, string[]> {
    const name = groupName.trim().toUpperCase();
    const items: Record<string, string[]> = {};

    if (['AKTIVA LANCAR', 'AKTIVA TETAP', 'AKTIVA LAIN'].includes(name)) {
        items['Aktiva'] = ['Kas', 'Bank', 'Tabungan & Simpanan Berjangka',
            'Surat-surat berharga', 'Piutang', 'Pinjaman yang diberikan',
            'BMPP kepada calon anggota, koperasi lain dan anggotanya',
            'Pendapatan yang masih harus diterima', 'Penyertaan pada non koperasi',
            'Aktiva Tetap'];
    }
    if (['HUTANG LANCAR', 'HUTANG JANGKA PANJANG'].includes(name)) {
        items['Kewajiban'] = ['Kewajiban Tertimbang'];
    }
    if (name === 'MODAL') {
        items['Modal'] = ['Modal Anggota', 'Modal Penyetaraan', 'Modal Penyertaan',
            'Cadangan Umum', 'Cadangan Tujuan Resiko', 'Modal Sumbangan',
            'SHU Yang belum dibagi'];
    }
    if (name === 'PENDAPATAN') {
        items['Pendapatan'] = ['Partisipasi Anggota'];
    }
    if (name === 'BIAYA') {
        items['Biaya'] = ['Biaya Operasional', 'Gaji dan Honorarium Karyawan'];
    }
    if (['AKTIVA LANCAR', 'AKTIVA TETAP', 'AKTIVA LAIN', 'HUTANG LANCAR',
        'HUTANG JANGKA PANJANG', 'MODAL'].includes(name)) {
        items['Cadangan'] = ['Cadangan Penghapusan Pinjaman',
            'Cadangan Penghapusan Pinjaman dari SHU'];
    }

    return items;
}

interface FormValues {
    group_id: string;
    no_header: string;
    nama: string;
    keterangan: string;
    jenis: string;
}

interface Props {
    headerData: AccHeaderRow;
    groups: AccGroupOption[];
}

export default function AccHeaderEdit({ headerData, groups }: Props) {
    const form = useForm<FormValues>({
        group_id: String(headerData.group_id),
        no_header: headerData.no_header,
        nama: headerData.nama,
        keterangan: headerData.keterangan,
        jenis: headerData.jenis,
    });

    const selectedGroupName = useMemo(
        () => groups.find((g) => String(g.id) === form.data.group_id)?.nama ?? '',
        [form.data.group_id, groups],
    );

    // Gabungkan opsi grup terpilih + pastikan jenis tersimpan tetap ada di daftar
    const radioItems = useMemo(() => {
        const items = selectedGroupName ? jenisOptions(selectedGroupName) : {};
        const all = new Set(Object.values(items).flat());
        if (form.data.jenis && !all.has(form.data.jenis)) {
            items['Lainnya'] = [form.data.jenis];
        }
        return items;
    }, [selectedGroupName, form.data.jenis]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.put(route('superadmin.account-header.update', headerData.id));
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Edit ${headerData.nama}`} />

            <PageHeader
                title="Edit Account Header"
                description={`Perbarui data ${headerData.nama}.`}
                icon={Pencil}
                backHref={route('superadmin.account-header')}
            />

            <form onSubmit={submit} className="max-w-2xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Data Header</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-5">
                        <div className="grid gap-5 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label>
                                    Grup <span className="text-brand-600">*</span>
                                </Label>
                                <Select
                                    value={form.data.group_id || undefined}
                                    onValueChange={(v) => {
                                        form.setData('group_id', v);
                                        form.setData('jenis', '');
                                    }}
                                >
                                    <SelectTrigger className="w-full" aria-label="Pilih Grup">
                                        <SelectValue placeholder="-- Pilih Grup --" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {groups.map((g) => (
                                            <SelectItem key={g.id} value={String(g.id)}>
                                                {g.nama}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {form.errors.group_id && (
                                    <p className="text-sm text-brand-600">{form.errors.group_id}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="no_header">
                                    Nomor Header <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="no_header"
                                    value={form.data.no_header}
                                    onChange={(e) => form.setData('no_header', e.target.value)}
                                    className="font-mono"
                                />
                                {form.errors.no_header && (
                                    <p className="text-sm text-brand-600">{form.errors.no_header}</p>
                                )}
                            </div>
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="nama">
                                Nama Header <span className="text-brand-600">*</span>
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

                        <div className="space-y-2">
                            <Label htmlFor="keterangan">
                                Keterangan <span className="text-brand-600">*</span>
                            </Label>
                            <textarea
                                id="keterangan"
                                rows={3}
                                value={form.data.keterangan}
                                onChange={(e) => form.setData('keterangan', e.target.value)}
                                className="border-input focus-visible:border-ring focus-visible:ring-ring/50 flex w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px]"
                            />
                            {form.errors.keterangan && (
                                <p className="text-sm text-brand-600">{form.errors.keterangan}</p>
                            )}
                        </div>

                        <div className="space-y-3">
                            <Label>
                                Jenis <span className="text-brand-600">*</span>
                            </Label>

                            {!selectedGroupName && (
                                <p className="rounded-lg bg-muted px-3 py-2 text-sm text-muted-foreground">
                                    Harap pilih grup terlebih dahulu.
                                </p>
                            )}

                            {Object.entries(radioItems).map(([groupLabel, options]) => (
                                <fieldset key={groupLabel} className="space-y-2">
                                    <legend className="mb-1">
                                        <span className="inline-flex rounded-md bg-brand-600/10 px-2 py-0.5 text-xs font-semibold text-brand-700 dark:text-brand-300">
                                            {groupLabel}
                                        </span>
                                    </legend>
                                    {options.map((opt) => (
                                        <label
                                            key={opt}
                                            className="flex cursor-pointer items-center gap-2.5 rounded-md px-2 py-1.5 text-sm transition hover:bg-muted"
                                        >
                                            <input
                                                type="radio"
                                                name="jenis"
                                                value={opt}
                                                checked={form.data.jenis === opt}
                                                onChange={() => form.setData('jenis', opt)}
                                                className="size-4 accent-[var(--color-brand-600)]"
                                            />
                                            {opt}
                                        </label>
                                    ))}
                                </fieldset>
                            ))}

                            {form.errors.jenis && (
                                <p className="text-sm text-brand-600">{form.errors.jenis}</p>
                            )}
                        </div>
                    </CardContent>
                </Card>

                <div className="mt-5 flex items-center justify-end gap-3">
                    <Button variant="outline" asChild>
                        <a href={route('superadmin.account-header')}>Kembali</a>
                    </Button>
                    <Button
                        type="submit"
                        disabled={form.processing}
                        className="bg-brand-600 hover:bg-brand-500"
                    >
                        {form.processing && <LoaderCircle className="animate-spin" />}
                        Perbarui Header
                    </Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
