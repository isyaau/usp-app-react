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
import { Switch } from '@/Components/ui/switch';
import type { MarketingRow } from '@/types/models';

interface KantorOption {
    id: number;
    nama_kantor: string;
}

interface FormValues {
    kode: string;
    nama: string;
    alamat: string;
    no_ktp: string;
    telepon: string;
    no_hp: string;
    kantor_id: string;
    aktif: boolean;
}

interface Props {
    marketingData: MarketingRow;
    kantorOptions: KantorOption[];
}

export default function MarketingEdit({ marketingData, kantorOptions }: Props) {
    const form = useForm<FormValues>({
        kode: marketingData.kode,
        nama: marketingData.nama,
        alamat: marketingData.alamat,
        no_ktp: marketingData.no_ktp,
        telepon: marketingData.telepon ?? '',
        no_hp: marketingData.no_hp ?? '',
        kantor_id: String(marketingData.kantor_id),
        aktif: marketingData.aktif,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.put(route('superadmin.marketing.update', marketingData.id));
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Edit ${marketingData.nama}`} />

            <PageHeader
                title="Edit Marketing"
                description={`Perbarui data ${marketingData.nama}.`}
                icon={Pencil}
                backHref={route('superadmin.marketing')}
            />

            <form onSubmit={submit} className="max-w-3xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Data Marketing</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-5">
                        <div className="grid gap-5 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label htmlFor="kode">
                                    Kode <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="kode"
                                    value={form.data.kode}
                                    onChange={(e) => form.setData('kode', e.target.value)}
                                    className="font-mono"
                                />
                                {form.errors.kode && (
                                    <p className="text-sm text-brand-600">{form.errors.kode}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="nama">
                                    Nama <span className="text-brand-600">*</span>
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
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="alamat">
                                Alamat <span className="text-brand-600">*</span>
                            </Label>
                            <textarea
                                id="alamat"
                                rows={2}
                                value={form.data.alamat}
                                onChange={(e) => form.setData('alamat', e.target.value)}
                                className="border-input focus-visible:border-ring focus-visible:ring-ring/50 flex w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px]"
                            />
                            {form.errors.alamat && (
                                <p className="text-sm text-brand-600">{form.errors.alamat}</p>
                            )}
                        </div>

                        <div className="grid gap-5 sm:grid-cols-3">
                            <div className="space-y-2">
                                <Label htmlFor="no_ktp">
                                    No. KTP <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="no_ktp"
                                    value={form.data.no_ktp}
                                    onChange={(e) => form.setData('no_ktp', e.target.value)}
                                    className="font-mono"
                                    inputMode="numeric"
                                />
                                {form.errors.no_ktp && (
                                    <p className="text-sm text-brand-600">{form.errors.no_ktp}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="telepon">Telepon</Label>
                                <Input
                                    id="telepon"
                                    value={form.data.telepon}
                                    onChange={(e) => form.setData('telepon', e.target.value)}
                                />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="no_hp">No. HP</Label>
                                <Input
                                    id="no_hp"
                                    value={form.data.no_hp}
                                    onChange={(e) => form.setData('no_hp', e.target.value)}
                                />
                            </div>
                        </div>

                        <div className="grid items-end gap-5 sm:grid-cols-2">
                            <div className="space-y-2">
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

                            <label className="flex cursor-pointer items-center justify-between rounded-lg border bg-card px-4 py-2.5 transition hover:bg-muted/50">
                                <span className="text-sm font-medium">Status Aktif</span>
                                <Switch
                                    checked={form.data.aktif}
                                    onCheckedChange={(v) => form.setData('aktif', v)}
                                    aria-label="Status aktif marketing"
                                />
                            </label>
                        </div>
                    </CardContent>
                </Card>

                <div className="mt-5 flex items-center justify-end gap-3">
                    <Button variant="outline" asChild>
                        <a href={route('superadmin.marketing')}>Kembali</a>
                    </Button>
                    <Button
                        type="submit"
                        disabled={form.processing}
                        className="bg-brand-600 hover:bg-brand-500"
                    >
                        {form.processing && <LoaderCircle className="animate-spin" />}
                        Perbarui Marketing
                    </Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
