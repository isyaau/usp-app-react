import { useForm, Head } from '@inertiajs/react';
import { LoaderCircle, ShieldCheck } from 'lucide-react';

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

interface FormValues {
    nama: string;
    username: string;
    email: string;
    role: string;
    password: string;
    password_confirmation: string;
    avatar: File | null;
}

export default function UserCreate() {
    const form = useForm<FormValues>({
        nama: '',
        username: '',
        email: '',
        role: '',
        password: '',
        password_confirmation: '',
        avatar: null,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(route('superadmin.user.store'), {
            forceFormData: true,
        });
    };

    const err = (field: keyof FormValues) =>
        (form.errors as Partial<Record<string, string>>)[field];

    return (
        <AuthenticatedLayout>
            <Head title="Tambah User" />

            <PageHeader
                title="Tambah User"
                description="Buat akun pengguna baru untuk sistem."
                icon={ShieldCheck}
                backHref={route('superadmin.user')}
            />

            <form onSubmit={submit} className="max-w-3xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Informasi Akun</CardTitle>
                    </CardHeader>
                    <CardContent className="grid gap-5 sm:grid-cols-2">
                        <div className="space-y-2">
                            <Label htmlFor="nama">
                                Nama <span className="text-brand-600">*</span>
                            </Label>
                            <Input
                                id="nama"
                                value={form.data.nama}
                                onChange={(e) => form.setData('nama', e.target.value)}
                                placeholder="Nama lengkap"
                            />
                            {err('nama') && <p className="text-sm text-brand-600">{err('nama')}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="username">
                                Username <span className="text-brand-600">*</span>
                            </Label>
                            <Input
                                id="username"
                                value={form.data.username}
                                onChange={(e) => form.setData('username', e.target.value)}
                                placeholder="username_unik"
                                className="font-mono"
                            />
                            {err('username') && (
                                <p className="text-sm text-brand-600">{err('username')}</p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="email">
                                Email <span className="text-brand-600">*</span>
                            </Label>
                            <Input
                                id="email"
                                type="email"
                                value={form.data.email}
                                onChange={(e) => form.setData('email', e.target.value)}
                                placeholder="nama@kopinka.co.id"
                            />
                            {err('email') && <p className="text-sm text-brand-600">{err('email')}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label>
                                Role <span className="text-brand-600">*</span>
                            </Label>
                            <Select
                                value={form.data.role}
                                onValueChange={(v) => form.setData('role', v)}
                            >
                                <SelectTrigger className="w-full" aria-label="Pilih Role">
                                    <SelectValue placeholder="-- Pilih Role --" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="superadmin">Superadmin</SelectItem>
                                    <SelectItem value="admin">Admin</SelectItem>
                                    <SelectItem value="user">User</SelectItem>
                                </SelectContent>
                            </Select>
                            {err('role') && <p className="text-sm text-brand-600">{err('role')}</p>}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="password">
                                Password <span className="text-brand-600">*</span>
                            </Label>
                            <Input
                                id="password"
                                type="password"
                                autoComplete="new-password"
                                value={form.data.password}
                                onChange={(e) => form.setData('password', e.target.value)}
                                placeholder="Minimal 8 karakter"
                            />
                            {err('password') && (
                                <p className="text-sm text-brand-600">{err('password')}</p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="password_confirmation">
                                Konfirmasi Password <span className="text-brand-600">*</span>
                            </Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                autoComplete="new-password"
                                value={form.data.password_confirmation}
                                onChange={(e) =>
                                    form.setData('password_confirmation', e.target.value)
                                }
                                placeholder="Ulangi password"
                            />
                            {err('password_confirmation') && (
                                <p className="text-sm text-brand-600">
                                    {err('password_confirmation')}
                                </p>
                            )}
                        </div>

                        <div className="space-y-2 sm:col-span-2">
                            <Label htmlFor="avatar">Avatar</Label>
                            <Input
                                id="avatar"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                onChange={(e) => form.setData('avatar', e.target.files?.[0] ?? null)}
                                className="file:mr-3 file:rounded-md file:border-0 file:bg-brand-600 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-brand-500"
                            />
                            <p className="text-xs text-muted-foreground">
                                JPG, PNG, atau WebP · maksimal 2MB.
                            </p>
                            {err('avatar') && <p className="text-sm text-brand-600">{err('avatar')}</p>}
                        </div>
                    </CardContent>
                </Card>

                <div className="mt-5 flex items-center justify-end gap-3">
                    <Button variant="outline" asChild>
                        <a href={route('superadmin.user')}>Kembali</a>
                    </Button>
                    <Button
                        type="submit"
                        disabled={form.processing}
                        className="bg-brand-600 hover:bg-brand-500"
                    >
                        {form.processing && <LoaderCircle className="animate-spin" />}
                        Simpan User
                    </Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
