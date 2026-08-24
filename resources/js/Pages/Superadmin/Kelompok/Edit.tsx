import { useEffect, useRef, useState } from 'react';
import { Link, useForm, Head} from '@inertiajs/react';
import { LoaderCircle, Pencil, Search } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import type { KelompokRow } from '@/types/models';

interface UserOption {
    id: number;
    nama: string;
}

interface FormValues {
    kode: string;
    nama: string;
    ketua_id: string;
}

function KetuaCombobox({
    value,
    onChange,
    error,
    initialLabel,
}: {
    value: string;
    onChange: (id: string) => void;
    error?: string;
    initialLabel?: string;
}) {
    const [query, setQuery] = useState(initialLabel ?? '');
    const [options, setOptions] = useState<UserOption[]>([]);
    const [open, setOpen] = useState(false);
    const boxRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (!open) return;
        const controller = new AbortController();
        const timer = setTimeout(() => {
            fetch(route('superadmin.kelompok.search-users', { q: query }), {
                signal: controller.signal,
            })
                .then((r) => r.json())
                .then(setOptions)
                .catch(() => {});
        }, 300);

        return () => {
            clearTimeout(timer);
            controller.abort();
        };
    }, [query, open]);

    useEffect(() => {
        const close = (e: MouseEvent) => {
            if (!boxRef.current?.contains(e.target as Node)) setOpen(false);
        };
        document.addEventListener('mousedown', close);
        return () => document.removeEventListener('mousedown', close);
    }, []);

    return (
        <div className="relative" ref={boxRef}>
            <div className="relative">
                <Search className="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    value={query}
                    onChange={(e) => {
                        setQuery(e.target.value);
                        onChange('');
                        setOpen(true);
                    }}
                    onFocus={() => setOpen(true)}
                    placeholder="Cari user untuk dijadikan ketua…"
                    className="pl-9"
                    autoComplete="off"
                />
            </div>
            {value && (
                <p className="mt-1.5 text-xs text-emerald-600">✓ Ketua terpilih (ID: {value})</p>
            )}
            {open && options.length > 0 && (
                <div className="absolute z-40 mt-1 w-full overflow-hidden rounded-lg border bg-popover shadow-lg">
                    {options.map((u) => (
                        <button
                            key={u.id}
                            type="button"
                            onClick={() => {
                                setQuery(u.nama);
                                onChange(String(u.id));
                                setOpen(false);
                            }}
                            className="block w-full px-3 py-2 text-left text-sm transition hover:bg-muted"
                        >
                            {u.nama}
                        </button>
                    ))}
                </div>
            )}
            {error && <p className="mt-1 text-sm text-brand-600">{error}</p>}
        </div>
    );
}

interface Props {
    kelompokData: KelompokRow;
}

export default function KelompokEdit({ kelompokData }: Props) {
    const form = useForm<FormValues>({
        kode: kelompokData.kode,
        nama: kelompokData.nama,
        ketua_id: kelompokData.ketua_id ? String(kelompokData.ketua_id) : '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        form.put(route('superadmin.kelompok.update', kelompokData.id));
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Edit ${kelompokData.nama}`} />

            <PageHeader
                title="Edit Kelompok"
                description={`Perbarui data ${kelompokData.nama}.`}
                icon={Pencil}
                backHref={route('superadmin.kelompok')}
            />

            <form onSubmit={submit} className="max-w-2xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Data Kelompok</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-5">
                        <div className="grid gap-5 sm:grid-cols-2">
                            <div className="space-y-2">
                                <Label htmlFor="kode">
                                    Kode <span className="text-brand-600">*</span>
                                </Label>
                                <Input
                                    id="kode"
                                    value={form.data.kode}
                                    onChange={(e) => form.setData('kode', e.target.value)}
                                    className="font-mono"
                                />
                                {form.errors.kode && (
                                    <p className="text-sm text-brand-600">{form.errors.kode}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="nama">
                                    Nama Kelompok <span className="text-brand-600">*</span>
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
                        </div>

                        <div className="space-y-2">
                            <Label>Ketua (opsional)</Label>
                            <KetuaCombobox
                                value={form.data.ketua_id}
                                onChange={(id) => form.setData('ketua_id', id)}
                                error={form.errors.ketua_id}
                                initialLabel={kelompokData.ketua?.nama}
                            />
                        </div>
                    </CardContent>
                </Card>

                <div className="mt-5 flex items-center justify-end gap-3">
                    <Button variant="outline" asChild>
                        <Link href={route('superadmin.kelompok')}>Kembali</Link>
                    </Button>
                    <Button
                        type="submit"
                        disabled={form.processing}
                        className="bg-brand-600 hover:bg-brand-500"
                    >
                        {form.processing && <LoaderCircle className="animate-spin" />}
                        Perbarui Kelompok
                    </Button>
                </div>
            </form>
        </AuthenticatedLayout>
    );
}
