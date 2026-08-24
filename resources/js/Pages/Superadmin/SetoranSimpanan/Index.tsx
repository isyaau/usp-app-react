import { ArrowDownToLine } from 'lucide-react';

import TransaksiIndex, { type ModulConfig } from './TransaksiIndex';

const config: ModulConfig = {
    label: 'Setoran Simpanan',
    routeIndex: 'superadmin.transaksi-simpanan.setoran-simpanan',
    routeCreate: 'superadmin.transaksi-simpanan.setoran-simpanan.create',
    icon: ArrowDownToLine,
    description: 'Catat setoran tunai ke rekening simpanan anggota.',
};

export default function Index(props: Omit<Parameters<typeof TransaksiIndex>[0], 'config'>) {
    return <TransaksiIndex {...props} config={config} />;
}
