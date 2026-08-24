import { Head, Link, useForm } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { ArrowLeft, DollarSign, Calculator } from 'lucide-react';
import { format } from 'date-fns';
import { id } from 'date-fns/locale';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { DenominationCalculator } from '@/Components/DenominationCalculator';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';

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
}

interface Props {
    kasHarian: KasHarian;
}

interface FormData {
    tanggal: string;
    kas_awal: string;
    kas_masuk: string;
    kas_keluar: string;
}

export default function Edit({ kasHarian }: Props) {
    const { data, setData, errors, put, processing, reset } = useForm<FormData>({
        tanggal: format(new Date(kasHarian.tanggal), 'yyyy-MM-dd'),
        kas_awal: kasHarian.kas_awal.toString(),
        kas_masuk: kasHarian.kas_masuk.toString(),
        kas_keluar: kasHarian.kas_keluar.toString(),
    });

    const [kasAkhir, setKasAkhir] = useState(kasHarian.kas_akhir);

    // Safe number parsing function
    const safeParse = (value: string): number => {
        const parsed = parseFloat(value.replace(/[^0-9.-]/g, ''));
        return isNaN(parsed) ? 0 : parsed;
    };

    useEffect(() => {
        const awal = safeParse(data.kas_awal);
        const masuk = safeParse(data.kas_masuk);
        const keluar = safeParse(data.kas_keluar);
        setKasAkhir(awal + masuk - keluar);
    }, [data.kas_awal, data.kas_masuk, data.kas_keluar]);

    const formatCurrencyInput = (value: string) => {
        const num = safeParse(value);
        return num.toString();
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        put(route('superadmin.kas-harian.update', kasHarian.id), {
            onSuccess: () => reset(),
            onError: () => {},
        });
    };

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

    return (
        <AuthenticatedLayout>
            <Head title="Edit Kas Harian - Superadmin" />

            <PageHeader
                title="Edit Kas Harian"
                description={`Perbarui data kas harian tanggal ${formatDate(kasHarian.tanggal)}`}
                icon={DollarSign}
                backHref={route('superadmin.kas-harian')}
            />

            <form onSubmit={handleSubmit} className="space-y-6">
                {/* Input Fields */}
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <DollarSign className="h-5 w-5" />
                            Data Kas Harian
                        </CardTitle>
                        <CardDescription>
                            Perbarui nominal kas harian
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="tanggal">Tanggal *</Label>
                                <Input
                                    id="tanggal"
                                    type="date"
                                    value={data.tanggal}
                                    onChange={(e) => setData('tanggal', e.target.value)}
                                    className={errors.tanggal ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''}
                                    required
                                />
                                {errors.tanggal && <p className="text-sm text-red-500">{errors.tanggal}</p>}
                            </div>

                            <div className="space-y-2">
                                <DenominationCalculator
                                    id="kas_awal"
                                    label="Kas Awal (Rp) *"
                                    value={data.kas_awal}
                                    onChange={(value) => setData('kas_awal', formatCurrencyInput(value))}
                                    error={errors.kas_awal}
                                    required
                                />
                            </div>

                            <div className="space-y-2">
                                <DenominationCalculator
                                    id="kas_masuk"
                                    label="Kas Masuk (Rp) *"
                                    value={data.kas_masuk}
                                    onChange={(value) => setData('kas_masuk', formatCurrencyInput(value))}
                                    error={errors.kas_masuk}
                                    required
                                />
                            </div>

                            <div className="space-y-2">
                                <DenominationCalculator
                                    id="kas_keluar"
                                    label="Kas Keluar (Rp) *"
                                    value={data.kas_keluar}
                                    onChange={(value) => setData('kas_keluar', formatCurrencyInput(value))}
                                    error={errors.kas_keluar}
                                    required
                                />
                            </div>
                        </div>

                        {/* Kas Akhir Preview */}
                        <div className="bg-muted/50 rounded-lg p-4 border mt-2">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-2">
                                    <Calculator className="h-5 w-5 text-muted-foreground" />
                                    <span className="font-medium text-lg">Kas Akhir (Otomatis)</span>
                                </div>
                                <div className="text-right">
                                    <p className="text-2xl font-bold font-mono tabular-nums text-purple-600">
                                        {formatCurrency(kasAkhir)}
                                    </p>
                                    <p className="text-xs text-muted-foreground">
                                        Kas Awal + Kas Masuk - Kas Keluar
                                    </p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Actions */}
                <div className="flex justify-end gap-3 pt-4 border-t">
                    <Link href={route('superadmin.kas-harian')}>
                        <Button type="button" variant="outline">
                            Batal
                        </Button>
                    </Link>
                    <Button type="submit" disabled={processing} className="gap-2">
                        {processing ? 'Menyimpan...' : 'Perbarui Kas Harian'}
                    </Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}