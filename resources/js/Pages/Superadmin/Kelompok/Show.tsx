import { Head } from '@inertiajs/react';
import { Eye, Users } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import type { KelompokRow } from '@/types/models';

interface Props {
    kelompokData: KelompokRow;
}

function InfoRow({ label, value }: { label: string; value: React.ReactNode }) {
    return (
        <div className="flex flex-col gap-1 py-2.5 sm:flex-row sm:items-center">
            <span className="w-40 shrink-0 text-sm font-medium text-muted-foreground">{label}</span>
            <span className="text-sm">{value ?? '—'}</span>
        </div>
    );
}

export default function KelompokShow({ kelompokData: kelompok }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${kelompok.nama}`} />

            <PageHeader
                title="Detail Kelompok"
                description="Informasi lengkap kelompok."
                icon={Eye}
                backHref={route('superadmin.kelompok')}
            />

            <Card className="max-w-2xl">
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Users className="size-4 text-brand-600" />
                        {kelompok.nama}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <Separator className="mb-2" />
                    <InfoRow
                        label="Kode"
                        value={
                            <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                {kelompok.kode}
                            </span>
                        }
                    />
                    <InfoRow label="Nama" value={kelompok.nama} />
                    <InfoRow label="Ketua" value={kelompok.ketua?.nama ?? '—'} />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
