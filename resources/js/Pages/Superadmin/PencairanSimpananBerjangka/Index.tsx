import { CalendarClock } from 'lucide-react';
import BerjangkaTransaksiIndex, { type BerjangkaModulConfig } from '../BerjangkaTransaksi/BerjangkaTransaksiIndex';

const config: BerjangkaModulConfig = {
    label: 'Pencairan Simpanan Berjangka',
    routeIndex: 'superadmin.transaksi-simpanan-berjangka.pencairan-simpanan-berjangka',
    routeCreate: 'superadmin.transaksi-simpanan-berjangka.pencairan-simpanan-berjangka.create',
    icon: CalendarClock,
    description: 'Catat pencairan simpanan berjangka saat jatuh tempo.',
};

export default function Index(props: any) {
    return <BerjangkaTransaksiIndex {...props} config={config} />;
}
