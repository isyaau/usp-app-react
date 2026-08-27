import { Head } from '@inertiajs/react';
import { HandCoins } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import type { AccountMini } from '@/types/models';

import { PinjamanProdukForm } from './PinjamanProdukForm';

interface Props {
    accounts: AccountMini[];
    parameters: Array<{ id: number; nama: string }>;
}

export default function PinjamanProdukCreate({ accounts, parameters }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title="Tambah Produk Pinjaman" />

            <PageHeader
                title="Tambah Produk Pinjaman"
                description="Konfigurasikan produk pinjaman beserta komponen biayanya."
                icon={HandCoins}
                backHref={route('superadmin.pinjaman.produk')}
            />

            <div className="w-full max-w-7xl">
                <PinjamanProdukForm
                    accounts={accounts}
                    parameters={parameters}
                    submitUrl={route('superadmin.pinjaman.produk.store')}
                    processingLabel="Simpan Produk"
                />
            </div>
        </AuthenticatedLayout>
    );
}
