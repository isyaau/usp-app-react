import { Head } from '@inertiajs/react';
import { Eye, Package } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import type { JaminanRow } from '@/types/jaminan';

interface Props {
    jaminanData: JaminanRow;
}

export default function JaminanShow({ jaminanData: jaminan }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${jaminan.nama}`} />

            <PageHeader
                title="Detail Jaminan"
                description="Informasi lengkap kategori jaminan."
                icon={Eye}
                backHref={route('superadmin.pinjaman.jaminan')}
            />

            <Card className="max-w-2xl">
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Package className="size-4 text-brand-600" />
                        {jaminan.nama}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <Separator className="mb-3" />
                    <p className="mb-2 text-sm font-medium text-muted-foreground">Detail Jaminan:</p>
                    {(jaminan.details?.length ?? 0) === 0 ? (
                        <p className="text-sm text-muted-foreground">Tidak ada detail.</p>
                    ) : (
                        <ul className="space-y-1.5">
                            {jaminan.details!.map((d, i) => (
                                <li key={d.id ?? i} className="flex items-center gap-2 text-sm">
                                    <span className="flex size-5 shrink-0 items-center justify-center rounded-full bg-brand-600/10 text-[10px] font-semibold text-brand-700 dark:text-brand-300">
                                        {i + 1}
                                    </span>
                                    {d.detail}
                                </li>
                            ))}
                        </ul>
                    )}
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
