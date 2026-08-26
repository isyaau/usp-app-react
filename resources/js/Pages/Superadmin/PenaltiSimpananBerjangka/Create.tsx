import { CalendarClock } from 'lucide-react';
import BerjangkaTransaksiCreate from '../BerjangkaTransaksi/BerjangkaTransaksiCreate';

export default function Create(props: any) {
    return (
        <BerjangkaTransaksiCreate
            {...props}
            config={{
                label: 'Penalti Simpanan Berjangka',
                routeIndex: 'superadmin.transaksi-simpanan-berjangka.penalti-simpanan-berjangka',
                storeRoute: 'superadmin.transaksi-simpanan-berjangka.penalti-simpanan-berjangka.store',
                icon: CalendarClock,
                fields: ['nominal'],
            }}
        />
    );
}
