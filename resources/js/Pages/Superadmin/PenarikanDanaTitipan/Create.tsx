import { Banknote } from 'lucide-react';
import BerjangkaTransaksiCreate from '../BerjangkaTransaksi/BerjangkaTransaksiCreate';
export default function Create(props: any) {
    return <BerjangkaTransaksiCreate {...props} depositos={[]} config={{ label: 'Penarikan Dana Titipan Anggota', routeIndex: 'superadmin.transaksi-titipan.penarikan-dana-titipan', storeRoute: 'superadmin.transaksi-titipan.penarikan-dana-titipan.store', icon: Banknote, fields: ['nominal'] }} />;
}
