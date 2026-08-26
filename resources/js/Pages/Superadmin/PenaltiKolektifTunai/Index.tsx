import { Users } from 'lucide-react';
import KolektifIndex, { type KolektifModulConfig } from '../AngsuranKolektif/KolektifIndex';

const config: KolektifModulConfig = {
    label: 'Penalti Pinjaman Kolektif Tunai',
    routeIndex: 'superadmin.transaksi-pinjaman.penalti-kolektif-tunai',
    routeCreate: 'superadmin.transaksi-pinjaman.penalti-kolektif-tunai.create',
    icon: Users,
    description: 'Catat Penalti Pinjaman Kolektif Tunai per kelompok anggota.',
};

export default function Index(props: any) {
    return <KolektifIndex {...props} config={config} />;
}