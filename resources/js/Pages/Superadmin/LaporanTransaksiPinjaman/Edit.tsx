import { Receipt } from 'lucide-react';
import BerjangkaTransaksiEdit from '../BerjangkaTransaksi/BerjangkaTransaksiEdit';
export default function Edit(props: any) {
    return <BerjangkaTransaksiEdit {...props} depositos={[]} config={{ label: 'Laporan Transaksi Pinjaman', routeIndex: 'superadmin.laporan.laporan-transaksi-pinjaman', updateRoute: 'superadmin.laporan.laporan-transaksi-pinjaman.update', icon: Receipt, fields: ['nominal'] }} />;
}
