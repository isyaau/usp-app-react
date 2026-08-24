import { Head } from '@inertiajs/react';
import { Eye, Package } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import type { DepositoJenisDetail } from '@/types/models';

interface Props {
    produkData: DepositoJenisDetail;
}

function InfoRow({ label, value }: { label: string; value: React.ReactNode }) {
    return (
        <div className="flex flex-col gap-1 py-2.5 sm:flex-row sm:items-center">
            <span className="w-44 shrink-0 text-sm font-medium text-muted-foreground">
                {label}
            </span>
            <span className="text-sm">{value ?? '—'}</span>
        </div>
    );
}

function AccountValue({
    account,
}: {
    account?: { no_account: string; nama: string } | null;
}) {
    if (!account) return <>—</>;

    return (
        <span className="font-mono text-xs">
            {account.no_account} · {account.nama}
        </span>
    );
}

const RUMUS_BUNGA_LABELS: Record<string, string> = {
    '1': 'Saldo Harian',
    '2': 'Saldo Rata-rata',
    '3': 'Saldo Terendah',
};

export default function ProdukShow({ produkData: p }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${p.nama}`} />

            <PageHeader
                title="Detail Produk Berjangka"
                description={p.nama}
                icon={Eye}
                backHref={route('superadmin.simpanan-berjangka.produk')}
            />

            <Card className="max-w-2xl">
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <Package className="size-4 text-brand-600" />
                        {p.nama}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <Separator className="mb-2" />
                    <InfoRow
                        label="Kode"
                        value={
                            <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                {p.kode}
                            </span>
                        }
                    />
                    <InfoRow label="Jangka Waktu" value={p.jangka_waktu ? `${p.jangka_waktu} bulan` : '—'} />
                    <InfoRow
                        label="Bunga"
                        value={p.bunga ? `${p.bunga}%` : '—'}
                    />
                    <InfoRow
                        label="Rumus Bunga"
                        value={
                            p.rumus_bunga
                                ? (RUMUS_BUNGA_LABELS[p.rumus_bunga] ?? p.rumus_bunga)
                                : '—'
                        }
                    />
                    <Separator className="my-2" />
                    <InfoRow label="Account Utama" value={<AccountValue account={p.account} />} />
                    <InfoRow label="Account Bunga" value={<AccountValue account={p.bungaAccount} />} />
                    <InfoRow label="Account Penalti" value={<AccountValue account={p.penaltiAccount} />} />
                    <InfoRow label="Account Pajak" value={<AccountValue account={p.pajakAccount} />} />
                    <Separator className="my-2" />
                    <InfoRow label="Penalti" value={p.penalti ? `${p.penalti}%` : '—'} />
                    <InfoRow label="Pajak" value={p.pajak ? `${p.pajak}%` : '—'} />
                    <InfoRow label="Saldo Pajak" value={p.saldo_pajak ?? '—'} />
                    <InfoRow label="Insentif" value={p.insentif ?? '—'} />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
