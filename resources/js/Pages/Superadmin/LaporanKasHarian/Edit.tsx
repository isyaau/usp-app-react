import { Receipt } from 'lucide-react';
import BerjangkaTransaksiEdit from '../BerjangkaTransaksi/BerjangkaTransaksiEdit';
export default function Edit(props: any) {
    return <BerjangkaTransaksiEdit {...props} depositos={[]} config={{ label: 'Laporan Transaksi Kas Harian', routeIndex: 'superadmin.laporan.laporan-kas-harian', updateRoute: 'superadmin.laporan.laporan-kas-harian.update', icon: Receipt, fields: ['nominal'] }} />;
}
