import { Head } from '@inertiajs/react';

import TransaksiShow from '@/Pages/Superadmin/SetoranSimpanan/TransaksiShow';

interface ShowProps {
    transaksiData: Parameters<typeof TransaksiShow>[0]['transaksiData'];
}

export default function Show({ transaksiData }: ShowProps) {
    return (
        <TransaksiShow
            transaksiData={transaksiData}
            backHref={route(
                'superadmin.transaksi-simpanan.pemindahbukuan-simpanan',
            )}
        />
    );
}
