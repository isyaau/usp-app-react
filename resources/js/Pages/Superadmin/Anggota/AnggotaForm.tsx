import { useState } from 'react';
import { LoaderCircle, Save } from 'lucide-react';

import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Switch } from '@/Components/ui/switch';
import { Textarea } from '@/Components/ui/textarea';
import { WilayahSelect } from '@/Components/WilayahSelect';

export const LIST_AGAMA = [
    'ISLAM',
    'KRISTEN',
    'KATOLIK',
    'HINDU',
    'BUDDHA',
    'KONGHUCU',
];

export const LIST_PENDIDIKAN = [
    'SD',
    'SMP',
    'SMA/SMK',
    'D3',
    'S1',
    'S2',
    'S3',
];

export const LIST_PEKERJAAN = [
    'PELAJAR / MAHASISWA',
    'PNS',
    'TNI / POLRI',
    'KARYAWAN SWASTA',
    'WIRASWASTA',
    'PETANI',
    'NELAYAN',
    'BURUH',
    'GURU / DOSEN',
    'TENAGA KESEHATAN',
    'IBU RUMAH TANGGA',
    'BELUM BEKERJA',
    'LAINNYA',
];

export const LIST_PERKAWINAN = [
    'Belum Kawin',
    'Kawin',
    'Cerai Hidup',
    'Cerai Mati',
];

export interface AnggotaFormValues {
    no_anggota: string;
    pin: string;
    nama: string;
    kelompok_id: string;
    kantor_id: string;
    alamat: string;
    provinsi_id: string;
    kota_id: string;
    kecamatan_id: string;
    kelurahan_id: string;
    email: string;
    tempat_lahir: string;
    tgl_lahir: string;
    jenis_kelamin: string;
    agama: string;
    pekerjaan: string;
    pendidikan: string;
    status_perkawinan: string;
    pasangan: string;
    telepon: string;
    no_hp: string;
    jenis_identitas: string;
    no_identitas: string;
    npwp: string;
    ibu: string;
    pengurus: boolean;
    pengurus_jabatan: string;
    tgl_pengurus_diangkat: string;
    tgl_pengurus_berhenti: string;
    pengurus_berhenti: string;
    pengawas: boolean;
    pengawas_jabatan: string;
    tgl_pengawas_diangkat: string;
    tgl_pengawas_berhenti: string;
    pengawas_berhenti: string;
    waris1: string;
    hubungan_waris1: string;
    waris2: string;
    hubungan_waris2: string;
    status: boolean;
    tgl_anggota_berhenti: string;
    anggota_berhenti: string;
}

interface Props {
    values: AnggotaFormValues;
    setData: (key: keyof AnggotaFormValues, value: unknown) => void;
    errors: Partial<Record<string, string>>;
    processing: boolean;
    isEdit: boolean;
    fotoName?: string | null;
    onFotoChange: (file: File | null) => void;
    fotoError?: string;
    onSubmit: (e: React.FormEvent) => void;
    backHref: string;
    submitLabel: string;
    optionKelompok: { id: number; nama: string }[];
    optionKantor: { id: number; nama_kantor: string }[];
}

const TABS = [
    { id: 'keanggotaan', label: 'Keanggotaan' },
    { id: 'pribadi', label: 'Data Pribadi' },
    { id: 'pengurus', label: 'Pengurus & Pengawas' },
    { id: 'waris', label: 'Ahli Waris' },
    { id: 'status', label: 'Status' },
] as const;

type TabId = (typeof TABS)[number]['id'];

