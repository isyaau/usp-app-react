import { Head } from '@inertiajs/react';
import { CalendarClock } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { PinjamanForm } from '@/Pages/Superadmin/Pinjaman/PinjamanForm';
import type {
    PinjamanEditRow,
    PinjamanAnggotaRow,
    PinjamanJenisRow,
    PinjamanMarketingRow,
    PinjamanAccountRow,
    PinjamanJaminanTypeRow,
    PinjamanSimpananRow,
    PinjamanKodeTarikanRow,
    PinjamanSektorRow,
    PinjamanSuratOption,
} from '@/types/models';

interface Props {
    transaksi: PinjamanEditRow & { no_transaksi?: string };
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
}

export default function Edit({
    transaksi,
    anggotaOptions,
    jenisOptions,
    marketingOptions,
    accountOptions,
    jaminanTypes,
    simpananOptions,
    kodeTarikanOptions,
    sektorOptions,
    bayarPokokPerOptions,
    suratOptions,
    satuanOptions,
}: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Edit ${transaksi?.no_transaksi ?? 'Jadwal Ulang'}`} />

            <PageHeader
                title="Edit Jadwal Ulang Pinjaman"
                description={transaksi?.no_transaksi ?? ''}
                icon={CalendarClock}
                backHref={route('superadmin.pinjaman.jadwal-ulang')}
            />

            <PinjamanForm
                key={`edit-${transaksi.id}`}
                initial={transaksi}
                anggotaOptions={anggotaOptions}
                jenisOptions={jenisOptions}
                marketingOptions={marketingOptions}
                accountOptions={accountOptions}
                jaminanTypes={jaminanTypes}
                simpananOptions={simpananOptions}
                kodeTarikanOptions={kodeTarikanOptions}
                sektorOptions={sektorOptions}
                bayarPokokPerOptions={bayarPokokPerOptions}
                suratOptions={suratOptions}
                satuanOptions={satuanOptions}
                submitUrl={route('superadmin.pinjaman.jadwal-ulang.update', transaksi.id)}
                submitMethod="put"
                processingLabel="Menyimpan…"
                reschedule
            />
        </AuthenticatedLayout>
    );
}
