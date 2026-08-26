import { CalendarClock } from 'lucide-react';
import BerjangkaTransaksiShow from '../BerjangkaTransaksi/BerjangkaTransaksiShow';
export default function Show(props: any) { return <BerjangkaTransaksiShow {...props} config={{ label: 'Setoran Simpanan Berjangka', routeIndex: 'superadmin.transaksi-simpanan-berjangka.setoran-simpanan-berjangka', icon: CalendarClock }} />; }
