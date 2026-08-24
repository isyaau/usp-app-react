import { Head, useForm } from '@inertiajs/react';
import { ArrowUpFromLine } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import {
    FormCard,
    TransaksiFormFields,
    useTransaksiForm,
} from '@/Pages/Superadmin/SetoranSimpanan/TransaksiForm';

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
        form.post(route('superadmin.transaksi-simpanan.tarikan-simpanan.store'));
    };

    return (
        <AuthenticatedLayout>
            <Head title="Tambah Tarikan Simpanan" />

            <PageHeader
                title="Tambah Tarikan Simpanan"
                description="Catat penarikan tunai dari rekening simpanan anggota."
                icon={ArrowUpFromLine}
                backHref={route('superadmin.transaksi-simpanan.tarikan-simpanan')}
            />

            <form onSubmit={submit}>
                <FormCard
                    title="Data Tarikan"
                    description="Nomor transaksi digenerate otomatis oleh sistem."
                >
                    <TransaksiFormFields
                        form={form}
                        simpananUrl={route(
                            'superadmin.transaksi-simpanan.simpanan-by-anggota',
                        )}
                        anggotas={anggotas}
                        kantors={kantors}
                        kodeTransaksis={kodeTransaksis}
                    />
                </FormCard>
            </form>
        </AuthenticatedLayout>
    );
}
