import { Link, useForm, Head} from '@inertiajs/react';
import { BookOpen, LoaderCircle } from 'lucide-react';

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

interface HeaderOption {
    id: number;
    nama: string;
    no_header: string;
}

interface FormValues {
    no_account: string;
    nama: string;
    header_id: string;
    tipe: string;
}

interface Props {
    headers: HeaderOption[];
}

export default function AccountCreate({ headers }: Props) {
    const form = useForm<FormValues>({
        no_account: '',
        nama: '',
        header_id: '',
        tipe: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(route('superadmin.account.store'));
    };

    return (
        <AuthenticatedLayout>
            <Head title="Tambah Account" />

            <PageHeader
                title="Tambah Account"
                description="Buat akun COA tingkat detail."
                icon={BookOpen}
                backHref={route('superadmin.account')}
            />

            <form onSubmit={submit} className="max-w-2xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Data Account</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-5">
                        <div className="grid gap-5 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label htmlFor="no_account">
                                    Nomor Account <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="no_account"
                                    value={form.data.no_account}
                                    onChange={(e) => form.setData('no_account', e.target.value)}
                                    placeholder="1-0001"
                                    className="font-mono"
                                />
                                {form.errors.no_account && (
                                    <p className="text-sm text-brand-600">{form.errors.no_account}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="nama">
                                    Nama Account <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="nama"
                                    value={form.data.nama}
                                    onChange={(e) => form.setData('nama', e.target.value)}
                                    placeholder="Kas Koperasi"
                                />
                                {form.errors.nama && (
                                    <p className="text-sm text-brand-600">{form.errors.nama}</p>
                                )}
                            </div>
                        </div>

                        <div className="grid gap-5 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label>
                                    Header <span className="text-brand-600">*</span>
                                </Label>
                                <Select
                                    value={form.data.header_id || undefined}
                                    onValueChange={(v) => form.setData('header_id', v)}
                                >
                                    <SelectTrigger className="w-full" aria-label="Pilih Header">
                                        <SelectValue placeholder="-- Pilih Header --" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {headers.map((h) => (
                                            <SelectItem key={h.id} value={String(h.id)}>
                                                {h.no_header} — {h.nama}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {form.errors.header_id && (
                                    <p className="text-sm text-brand-600">{form.errors.header_id}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label>
                                    Tipe <span className="text-brand-600">*</span>
                                </Label>
                                <Select
                                    value={form.data.tipe || undefined}
                                    onValueChange={(v) => form.setData('tipe', v)}
                                >
                                    <SelectTrigger className="w-full" aria-label="Pilih Tipe">
                                        <SelectValue placeholder="-- Pilih Tipe --" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="Debet">Debet</SelectItem>
                                        <SelectItem value="Kredit">Kredit</SelectItem>
                                    </SelectContent>
                                </Select>
                                {form.errors.tipe && (
                                    <p className="text-sm text-brand-600">{form.errors.tipe}</p>
                                )}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div className="mt-5 flex items-center justify-end gap-3">
                    <Button variant="outline" asChild>
                        <Link href={route('superadmin.account')}>Kembali</Link>
                    </Button>
                    <Button
                        type="submit"
                        disabled={form.processing}
                        className="bg-brand-600 hover:bg-brand-500"
                    >
                        {form.processing && <LoaderCircle className="animate-spin" />}
                        Simpan Account
                    </Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
