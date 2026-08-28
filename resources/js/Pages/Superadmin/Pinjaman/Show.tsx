import { Head } from '@inertiajs/react';
import { Banknote } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Link } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';

interface DetailItem {
    id: number;
    nama?: string;
    keterangan?: string | null;
    nominal?: string;
    surat?: string;
    surat_id?: number | string;
    tempat_lahir?: string;
    tgl_lahir?: string;
    no_ktp?: string;
    alamat?: string;
    hubungan?: string;
    telepon?: string;
    ibu?: string;
    persen?: string;
}

interface PinjamanShow {
    id: number;
    tanggal: string;
    no_pinjaman: string;
    plafon: string | null;
    bunga: string | null;
    jangka_waktu: string | null;
    satuan: string | null;
    nominal_angsuran: string | null;
    periode: string | null;
    angsuranke: string | null;
    jatuh_tempo: string | null;
    aktif: string | null;
    jenisPinjaman?: { id: number; nama: string; angsuran: string } | null;
    anggota?: {
        id: number;
        no_anggota: string;
        nama: string;
        alamat: string | null;
        no_identitas: string | null;
        telepon: string | null;
        status: string | null;
    } | null;
    kantor?: { id: number; nama_kantor: string } | null;
    user?: { id: number; nama: string } | null;
    biaya: DetailItem[];
    jaminan: DetailItem[];
    saksi: DetailItem[];
    surat: DetailItem[];
    penjamin: DetailItem[];
}

