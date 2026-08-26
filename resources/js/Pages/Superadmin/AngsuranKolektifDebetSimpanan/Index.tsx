import { Users } from 'lucide-react';
import KolektifIndex, { type KolektifModulConfig } from '../AngsuranKolektif/KolektifIndex';

const config: KolektifModulConfig = {
    label: 'Angsuran Kolektif Debet Simpanan',
    routeIndex: 'superadmin.transaksi-pinjaman.angsuran-kolektif-debet-simpanan',
    routeCreate: 'superadmin.transaksi-pinjaman.angsuran-kolektif-debet-simpanan.create',
    icon: Users,
    description: 'Catat Angsuran Kolektif Debet Simpanan per kelompok anggota.',
};

export default function Index(props: any) {
    return <KolektifIndex {...props} config={config} />;
}