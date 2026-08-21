import { Head, useForm } from '@inertiajs/react';
import { UserPlus } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import {
    AnggotaForm,
    type AnggotaFormValues,
} from '@/Pages/Superadmin/Anggota/AnggotaForm';

interface Props {
    kelompoks: { id: number; nama: string }[];
    kantors: { id: number; nama_kantor: string }[];
}

type FormValues = AnggotaFormValues & { foto: File | null };

export default function AnggotaCreate({ kelompoks, kantors }: Props) {
    const form = useForm<FormValues>({
        foto: null,
        no_anggota: '',
        pin: '',
        nama: '',
        kelompok_id: '',
        kantor_id: '',
        alamat: '',
        provinsi_id: '',
        kota_id: '',
        kecamatan_id: '',
        kelurahan_id: '',
        email: '',
        tempat_lahir: '',
        tgl_lahir: '',
        jenis_kelamin: '',
        agama: '',
        pekerjaan: '',
        pendidikan: '',
        status_perkawinan: '',
        pasangan: '',
        telepon: '',
        no_hp: '',
        jenis_identitas: '',
        no_identitas: '',
        npwp: '',
        ibu: '',
        pengurus: false,
        pengurus_jabatan: '',
        tgl_pengurus_diangkat: '',
        tgl_pengurus_berhenti: '',
        pengurus_berhenti: '',
        pengawas: false,
        pengawas_jabatan: '',
        tgl_pengawas_diangkat: '',
        tgl_pengawas_berhenti: '',
        pengawas_berhenti: '',
        waris1: '',
        hubungan_waris1: '',
        waris2: '',
        hubungan_waris2: '',
        status: true,
        tgl_anggota_berhenti: '',
        anggota_berhenti: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.post(route('superadmin.anggota.store'), {
            forceFormData: true,
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Tambah Anggota" />

            <PageHeader
                title="Tambah Anggota"
                description="Daftarkan anggota baru ke dalam sistem koperasi."
                icon={UserPlus}
                backHref={route('superadmin.anggota')}
            />

            <AnggotaForm
                values={form.data}
                setData={(key, value) => form.setData(key, value as never)}
                errors={form.errors}
                processing={form.processing}
                isEdit={false}
                fotoName={null}
                onFotoChange={(file) => form.setData('foto', file)}
                onSubmit={submit}
                backHref={route('superadmin.anggota')}
                submitLabel="Simpan Anggota"
                optionKelompok={kelompoks}
                optionKantor={kantors}
            />
        </AuthenticatedLayout>
    );
}
