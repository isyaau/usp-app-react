import { Link, useForm, Head} from '@inertiajs/react';
import { LoaderCircle, Pencil } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { WilayahSelect } from '@/Components/WilayahSelect';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import type { KantorRow } from '@/types/models';

interface Props {
    kantorData: KantorRow;
}

interface FormValues {
    kode: string;
    nama_kantor: string;
    alamat_kantor: string;
    provinsi_id: string;
    kota_id: string;
    kecamatan_id: string;
    kelurahan_id: string;
    pejabat: string;
    jabatan: string;
    bendahara: string;
}

export default function KantorEdit({ kantorData }: Props) {
    const form = useForm<FormValues>({
        kode: kantorData.kode,
        nama_kantor: kantorData.nama_kantor,
        alamat_kantor: kantorData.alamat_kantor,
        provinsi_id: kantorData.provinsi_id ?? '',
        kota_id: kantorData.kota_id ?? '',
        kecamatan_id: kantorData.kecamatan_id ?? '',
        kelurahan_id: kantorData.kelurahan_id ?? '',
        pejabat: kantorData.pejabat,
        jabatan: kantorData.jabatan,
        bendahara: kantorData.bendahara,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.put(route('superadmin.kantor.update', kantorData.id));
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Edit ${kantorData.nama_kantor}`} />

            <PageHeader
                title="Edit Kantor"
                description={`Perbarui data ${kantorData.nama_kantor}.`}
                icon={Pencil}
                backHref={route('superadmin.kantor')}
            />

            <form onSubmit={submit} className="max-w-4xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Data Kantor</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-5">
                        <div className="grid gap-5 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label htmlFor="kode">
                                    Kode Kantor <span className="text-brand-600">*</span>
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
                                <Label htmlFor="nama_kantor">
                                    Nama Kantor <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="nama_kantor"
                                    value={form.data.nama_kantor}
                                    onChange={(e) => form.setData('nama_kantor', e.target.value)}
                                />
                                {form.errors.nama_kantor && (
                                    <p className="text-sm text-brand-600">{form.errors.nama_kantor}</p>
                                )}
                            </div>
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="alamat_kantor">
                                Alamat Kantor <span className="text-brand-600">*</span>
                            </Label>
                            <textarea
                                id="alamat_kantor"
                                rows={3}
                                value={form.data.alamat_kantor}
                                onChange={(e) => form.setData('alamat_kantor', e.target.value)}
                                className="border-input focus-visible:border-ring focus-visible:ring-ring/50 flex w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px]"
                            />
                            {form.errors.alamat_kantor && (
                                <p className="text-sm text-brand-600">{form.errors.alamat_kantor}</p>
                            )}
                        </div>

                        <WilayahSelect
                            values={{
                                provinsi_id: form.data.provinsi_id,
                                kota_id: form.data.kota_id,
                                kecamatan_id: form.data.kecamatan_id,
                                kelurahan_id: form.data.kelurahan_id,
                            }}
                            onChange={(field, code) => form.setData(field, code)}
                            errors={form.errors as Partial<Record<string, string>>}
                        />

                        <SeparatorLine />

                        <div className="grid gap-5 sm:grid-cols-3">
                            <div className="space-y-2">
                                <Label htmlFor="pejabat">
                                    Nama Pejabat <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="pejabat"
                                    value={form.data.pejabat}
                                    onChange={(e) => form.setData('pejabat', e.target.value)}
                                />
                                {form.errors.pejabat && (
                                    <p className="text-sm text-brand-600">{form.errors.pejabat}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="jabatan">
                                    Jabatan <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="jabatan"
                                    value={form.data.jabatan}
                                    onChange={(e) => form.setData('jabatan', e.target.value)}
                                />
                                {form.errors.jabatan && (
                                    <p className="text-sm text-brand-600">{form.errors.jabatan}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="bendahara">
                                    Bendahara <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="bendahara"
                                    value={form.data.bendahara}
                                    onChange={(e) => form.setData('bendahara', e.target.value)}
                                />
                                {form.errors.bendahara && (
                                    <p className="text-sm text-brand-600">{form.errors.bendahara}</p>
                                )}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div className="mt-5 flex items-center justify-end gap-3">
                    <Button variant="outline" asChild>
                        <Link href={route('superadmin.kantor')}>Kembali</Link>
                    </Button>
                    <Button
                        type="submit"
                        disabled={form.processing}
                        className="bg-brand-600 hover:bg-brand-500"
                    >
                        {form.processing && <LoaderCircle className="animate-spin" />}
                        Perbarui Kantor
                    </Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}

function SeparatorLine() {
    return <div className="h-px bg-border" role="separator" />;
}
