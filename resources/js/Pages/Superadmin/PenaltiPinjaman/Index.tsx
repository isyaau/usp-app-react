import { ShieldAlert } from 'lucide-react';
import TransaksiIndex, { type ModulConfig } from '../SetoranSimpanan/TransaksiIndex';

const config: ModulConfig = {
    label: 'Penalti Pinjaman',
    routeIndex: 'superadmin.transaksi-pinjaman.penalti-pinjaman',
    routeCreate: 'superadmin.transaksi-pinjaman.penalti-pinjaman.create',
    icon: ShieldAlert,
    description: 'Catat penalti atau denda atas keterlambatan angsuran pinjaman.',
};

export default function Index(props: Omit<Parameters<typeof TransaksiIndex>[0], 'config'>) {
    return <TransaksiIndex {...props} config={config} />;
}