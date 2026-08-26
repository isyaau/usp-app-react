import { Users } from 'lucide-react';
import KolektifIndex, { type KolektifModulConfig } from '../AngsuranKolektif/KolektifIndex';

const config: KolektifModulConfig = {
    label: 'Angsuran Kolektif Tunai',
    routeIndex: 'superadmin.transaksi-pinjaman.angsuran-kolektif-tunai',
    routeCreate: 'superadmin.transaksi-pinjaman.angsuran-kolektif-tunai.create',
    icon: Users,
    description: 'Catat Angsuran Kolektif Tunai per kelompok anggota.',
};

export default function Index(props: any) {
    return <KolektifIndex {...props} config={config} />;
}