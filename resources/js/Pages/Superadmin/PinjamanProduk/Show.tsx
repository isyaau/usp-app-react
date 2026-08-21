import { Head } from '@inertiajs/react';
import { Eye, HandCoins } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Badge } from '@/Components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Separator } from '@/Components/ui/separator';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import type { PinjamanProdukRow } from '@/types/pinjaman';

const KOLEKTABILITAS_LABELS = ['Lancar', 'Kurang Lancar', 'Diragukan', 'Macet'];

interface Props {
    produkData: PinjamanProdukRow;
}

function InfoRow({ label, value }: { label: string; value: React.ReactNode }) {
    return (
        <div className="flex flex-col gap-1 py-2.5 sm:flex-row sm:items-center">
            <span className="w-52 shrink-0 text-sm font-medium text-muted-foreground">{label}</span>
            <span className="text-sm">{value ?? '—'}</span>
        </div>
    );
}

const rupiah = (v: number | string | null | undefined) =>
    v == null || v === ''
        ? '—'
        : `Rp ${Number(v).toLocaleString('id-ID')}`;

export default function PinjamanProdukShow({ produkData: p }: Props) {
    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${p.nama}`} />

            <PageHeader
                title="Detail Produk Pinjaman"
                description="Informasi lengkap produk pinjaman."
                icon={Eye}
                backHref={route('superadmin.pinjaman.produk')}
            />

            <div className="grid max-w-5xl gap-5">
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <HandCoins className="size-4 text-brand-600" />
                            {p.nama}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Separator className="mb-2" />
                        <InfoRow
                            label="Kode"
                            value={
                                <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                    {p.kode}
                                </span>
                            }
                        />
                        <InfoRow
                            label="Akun Pinjaman"
                            value={p.account ? `${p.account.no_account} — ${p.account.nama}` : '—'}
                        />
                        <InfoRow label="Bunga" value={`${p.bunga}%`} />
                        <InfoRow label="Insentif" value={`${p.insentif}%`} />
                        <InfoRow label="Toleransi Pembulatan" value={rupiah(p.toleransi)} />
                        <InfoRow label="Metode Angsuran" value={<Badge variant="default">{p.angsuran}</Badge>} />
                        <InfoRow
                            label="Wajib Simpanan"
                            value={
                                p.simpanan ? (
                                    <span className="text-emerald-600">Aktif</span>
                                ) : (
                                    'Tidak aktif'
                                )
                            }
                        />
                        {p.simpanan && (
                            <>
                                <InfoRow label="SWP saat Cair" value={p.swp_cair ? 'Ya' : 'Tidak'} />
                                <InfoRow label="SWP per Angsuran" value={p.swp_angsur ? 'Ya' : 'Tidak'} />
                                <InfoRow
                                    label="Nominal Simpanan"
                                    value={
                                        p.swp_persen
                                            ? `Persentase dari angsuran`
                                            : rupiah(p.nominal_simpanan)
                                    }
                                />
                            </>
                        )}
                        <InfoRow
                            label="Simpanan Pokok"
                            value={
                                p.simpanan_pokok ? (
                                    rupiah(p.nominal_simpanan_pokok)
                                ) : (
                                    'Tidak diwajibkan'
                                )
                            }
                        />
                        <InfoRow label="Bunga Ditangguhkan" value={p.ditangguhkan ? 'Ya' : 'Tidak'} />
                        <InfoRow label="Kas di Kantor" value={rupiah(p.kas)} />
                    </CardContent>
                </Card>

                {/* Kolektabilitas */}
                <Card>
                    <CardHeader>
                        <CardTitle>Kolektabilitas</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead className="w-40">Kualitas</TableHead>
                                    <TableHead>Rumus / Keterangan</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {(p.kolektabilitas ?? []).map((k) => (
                                    <TableRow key={k.kualitas_id}>
                                        <TableCell className="font-medium">
                                            {KOLEKTABILITAS_LABELS[Number(k.kualitas_id) - 1] ??
                                                `Kualitas ${k.kualitas_id}`}
                                        </TableCell>
                                        <TableCell className="font-mono text-xs">
                                            {k.keterangan || '—'}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                {/* Komponen */}
                <Card>
                    <CardHeader>
                        <CardTitle>Komponen Biaya</CardTitle>
                    </CardHeader>
                    <CardContent className="overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nama</TableHead>
                                    <TableHead>Nominal</TableHead>
                                    <TableHead>%</TableHead>
                                    <TableHead>Cair</TableHead>
                                    <TableHead>Angsuran</TableHead>
                                    <TableHead>Penalti</TableHead>
                                    <TableHead>Rumus C/A/P</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {(p.komponen ?? []).map((c, i) => (
                                    <TableRow key={c.id ?? i}>
                                        <TableCell className="font-medium">{c.nama}</TableCell>
                                        <TableCell>{c.persen ? `${c.nominal}%` : rupiah(c.nominal)}</TableCell>
                                        <TableCell>{c.persen ? '✓' : '—'}</TableCell>
                                        <TableCell>{c.cair ? '✓' : '—'}</TableCell>
                                        <TableCell>{c.angsuran ? '✓' : '—'}</TableCell>
                                        <TableCell>{c.penalti ? '✓' : '—'}</TableCell>
                                        <TableCell className="font-mono text-[10px]">
                                            {[c.rumus_c && `C:${c.rumus_c}`, c.rumus_a && `A:${c.rumus_a}`, c.rumus_p && `P:${c.rumus_p}`]
                                                .filter(Boolean)
                                                .join(' · ') || '—'}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}
