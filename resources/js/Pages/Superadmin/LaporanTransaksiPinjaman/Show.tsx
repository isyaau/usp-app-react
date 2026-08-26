import { Receipt } from 'lucide-react';
import BerjangkaTransaksiShow from '../BerjangkaTransaksi/BerjangkaTransaksiShow';
export default function Show(props: any) {
    return <BerjangkaTransaksiShow {...props} config={{ label: 'Laporan Transaksi Pinjaman', routeIndex: 'superadmin.laporan.laporan-transaksi-pinjaman', icon: Receipt }} />;
}
