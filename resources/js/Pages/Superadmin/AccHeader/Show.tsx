import { Head } from '@inertiajs/react';
import { Bookmark, Eye } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import type { AccHeaderRow } from '@/types/models';

interface Props {
    headerData: AccHeaderRow;
}

function InfoRow({ label, value }: { label: string; value: React.ReactNode }) {
    return (
        <div className="flex flex-col gap-1 py-2.5 sm:flex-row sm:items-center">
            <span className="w-40 shrink-0 text-sm font-medium text-muted-foreground">{label}</span>
            <span className="text-sm">{value ?? '—'}</span>
        </div>
    );
}

export default function AccHeaderShow({ headerData: header }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${header.nama}`} />

            <PageHeader
                title="Detail Account Header"
                description="Informasi lengkap header akun."
                icon={Eye}
                backHref={route('superadmin.account-header')}
            />

            <Card className="max-w-2xl">
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Bookmark className="size-4 text-brand-600" />
                        {header.nama}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <Separator className="mb-2" />
                    <InfoRow
                        label="No. Header"
                        value={
                            <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                {header.no_header}
                            </span>
                        }
                    />
                    <InfoRow label="Grup" value={header.group?.nama ?? '—'} />
                    <InfoRow
                        label="Jenis"
                        value={
                            <span className="rounded-full bg-brand-600/10 px-2.5 py-0.5 text-xs font-medium text-brand-700 dark:text-brand-300">
                                {header.jenis}
                            </span>
                        }
                    />
                    <InfoRow label="Keterangan" value={header.keterangan} />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
