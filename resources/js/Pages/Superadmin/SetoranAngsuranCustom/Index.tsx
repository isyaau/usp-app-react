import { Users } from 'lucide-react';
import KolektifIndex, { type KolektifModulConfig } from '../AngsuranKolektif/KolektifIndex';

const config: KolektifModulConfig = {
    label: 'Setoran Simpanan & Angsuran Custom',
    routeIndex: 'superadmin.transaksi-pinjaman.setoran-angsuran-custom',
    routeCreate: 'superadmin.transaksi-pinjaman.setoran-angsuran-custom.create',
    icon: Users,
    description: 'Catat Setoran Simpanan & Angsuran Custom per kelompok anggota.',
};

export default function Index(props: any) {
    return <KolektifIndex {...props} config={config} />;
}