interface Props {
    pinjaman: PinjamanShow;
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

export default function Show({ pinjaman }: Props) {
    const Info = ({ k, v }: { k: string; v?: string | number | null }) => (
        <div>
            <p className="text-xs text-muted-foreground">{k}</p>
            <p className="font-medium">{v ?? '—'}</p>
        </div>
    );

    return (
        <AuthenticatedLayout>
            <Head title={`Detail Pinjaman — ${pinjaman.no_pinjaman}`} />

            <PageHeader
                title={`Detail Pinjaman — ${pinjaman.no_pinjaman}`}
                description="Informasi lengkap transaksi pinjaman anggota."
                icon={Banknote}
                backHref={route('superadmin.pinjaman.pinjaman')}
            >
                <Button variant="outline" asChild>
                    <Link href={route('superadmin.pinjaman.pinjaman.edit', pinjaman.id)}>Edit</Link>
                </Button>
            </PageHeader>

            <div className="max-w-4xl space-y-4">
                <Card>
                    <CardHeader className="flex-row items-center justify-between">
                        <CardTitle>Informasi Pinjaman</CardTitle>
                        <Badge
                            variant="outline"
                            className={pinjaman.aktif === '1'
                                ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400'
                                : 'border-muted-foreground/30 bg-muted text-muted-foreground'}
                        >
                            {pinjaman.aktif === '1' ? 'Aktif' : 'Nonaktif'}
                        </Badge>
                    </CardHeader>
                    <CardContent className="grid gap-3 sm:grid-cols-3">
                        <Info k="No. Pinjaman" v={pinjaman.no_pinjaman} />
                        <Info k="Tanggal" v={pinjaman.tanggal} />
                        <Info k="Produk" v={pinjaman.jenisPinjaman?.nama} />
                        <Info k="Plafon" v={rupiah(pinjaman.plafon)} />
                        <Info k="Bagi Hasil" v={pinjaman.bunga ? `${pinjaman.bunga}%` : null} />
                        <Info k="Jangka Waktu" v={pinjaman.jangka_waktu ? `${pinjaman.jangka_waktu} ${pinjaman.satuan}` : null} />
                        <Info k="Nominal Angsuran" v={rupiah(pinjaman.nominal_angsuran)} />
                        <Info k="Periode / Angsuran ke" v={pinjaman.periode ? `${pinjaman.periode} / ke-${pinjaman.angsuranke ?? 0}` : null} />
                        <Info k="Jatuh Tempo" v={pinjaman.jatuh_tempo} />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle>Data Anggota</CardTitle></CardHeader>
                    <CardContent className="grid gap-3 sm:grid-cols-2">
                        <Info k="Nama" v={pinjaman.anggota?.nama} />
                        <Info k="No. Anggota" v={pinjaman.anggota?.no_anggota} />
                        <Info k="No. Identitas" v={pinjaman.anggota?.no_identitas} />
                        <Info k="Telepon" v={pinjaman.anggota?.telepon} />
                        <Info k="Alamat" v={pinjaman.anggota?.alamat} />
                        <Info k="Kantor" v={pinjaman.kantor?.nama_kantor} />
                        <Info k="Dicatat oleh" v={pinjaman.user?.nama} />
                    </CardContent>
                </Card>

                {pinjaman.biaya.length > 0 && (
                    <Section title="Biaya">
                        <Table className="rounded-md border">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nama</TableHead>
                                    <TableHead>Jenis</TableHead>
                                    <TableHead className="text-right">Nominal</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {pinjaman.biaya.map((b) => (
                                    <TableRow key={b.id}>
                                        <TableCell>{b.nama || '-'}</TableCell>
                                        <TableCell>{b.persen === '1' ? 'Persentase' : 'Nominal'} {b.persen === '1' ? `(${b.nominal}%)` : ''}</TableCell>
                                        <TableCell className="text-right font-mono">
                                            {b.persen === '1' ? '' : rupiah(b.nominal)}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </Section>
                )}

                {pinjaman.jaminan.length > 0 && (
                    <Section title="Jaminan">
                        <Table className="rounded-md border">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nama</TableHead>
                                    <TableHead>Keterangan</TableHead>
                                    <TableHead className="text-right">Nominal</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {pinjaman.jaminan.map((j) => (
                                    <TableRow key={j.id}>
                                        <TableCell>{j.nama || '-'}</TableCell>
                                        <TableCell>{j.keterangan || '-'}</TableCell>
                                        <TableCell className="text-right font-mono">{rupiah(j.nominal)}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </Section>
                )}

                {pinjaman.saksi.length > 0 && (
                    <Section title="Saksi">
                        <Table className="rounded-md border">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nama</TableHead>
                                    <TableHead>Tempat / Tgl Lahir</TableHead>
                                    <TableHead>No. KTP</TableHead>
                                    <TableHead>Alamat</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {pinjaman.saksi.map((s) => (
                                    <TableRow key={s.id}>
                                        <TableCell>{s.nama || '-'}</TableCell>
                                        <TableCell>{s.tempat_lahir ? `${s.tempat_lahir}${s.tgl_lahir ? ', ' + s.tgl_lahir : ''}` : '-'}</TableCell>
                                        <TableCell>{s.no_ktp || '-'}</TableCell>
                                        <TableCell>{s.alamat || '-'}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </Section>
                )}

                {pinjaman.surat.length > 0 && (
                    <Section title="Surat">
                        <Table className="rounded-md border">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Jenis Surat</TableHead>
                                    <TableHead>Keterangan</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {pinjaman.surat.map((s) => (
                                    <TableRow key={s.id}>
                                        <TableCell>{s.surat || '-'}</TableCell>
                                        <TableCell>{s.keterangan || '-'}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </Section>
                )}

                {pinjaman.penjamin.length > 0 && (
                    <Section title="Penjamin">
                        <Table className="rounded-md border">
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Nama</TableHead>
                                    <TableHead>Hubungan</TableHead>
                                    <TableHead>Alamat</TableHead>
                                    <TableHead>No. KTP</TableHead>
                                    <TableHead>Telepon</TableHead>
                                    <TableHead>Ibu</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {pinjaman.penjamin.map((p) => (
                                    <TableRow key={p.id}>
                                        <TableCell>{p.nama || '-'}</TableCell>
                                        <TableCell>{p.hubungan || '-'}</TableCell>
                                        <TableCell>{p.alamat || '-'}</TableCell>
                                        <TableCell>{p.no_ktp || '-'}</TableCell>
                                        <TableCell>{p.telepon || '-'}</TableCell>
                                        <TableCell>{p.ibu || '-'}</TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </Section>
                )}
            </div>
        </AuthenticatedLayout>
    );
}