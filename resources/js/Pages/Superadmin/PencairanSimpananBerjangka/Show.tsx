import { CalendarClock } from 'lucide-react';
import BerjangkaTransaksiShow from '../BerjangkaTransaksi/BerjangkaTransaksiShow';
export default function Show(props: any) { return <BerjangkaTransaksiShow {...props} config={{ label: 'Pencairan Simpanan Berjangka', routeIndex: 'superadmin.transaksi-simpanan-berjangka.pencairan-simpanan-berjangka', icon: CalendarClock }} />; }