export function AnggotaForm({
    values,
    setData,
    errors,
    processing,
    isEdit,
    fotoName,
    onFotoChange,
    fotoError,
    onSubmit,
    backHref,
    submitLabel,
    optionKelompok,
    optionKantor,
}: Props) {
    const [tab, setTab] = useState<TabId>('keanggotaan');

    const err = (field: string) => errors[field];

    return (
        <form onSubmit={onSubmit} className="space-y-5">
            {/* ============================ Tab nav ============================ */}
            <div
                className="flex flex-wrap gap-1 rounded-xl border bg-card p-1"
                role="tablist"
            >
                {TABS.map((t) => (
                    <button
                        key={t.id}
                        type="button"
                        role="tab"
                        aria-selected={tab === t.id}
                        onClick={() => setTab(t.id)}
                        className={`rounded-lg px-4 py-2 text-sm font-medium transition ${
                            tab === t.id
                                ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30'
                                : 'text-muted-foreground hover:bg-muted hover:text-foreground'
                        }`}
                    >
                        {t.label}
                    </button>
                ))}
            </div>

            {/* ========================= Tab Keanggotaan ======================= */}
            {tab === 'keanggotaan' && (
                <Card>
                    <CardHeader>
                        <CardTitle>Data Keanggotaan</CardTitle>
                        <CardDescription>
                            Identitas utama anggota dan lokasi domisili.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-5">
                        <div className="grid gap-5 sm:grid-cols-3">
                            <Field
                                label="No Anggota"
                                required
                                error={err('no_anggota')}
                            >
                                <Input
                                    id="no_anggota"
                                    value={values.no_anggota}
                                    onChange={(e) =>
                                        setData('no_anggota', e.target.value)
                                    }
                                    placeholder="AGT-0001"
                                    className="font-mono"
                                />
                            </Field>
                            <Field label="PIN" required error={err('pin')}>
                                <Input
                                    id="pin"
                                    type="password"
                                    value={values.pin}
                                    onChange={(e) =>
                                        setData('pin', e.target.value)
                                    }
                                    placeholder="PIN transaksi"
                                />
                            </Field>
                            <Field label="Nama Lengkap" required error={err('nama')}>
                                <Input
                                    id="nama"
                                    value={values.nama}
                                    onChange={(e) =>
                                        setData('nama', e.target.value)
                                    }
                                    placeholder="Nama sesuai identitas"
                                />
                            </Field>
                        </div>

                        <div className="grid gap-5 sm:grid-cols-2">
                            <Field label="Kelompok" error={err('kelompok_id')}>
                                <Select
                                    value={values.kelompok_id || undefined}
                                    onValueChange={(v) =>
                                        setData('kelompok_id', v)
                                    }
                                >
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="-- Pilih Kelompok --" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {optionKelompok.map((k) => (
                                            <SelectItem
                                                key={k.id}
                                                value={String(k.id)}
                                            >
                                                {k.nama}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </Field>
                            <Field label="Kantor" error={err('kantor_id')}>
                                <Select
                                    value={values.kantor_id || undefined}
                                    onValueChange={(v) =>
                                        setData('kantor_id', v)
                                    }
                                >
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="-- Pilih Kantor --" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {optionKantor.map((k) => (
                                            <SelectItem
                                                key={k.id}
                                                value={String(k.id)}
                                            >
                                                {k.nama_kantor}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </Field>
                        </div>

                        <Field label="Alamat" required error={err('alamat')}>
                            <Textarea
                                id="alamat"
                                rows={2}
                                value={values.alamat}
                                onChange={(e) =>
                                    setData('alamat', e.target.value)
                                }
                                placeholder="Alamat lengkap domisili"
                            />
                        </Field>

                        <WilayahSelect
                            values={{
                                provinsi_id: values.provinsi_id,
                                kota_id: values.kota_id,
                                kecamatan_id: values.kecamatan_id,
                                kelurahan_id: values.kelurahan_id,
                            }}
                            onChange={(field, code) => setData(field, code)}
                            errors={errors}
                        />

                        <Field label="Email" required error={err('email')}>
                            <Input
                                id="email"
                                type="email"
                                value={values.email}
                                onChange={(e) =>
                                    setData('email', e.target.value)
                                }
                                placeholder="nama@email.com"
                            />
                        </Field>
                    </CardContent>
                </Card>
            )}

            {/* ========================== Tab Pribadi ========================== */}
            {tab === 'pribadi' && (
                <Card>
                    <CardHeader>
                        <CardTitle>Data Pribadi</CardTitle>
                        <CardDescription>
                            Data diri, kontak, dan identitas resmi anggota.
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-5">
                        <div className="grid gap-5 sm:grid-cols-3">
                            <Field
                                label="Tempat Lahir"
                                required
                                error={err('tempat_lahir')}
                            >
                                <Input
                                    id="tempat_lahir"
                                    value={values.tempat_lahir}
                                    onChange={(e) =>
                                        setData('tempat_lahir', e.target.value)
                                    }
                                />
                            </Field>
                            <Field
                                label="Tanggal Lahir"
                                required
                                error={err('tgl_lahir')}
                            >
                                <Input
                                    id="tgl_lahir"
                                    type="date"
                                    value={values.tgl_lahir ?? ''}
                                    onChange={(e) =>
                                        setData('tgl_lahir', e.target.value)
                                    }
                                />
                            </Field>
                            <Field
                                label="Jenis Kelamin"
                                required
                                error={err('jenis_kelamin')}
                            >
                                <Select
                                    value={values.jenis_kelamin || undefined}
                                    onValueChange={(v) =>
                                        setData('jenis_kelamin', v)
                                    }
                                >
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="-- Pilih --" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="Laki-laki">
                                            Laki-laki
                                        </SelectItem>
                                        <SelectItem value="Perempuan">
                                            Perempuan
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </Field>
                        </div>

                        <div className="grid gap-5 sm:grid-cols-3">
                            <Field label="Agama" required error={err('agama')}>
                                <Select
                                    value={values.agama || undefined}
                                    onValueChange={(v) => setData('agama', v)}
                                >
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="-- Pilih --" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {LIST_AGAMA.map((a) => (
                                            <SelectItem key={a} value={a}>
                                                {a}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </Field>
                            <Field
                                label="Pendidikan"
                                required
                                error={err('pendidikan')}
                            >
                                <Select
                                    value={values.pendidikan || undefined}
                                    onValueChange={(v) =>
                                        setData('pendidikan', v)
                                    }
                                >
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="-- Pilih --" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {LIST_PENDIDIKAN.map((p) => (
                                            <SelectItem key={p} value={p}>
                                                {p}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </Field>
                            <Field
                                label="Pekerjaan"
                                required
                                error={err('pekerjaan')}
                            >
                                <Select
                                    value={values.pekerjaan || undefined}
                                    onValueChange={(v) =>
                                        setData('pekerjaan', v)
                                    }
                                >
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="-- Pilih --" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {LIST_PEKERJAAN.map((p) => (
                                            <SelectItem key={p} value={p}>
                                                {p}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </Field>
                        </div>

                        <div className="grid gap-5 sm:grid-cols-2">
                            <Field
                                label="Status Perkawinan"
                                required
                                error={err('status_perkawinan')}
                            >
                                <Select
                                    value={
                                        values.status_perkawinan || undefined
                                    }
                                    onValueChange={(v) =>
                                        setData('status_perkawinan', v)
                                    }
                                >
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="-- Pilih --" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {LIST_PERKAWINAN.map((p) => (
                                            <SelectItem key={p} value={p}>
                                                {p}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </Field>
                            <Field
                                label="Nama Pasangan"
                                error={err('pasangan')}
                                hint="Diisi jika sudah menikah"
                            >
                                <Input
                                    id="pasangan"
                                    value={values.pasangan ?? ''}
                                    onChange={(e) =>
                                        setData('pasangan', e.target.value)
                                    }
                                />
                            </Field>
                        </div>

                        <div className="grid gap-5 sm:grid-cols-2">
                            <Field
                                label="Telepon"
                                required
                                error={err('telepon')}
                            >
                                <Input
                                    id="telepon"
                                    value={values.telepon}
                                    onChange={(e) =>
                                        setData('telepon', e.target.value)
                                    }
                                />
                            </Field>
                            <Field label="No HP" required error={err('no_hp')}>
                                <Input
                                    id="no_hp"
                                    value={values.no_hp}
                                    onChange={(e) =>
                                        setData('no_hp', e.target.value)
                                    }
                                />
                            </Field>
                        </div>

                        <div className="grid gap-5 sm:grid-cols-3">
                            <Field
                                label="Jenis Identitas"
                                required
                                error={err('jenis_identitas')}
                            >
                                <Select
                                    value={
                                        values.jenis_identitas || undefined
                                    }
                                    onValueChange={(v) =>
                                        setData('jenis_identitas', v)
                                    }
                                >
                                    <SelectTrigger className="w-full">
                                        <SelectValue placeholder="-- Pilih --" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="KTP">KTP</SelectItem>
                                        <SelectItem value="SIM">SIM</SelectItem>
                                        <SelectItem value="PASPOR">
                                            PASPOR
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </Field>
                            <Field
                                label="No Identitas"
                                required
                                error={err('no_identitas')}
                            >
                                <Input
                                    id="no_identitas"
                                    value={values.no_identitas}
                                    onChange={(e) =>
                                        setData('no_identitas', e.target.value)
                                    }
                                />
                            </Field>
                            <Field label="NPWP" required error={err('npwp')}>
                                <Input
                                    id="npwp"
                                    value={values.npwp}
                                    onChange={(e) =>
                                        setData('npwp', e.target.value)
                                    }
                                    placeholder="-" 
                                />
                            </Field>
                        </div>

                        <Field
                            label="Nama Ibu Kandung"
                            required
                            error={err('ibu')}
                        >
                            <Input
                                id="ibu"
                                value={values.ibu}
                                onChange={(e) => setData('ibu', e.target.value)}
                            />
                        </Field>

                        <Field
                            label={
                                isEdit
                                    ? 'Ganti Foto'
                                    : 'Foto'
                            }
                            required={!isEdit}
                            error={fotoError ?? err('foto')}
                            hint={
                                isEdit && fotoName
                                    ? `Foto saat ini: ${fotoName}`
                                    : 'Format jpg/jpeg/png/webp maksimal 2MB'
                            }
                        >
                            <Input
                                id="foto"
                                type="file"
                                accept=".jpg,.jpeg,.png,.webp"
                                onChange={(e) =>
                                    onFotoChange(e.target.files?.[0] ?? null)
                                }
                            />
                        </Field>
                    </CardContent>
                </Card>
            )}

            {/* ====================== Tab Pengurus & Pengawas =================== */}
            {tab === 'pengurus' && (
                <div className="grid gap-5 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center justify-between">
                                Pengurus
                                <Switch
                                    checked={values.pengurus}
                                    onCheckedChange={(v) =>
                                        setData('pengurus', v)
                                    }
                                    aria-label="Status pengurus"
                                />
                            </CardTitle>
                            <CardDescription>
                                Isi data jika anggota menjabat sebagai pengurus.
                            </CardDescription>
                        </CardHeader>
                        {values.pengurus && (
                            <CardContent className="space-y-5">
                                <Field
                                    label="Jabatan"
                                    error={err('pengurus_jabatan')}
                                >
                                    <Input
                                        id="pengurus_jabatan"
                                        value={values.pengurus_jabatan ?? ''}
                                        onChange={(e) =>
                                            setData(
                                                'pengurus_jabatan',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="Ketua, Sekretaris, …"
                                    />
                                </Field>
                                <Field
                                    label="Tanggal Diangkat"
                                    error={err('tgl_pengurus_diangkat')}
                                >
                                    <Input
                                        id="tgl_pengurus_diangkat"
                                        type="date"
                                        value={
                                            values.tgl_pengurus_diangkat ?? ''
                                        }
                                        onChange={(e) =>
                                            setData(
                                                'tgl_pengurus_diangkat',
                                                e.target.value,
                                            )
                                        }
                                    />
                                </Field>
                            </CardContent>
                        )}
                        {!values.pengurus && (
                            <CardContent className="space-y-5">
                                <Field
                                    label="Keterangan Berhenti"
                                    error={err('pengurus_berhenti')}
                                >
                                    <Input
                                        id="pengurus_berhenti"
                                        value={values.pengurus_berhenti ?? ''}
                                        onChange={(e) =>
                                            setData(
                                                'pengurus_berhenti',
                                                e.target.value,
                                            )
                                        }
                                    />
                                </Field>
                                <Field
                                    label="Tanggal Berhenti"
                                    error={err('tgl_pengurus_berhenti')}
                                >
                                    <Input
                                        id="tgl_pengurus_berhenti"
                                        type="date"
                                        value={
                                            values.tgl_pengurus_berhenti ?? ''
                                        }
                                        onChange={(e) =>
                                            setData(
                                                'tgl_pengurus_berhenti',
                                                e.target.value,
                                            )
                                        }
                                    />
                                </Field>
                            </CardContent>
                        )}
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center justify-between">
                                Pengawas
                                <Switch
                                    checked={values.pengawas}
                                    onCheckedChange={(v) =>
                                        setData('pengawas', v)
                                    }
                                    aria-label="Status pengawas"
                                />
                            </CardTitle>
                            <CardDescription>
                                Isi data jika anggota menjabat sebagai pengawas.
                            </CardDescription>
                        </CardHeader>
                        {values.pengawas && (
                            <CardContent className="space-y-5">
                                <Field
                                    label="Jabatan"
                                    error={err('pengawas_jabatan')}
                                >
                                    <Input
                                        id="pengawas_jabatan"
                                        value={values.pengawas_jabatan ?? ''}
                                        onChange={(e) =>
                                            setData(
                                                'pengawas_jabatan',
                                                e.target.value,
                                            )
                                        }
                                        placeholder="Pengawas I, …"
                                    />
                                </Field>
                                <Field
                                    label="Tanggal Diangkat"
                                    error={err('tgl_pengawas_diangkat')}
                                >
                                    <Input
                                        id="tgl_pengawas_diangkat"
                                        type="date"
                                        value={
                                            values.tgl_pengawas_diangkat ?? ''
                                        }
                                        onChange={(e) =>
                                            setData(
                                                'tgl_pengawas_diangkat',
                                                e.target.value,
                                            )
                                        }
                                    />
                                </Field>
                            </CardContent>
                        )}
                        {!values.pengawas && (
                            <CardContent className="space-y-5">
                                <Field
                                    label="Keterangan Berhenti"
                                    error={err('pengawas_berhenti')}
                                >
                                    <Input
                                        id="pengawas_berhenti"
                                        value={values.pengawas_berhenti ?? ''}
                                        onChange={(e) =>
                                            setData(
                                                'pengawas_berhenti',
                                                e.target.value,
                                            )
                                        }
                                    />
                                </Field>
                                <Field
                                    label="Tanggal Berhenti"
                                    error={err('tgl_pengawas_berhenti')}
                                >
                                    <Input
                                        id="tgl_pengawas_berhenti"
                                        type="date"
                                        value={
                                            values.tgl_pengawas_berhenti ?? ''
                                        }
                                        onChange={(e) =>
                                            setData(
                                                'tgl_pengawas_berhenti',
                                                e.target.value,
                                            )
                                        }
                                    />
                                </Field>
                            </CardContent>
                        )}
                    </Card>
                </div>
            )}

            {/* ========================= Tab Ahli Waris ======================== */}
            {tab === 'waris' && (
                <Card>
                    <CardHeader>
                        <CardTitle>Ahli Waris</CardTitle>
                        <CardDescription>
                            Data ahli waris (maksimal 2 orang).
                        </CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-5">
                        <div className="grid gap-5 sm:grid-cols-2">
                            <Field label="Nama Waris 1" error={err('waris1')}>
                                <Input
                                    id="waris1"
                                    value={values.waris1 ?? ''}
                                    onChange={(e) =>
                                        setData('waris1', e.target.value)
                                    }
                                />
                            </Field>
                            <Field
                                label="Hubungan Waris 1"
                                error={err('hubungan_waris1')}
                            >
                                <Input
                                    id="hubungan_waris1"
                                    value={values.hubungan_waris1 ?? ''}
                                    onChange={(e) =>
                                        setData(
                                            'hubungan_waris1',
                                            e.target.value,
                                        )
                                    }
                                    placeholder="Anak / Suami / Istri / …"
                                />
                            </Field>
                            <Field label="Nama Waris 2" error={err('waris2')}>
                                <Input
                                    id="waris2"
                                    value={values.waris2 ?? ''}
                                    onChange={(e) =>
                                        setData('waris2', e.target.value)
                                    }
                                />
                            </Field>
                            <Field
                                label="Hubungan Waris 2"
                                error={err('hubungan_waris2')}
                            >
                                <Input
                                    id="hubungan_waris2"
                                    value={values.hubungan_waris2 ?? ''}
                                    onChange={(e) =>
                                        setData(
                                            'hubungan_waris2',
                                            e.target.value,
                                        )
                                    }
                                />
                            </Field>
                        </div>
                    </CardContent>
                </Card>
            )}

            {/* =========================== Tab Status ========================== */}
            {tab === 'status' && (
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center justify-between">
                            Status Keanggotaan
                            <Switch
                                checked={values.status}
                                onCheckedChange={(v) => setData('status', v)}
                                aria-label="Status anggota aktif"
                            />
                        </CardTitle>
                        <CardDescription>
                            {values.status
                                ? 'Anggota terdaftar aktif.'
                                : 'Nonaktifkan untuk mencatat berhenti.'}
                        </CardDescription>
                    </CardHeader>
                    {!values.status && (
                        <CardContent className="space-y-5">
                            <Field
                                label="Tanggal Berhenti"
                                error={err('tgl_anggota_berhenti')}
                            >
                                <Input
                                    id="tgl_anggota_berhenti"
                                    type="date"
                                    value={values.tgl_anggota_berhenti ?? ''}
                                    onChange={(e) =>
                                        setData(
                                            'tgl_anggota_berhenti',
                                            e.target.value,
                                        )
                                    }
                                />
                            </Field>
                            <Field
                                label="Alasan Berhenti"
                                error={err('anggota_berhenti')}
                            >
                                <Textarea
                                    id="anggota_berhenti"
                                    rows={2}
                                    value={values.anggota_berhenti ?? ''}
                                    onChange={(e) =>
                                        setData(
                                            'anggota_berhenti',
                                            e.target.value,
                                        )
                                    }
                                />
                            </Field>
                        </CardContent>
                    )}
                </Card>
            )}

            {/* ============================ Aksi =============================== */}
            <div className="flex items-center justify-end gap-3">
                <Button variant="outline" asChild>
                    <a href={backHref}>Kembali</a>
                </Button>
                <Button
                    type="submit"
                    disabled={processing}
                    className="bg-brand-600 hover:bg-brand-500"
                >
                    {processing ? (
                        <LoaderCircle className="animate-spin" />
                    ) : (
                        <Save />
                    )}
                    {submitLabel}
                </Button>
            </div>
        </form>
    );
}

function Field({
    label,
    required,
    error,
    hint,
    children,
}: {
    label: string;
    required?: boolean;
    error?: string;
    hint?: string;
    children: React.ReactNode;
}) {
    return (
        <div className="space-y-2">
            <Label>
                {label}{' '}
                {required && <span className="text-brand-600">*</span>}
            </Label>
            {children}
            {hint && !error && (
                <p className="text-xs text-muted-foreground">{hint}</p>
            )}
            {error && <p className="text-sm text-brand-600">{error}</p>}
        </div>
    );
}
