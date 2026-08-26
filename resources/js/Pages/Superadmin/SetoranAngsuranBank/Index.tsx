import { Users } from 'lucide-react';
import KolektifIndex, { type KolektifModulConfig } from '../AngsuranKolektif/KolektifIndex';

const config: KolektifModulConfig = {
    label: 'Setoran Simpanan & Angsuran Bank',
    routeIndex: 'superadmin.transaksi-pinjaman.setoran-angsuran-bank',
    routeCreate: 'superadmin.transaksi-pinjaman.setoran-angsuran-bank.create',
    icon: Users,
    description: 'Catat Setoran Simpanan & Angsuran Bank per kelompok anggota.',
};

export default function Index(props: any) {
    return <KolektifIndex {...props} config={config} />;
}