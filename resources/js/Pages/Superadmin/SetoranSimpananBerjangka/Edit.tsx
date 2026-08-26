import { CalendarClock } from 'lucide-react';
import BerjangkaTransaksiEdit from '../BerjangkaTransaksi/BerjangkaTransaksiEdit';
export default function Edit(props: any) { return <BerjangkaTransaksiEdit {...props} config={{ label: 'Setoran Simpanan Berjangka', routeIndex: 'superadmin.transaksi-simpanan-berjangka.setoran-simpanan-berjangka', updateRoute: 'superadmin.transaksi-simpanan-berjangka.setoran-simpanan-berjangka.update', icon: CalendarClock, fields: ['nominal'] }} />; }
