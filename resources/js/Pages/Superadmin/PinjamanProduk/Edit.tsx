import { Head } from '@inertiajs/react';
import { Pencil } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import type { AccountMini } from '@/types/models';
import type { PinjamanProdukRow } from '@/types/pinjaman';

import { PinjamanProdukForm } from './PinjamanProdukForm';

interface Props {
    produkData: PinjamanProdukRow;
    accounts: AccountMini[];
    parameters: Array<{ id: number; nama: string }>;
}

export default function PinjamanProdukEdit({ produkData, accounts, parameters }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Edit ${produkData.nama}`} />

            <PageHeader
                title="Edit Produk Pinjaman"
                description={`Perbarui konfigurasi ${produkData.nama}.`}
                icon={Pencil}
                backHref={route('superadmin.pinjaman.produk')}
            />

            <div className="w-full max-w-7xl">
                <PinjamanProdukForm
                    initial={produkData}
                    accounts={accounts}
                    parameters={parameters}
                    submitUrl={route('superadmin.pinjaman.produk.update', produkData.id)}
                    submitMethod="put"
                    processingLabel="Perbarui Produk"
                />
            </div>
        </AuthenticatedLayout>
    );
}
