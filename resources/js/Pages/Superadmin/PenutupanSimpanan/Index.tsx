import { ArrowDownToLine } from 'lucide-react';

import TransaksiIndex, {
    type ModulConfig,
} from '@/Pages/Superadmin/SetoranSimpanan/TransaksiIndex';

const config: ModulConfig = {
    label: 'Penutupan Simpanan',
    routeIndex: 'superadmin.transaksi-simpanan.penutupan-simpanan',
    routeCreate: 'superadmin.transaksi-simpanan.penutupan-simpanan.create',
    icon: ArrowDownToLine,
    description: 'Tutup rekening simpanan anggota beserta pelunasan dan bunganya.',
};

export default function Index(props: Omit<Parameters<typeof TransaksiIndex>[0], 'config'>) {
    return <TransaksiIndex {...props} config={config} />;
}
