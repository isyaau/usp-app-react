import { Head, useForm } from '@inertiajs/react';
import { ArrowRightLeft } from 'lucide-react';

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
        form.post(
            route('superadmin.transaksi-simpanan.pemindahbukuan-simpanan.store'),
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Tambah Pemindahbukuan Simpanan" />

            <PageHeader
                title="Tambah Pemindahbukuan Simpanan"
                description="Pindahkan dana antar rekening milik anggota yang sama."
                icon={ArrowRightLeft}
                backHref={route(
                    'superadmin.transaksi-simpanan.pemindahbukuan-simpanan',
                )}
            />

            <form onSubmit={submit}>
                <FormCard
                    title="Data Pemindahbukuan"
                    description="Rekening tujuan harus berbeda dengan rekening asal."
                >
                    <TransaksiFormFields
                        form={form}
                        pemindahbukuan
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
