import { Head } from '@inertiajs/react';
import { CreditCard, Printer } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { ReportFilterBar } from '@/Components/ReportFilterBar';
import { Button } from '@/Components/ui/button';
import { Card } from '@/Components/ui/card';
import type { AnggotaRow, Paginated } from '@/types/models';

interface Props {
    data: Paginated<AnggotaRow>;
    filters: Record<string, string>;
    kelompoks: Array<{ id: number; kode: string; nama: string }>;
    kantors: Array<{ id: number; kode: string; nama_kantor: string }>;
    variantTitle: string;
}

export default function KartuAnggota({ data, filters, kelompoks, kantors, variantTitle }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Kartu Anggota'} />

            <PageHeader
                title={variantTitle || 'Kartu Anggota'}
                description="Kartu identitas anggota koperasi."
                icon={CreditCard}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <ReportFilterBar
                        routeName="superadmin.laporan-cs.anggota.kartu"
                        filters={filters}
                        kelompoks={kelompoks}
                        kantors={kantors}
                        showKelompok
                        showKantor
                    />
                </div>

                <div className="px-5">
                    {data.data.length === 0 && (
                        <div className="flex h-32 items-center justify-center text-muted-foreground">
                            Tidak ada data anggota.
                        </div>
                    )}

                    <div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        {data.data.map((item) => (
                            <div
                                key={item.id}
                                className="flex items-start justify-between rounded-lg border bg-card p-4 shadow-sm"
                            >
                                <div className="space-y-1.5">
                                    <div className="flex items-center gap-2">
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs font-medium">
                                            {item.no_anggota}
                                        </span>
                                    </div>
                                    <h3 className="text-base font-semibold">{item.nama}</h3>
                                    <div className="space-y-0.5 text-sm text-muted-foreground">
                                        <p>
                                            <span className="font-medium text-foreground">Kelompok:</span>{' '}
                                            {item.kelompok?.nama ?? '—'}
                                        </p>
                                        <p>
                                            <span className="font-medium text-foreground">Kantor:</span>{' '}
                                            {item.kantor?.nama_kantor ?? '—'}
                                        </p>
                                        <p>
                                            <span className="font-medium text-foreground">Alamat:</span>{' '}
                                            {item.alamat ?? '—'}
                                        </p>
                                        <p>
                                            <span className="font-medium text-foreground">Telepon:</span>{' '}
                                            {item.telepon ?? item.no_hp ?? '—'}
                                        </p>
                                    </div>
                                </div>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={() =>
                                        window.open(
                                            route('superadmin.laporan-cs.anggota.kartu.cetak', item.id),
                                            '_blank',
                                        )
                                    }
                                >
                                    <Printer className="size-4" />
                                    Cetak
                                </Button>
                            </div>
                        ))}
                    </div>
                </div>

                <div className="border-t px-5 pt-4">
                    <Pagination
                        links={data.links}
                        currentPage={data.current_page}
                        lastPage={data.last_page}
                        from={data.from}
                        to={data.to}
                        total={data.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
