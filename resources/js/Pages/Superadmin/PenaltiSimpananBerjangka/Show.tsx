import { CalendarClock } from 'lucide-react';
import BerjangkaTransaksiShow from '../BerjangkaTransaksi/BerjangkaTransaksiShow';
export default function Show(props: any) { return <BerjangkaTransaksiShow {...props} config={{ label: 'Penalti Simpanan Berjangka', routeIndex: 'superadmin.transaksi-simpanan-berjangka.penalti-simpanan-berjangka', icon: CalendarClock }} />; }
