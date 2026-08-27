import { Head } from '@inertiajs/react';
import { Pencil } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { ProposalForm } from './ProposalForm';
import type {
    LoanCostComponentRow,
    PinjamanAccountRow,
    PinjamanAnggotaRow,
    PinjamanMarketingRow,
    ProposalEditRow,
    ProposalProdukRow,
} from '@/types/models';

interface Props {
    proposal: ProposalEditRow;
    noBuktiOtomatis?: string;
    anggotaOptions: PinjamanAnggotaRow[];
    produkOptions: ProposalProdukRow[];
    marketingOptions: PinjamanMarketingRow[];
    accountOptions: PinjamanAccountRow[];
    costComponents: LoanCostComponentRow[];
    satuanOptions: { value: string; label: string }[];
    metodeOptions: string[];
    bayarPokokPerOptions: string[];
}

export default function ProposalEdit({ proposal, ...props }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Edit ${proposal.no_bukti}`} />

            <PageHeader
                title="Edit Proposal Pinjaman"
                description={`Perbarui proposal ${proposal.no_bukti}.`}
                icon={Pencil}
                backHref={route('superadmin.pinjaman.proposal')}
            />

            <ProposalForm
                {...props}
                initial={proposal}
                proposalId={proposal.id}
                submitUrl={route('superadmin.pinjaman.proposal.update', proposal.id)}
                submitMethod="put"
                processingLabel="Perbarui Proposal"
            />
        </AuthenticatedLayout>
    );
}
