import { Receipt } from 'lucide-react';
import BerjangkaTransaksiCreate from '../BerjangkaTransaksi/BerjangkaTransaksiCreate';
export default function Create(props: any) {
    return <BerjangkaTransaksiCreate {...props} depositos={[]} config={{ label: 'Laporan Transaksi Pinjaman', routeIndex: 'superadmin.laporan.laporan-transaksi-pinjaman', storeRoute: 'superadmin.laporan.laporan-transaksi-pinjaman.store', icon: Receipt, fields: ['nominal'] }} />;
}
