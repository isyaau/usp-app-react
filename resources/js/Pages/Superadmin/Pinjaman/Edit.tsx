import { Head } from '@inertiajs/react';
import { Pencil } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { PinjamanForm } from './PinjamanForm';
import type {
    PinjamanAccountRow,
    PinjamanAnggotaRow,
    PinjamanEditRow,
    PinjamanJaminanTypeRow,
    PinjamanJenisRow,
    PinjamanKodeTarikanRow,
    PinjamanMarketingRow,
    PinjamanSektorRow,
    PinjamanSimpananRow,
    PinjamanSuratOption,
} from '@/types/models';

interface Props {
    pinjaman: PinjamanEditRow;
    anggotaOptions: PinjamanAnggotaRow[];
    jenisOptions: PinjamanJenisRow[];
    marketingOptions: PinjamanMarketingRow[];
    accountOptions: PinjamanAccountRow[];
    jaminanTypes: PinjamanJaminanTypeRow[];
    simpananOptions: PinjamanSimpananRow[];
    kodeTarikanOptions: PinjamanKodeTarikanRow[];
    sektorOptions: PinjamanSektorRow[];
    bayarPokokPerOptions: string[];
    suratOptions: PinjamanSuratOption[];
    satuanOptions: { value: string; label: string }[];
    nomorOtomatis: string;
}

export default function PinjamanEdit({ pinjaman, ...props }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Edit ${pinjaman.no_pinjaman}`} />

            <PageHeader
                title={`Edit Transaksi Pinjaman`}
                description={`Perbarui transaksi ${pinjaman.no_pinjaman}.`}
                icon={Pencil}
                backHref={route('superadmin.pinjaman.pinjaman')}
            />

            <PinjamanForm
                {...props}
                initial={pinjaman}
                pinjamanId={pinjaman.id}
                submitUrl={route('superadmin.pinjaman.pinjaman.update', pinjaman.id)}
                submitMethod="put"
                processingLabel="Perbarui Transaksi"
            />
        </AuthenticatedLayout>
    );
}
