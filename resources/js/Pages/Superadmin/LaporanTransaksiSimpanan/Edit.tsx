import { Receipt } from 'lucide-react';
import BerjangkaTransaksiEdit from '../BerjangkaTransaksi/BerjangkaTransaksiEdit';
export default function Edit(props: any) {
    return <BerjangkaTransaksiEdit {...props} depositos={[]} config={{ label: 'Laporan Transaksi Simpanan', routeIndex: 'superadmin.laporan.laporan-transaksi-simpanan', updateRoute: 'superadmin.laporan.laporan-transaksi-simpanan.update', icon: Receipt, fields: ['nominal'] }} />;
}
