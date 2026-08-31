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
import type { AccountMini } from '@/types/models';
import {
    JENIS_SIMPANAN_LABELS,
    RUMUS_BUNGA_OPTIONS,
    type SimpananProdukRow,
} from '@/types/simpanan';

interface Props {
    produkData: SimpananProdukRow;
    accounts: AccountMini[];
}

const rupiah = (v: number | string | null | undefined) =>
    v == null || v === '' ? '—' : `Rp ${Number(v).toLocaleString('id-ID')}`;

function InfoRow({ label, value, unit }: { label: string; value: React.ReactNode; unit?: string }) {
    return (
        <div className="flex flex-col gap-1 py-2.5 sm:flex-row sm:items-center">
            <span className="w-56 shrink-0 text-sm font-medium text-muted-foreground">{label}</span>
            <span className="text-sm">
                {value ?? '—'}
                {unit && value != null ? <span className="ml-0.5 text-xs text-muted-foreground">{unit}</span> : null}
            </span>
        </div>
    );
}

export default function SimpananProdukShow({ produkData: p, accounts }: Props) {
    const rumusLabel = RUMUS_BUNGA_OPTIONS.find((r) => r.value === p.rumus_bunga)?.label;

    const accountNo = (id: number | string | null | undefined) => {
        if (id == null || id === '') return undefined;
        const a = accounts.find((x) => String(x.id) === String(id));
        return a ? `${a.no_account} — ${a.nama}` : undefined;
    };

    const kodeLabel = (v?: { kode?: string; nama?: string } | null) =>
        v ? `${v.kode} — ${v.nama}` : undefined;

    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${p.nama}`} />

            <PageHeader
                title="Detail Produk Simpanan"
                description="Informasi lengkap produk simpanan."
                icon={Eye}
                backHref={route('superadmin.simpanan.produk-simpanan')}
            />

            <div className="grid max-w-5xl gap-5">
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
                        <InfoRow
                            label="Jenis"
                            value={<Badge variant="secondary">{JENIS_SIMPANAN_LABELS[p.jenis] ?? '-'}</Badge>}
                        />
                        <InfoRow
                            label="No. Account"
                            value={p.id_account ? `${p.id_account.no_account} — ${p.id_account.nama}` : '—'}
                        />
                        <InfoRow label="Saldo Minimum" value={rupiah(p.minimum)} />
                        <InfoRow label="Mengendap" value={p.mengendap} unit="bln" />
                        <InfoRow label="Harga Saham" value={p.saham ? rupiah(p.harga_saham) : '—'} />
                        <InfoRow label="Nil. Setoran" value={rupiah(p.nominal)} />
                        <InfoRow label="Insentif Mkt." value={p.insentif != null ? p.insentif : '—'} unit="%" />
                        <InfoRow label="Kode Setoran" value={kodeLabel(p.setor_kode)} />
                        <InfoRow label="Kode Tarikan" value={kodeLabel(p.tarik_kode)} />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Bagi Hasil</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Separator className="mb-2" />
                        <InfoRow label="Kode" value={kodeLabel(p.bunga_kode)} />
                        <InfoRow
                            label="Pola"
                            value={<Badge variant="secondary">{p.jenis_bunga === 2 ? 'Bertingkat' : 'Tidak Bertingkat'}</Badge>}
                        />
                        <InfoRow label="B. Hasil/Tahun" value={p.jenis_bunga === 1 ? p.bunga : '—'} unit="%" />
                        <InfoRow label="No. Account" value={accountNo(p.account_bunga)} />
                        <InfoRow label="Rumus" value={rumusLabel ?? '—'} />
                        {p.rumus_bunga === 1 && (
                            <InfoRow label="1 Bulan" value={p.bulan ? 'Ya' : 'Tidak'} />
                        )}
                    </CardContent>
                </Card>

                {p.jenis_bunga === 2 && (p.tingkat ?? []).length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Tabel B. Hasil Bertingkat</CardTitle>
                        </CardHeader>
                        <CardContent className="overflow-x-auto">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Saldo Min (Rp)</TableHead>
                                        <TableHead>Saldo Maks (Rp)</TableHead>
                                        <TableHead>B. Hasil (%)</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {p.tingkat!.map((t, i) => (
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

                <Card>
                    <CardHeader>
                        <CardTitle>Biaya Administrasi</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Separator className="mb-2" />
                        <InfoRow label="Kode" value={kodeLabel(p.biaya_kode)} />
                        <InfoRow label="Biaya Adm." value={rupiah(p.biaya)} />
                        <InfoRow label="No. Account" value={accountNo(p.account_biaya)} />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Pajak</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Separator className="mb-2" />
                        <InfoRow label="Kode" value={kodeLabel(p.pajak_kode)} />
                        <InfoRow label="Pajak" value={p.pajak != null ? p.pajak : '—'} unit="%" />
                        <InfoRow label="No. Account" value={accountNo(p.account_pajak)} />
                        <InfoRow label="Saldo Minimum" value={rupiah(p.pajak_saldo)} />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Biaya Android</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Separator className="mb-2" />
                        <InfoRow label="Kode" value={kodeLabel(p.android_kode)} />
                        <InfoRow label="Biaya Android" value={rupiah(p.nominal_android)} />
                        <InfoRow label="No. Account" value={accountNo(p.account_android)} />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Transaksi</CardTitle>
                    </CardHeader>
                    <CardContent className="overflow-x-auto">
                        {(p.simpanan_kodes ?? []).length === 0 ? (
                            <p className="rounded-lg bg-muted px-3 py-6 text-center text-sm text-muted-foreground">
                                Belum ada transaksi.
                            </p>
                        ) : (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Kode</TableHead>
                                        <TableHead>Nama Transaksi</TableHead>
                                        <TableHead>Acc. Debet</TableHead>
                                        <TableHead>Acc. Kredit</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {p.simpanan_kodes!.map((k) => {
                                        const db = k.debet_account
                                            ? `${k.debet_account.no_account} — ${k.debet_account.nama}`
                                            : accountNo(k.account_debet);
                                        const cr = k.kredit_account
                                            ? `${k.kredit_account.no_account} — ${k.kredit_account.nama}`
                                            : accountNo(k.account_kredit);
                                        return (
                                            <TableRow key={k.id}>
                                                <TableCell>
                                                    <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                                        {k.kode}
                                                    </span>
                                                </TableCell>
                                                <TableCell className="font-medium">{k.nama}</TableCell>
                                                <TableCell className="font-mono text-xs">{db ?? '—'}</TableCell>
                                                <TableCell className="font-mono text-xs">{cr ?? '—'}</TableCell>
                                            </TableRow>
                                        );
                                    })}
                                </TableBody>
                            </Table>
                        )}
                    </CardContent>
                </Card>

                <label className="flex cursor-pointer items-center gap-2 rounded-lg border bg-card px-4 py-3 text-sm">
                    <input
                        type="checkbox"
                        readOnly
                        checked={Boolean(p.update_bagi_hasil)}
                        className="size-4 accent-[var(--color-brand-600)]"
                    />
                    Update Bagi Hasil ke Semua Simpanan
                </label>
            </div>
        </AuthenticatedLayout>
    );
}
