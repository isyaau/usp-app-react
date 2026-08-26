import { Banknote } from 'lucide-react';
import BerjangkaTransaksiEdit from '../BerjangkaTransaksi/BerjangkaTransaksiEdit';
export default function Edit(props: any) {
    return <BerjangkaTransaksiEdit {...props} depositos={[]} config={{ label: 'Penarikan Dana Titipan Anggota', routeIndex: 'superadmin.transaksi-titipan.penarikan-dana-titipan', updateRoute: 'superadmin.transaksi-titipan.penarikan-dana-titipan.update', icon: Banknote, fields: ['nominal'] }} />;
}
