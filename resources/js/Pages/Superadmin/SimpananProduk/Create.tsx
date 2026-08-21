import { Head } from '@inertiajs/react';
import { PiggyBank } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import type { AccountMini } from '@/types/models';
import type { SimpananKodeOption } from '@/types/simpanan';

import { SimpananProdukForm } from './SimpananProdukForm';

interface Props {
    accounts: AccountMini[];
    kodes: SimpananKodeOption[];
}

export default function SimpananProdukCreate({ accounts, kodes }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title="Tambah Produk Simpanan" />

            <PageHeader
                title="Tambah Produk Simpanan"
                description="Konfigurasikan produk simpanan beserta bunga dan biayanya."
                icon={PiggyBank}
                backHref={route('superadmin.simpanan.produk-simpanan')}
            />

            <div className="max-w-5xl">
                <SimpananProdukForm
                    accounts={accounts}
                    kodes={kodes}
                    submitUrl={route('superadmin.simpanan.produk-simpanan.store')}
                    processingLabel="Simpan Produk"
                />
            </div>
        </AuthenticatedLayout>
    );
}
