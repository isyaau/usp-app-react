import { useForm, Head } from '@inertiajs/react';
import { LoaderCircle, Pencil, ShieldCheck } from 'lucide-react';

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
import type { UserRow } from '@/types/models';

interface Props {
    userData: UserRow;
}

interface FormValues {
    nama: string;
    username: string;
    email: string;
    role: string;
    password: string;
    password_confirmation: string;
    avatar: File | null;
}

export default function UserEdit({ userData }: Props) {
    const form = useForm<FormValues>({
        nama: userData.nama,
        username: userData.username,
        email: userData.email,
        role: userData.role,
        password: '',
        password_confirmation: '',
        avatar: null,
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(route('superadmin.user.update', userData.id), {
            forceFormData: true,
            method: 'put',
        });
    };

    const err = (field: keyof FormValues) =>
        (form.errors as Partial<Record<string, string>>)[field];

    return (
        <AuthenticatedLayout>
            <Head title={`Edit ${userData.nama}`} />

            <PageHeader
                title="Edit User"
                description={`Perbarui data akun ${userData.nama}.`}
                icon={Pencil}
                backHref={route('superadmin.user')}
            />

            <form onSubmit={submit} className="max-w-3xl">
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <ShieldCheck className="size-4 text-brand-600" />
                            Informasi Akun
                        </CardTitle>
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
                            <Label htmlFor="password">Password Baru</Label>
                            <Input
                                id="password"
                                type="password"
                                autoComplete="new-password"
                                value={form.data.password}
                                onChange={(e) => form.setData('password', e.target.value)}
                                placeholder="Kosongkan jika tidak diubah"
                            />
                            {err('password') && (
                                <p className="text-sm text-brand-600">{err('password')}</p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label htmlFor="password_confirmation">Konfirmasi Password Baru</Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                autoComplete="new-password"
                                value={form.data.password_confirmation}
                                onChange={(e) =>
                                    form.setData('password_confirmation', e.target.value)
                                }
                                placeholder="Ulangi password baru"
                            />
                        </div>

                        <div className="space-y-2 sm:col-span-2">
                            <Label htmlFor="avatar">Ganti Avatar</Label>
                            <Input
                                id="avatar"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                onChange={(e) => form.setData('avatar', e.target.files?.[0] ?? null)}
                                className="file:mr-3 file:rounded-md file:border-0 file:bg-brand-600 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white hover:file:bg-brand-500"
                            />
                            <p className="text-xs text-muted-foreground">
                                Kosongkan untuk mempertahankan avatar saat ini.
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
                        Perbarui User
                    </Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
