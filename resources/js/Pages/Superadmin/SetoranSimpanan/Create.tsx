import { Head, useForm } from '@inertiajs/react';
import { ArrowDownToLine } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import {
    FormCard,
    TransaksiFormFields,
    useTransaksiForm,
} from './TransaksiForm';

interface Props {
    anggotas: { id: number; no_anggota: string; nama: string }[];
    kantors: { id: number; kode: string; nama_kantor: string }[];
    kodeTransaksis: { id: number; kode: string; nama: string }[];
    simpananUrl?: string;
}

export default function Create({ anggotas, kantors, kodeTransaksis }: Props) {
    const form = useTransaksiForm();

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(route('superadmin.transaksi-simpanan.setoran-simpanan.store'));
    };

    return (
        <AuthenticatedLayout>
            <Head title="Tambah Setoran Simpanan" />

            <PageHeader
                title="Tambah Setoran Simpanan"
                description="Catat setoran tunai ke rekening simpanan anggota."
                icon={ArrowDownToLine}
                backHref={route('superadmin.transaksi-simpanan.setoran-simpanan')}
            />

            <form onSubmit={submit}>
                <FormCard
                    title="Data Setoran"
                    description="Nomor transaksi digenerate otomatis oleh sistem."
                >
                    <TransaksiFormFields
                        form={form}
                        simpananUrl="/superadmin/transaksi-simpanan/simpanan-by-anggota"
                        anggotas={anggotas}
                        kantors={kantors}
                        kodeTransaksis={kodeTransaksis}
                    />
                </FormCard>
            </form>
        </AuthenticatedLayout>
    );
}
