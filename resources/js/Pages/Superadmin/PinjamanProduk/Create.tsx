import { Head } from '@inertiajs/react';
import { HandCoins } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import type { AccountMini } from '@/types/models';

import { PinjamanProdukForm } from './PinjamanProdukForm';

interface Props {
    accounts: AccountMini[];
}

export default function PinjamanProdukCreate({ accounts }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title="Tambah Produk Pinjaman" />

            <PageHeader
                title="Tambah Produk Pinjaman"
                description="Konfigurasikan produk pinjaman beserta komponen biayanya."
                icon={HandCoins}
                backHref={route('superadmin.pinjaman.produk')}
            />

            <div className="max-w-5xl">
                <PinjamanProdukForm
                    accounts={accounts}
                    submitUrl={route('superadmin.pinjaman.produk.store')}
                    processingLabel="Simpan Produk"
                />
            </div>
        </AuthenticatedLayout>
    );
}
