import { Head, Link } from '@inertiajs/react';
import { Eye, Pencil } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import type { AnggotaDetail } from '@/types/models';

interface Props {
    anggotaData: AnggotaDetail & {
        kelompok?: { id: number; nama: string } | null;
        kantor?: { id: number; nama_kantor: string } | null;
        provinsi?: { code: string; name: string } | null;
        kota?: { code: string; name: string } | null;
        kecamatan?: { code: string; name: string } | null;
        kelurahan?: { code: string; name: string } | null;
    };
}

export default function AnggotaShow({ anggotaData }: Props) {
    const a = anggotaData;

    const lokasi = [
        a.provinsi?.name,
        a.kota?.name,
        a.kecamatan?.name,
        a.kelurahan?.name,
    ]
        .filter(Boolean)
        .join(', ');

    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${a.nama}`} />

            <PageHeader
                title={a.nama}
                description={`No. Anggota ${a.no_anggota}`}
                icon={Eye}
                backHref={route('superadmin.anggota')}
            >
                <div className="flex items-center gap-2">
                    <Badge
                        className={
                            Number(a.status) === 1
                                ? 'bg-emerald-600 hover:bg-emerald-600'
                                : 'bg-amber-600 hover:bg-amber-600'
                        }
                    >
                        {Number(a.status) === 1 ? 'Aktif' : 'Berhenti'}
                    </Badge>
                    <Button variant="outline" asChild>
                        <Link href={route('superadmin.anggota.edit', a.id)}>
                            <Pencil /> Edit
                        </Link>
                    </Button>
                </div>
            </PageHeader>

            <div className="grid gap-5 lg:grid-cols-3">
                {/* ========================== Kartu profil ========================= */}
                <Card className="lg:col-span-1">
                    <CardContent className="flex flex-col items-center gap-4 pt-6 text-center">
                        <img
                            src={a.foto ? `/storage/${a.foto}` : '/favicon.ico'}
                            alt={a.nama}
                            className="size-32 rounded-xl border object-cover shadow-sm"
                        />
                        <div>
                            <h2 className="text-lg font-semibold">{a.nama}</h2>
                            <p className="font-mono text-xs text-muted-foreground">
                                {a.no_anggota}
                            </p>
                        </div>
                        <div className="grid w-full grid-cols-2 gap-3 text-sm">
                            <InfoMini label="Kelompok" value={a.kelompok?.nama ?? '-'} />
                            <InfoMini label="Kantor" value={a.kantor?.nama_kantor ?? '-'} />
                            <InfoMini label="Telepon" value={a.telepon ?? '-'} />
                            <InfoMini label="No HP" value={a.no_hp ?? '-'} />
                        </div>
                    </CardContent>
                </Card>

                {/* ========================= Detail lengkap ======================== */}
                <Card className="lg:col-span-2">
                    <CardHeader>
                        <CardTitle>Data Lengkap</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        <Section title="Identitas & Kontak">
                            <DetailGrid
                                items={[
                                    ['Email', a.email],
                                    ['Alamat', a.alamat],
                                    [
                                        'Wilayah',
                                        lokasi || '-',
                                    ],
                                    ['Tempat/Tgl Lahir', `${a.tempat_lahir ?? '-'}, ${formatDate(a.tgl_lahir)}`],
                                    ['Jenis Kelamin', a.jenis_kelamin],
                                    ['Agama', a.agama],
                                ]}
                            />
                        </Section>

                        <Section title="Pendidikan & Pekerjaan">
                            <DetailGrid
                                items={[
                                    ['Pendidikan', a.pendidikan],
                                    ['Pekerjaan', a.pekerjaan],
                                    ['Status Perkawinan', a.status_perkawinan],
                                    ['Nama Pasangan', a.pasangan],
                                    ['Nama Ibu Kandung', a.ibu],
                                    ['PIN', '••••••'],
                                ]}
                            />
                        </Section>

                        <Section title="Identitas Resmi">
                            <DetailGrid
                                items={[
                                    ['Jenis Identitas', a.jenis_identitas],
                                    ['No Identitas', a.no_identitas],
                                    ['NPWP', a.npwp],
                                ]}
                            />
                        </Section>

                        {(Number(a.pengurus) === 1 ||
                            Number(a.pengawas) === 1) && (
                            <Section title="Keorganisasian">
                                <DetailGrid
                                    items={[
                                        ...(Number(a.pengurus) === 1
                                            ? ([
                                                  [
                                                      'Pengurus',
                                                      `${a.pengurus_jabatan ?? '-'} (diangkat ${formatDate(
                                                          a.tgl_pengurus_diangkat,
                                                      )})`,
                                                  ],
                                              ] as [string, string][])
                                            : []),
                                        ...(Number(a.pengawas) === 1
                                            ? ([
                                                  [
                                                      'Pengawas',
                                                      `${a.pengawas_jabatan ?? '-'} (diangkat ${formatDate(
                                                          a.tgl_pengawas_diangkat,
                                                      )})`,
                                                  ],
                                              ] as [string, string][])
                                            : []),
                                    ]}
                                />
                            </Section>
                        )}

                        <Section title="Ahli Waris">
                            <DetailGrid
                                items={[
                                    ['Waris 1', joinDash(a.waris1, a.hubungan_waris1)],
                                    ['Waris 2', joinDash(a.waris2, a.hubungan_waris2)],
                                ]}
                            />
                        </Section>

                        {Number(a.status) !== 1 && (
                            <Section title="Berhenti">
                                <DetailGrid
                                    items={[
                                        [
                                            'Tanggal Berhenti',
                                            formatDate(a.tgl_anggota_berhenti),
                                        ],
                                        ['Alasan', a.anggota_berhenti],
                                    ]}
                                />
                            </Section>
                        )}
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}

function InfoMini({ label, value }: { label: string; value: string }) {
    return (
        <div className="rounded-lg bg-muted/60 p-2">
            <p className="text-xs text-muted-foreground">{label}</p>
            <p className="truncate font-medium">{value}</p>
        </div>
    );
}

function Section({
    title,
    children,
}: {
    title: string;
    children: React.ReactNode;
}) {
    return (
        <div>
            <h3 className="mb-2 text-sm font-semibold tracking-wide text-muted-foreground uppercase">
                {title}
            </h3>
            {children}
        </div>
    );
}

function DetailGrid({ items }: { items: [string, string | null][] }) {
    return (
        <dl className="grid gap-x-6 gap-y-3 sm:grid-cols-2">
            {items.map(([k, v]) => (
                <div key={k} className="min-w-0">
                    <dt className="text-xs text-muted-foreground">{k}</dt>
                    <dd className="truncate text-sm font-medium" title={v ?? ''}>
                        {v || '-'}
                    </dd>
                </div>
            ))}
        </dl>
    );
}

function formatDate(value?: string | null): string {
    if (!value) return '-';
    try {
        return new Intl.DateTimeFormat('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        }).format(new Date(value));
    } catch {
        return value;
    }
}

function joinDash(a?: string | null, b?: string | null): string {
    return [a, b].filter(Boolean).join(' — ') || '-';
}
