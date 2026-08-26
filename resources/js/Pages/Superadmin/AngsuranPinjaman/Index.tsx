import { ArrowDownUp } from 'lucide-react';
import TransaksiIndex, { type ModulConfig } from '../SetoranSimpanan/TransaksiIndex';

const config: ModulConfig = {
    label: 'Angsuran Pinjaman',
    routeIndex: 'superadmin.transaksi-pinjaman.angsuran-pinjaman',
    routeCreate: 'superadmin.transaksi-pinjaman.angsuran-pinjaman.create',
    icon: ArrowDownUp,
    description: 'Catat angsuran pinjaman individual per anggota.',
};

export default function Index(props: Omit<Parameters<typeof TransaksiIndex>[0], 'config'>) {
    return <TransaksiIndex {...props} config={config} />;
}