import { Head, Link, router } from '@inertiajs/react';
import { ArrowLeft, DollarSign, ArrowDownLeft, ArrowUpRight, Calendar, User, Clock, Edit, Trash2 } from 'lucide-react';
import { format } from 'date-fns';
import { id } from 'date-fns/locale';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { ConfirmDelete } from '@/Components/ConfirmDelete';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';

interface KasHarian {
    id: number;
    tanggal: string;
    kas_awal: number;
    kas_masuk: number;
    kas_keluar: number;
    kas_akhir: number;
    user: {
        name: string;
    };
    created_at: string;
    updated_at: string;
}

interface Props {
    kasHarian: KasHarian;
}

export default function Show({ kasHarian }: Props) {
    const formatCurrency = (value: number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    };

    const formatDate = (dateString: string) => {
        return format(new Date(dateString), 'dd MMM yyyy', { locale: id });
    };

    const formatDateTime = (dateString: string) => {
        return format(new Date(dateString), 'dd MMM yyyy HH:mm', { locale: id });
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Detail Kas Harian - ${formatDate(kasHarian.tanggal)}`} />

            <PageHeader
                title="Detail Kas Harian"
                description={`Tanggal ${formatDate(kasHarian.tanggal)}`}
                icon={DollarSign}
                backHref={route('superadmin.kas-harian')}
            >
                <div className="flex items-center gap-2">
                    <Link href={route('superadmin.kas-harian.edit', kasHarian.id)}>
                        <Button variant="outline" className="gap-2">
                            <Edit className="size-4" />
                            Edit
                        </Button>
                    </Link>
                    <ConfirmDelete
                        routeName="superadmin.kas-harian.destroy"
                        id={kasHarian.id}
                        label={formatDate(kasHarian.tanggal)}
                        description={`Data kas harian tanggal ${formatDate(kasHarian.tanggal)} akan dihapus permanen.`}
                    >
                        <Button variant="destructive" className="gap-2">
                            <Trash2 className="size-4" />
                            Hapus
                        </Button>
                    </ConfirmDelete>
                </div>
            </PageHeader>

            {/* Detail Cards */}
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader className="pb-2">
                        <CardTitle className="text-sm font-medium text-muted-foreground flex items-center gap-2">
                            <DollarSign className="h-4 w-4" />
                            Kas Awal
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p className="text-2xl font-bold font-mono tabular-nums">{formatCurrency(kasHarian.kas_awal)}</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader className="pb-2">
                        <CardTitle className="text-sm font-medium text-muted-foreground flex items-center gap-2 text-green-600">
                            <ArrowDownLeft className="h-4 w-4" />
                            Kas Masuk
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p className="text-2xl font-bold font-mono tabular-nums text-green-600">{formatCurrency(kasHarian.kas_masuk)}</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader className="pb-2">
                        <CardTitle className="text-sm font-medium text-muted-foreground flex items-center gap-2 text-red-600">
                            <ArrowUpRight className="h-4 w-4" />
                            Kas Keluar
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p className="text-2xl font-bold font-mono tabular-nums text-red-600">{formatCurrency(kasHarian.kas_keluar)}</p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader className="pb-2">
                        <CardTitle className="text-sm font-medium text-muted-foreground flex items-center gap-2">
                            <DollarSign className="h-4 w-4" />
                            Kas Akhir
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p className="text-3xl font-bold font-mono tabular-nums text-purple-600">{formatCurrency(kasHarian.kas_akhir)}</p>
                    </CardContent>
                </Card>
            </div>

            {/* Detail Info */}
            <div className="grid gap-4 sm:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Calendar className="h-5 w-5" />
                            Informasi Tanggal
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-3">
                        <div className="flex justify-between py-2 border-b">
                            <span className="text-muted-foreground">Tanggal Kas</span>
                            <span className="font-medium">{formatDate(kasHarian.tanggal)}</span>
                        </div>
                        <div className="flex justify-between py-2 border-b">
                            <span className="text-muted-foreground">Dibuat</span>
                            <span className="font-medium">{formatDateTime(kasHarian.created_at)}</span>
                        </div>
                        <div className="flex justify-between py-2">
                            <span className="text-muted-foreground">Diperbarui</span>
                            <span className="font-medium">{formatDateTime(kasHarian.updated_at)}</span>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <User className="h-5 w-5" />
                            Informasi Petugas
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-3">
                        <div className="flex justify-between py-2 border-b">
                            <span className="text-muted-foreground">Petugas</span>
                            <span className="font-medium">{kasHarian.user?.name ?? '-'}</span>
                        </div>
                        <div className="flex justify-between py-2">
                            <span className="text-muted-foreground">ID Record</span>
                            <span className="font-mono text-sm">#{kasHarian.id}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>

            {/* Calculation Formula */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center gap-2">
                        <DollarSign className="h-5 w-5" />
                        Rumus Perhitungan
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div className="bg-muted/50 rounded-lg p-4 font-mono space-y-2">
                        <div className="flex justify-between text-sm">
                            <span>Kas Awal</span>
                            <span>{formatCurrency(kasHarian.kas_awal)}</span>
                        </div>
                        <div className="flex justify-between text-sm text-green-600">
                            <span>+ Kas Masuk</span>
                            <span>{formatCurrency(kasHarian.kas_masuk)}</span>
                        </div>
                        <div className="flex justify-between text-sm text-red-600">
                            <span>- Kas Keluar</span>
                            <span>{formatCurrency(kasHarian.kas_keluar)}</span>
                        </div>
                        <Separator />
                        <div className="flex justify-between text-lg font-bold text-purple-600">
                            <span>= Kas Akhir</span>
                            <span>{formatCurrency(kasHarian.kas_akhir)}</span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </AuthenticatedLayout>
    );
}