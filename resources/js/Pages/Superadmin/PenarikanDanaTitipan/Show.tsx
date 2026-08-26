import { Banknote } from 'lucide-react';
import BerjangkaTransaksiShow from '../BerjangkaTransaksi/BerjangkaTransaksiShow';
export default function Show(props: any) {
    return <BerjangkaTransaksiShow {...props} config={{ label: 'Penarikan Dana Titipan Anggota', routeIndex: 'superadmin.transaksi-titipan.penarikan-dana-titipan', icon: Banknote }} />;
}
