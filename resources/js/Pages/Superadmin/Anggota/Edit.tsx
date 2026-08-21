import { Head, useForm } from '@inertiajs/react';
import { UserPen } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import {
    AnggotaForm,
    type AnggotaFormValues,
} from '@/Pages/Superadmin/Anggota/AnggotaForm';
import type { AnggotaDetail } from '@/types/models';

interface Props {
    anggotaData: AnggotaDetail;
    kelompoks: { id: number; nama: string }[];
    kantors: { id: number; nama_kantor: string }[];
}

type FormValues = AnggotaFormValues & { foto: File | null };

export default function AnggotaEdit({
    anggotaData,
    kelompoks,
    kantors,
}: Props) {
    const form = useForm<FormValues>({
        foto: null,
        no_anggota: anggotaData.no_anggota ?? '',
        pin: anggotaData.pin ?? '',
        nama: anggotaData.nama ?? '',
        kelompok_id: anggotaData.kelompok_id
            ? String(anggotaData.kelompok_id)
            : '',
        kantor_id: anggotaData.kantor_id ? String(anggotaData.kantor_id) : '',
        alamat: anggotaData.alamat ?? '',
        provinsi_id: anggotaData.provinsi_id ?? '',
        kota_id: anggotaData.kota_id ?? '',
        kecamatan_id: anggotaData.kecamatan_id ?? '',
        kelurahan_id: anggotaData.kelurahan_id ?? '',
        email: anggotaData.email ?? '',
        tempat_lahir: anggotaData.tempat_lahir ?? '',
        tgl_lahir: anggotaData.tgl_lahir?.slice(0, 10) ?? '',
        jenis_kelamin: anggotaData.jenis_kelamin ?? '',
        agama: anggotaData.agama ?? '',
        pekerjaan: anggotaData.pekerjaan ?? '',
        pendidikan: anggotaData.pendidikan ?? '',
        status_perkawinan: anggotaData.status_perkawinan ?? '',
        pasangan: anggotaData.pasangan ?? '',
        telepon: anggotaData.telepon ?? '',
        no_hp: anggotaData.no_hp ?? '',
        jenis_identitas: anggotaData.jenis_identitas ?? '',
        no_identitas: anggotaData.no_identitas ?? '',
        npwp: anggotaData.npwp ?? '',
        ibu: anggotaData.ibu ?? '',
        pengurus: Number(anggotaData.pengurus) === 1,
        pengurus_jabatan: anggotaData.pengurus_jabatan ?? '',
        tgl_pengurus_diangkat:
            anggotaData.tgl_pengurus_diangkat?.slice(0, 10) ?? '',
        tgl_pengurus_berhenti:
            anggotaData.tgl_pengurus_berhenti?.slice(0, 10) ?? '',
        pengurus_berhenti: anggotaData.pengurus_berhenti ?? '',
        pengawas: Number(anggotaData.pengawas) === 1,
        pengawas_jabatan: anggotaData.pengawas_jabatan ?? '',
        tgl_pengawas_diangkat:
            anggotaData.tgl_pengawas_diangkat?.slice(0, 10) ?? '',
        tgl_pengawas_berhenti:
            anggotaData.tgl_pengawas_berhenti?.slice(0, 10) ?? '',
        pengawas_berhenti: anggotaData.pengawas_berhenti ?? '',
        waris1: anggotaData.waris1 ?? '',
        hubungan_waris1: anggotaData.hubungan_waris1 ?? '',
        waris2: anggotaData.waris2 ?? '',
        hubungan_waris2: anggotaData.hubungan_waris2 ?? '',
        status: Number(anggotaData.status) === 1,
        tgl_anggota_berhenti: anggotaData.tgl_anggota_berhenti?.slice(0, 10) ?? '',
        anggota_berhenti: anggotaData.anggota_berhenti ?? '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.transform((data) => ({ ...data, _method: 'put' }));
        form.post(route('superadmin.anggota.update', anggotaData.id), {
            forceFormData: true,
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Edit Anggota" />

            <PageHeader
                title={`Edit — ${anggotaData.nama}`}
                description={`Perbarui data anggota ${anggotaData.no_anggota}.`}
                icon={UserPen}
                backHref={route('superadmin.anggota.show', anggotaData.id)}
            />

            <AnggotaForm
                values={form.data}
                setData={(key, value) => form.setData(key, value as never)}
                errors={form.errors}
                processing={form.processing}
                isEdit
                fotoName={anggotaData.foto}
                onFotoChange={(file) => form.setData('foto', file)}
                onSubmit={submit}
                backHref={route('superadmin.anggota.show', anggotaData.id)}
                submitLabel="Simpan Perubahan"
                optionKelompok={kelompoks}
                optionKantor={kantors}
            />
        </AuthenticatedLayout>
    );
}
