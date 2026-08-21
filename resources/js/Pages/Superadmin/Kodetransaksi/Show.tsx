import { Head } from '@inertiajs/react';
import { ArrowLeftRight, Eye } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Badge } from '@/Components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import type { KodeFlag, SimpananKodeRow } from '@/types/models';

const FLAGS: Array<{ key: KodeFlag; label: string }> = [
    { key: 'setoran', label: 'Setoran' },
    { key: 'tarikan', label: 'Tarikan' },
    { key: 'transfer', label: 'Transfer' },
    { key: 'pokok', label: 'Simpanan Pokok' },
    { key: 'wajib', label: 'Simpanan Wajib' },
    { key: 'sukarela', label: 'Simpanan Sukarela' },
    { key: 'pinjaman', label: 'Pinjaman' },
    { key: 'saham', label: 'Saham' },
    { key: 'pokok_pinjaman', label: 'Pokok Pinjaman' },
    { key: 'rencana', label: 'Rencana' },
];

interface Props {
    kodeData: SimpananKodeRow;
}

function InfoRow({ label, value }: { label: string; value: React.ReactNode }) {
    return (
        <div className="flex flex-col gap-1 py-2.5 sm:flex-row sm:items-center">
            <span className="w-44 shrink-0 text-sm font-medium text-muted-foreground">{label}</span>
            <span className="text-sm">{value ?? '—'}</span>
        </div>
    );
}

export default function KodetransaksiShow({ kodeData: kode }: Props) {
    const activeFlags = FLAGS.filter(({ key }) => kode[key]);

    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${kode.nama}`} />

            <PageHeader
                title="Detail Kode Transaksi"
                description="Informasi lengkap kode transaksi."
                icon={Eye}
                backHref={route('superadmin.simpanan.kode-transaksi')}
            />

            <Card className="max-w-2xl">
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <ArrowLeftRight className="size-4 text-brand-600" />
                        {kode.nama}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <Separator className="mb-2" />
                    <InfoRow
                        label="Kode"
                        value={
                            <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                {kode.kode}
                            </span>
                        }
                    />
                    <InfoRow
                        label="Account Debet"
                        value={
                            kode.debetAccount
                                ? `${kode.debetAccount.no_account} — ${kode.debetAccount.nama}`
                                : '—'
                        }
                    />
                    <InfoRow
                        label="Account Kredit"
                        value={
                            kode.kreditAccount
                                ? `${kode.kreditAccount.no_account} — ${kode.kreditAccount.nama}`
                                : '—'
                        }
                    />
                    <InfoRow
                        label="Flag Aktif"
                        value={
                            activeFlags.length > 0 ? (
                                <div className="flex flex-wrap gap-1.5">
                                    {activeFlags.map(({ key, label }) => (
                                        <Badge key={key} variant="success">
                                            {label}
                                        </Badge>
                                    ))}
                                </div>
                            ) : (
                                'Tidak ada'
                            )
                        }
                    />
                    <InfoRow label="Keterangan" value={kode.keterangan ?? '—'} />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
