import { useState } from 'react';
import { Head } from '@inertiajs/react';
import { CalendarClock, LoaderCircle, Search } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { LookupModal } from '@/Components/LookupModal';
import { PinjamanForm } from '@/Pages/Superadmin/Pinjaman/PinjamanForm';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import type {
    PinjamanAnggotaRow,
    PinjamanEditRow,
    PinjamanJenisRow,
    PinjamanMarketingRow,
    PinjamanAccountRow,
    PinjamanJaminanTypeRow,
    PinjamanSimpananRow,
    PinjamanKodeTarikanRow,
    PinjamanSektorRow,
    PinjamanSuratOption,
} from '@/types/models';

interface AnggotaOption extends PinjamanAnggotaRow {}

interface PinjamanLite {
    id: number;
    no_pinjaman: string;
    plafon: number;
    sisa_pokok: number;
}

interface Props {
    anggotaOptions: AnggotaOption[];
    jenisOptions: PinjamanJenisRow[];
    marketingOptions: PinjamanMarketingRow[];
    accountOptions: PinjamanAccountRow[];
    jaminanTypes: PinjamanJaminanTypeRow[];
    simpananOptions: PinjamanSimpananRow[];
    kodeTarikanOptions: PinjamanKodeTarikanRow[];
    sektorOptions: PinjamanSektorRow[];
    bayarPokokPerOptions: string[];
    suratOptions: PinjamanSuratOption[];
    satuanOptions: { value: string; label: string }[];
}

const rupiah = (v: number) => `Rp ${Number(v ?? 0).toLocaleString('id-ID')}`;

export default function Create({
    anggotaOptions,
    jenisOptions,
    marketingOptions,
    accountOptions,
    jaminanTypes,
    simpananOptions,
    kodeTarikanOptions,
    sektorOptions,
    bayarPokokPerOptions,
    suratOptions,
    satuanOptions,
}: Props) {
    const [anggotaId, setAnggotaId] = useState<string>('');
    const [pinjamanList, setPinjamanList] = useState<PinjamanLite[]>([]);
    const [anggotaOpen, setAnggotaOpen] = useState(false);
    const [initial, setInitial] = useState<PinjamanEditRow | null>(null);
    const [loading, setLoading] = useState(false);

    const pickAnggota = (a: AnggotaOption) => {
        setAnggotaId(String(a.id));
        setInitial(null);
        setPinjamanList([]);
        fetch(route('superadmin.pinjaman.jadwal-ulang.pinjaman-by-anggota', a.id))
            .then((r) => r.json())
            .then(setPinjamanList)
            .catch(() => setPinjamanList([]));
        setAnggotaOpen(false);
    };

    const pickPinjaman = (v: string) => {
        setLoading(true);
        setInitial(null);
        fetch(route('superadmin.pinjaman.jadwal-ulang.pinjaman-asal', v))
            .then((r) => r.json())
            .then((row) => setInitial(row))
            .catch(() => setInitial(null))
            .finally(() => setLoading(false));
    };

    const selectedAnggota = anggotaOptions.find((a) => String(a.id) === anggotaId);

    return (
        <AuthenticatedLayout>
            <Head title="Tambah Jadwal Ulang Pinjaman" />

            <PageHeader
                title="Tambah Jadwal Ulang Pinjaman"
                description="Jadwalkan ulang pinjaman dengan data form pinjaman (6 tab)."
                icon={CalendarClock}
                backHref={route('superadmin.pinjaman.jadwal-ulang')}
            />

            {!initial && (
                <Card className="mb-5">
                    <CardHeader>
                        <CardTitle>Pilih Pinjaman Asal</CardTitle>
                        <CardDescription>Pilih pinjaman yang akan dijadwal ulang untuk mengisi data form otomatis.</CardDescription>
                    </CardHeader>
                    <CardContent className="grid gap-4 sm:grid-cols-2">
                        <div className="space-y-2">
                            <Label>Anggota</Label>
                            <div className="flex gap-2">
                                <Input
                                    value={selectedAnggota ? `${selectedAnggota.no_anggota} — ${selectedAnggota.nama}` : ''}
                                    placeholder="Pilih anggota…" readOnly onClick={() => setAnggotaOpen(true)} className="cursor-pointer"
                                />
                                <Button type="button" variant="outline" size="icon" onClick={() => setAnggotaOpen(true)}><Search /></Button>
                            </div>
                        </div>
                        <div className="space-y-2">
                            <Label>Pinjaman Asal</Label>
                            <Select value="" onValueChange={pickPinjaman} disabled={!anggotaId}>
                                <SelectTrigger><SelectValue placeholder={loading ? 'Memuat…' : 'Pilih pinjaman'} /></SelectTrigger>
                                <SelectContent>
                                    {pinjamanList.map((p) => (
                                        <SelectItem key={p.id} value={String(p.id)}>
                                            {p.no_pinjaman} (sisa {rupiah(p.sisa_pokok)})
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>
                    </CardContent>
                </Card>
            )}

            {loading && (
                <div className="flex items-center justify-center gap-2 rounded-lg border bg-card p-10 text-muted-foreground">
                    <LoaderCircle className="size-5 animate-spin" /> Memuat data pinjaman asal…
                </div>
            )}

            {initial && (
                <PinjamanForm
                    key={initial.id}
                    initial={initial}
                    anggotaOptions={anggotaOptions}
                    jenisOptions={jenisOptions}
                    marketingOptions={marketingOptions}
                    accountOptions={accountOptions}
                    jaminanTypes={jaminanTypes}
                    simpananOptions={simpananOptions}
                    kodeTarikanOptions={kodeTarikanOptions}
                    sektorOptions={sektorOptions}
                    bayarPokokPerOptions={bayarPokokPerOptions}
                    suratOptions={suratOptions}
                    satuanOptions={satuanOptions}
                    submitUrl={route('superadmin.pinjaman.jadwal-ulang.store')}
                    submitMethod="post"
                    processingLabel="Menyimpan…"
                    reschedule
                />
            )}

            <LookupModal<AnggotaOption>
                open={anggotaOpen}
                onOpenChange={setAnggotaOpen}
                title="Pilih Anggota"
                columns={[
                    { key: 'no_anggota', header: 'No. Anggota', render: (a) => <span className="font-mono text-xs">{a.no_anggota}</span> },
                    { key: 'nama', header: 'Nama' },
                ]}
                rows={anggotaOptions}
                onSelect={pickAnggota}
                getSearchText={(a) => `${a.no_anggota} ${a.nama}`}
                searchPlaceholder="Cari no. anggota / nama…"
            />
        </AuthenticatedLayout>
    );
}
