import { ArrowRightLeft } from 'lucide-react';

import TransaksiIndex, {
    type ModulConfig,
} from '@/Pages/Superadmin/SetoranSimpanan/TransaksiIndex';

const config: ModulConfig = {
    label: 'Pemindahbukuan Simpanan',
    routeIndex: 'superadmin.transaksi-simpanan.pemindahbukuan-simpanan',
    routeCreate:
        'superadmin.transaksi-simpanan.pemindahbukuan-simpanan.create',
    icon: ArrowRightLeft,
    description: 'Pindahkan dana antar rekening milik anggota yang sama.',
};

export default function Index(props: Omit<Parameters<typeof TransaksiIndex>[0], 'config'>) {
    return <TransaksiIndex {...props} config={config} />;
}
