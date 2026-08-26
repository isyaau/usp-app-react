import { Receipt } from 'lucide-react';
import BerjangkaTransaksiCreate from '../BerjangkaTransaksi/BerjangkaTransaksiCreate';
export default function Create(props: any) {
    return <BerjangkaTransaksiCreate {...props} depositos={[]} config={{ label: 'Laporan Transaksi Simpanan', routeIndex: 'superadmin.laporan.laporan-transaksi-simpanan', storeRoute: 'superadmin.laporan.laporan-transaksi-simpanan.store', icon: Receipt, fields: ['nominal'] }} />;
}
