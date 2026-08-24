import { ArrowUpFromLine } from 'lucide-react';

import TransaksiIndex, { type ModulConfig } from '@/Pages/Superadmin/SetoranSimpanan/TransaksiIndex';

const config: ModulConfig = {
    label: 'Tarikan Simpanan',
    routeIndex: 'superadmin.transaksi-simpanan.tarikan-simpanan',
    routeCreate: 'superadmin.transaksi-simpanan.tarikan-simpanan.create',
    icon: ArrowUpFromLine,
    description: 'Catat penarikan tunai dari rekening simpanan anggota.',
};

export default function Index(props: Omit<Parameters<typeof TransaksiIndex>[0], 'config'>) {
    return <TransaksiIndex {...props} config={config} />;
}
