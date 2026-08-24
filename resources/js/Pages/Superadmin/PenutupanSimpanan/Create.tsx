import { Head, useForm } from '@inertiajs/react';
import { ArrowDownToLine } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
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
    const bunga = useForm<{ nominal_bunga: string }>({ nominal_bunga: '' });
    const form = useTransaksiForm();

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        // Gabungkan nominal_bunga ke payload transaksi utama.
        form.transform((data) => ({
            ...data,
            nominal_bunga: bunga.data.nominal_bunga || 0,
        }));
        form.post(route('superadmin.transaksi-simpanan.penutupan-simpanan.store'));
    };

    return (
        <AuthenticatedLayout>
            <Head title="Tambah Penutupan Simpanan" />

            <PageHeader
                title="Tambah Penutupan Simpanan"
                description="Tutup rekening simpanan anggota beserta pelunasan dan bunganya."
                icon={ArrowDownToLine}
                backHref={route('superadmin.transaksi-simpanan.penutupan-simpanan')}
            />

            <form onSubmit={submit}>
                <FormCard
                    title="Data Penutupan"
                    description="Nominal pokok wajib diisi, nominal bunga opsional (default 0)."
                >
                    <TransaksiFormFields
                        form={form}
                        simpananUrl={route(
                            'superadmin.transaksi-simpanan.simpanan-by-anggota',
                        )}
                        anggotas={anggotas}
                        kantors={kantors}
                        kodeTransaksis={kodeTransaksis}
                        extra={
                            <div className="space-y-2">
                                <Label htmlFor="nominal_bunga">
                                    Nominal Bunga
                                </Label>
                                <Input
                                    id="nominal_bunga"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    value={bunga.data.nominal_bunga}
                                    onChange={(e) =>
                                        bunga.setData(
                                            'nominal_bunga',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="0"
                                    className="font-mono"
                                />
                            </div>
                        }
                    />
                </FormCard>
            </form>
        </AuthenticatedLayout>
    );
}
