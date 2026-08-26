import { Receipt } from 'lucide-react';
import BerjangkaTransaksiIndex, { type BerjangkaModulConfig } from '../BerjangkaTransaksi/BerjangkaTransaksiIndex';

const config: BerjangkaModulConfig = {
    label: 'Laporan Transaksi Kas Harian',
    routeIndex: 'superadmin.laporan.laporan-kas-harian',
    routeCreate: 'superadmin.laporan.laporan-kas-harian.create',
    icon: Receipt,
    description: 'Daftar laporan transaksi kas harian.',
};

export default function Index(props: any) {
    return <BerjangkaTransaksiIndex {...props} config={config} />;
}
