import { Head } from '@inertiajs/react';
import { Eye, Megaphone } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Badge } from '@/Components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import type { MarketingRow } from '@/types/models';

interface Props {
    marketingData: MarketingRow;
}

function InfoRow({ label, value }: { label: string; value: React.ReactNode }) {
    return (
        <div className="flex flex-col gap-1 py-2.5 sm:flex-row sm:items-center">
            <span className="w-40 shrink-0 text-sm font-medium text-muted-foreground">{label}</span>
            <span className="text-sm">{value ?? '—'}</span>
        </div>
    );
}

export default function MarketingShow({ marketingData: m }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${m.nama}`} />

            <PageHeader
                title="Detail Marketing"
                description="Informasi lengkap marketing."
                icon={Eye}
                backHref={route('superadmin.marketing')}
            />

            <Card className="max-w-2xl">
                <CardHeader>
                    <div className="flex items-center justify-between">
                        <CardTitle className="flex items-center gap-2">
                            <Megaphone className="size-4 text-brand-600" />
                            {m.nama}
                        </CardTitle>
                        <Badge variant={m.aktif ? 'success' : 'secondary'}>
                            {m.aktif ? 'Aktif' : 'Nonaktif'}
                        </Badge>
                    </div>
                </CardHeader>
                <CardContent>
                    <Separator className="mb-2" />
                    <InfoRow
                        label="Kode"
                        value={
                            <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                {m.kode}
                            </span>
                        }
                    />
                    <InfoRow label="Alamat" value={m.alamat} />
                    <InfoRow
                        label="No. KTP"
                        value={<span className="font-mono text-xs">{m.no_ktp}</span>}
                    />
                    <InfoRow label="Telepon" value={m.telepon ?? '—'} />
                    <InfoRow label="No. HP" value={m.no_hp ?? '—'} />
                    <InfoRow label="Kantor" value={m.kantor?.nama_kantor ?? '—'} />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
