import { CalendarClock } from 'lucide-react';
import BerjangkaTransaksiIndex, { type BerjangkaModulConfig } from '../BerjangkaTransaksi/BerjangkaTransaksiIndex';

const config: BerjangkaModulConfig = {
    label: 'Setoran Simpanan Berjangka',
    routeIndex: 'superadmin.transaksi-simpanan-berjangka.setoran-simpanan-berjangka',
    routeCreate: 'superadmin.transaksi-simpanan-berjangka.setoran-simpanan-berjangka.create',
    icon: CalendarClock,
    description: 'Catat setoran simpanan berjangka dari anggota.',
};

export default function Index(props: any) {
    return <BerjangkaTransaksiIndex {...props} config={config} />;
}
