import { Head } from '@inertiajs/react';
import { Wallet } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Badge } from '@/Components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import type { SimpananDetail } from '@/types/models';

interface Props {
    simpananData: SimpananDetail;
    signatureUrl: string | null;
}

function InfoRow({ label, value }: { label: string; value: React.ReactNode }) {
    return (
        <div className="flex flex-col gap-1 py-2.5 sm:flex-row sm:items-center">
            <span className="w-48 shrink-0 text-sm font-medium text-muted-foreground">
                {label}
            </span>
            <span className="text-sm">{value ?? '—'}</span>
        </div>
    );
}

const rupiah = (v: string | number | null) =>
    `Rp ${Number(v ?? 0).toLocaleString('id-ID')}`;

export default function SimpananShow({ simpananData: s, signatureUrl }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${s.no_rekening}`} />

            <PageHeader
                title="Detail Simpanan"
                description={`No. ${s.no_rekening}`}
                icon={Wallet}
                backHref={route('superadmin.simpanan')}
            />

            <div className="grid max-w-5xl gap-5 lg:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center justify-between gap-2">
                            <span className="flex items-center gap-2">
                                <Wallet className="size-4 text-brand-600" />
                                {s.no_rekening}
                            </span>
                            <Badge variant={s.aktif === '1' ? 'success' : 'secondary'}>
                                {s.aktif === '1' ? 'Aktif' : 'Nonaktif'}
                            </Badge>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Separator className="mb-2" />
                        <InfoRow label="Tanggal" value={s.tanggal} />
                        <InfoRow
                            label="Anggota"
                            value={
                                s.anggota && (
                                    <>
                                        <span className="font-medium">{s.anggota.nama}</span>{' '}
                                        <span className="font-mono text-xs text-muted-foreground">
                                            ({s.anggota.no_anggota})
                                        </span>
                                    </>
                                )
                            }
                        />
                        <InfoRow
                            label="Produk Simpanan"
                            value={
                                s.jenis_simpanan && (
                                    <>
                                        <span className="font-medium">
                                            {s.jenis_simpanan.nama}
                                        </span>{' '}
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {s.jenis_simpanan.kode}
                                        </span>
                                    </>
                                )
                            }
                        />
                        <InfoRow label="Bagi Hasil / Tahun" value={s.bunga ? `${s.bunga}%` : '—'} />
                        <InfoRow label="QQ" value={s.qq || '—'} />
                        <InfoRow label="Marketing" value={s.marketing?.nama} />
                        <InfoRow label="Kantor" value={s.kantor?.nama_kantor} />
                        <InfoRow label="Nominal Setoran Awal" value={rupiah(s.nominal_setor)} />
                        <InfoRow
                            label="Notifikasi SMS"
                            value={
                                <Badge variant={s.sms === '1' ? 'success' : 'secondary'}>
                                    {s.sms === '1' ? 'Aktif' : 'Nonaktif'}
                                </Badge>
                            }
                        />
                    </CardContent>
                </Card>

                <div className="space-y-5">
                    <Card>
                        <CardHeader>
                            <CardTitle>Tanda Tangan</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {signatureUrl ? (
                                <img
                                    src={`/storage/${signatureUrl}`}
                                    alt={`Tanda tangan ${s.no_rekening}`}
                                    className="h-24 rounded border bg-white p-2"
                                />
                            ) : (
                                <p className="text-sm text-muted-foreground">
                                    Tidak ada tanda tangan tersimpan.
                                </p>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Blokir</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Separator className="mb-2" />
                            <InfoRow
                                label="Blokir Simpanan"
                                value={
                                    <Badge variant={s.blokir_simpanan === '1' ? 'destructive' : 'secondary'}>
                                        {s.blokir_simpanan === '1' ? 'Diblokir' : 'Tidak'}
                                    </Badge>
                                }
                            />
                            <InfoRow
                                label="Blokir Nominal"
                                value={
                                    s.blokir_nominal === '1' ? rupiah(s.nominal_blokir) : '—'
                                }
                            />
                            <InfoRow
                                label="Blokir s/d Tanggal"
                                value={
                                    s.blokir_tgl === '1' && s.tgl_blokir ? s.tgl_blokir : '—'
                                }
                            />
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
