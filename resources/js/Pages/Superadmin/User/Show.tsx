import { Head } from '@inertiajs/react';
import { Eye, ShieldCheck } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Badge } from '@/Components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import type { UserRow } from '@/types/models';

interface Props {
    userData: UserRow;
}

function InfoRow({ label, value }: { label: string; value: React.ReactNode }) {
    return (
        <div className="flex flex-col gap-1 py-2.5 sm:flex-row sm:items-center">
            <span className="w-40 shrink-0 text-sm font-medium text-muted-foreground">{label}</span>
            <span className="text-sm">{value ?? '—'}</span>
        </div>
    );
}

export default function UserShow({ userData: user }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${user.nama}`} />

            <PageHeader
                title="Detail User"
                description="Informasi lengkap akun pengguna."
                icon={Eye}
                backHref={route('superadmin.user')}
            />

            <Card className="max-w-3xl">
                <CardHeader>
                    <div className="flex items-center gap-4">
                        <Avatar className="size-16 border">
                            {user.avatar && (
                                <AvatarImage src={`/storage/${user.avatar}`} alt={user.nama} />
                            )}
                            <AvatarFallback className="bg-gradient-to-br from-brand-500 to-brand-700 text-xl font-bold text-white">
                                {user.nama.charAt(0).toUpperCase()}
                            </AvatarFallback>
                        </Avatar>
                        <div>
                            <CardTitle className="text-lg">{user.nama}</CardTitle>
                            <Badge variant="default" className="mt-1.5 capitalize">
                                {user.role}
                            </Badge>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <Separator className="mb-2" />
                    <InfoRow label="Username" value={<span className="font-mono text-xs">{user.username}</span>} />
                    <InfoRow label="Email" value={user.email} />
                    <InfoRow label="Role" value={<span className="capitalize">{user.role}</span>} />
                    <InfoRow label="Terdaftar" value={new Date(user.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })} />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
