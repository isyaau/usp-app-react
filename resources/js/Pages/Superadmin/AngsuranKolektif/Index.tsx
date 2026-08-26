import { Users } from 'lucide-react';
import KolektifIndex, { type KolektifModulConfig } from './KolektifIndex';

const config: KolektifModulConfig = {
    label: 'Angsuran Kolektif',
    routeIndex: 'superadmin.transaksi-pinjaman.angsuran-kolektif',
    routeCreate: 'superadmin.transaksi-pinjaman.angsuran-kolektif.create',
    icon: Users,
    description: 'Catat angsuran kolektif per kelompok anggota.',
};

export default function Index(props: any) {
    return <KolektifIndex {...props} config={config} />;
}