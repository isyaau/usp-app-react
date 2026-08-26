import { Receipt } from 'lucide-react';
import BerjangkaTransaksiCreate from '../BerjangkaTransaksi/BerjangkaTransaksiCreate';
export default function Create(props: any) {
    return <BerjangkaTransaksiCreate {...props} depositos={[]} config={{ label: 'Laporan Transaksi Kas Harian', routeIndex: 'superadmin.laporan.laporan-kas-harian', storeRoute: 'superadmin.laporan.laporan-kas-harian.store', icon: Receipt, fields: ['nominal'] }} />;
}
