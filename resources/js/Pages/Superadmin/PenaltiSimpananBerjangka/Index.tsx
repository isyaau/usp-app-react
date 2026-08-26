import { CalendarClock } from 'lucide-react';
import BerjangkaTransaksiIndex, { type BerjangkaModulConfig } from '../BerjangkaTransaksi/BerjangkaTransaksiIndex';

const config: BerjangkaModulConfig = {
    label: 'Penalti Simpanan Berjangka',
    routeIndex: 'superadmin.transaksi-simpanan-berjangka.penalti-simpanan-berjangka',
    routeCreate: 'superadmin.transaksi-simpanan-berjangka.penalti-simpanan-berjangka.create',
    icon: CalendarClock,
    description: 'Catat penalti atau denda simpanan berjangka.',
};

export default function Index(props: any) {
    return <BerjangkaTransaksiIndex {...props} config={config} />;
}
