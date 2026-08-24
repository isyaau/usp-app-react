import { useEffect, useState } from 'react';
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
    transaksiData: {
        id: number;
        tgl_transaksi: string;
        anggota_id: number;
        simpanan_dari_id: number;
        simpanan_ke_id: number;
        kode_transaksi_id: number;
        nominal: string;
        keterangan: string | null;
        kantor_id: number;
        status: 'draft' | 'posted' | 'batal';
    };
    anggotas: { id: number; no_anggota: string; nama: string }[];
    kantors: { id: number; kode: string; nama_kantor: string }[];
    kodeTransaksis: { id: number; kode: string; nama: string }[];
    simpananUrl?: string;
}

export default function Edit({
    transaksiData,
    anggotas,
    kantors,
    kodeTransaksis,
}: Props) {
    const [rekeningAwal, setRekeningAwal] = useState<
        { id: number; no_rekening: string; jenis?: string | null }[]
    >([]);
    const [siap, setSiap] = useState(false);

    const form = useTransaksiForm({
        tgl_transaksi: transaksiData.tgl_transaksi,
        anggota_id: String(transaksiData.anggota_id),
        simpanan_id: String(transaksiData.simpanan_dari_id),
        simpanan_ke_id: String(transaksiData.simpanan_ke_id),
        kode_transaksi_id: String(transaksiData.kode_transaksi_id),
        nominal: String(transaksiData.nominal),
        keterangan: transaksiData.keterangan ?? '',
        kantor_id: String(transaksiData.kantor_id),
        status: transaksiData.status,
    });

    // Muat daftar rekening anggota agar dropdown asal/tujuan tampil terpilih.
    useEffect(() => {
        fetch(
            route('superadmin.transaksi-simpanan.simpanan-by-anggota', {
                anggota: transaksiData.anggota_id,
            }),
        )
            .then((res) => (res.ok ? res.json() : []))
            .then((data) => {
                setRekeningAwal(data);
                setSiap(true);
            })
            .catch(() => setSiap(true));
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.put(
            route(
                'superadmin.transaksi-simpanan.pemindahbukuan-simpanan.update',
                { pemindahbukuanSimpanan: transaksiData.id },
            ),
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Edit Pemindahbukuan Simpanan" />

            <PageHeader
                title="Edit Pemindahbukuan Simpanan"
                description="Perbarui data pemindahan dana antar rekening."
                icon={ArrowRightLeft}
                backHref={route(
                    'superadmin.transaksi-simpanan.pemindahbukuan-simpanan',
                )}
            />

            <form onSubmit={submit}>
                <FormCard
                    title={`Transaksi #${transaksiData.id}`}
                    description="Ubah detail pemindahbukuan sesuai kebutuhan."
                >
                    {siap && (
                        <TransaksiFormFields
                            form={form}
                            pemindahbukuan
                            initialRekenings={rekeningAwal}
                            simpananUrl={route(
                                'superadmin.transaksi-simpanan.simpanan-by-anggota',
                            )}
                            anggotas={anggotas}
                            kantors={kantors}
                            kodeTransaksis={kodeTransaksis}
                        />
                    )}
                </FormCard>
            </form>
        </AuthenticatedLayout>
    );
}
