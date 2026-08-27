import { Head } from '@inertiajs/react';
import { FileText } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { ReportFilterBar } from '@/Components/ReportFilterBar';
import { Card } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';

interface Props {
    filters: Record<string, string>;
    variantTitle: string;
}

export default function PasalKartuSimpanan({ filters, variantTitle }: Props) {
    const handlePrint = () => {
        window.open(route('superadmin.laporan-cs.simpanan.pasal-kartu.cetak', filters), '_blank');
    };

    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Pasal-pasal Kartu Simpanan'} />

            <PageHeader
                title={variantTitle || 'Pasal-pasal Kartu Simpanan'}
                description="Cetakan pasal-pasal pada kartu simpanan."
                icon={FileText}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.simpanan.pasal-kartu"
                        filters={filters}
                    />
                </div>

                <div className="px-5">
                    <h3 className="text-lg font-semibold">Pasal-pasal Kartu Simpanan</h3>
                    <p className="mt-2 text-sm text-muted-foreground">
                        Halaman ini berisi pasal-pasal yang berlaku pada kartu simpanan anggota koperasi.
                    </p>
                    <Button variant="outline" onClick={handlePrint} className="mt-4">
                        <FileText className="mr-2 size-4" />
                        Cetak
                    </Button>
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
