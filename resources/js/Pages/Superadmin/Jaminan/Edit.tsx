import { Link, useForm, Head} from '@inertiajs/react';
import { LoaderCircle, Pencil, Trash2 } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import type { JaminanRow } from '@/types/jaminan';

interface FormValues {
    nama: string;
    detail: string[];
}

interface Props {
    jaminanData: JaminanRow;
}

export default function JaminanEdit({ jaminanData }: Props) {
    const form = useForm<FormValues>({
        nama: jaminanData.nama,
        // Selalu sediakan satu baris kosong di akhir untuk input baru
        detail: [...(jaminanData.details ?? []).map((d) => d.detail), ''],
    });

    const setDetail = (idx: number, value: string) => {
        const isLastRowFilled =
            idx === form.data.detail.length - 1 && value.trim() !== '';

        form.setData((data) => ({
            ...data,
            detail: data.detail.map((d, i) => (i === idx ? value : d)),
            ...(isLastRowFilled ? { detail: [...data.detail, ''] } : {}),
        }));
    };

    const removeDetail = (idx: number) => {
        form.setData((data) => {
            const next = data.detail.filter((_, i) => i !== idx);
            return { ...data, detail: next.length ? next : [''] };
        });
    };

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.put(route('superadmin.pinjaman.jaminan.update', jaminanData.id));
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Edit ${jaminanData.nama}`} />

            <PageHeader
                title="Edit Jaminan"
                description={`Perbarui data ${jaminanData.nama}.`}
                icon={Pencil}
                backHref={route('superadmin.pinjaman.jaminan')}
            />

            <form onSubmit={submit} className="max-w-2xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Data Jaminan</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-5">
                        <div className="space-y-2">
                            <Label htmlFor="nama">
                                Nama Jaminan <span className="text-brand-600">*</span>
                            </Label>
                            <Input
                                id="nama"
                                value={form.data.nama}
                                onChange={(e) => form.setData('nama', e.target.value)}
                            />
                            {form.errors.nama && (
                                <p className="text-sm text-brand-600">{form.errors.nama}</p>
                            )}
                        </div>

                        <div className="space-y-2">
                            <Label>
                                Detail Jaminan <span className="text-brand-600">*</span>
                            </Label>
                            <div className="space-y-2">
                                {form.data.detail.map((d, i) => (
                                    <div key={i} className="flex items-center gap-2">
                                        <Input
                                            value={d}
                                            onChange={(e) => setDetail(i, e.target.value)}
                                            placeholder={`Detail ${i + 1}`}
                                        />
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon"
                                            className="size-9 shrink-0 text-muted-foreground hover:text-destructive"
                                            onClick={() => removeDetail(i)}
                                            aria-label={`Hapus detail ${i + 1}`}
                                        >
                                            <Trash2 className="size-4" />
                                        </Button>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div className="mt-5 flex items-center justify-end gap-3">
                    <Button variant="outline" asChild>
                        <Link href={route('superadmin.pinjaman.jaminan')}>Kembali</Link>
                    </Button>
                    <Button
                        type="submit"
                        disabled={form.processing}
                        className="bg-brand-600 hover:bg-brand-500"
                    >
                        {form.processing && <LoaderCircle className="animate-spin" />}
                        Perbarui Jaminan
                    </Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
