import { Head } from '@inertiajs/react';
import { Eye, PiggyBank } from 'lucide-react';

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
import {
    JENIS_SIMPANAN_LABELS,
    RUMUS_BUNGA_OPTIONS,
    type SimpananProdukRow,
} from '@/types/simpanan';

interface Props {
    produkData: SimpananProdukRow;
}

const rupiah = (v: number | string | null | undefined) =>
    v == null || v === '' ? '—' : `Rp ${Number(v).toLocaleString('id-ID')}`;

function InfoRow({ label, value }: { label: string; value: React.ReactNode }) {
    return (
        <div className="flex flex-col gap-1 py-2.5 sm:flex-row sm:items-center">
            <span className="w-52 shrink-0 text-sm font-medium text-muted-foreground">{label}</span>
            <span className="text-sm">{value ?? '—'}</span>
        </div>
    );
}

export default function SimpananProdukShow({ produkData: p }: Props) {
    const rumusLabel = RUMUS_BUNGA_OPTIONS.find((r) => r.value === p.rumus_bunga)?.label;

    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${p.nama}`} />

            <PageHeader
                title="Detail Produk Simpanan"
                description="Informasi lengkap produk simpanan."
                icon={Eye}
                backHref={route('superadmin.simpanan.produk-simpanan')}
            />

            <div className="grid max-w-4xl gap-5">
                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <PiggyBank className="size-4 text-brand-600" />
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
                        <InfoRow label="Jenis" value={<Badge variant="secondary">{JENIS_SIMPANAN_LABELS[p.jenis] ?? '-'}</Badge>} />
                        <InfoRow
                            label="Akun Simpanan"
                            value={p.idAccount ? `${p.idAccount.no_account} — ${p.idAccount.nama}` : '—'}
                        />
                        <InfoRow label="Saldo Minimum" value={rupiah(p.minimum)} />
                        <InfoRow label="Saldo Mengendap" value={rupiah(p.mengendap)} />
                        <InfoRow label="Nominal Setoran Awal" value={rupiah(p.nominal)} />
                        <InfoRow label="Insentif" value={p.insentif != null ? `${p.insentif}%` : '—'} />
                        <InfoRow label="Simpanan Saham" value={p.saham ? 'Ya' : 'Tidak'} />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Bunga</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Separator className="mb-2" />
                        <InfoRow
                            label="Jenis Bunga"
                            value={p.jenis_bunga === 2 ? 'Bertingkat' : 'Flat'}
                        />
                        {p.jenis_bunga === 1 && <InfoRow label="Bunga Flat" value={`${p.bunga ?? 0}%`} />}
                        <InfoRow label="Rumus Perhitungan" value={rumusLabel ?? '—'} />
                        {p.rumus_bunga === 1 && (
                            <InfoRow label="Hanya Bulan Berjalan" value={p.bulan ? 'Ya' : 'Tidak'} />
                        )}
                    </CardContent>
                </Card>

                {p.tingkat && p.tingkat.length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Tingkatan Bunga</CardTitle>
                        </CardHeader>
                        <CardContent className="overflow-x-auto">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Minimal</TableHead>
                                        <TableHead>Maksimal</TableHead>
                                        <TableHead>Bunga</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {p.tingkat.map((t, i) => (
                                        <TableRow key={t.id ?? i}>
                                            <TableCell>{rupiah(t.minimal)}</TableCell>
                                            <TableCell>{rupiah(t.maksimal)}</TableCell>
                                            <TableCell className="font-mono text-xs">{t.bunga}%</TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>
                )}

                {(p.simpananKodes?.length ?? 0) > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Kode Transaksi Terkait</CardTitle>
                        </CardHeader>
                        <CardContent className="flex flex-wrap gap-2">
                            {p.simpananKodes!.map((k) => (
                                <Badge key={k.kode} variant="secondary">
                                    <span className="font-mono text-[10px]">{k.kode}</span> {k.nama}
                                </Badge>
                            ))}
                        </CardContent>
                    </Card>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
