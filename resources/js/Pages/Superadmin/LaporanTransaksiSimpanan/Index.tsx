import { Receipt } from 'lucide-react';
import BerjangkaTransaksiIndex, { type BerjangkaModulConfig } from '../BerjangkaTransaksi/BerjangkaTransaksiIndex';

const config: BerjangkaModulConfig = {
    label: 'Laporan Transaksi Simpanan',
    routeIndex: 'superadmin.laporan.laporan-transaksi-simpanan',
    routeCreate: 'superadmin.laporan.laporan-transaksi-simpanan.create',
    icon: Receipt,
    description: 'Daftar laporan transaksi simpanan anggota.',
};

export default function Index(props: any) {
    return <BerjangkaTransaksiIndex {...props} config={config} />;
}
