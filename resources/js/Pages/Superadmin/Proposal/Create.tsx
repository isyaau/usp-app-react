import { Head } from '@inertiajs/react';
import { FileText } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { ProposalForm } from './ProposalForm';
import type {
    LoanCostComponentRow,
    PinjamanAccountRow,
    PinjamanAnggotaRow,
    PinjamanMarketingRow,
    ProposalProdukRow,
} from '@/types/models';

interface Props {
    noBuktiOtomatis: string;
    anggotaOptions: PinjamanAnggotaRow[];
    produkOptions: ProposalProdukRow[];
    marketingOptions: PinjamanMarketingRow[];
    accountOptions: PinjamanAccountRow[];
    costComponents: LoanCostComponentRow[];
    satuanOptions: { value: string; label: string }[];
    metodeOptions: string[];
    bayarPokokPerOptions: string[];
}

export default function ProposalCreate(props: Props) {
    return (
        <AuthenticatedLayout>
            <Head title="Buat Proposal Pinjaman" />

            <PageHeader
                title="Buat Proposal Pinjaman"
                description="Entri proposal pinjaman baru."
                icon={FileText}
                backHref={route('superadmin.pinjaman.proposal')}
            />

            <ProposalForm
                {...props}
                noBuktiOtomatis={props.noBuktiOtomatis}
                submitUrl={route('superadmin.pinjaman.proposal.store')}
                processingLabel="Simpan Proposal"
            />
        </AuthenticatedLayout>
    );
}
