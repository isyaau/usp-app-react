import { Receipt } from 'lucide-react';
import BerjangkaTransaksiShow from '../BerjangkaTransaksi/BerjangkaTransaksiShow';
export default function Show(props: any) {
    return <BerjangkaTransaksiShow {...props} config={{ label: 'Laporan Transaksi Kas Harian', routeIndex: 'superadmin.laporan.laporan-kas-harian', icon: Receipt }} />;
}
