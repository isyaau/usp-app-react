import { Head, Link } from '@inertiajs/react';
import { FileText, Printer } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import type { ProposalShowRow } from '@/types/models';

interface Props {
    proposal: ProposalShowRow;
}

const rupiah = (v: string | number | null) =>
    `Rp ${Number(v ?? 0).toLocaleString('id-ID')}`;

function Section({ title, children }: { title: string; children: React.ReactNode }) {
    return (
        <Card>
            <CardHeader><CardTitle className="text-base">{title}</CardTitle></CardHeader>
            <CardContent>{children}</CardContent>
        </Card>
    );
}

export default function Show({ proposal }: Props) {
    const Info = ({ k, v }: { k: string; v?: string | number | null }) => (
        <div>
            <p className="text-xs text-muted-foreground">{k}</p>
            <p className="font-medium">{v ?? '—'}</p>
        </div>
    );

    return (
        <AuthenticatedLayout>
            <Head title={`Detail Proposal — ${proposal.no_bukti}`} />

            <PageHeader
                title={`Detail Proposal — ${proposal.no_bukti}`}
                description="Informasi lengkap proposal pinjaman anggota."
                icon={FileText}
                backHref={route('superadmin.pinjaman.proposal')}
            >
                <Button variant="outline" asChild>
                    <Link href={route('superadmin.pinjaman.proposal.cetak', proposal.id)}>
                        <Printer />
                        Cetak
                    </Link>
                </Button>
                <Button asChild className="bg-brand-600 hover:bg-brand-500">
                    <Link href={route('superadmin.pinjaman.proposal.edit', proposal.id)}>
                        Edit
                    </Link>
                </Button>
            </PageHeader>

            <div className="max-w-4xl space-y-4">
                <Card>
                    <CardHeader className="flex-row items-center justify-between">
                        <CardTitle>Informasi Proposal</CardTitle>
                        <Badge
                            variant="outline"
                            className={proposal.status === '1'
                                ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                                : 'border-muted-foreground/30 bg-muted text-muted-foreground'}
                        >
                            {proposal.status === '1' ? 'Aktif' : 'Nonaktif'}
                        </Badge>
                    </CardHeader>
                    <CardContent className="grid gap-3 sm:grid-cols-3">
                        <Info k="No. Bukti" v={proposal.no_bukti} />
                        <Info k="Tanggal" v={proposal.tanggal} />
                        <Info k="Produk" v={proposal.jenis_pinjaman?.nama} />
                        <Info k="Metode Angsuran" v={proposal.jenis_angsuran} />
                        <Info k="Plafon" v={rupiah(proposal.plafon)} />
                        <Info k="Bunga" v={proposal.bunga ? `${proposal.bunga}%` : null} />
                        <Info k="Jangka Waktu" v={proposal.jangka_waktu ? `${proposal.jangka_waktu} ${proposal.satuan}` : null} />
                        <Info k="Nominal Angsuran" v={rupiah(proposal.nominal_angsuran)} />
                        <Info k="Bayar Pokok Per" v={proposal.bayar_pokok_per ? `${proposal.bayar_pokok_per} ${proposal.satuan}` : null} />
                        <Info k="Penggunaan Kredit" v={proposal.penggunaan_kredit} />
                        <Info k="Jaminan" v={proposal.jaminan} />
                        <Info k="Pembayaran" v={proposal.pembayaran} />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle>Data Debitur</CardTitle></CardHeader>
                    <CardContent className="grid gap-3 sm:grid-cols-2">
                        <Info k="Nama" v={proposal.anggota?.nama} />
                        <Info k="No. Anggota" v={proposal.anggota?.no_anggota} />
                        <Info k="No. Identitas" v={proposal.anggota?.no_identitas} />
                        <Info k="Telepon" v={proposal.anggota?.telepon} />
                        <Info k="Alamat" v={proposal.anggota?.alamat} />
                        <Info k="Marketing" v={proposal.marketing ? `${proposal.marketing.nama} (${proposal.marketing.kode})` : null} />
                        <Info k="Kantor" v={proposal.kantor?.nama_kantor} />
                        <Info k="Dicatat oleh" v={proposal.user?.nama} />
                    </CardContent>
                </Card>

                <Section title="Biaya & Pencairan">
                    <Table className="rounded-md border">
                        <TableHeader>
                            <TableRow>
                                <TableHead>Nama</TableHead>
                                <TableHead>Jenis</TableHead>
                                <TableHead className="text-right">Nominal</TableHead>
                                <TableHead className="text-right">Potongan Pencairan</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {proposal.biaya.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={4} className="h-16 text-center text-muted-foreground">
                                        Tidak ada biaya.
                                    </TableCell>
                                </TableRow>
                            )}
                            {proposal.biaya.map((b) => (
                                <TableRow key={b.id}>
                                    <TableCell>{b.nama || '-'}</TableCell>
                                    <TableCell>
                                        {b.persen === '1' ? `Persentase (${b.nominal}%)` : 'Nominal'}
                                    </TableCell>
                                    <TableCell className="text-right font-mono">
                                        {b.persen === '1' ? '' : rupiah(b.nominal)}
                                    </TableCell>
                                    <TableCell className="text-right">
                                        {b.is_deducted_from_disbursement === '1' ? (
                                            <span className="text-amber-600 dark:text-amber-400">Potong</span>
                                        ) : (
                                            <span className="text-muted-foreground">—</span>
                                        )}
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                    <div className="mt-4 grid gap-3 sm:grid-cols-2">
                        <Info k="Total Biaya" v={rupiah(proposal.total_biaya)} />
                        <Info k="Total Terima" v={rupiah(proposal.total_terima)} />
                    </div>
                </Section>
            </div>
        </AuthenticatedLayout>
    );
}