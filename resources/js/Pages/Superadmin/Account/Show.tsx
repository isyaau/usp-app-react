import { Head } from '@inertiajs/react';
import { BookOpen, Eye } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import type { AccountRow } from '@/types/models';

interface Props {
    accountData: AccountRow;
}

function InfoRow({ label, value }: { label: string; value: React.ReactNode }) {
    return (
        <div className="flex flex-col gap-1 py-2.5 sm:flex-row sm:items-center">
            <span className="w-40 shrink-0 text-sm font-medium text-muted-foreground">{label}</span>
            <span className="text-sm">{value ?? '—'}</span>
        </div>
    );
}

export default function AccountShow({ accountData: account }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${account.nama}`} />

            <PageHeader
                title="Detail Account"
                description="Informasi lengkap akun."
                icon={Eye}
                backHref={route('superadmin.account')}
            />

            <Card className="max-w-2xl">
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <BookOpen className="size-4 text-brand-600" />
                        {account.nama}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <Separator className="mb-2" />
                    <InfoRow
                        label="No. Account"
                        value={
                            <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                {account.no_account}
                            </span>
                        }
                    />
                    <InfoRow
                        label="Header"
                        value={
                            account.header
                                ? `${account.header.no_header} — ${account.header.nama}`
                                : '—'
                        }
                    />
                    <InfoRow
                        label="Tipe"
                        value={
                            <span
                                className={`rounded-full px-2.5 py-0.5 text-xs font-semibold ${
                                    account.tipe === 'Debet'
                                        ? 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400'
                                        : 'bg-sky-500/10 text-sky-700 dark:text-sky-400'
                                }`}
                            >
                                {account.tipe}
                            </span>
                        }
                    />
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}
