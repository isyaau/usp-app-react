import { Receipt } from 'lucide-react';
import BerjangkaTransaksiIndex, { type BerjangkaModulConfig } from '../BerjangkaTransaksi/BerjangkaTransaksiIndex';

const config: BerjangkaModulConfig = {
    label: 'Laporan Transaksi Pinjaman',
    routeIndex: 'superadmin.laporan.laporan-transaksi-pinjaman',
    routeCreate: 'superadmin.laporan.laporan-transaksi-pinjaman.create',
    icon: Receipt,
    description: 'Daftar laporan transaksi pinjaman anggota.',
};

export default function Index(props: any) {
    return <BerjangkaTransaksiIndex {...props} config={config} />;
}
