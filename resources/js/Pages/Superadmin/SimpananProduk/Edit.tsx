import { Head } from '@inertiajs/react';
import { Pencil } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import type { AccountMini } from '@/types/models';
import type { SimpananKodeOption, SimpananProdukRow } from '@/types/simpanan';

import { SimpananProdukForm } from './SimpananProdukForm';

interface Props {
    produkData: SimpananProdukRow;
    accounts: AccountMini[];
    kodes: SimpananKodeOption[];
}

export default function SimpananProdukEdit({ produkData, accounts, kodes }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Edit ${produkData.nama}`} />

            <PageHeader
                title="Edit Produk Simpanan"
                description={`Perbarui konfigurasi ${produkData.nama}.`}
                icon={Pencil}
                backHref={route('superadmin.simpanan.produk-simpanan')}
            />

            <div className="max-w-5xl">
                <SimpananProdukForm
                    initial={produkData}
                    accounts={accounts}
                    kodes={kodes}
                    submitUrl={route('superadmin.simpanan.produk-simpanan.update', produkData.id)}
                    submitMethod="put"
                    processingLabel="Perbarui Produk"
                />
            </div>
        </AuthenticatedLayout>
    );
}
