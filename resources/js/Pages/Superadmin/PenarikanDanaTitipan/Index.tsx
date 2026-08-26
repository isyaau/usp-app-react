import { Banknote } from 'lucide-react';
import BerjangkaTransaksiIndex, { type BerjangkaModulConfig } from '../BerjangkaTransaksi/BerjangkaTransaksiIndex';

const config: BerjangkaModulConfig = {
    label: 'Penarikan Dana Titipan Anggota',
    routeIndex: 'superadmin.transaksi-titipan.penarikan-dana-titipan',
    routeCreate: 'superadmin.transaksi-titipan.penarikan-dana-titipan.create',
    icon: Banknote,
    description: 'Catat penarikan dana titipan dari rekening anggota.',
};

export default function Index(props: any) {
    return <BerjangkaTransaksiIndex {...props} config={config} />;
}
