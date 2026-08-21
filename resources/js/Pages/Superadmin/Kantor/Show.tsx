import { Head } from '@inertiajs/react';
import { Building2, Eye } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import type { KantorRow } from '@/types/models';

interface Props {
    kantorData: KantorRow;
}

function InfoRow({ label, value }: { label: string; value: React.ReactNode }) {
    return (
        <div className="flex flex-col gap-1 py-2.5 sm:flex-row sm:items-center">
            <span className="w-44 shrink-0 text-sm font-medium text-muted-foreground">{label}</span>
            <span className="text-sm">{value ?? '—'}</span>
        </div>
    );
}

export default function KantorShow({ kantorData: kantor }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${kantor.nama_kantor}`} />

            <PageHeader
                title="Detail Kantor"
                description="Informasi lengkap kantor."
                icon={Eye}
                backHref={route('superadmin.kantor')}
            />

            <Card className="max-w-3xl">
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Building2 className="size-4 text-brand-600" />
                        {kantor.nama_kantor}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <Separator className="mb-2" />
                    <InfoRow
                        label="Kode"
                        value={
                            <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                {kantor.kode}
                            </span>
                        }
                    />
                    <InfoRow label="Alamat" value={kantor.alamat_kantor} />
                    <InfoRow label="Provinsi" value={kantor.provinsi?.name ?? '—'} />
                    <InfoRow label="Kota/Kabupaten" value={kantor.kota?.name ?? '—'} />
                    <InfoRow label="Kecamatan" value={kantor.kecamatan?.name ?? '—'} />
                    <InfoRow label="Kelurahan/Desa" value={kantor.kelurahan?.name ?? '—'} />
                    <InfoRow
                        label="Pejabat"
                        value={`${kantor.pejabat} — ${kantor.jabatan}`}
                    />
                    <InfoRow label="Bendahara" value={kantor.bendahara} />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
