import React from 'react';
import { Head } from '@inertiajs/react';
import { ArrowDownUp, BookImage, Search, X } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { ReportFilterBar } from '@/Components/ReportFilterBar';
import { Card } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import type { Paginated } from '@/types/models';

interface TransaksiRow {
    id: number;
    no_transaksi: string;
    tgl_transaksi: string;
    nominal: string | number;
    keterangan: string;
    anggota?: { id: number; no_anggota: string; nama: string };
    simpanan?: { id: number; no_rekening: string; jenis_simpanan?: { id: number; kode: string; nama: string } };
    kodeTransaksi?: { id: number; kode: string; nama: string };
    kantor?: { id: number; kode: string; nama_kantor: string };
}

interface SimpananItem {
    id: number;
    no_rekening: string;
    anggota?: { no_anggota?: string; nama?: string };
}

interface BookItem {
    no_transaksi: string;
    tgl_transaksi: string;
    kode: string;
    setoran: boolean;
    nominal: number;
    keterangan: string | null;
    opt: string;
}

interface Props {
    data: Paginated<TransaksiRow>;
    filters: Record<string, string>;
    kantors?: Array<{ id: number; kode: string; nama_kantor: string }>;
    jenisList?: Array<{ id: number; kode: string; nama: string }>;
    simpananList?: SimpananItem[];
    variantTitle: string;
}

export default function TransaksiSimpanan({ data, filters, kantors, jenisList, simpananList, variantTitle }: Props) {
    const [search, setSearch] = React.useState('');
    const [selectedRekening, setSelectedRekening] = React.useState<string>('');
    const [selectedLabel, setSelectedLabel] = React.useState('');
    const [showSuggestions, setShowSuggestions] = React.useState(false);
    const [bookItems, setBookItems] = React.useState<BookItem[] | null>(null);
    const [loading, setLoading] = React.useState(false);
    const [paperWidth, setPaperWidth] = React.useState(140);
    const [paperHeight, setPaperHeight] = React.useState(200);
    const [marginTop, setMarginTop] = React.useState(15);
    const [marginLeft, setMarginLeft] = React.useState(5);
    const [marginRight, setMarginRight] = React.useState(5);
    const [fontSize, setFontSize] = React.useState(8);
    const [lineHeight, setLineHeight] = React.useState(4);
    const [startFrom, setStartFrom] = React.useState(1);
    const [skipLines, setSkipLines] = React.useState(0);
    const [totalLines, setTotalLines] = React.useState(30);
    const [colNo, setColNo] = React.useState(8);
    const [colTanggal, setColTanggal] = React.useState(22);
    const [colKode, setColKode] = React.useState(12);
    const [colDebet, setColDebet] = React.useState(22);
    const [colKredit, setColKredit] = React.useState(22);
    const [colOpt, setColOpt] = React.useState(14);

    const list = simpananList ?? [];
    const filtered = list.filter(
        (s) =>
            s.no_rekening.toLowerCase().includes(search.toLowerCase()) ||
            (s.anggota?.nama ?? '').toLowerCase().includes(search.toLowerCase()) ||
            (s.anggota?.no_anggota ?? '').toLowerCase().includes(search.toLowerCase())
    );

    const selectRekening = (item: SimpananItem) => {
        setSelectedRekening(String(item.id));
        setSelectedLabel(`${item.no_rekening}${item.anggota?.nama ? ' — ' + item.anggota.nama : ''}`);
        setSearch(item.no_rekening);
        setShowSuggestions(false);
        setBookItems(null);
        fetchData(String(item.id));
    };

    const fetchData = async (id: string) => {
        setLoading(true);
        setBookItems(null);
        try {
            const res = await fetch(`/superadmin/laporan-cs/simpanan/buku-tabungan/data?simpanan_id=${encodeURIComponent(id)}`);
            const json = await res.json();
            setBookItems(json?.items ?? []);
        } catch {
            setBookItems([]);
        } finally {
            setLoading(false);
        }
    };

    const handleCetakBuku = () => {
        if (!selectedRekening) return;
        const params = new URLSearchParams({
            simpanan_id: selectedRekening,
            start_from: String(startFrom),
            skip_lines: String(skipLines),
            total_lines: String(totalLines),
            paper_width: String(paperWidth),
            paper_height: String(paperHeight),
            margin_top: String(marginTop),
            margin_left: String(marginLeft),
            margin_right: String(marginRight),
            font_size: String(fontSize),
            line_height: String(lineHeight),
            col_no: String(colNo),
            col_tanggal: String(colTanggal),
            col_kode: String(colKode),
            col_debet: String(colDebet),
            col_kredit: String(colKredit),
            col_opt: String(colOpt),
        });
        window.open(`/superadmin/laporan-cs/simpanan/buku-tabungan/cetak?${params.toString()}`, '_blank');
    };

    let runningBalance = 0;
    for (const it of bookItems ?? []) {
        if (it.setoran) runningBalance += it.nominal;
        else runningBalance -= it.nominal;
    }

    return (
        <AuthenticatedLayout>
            <Head title={variantTitle || 'Transaksi Simpanan'} />

            <PageHeader
                title={variantTitle || 'Transaksi Simpanan'}
                description="Daftar transaksi simpanan anggota."
                icon={ArrowDownUp}
            />

            <Card className="gap-4 py-5">
                <div className="px-5">
                    <div className="flex flex-wrap items-center gap-3">
                        <div className="flex-1">
                            <ReportFilterBar
                                routeName="superadmin.laporan-cs.simpanan.transaksi-simpanan"
                                filters={filters}
                                kantors={kantors}
                                showKantor
                                showDateRange
                                printRoute="superadmin.laporan-cs.simpanan.transaksi-simpanan.cetak"
                            />
                        </div>
                    </div>
                </div>

                {list.length > 0 && (
                    <div className="mx-5 rounded-lg border bg-muted/30 p-4">
                        <div className="mb-3 flex items-center gap-2 text-sm font-medium">
                            <BookImage className="size-4" />
                            Cetak Buku Tabungan (Passbook)
                        </div>

                        <p className="mb-3 text-xs text-muted-foreground">
                            Ketik <b>no. rekening</b> atau <b>nama anggota</b> untuk mencari, lalu pilih hasilnya.
                            Isi <b>Transaksi #</b> = nomor transaksi pertama yang dicetak.
                            Isi <b>Skip Baris</b> = jumlah baris di halaman yang sudah tercetak sebelumnya.
                            <b> Total Baris</b> = jumlah baris maksimal per halaman buku tabungan.
                        </p>

                        <div className="flex flex-wrap items-end gap-3">
                            <div className="relative w-72">
                                <label className="mb-1 block text-xs text-muted-foreground">Cari No. Rekening / Anggota</label>
                                <div className="relative">
                                    <Search className="pointer-events-none absolute left-2.5 top-2.5 size-4 text-muted-foreground" />
                                    <input
                                        type="text"
                                        value={search}
                                        onChange={(e) => {
                                            setSearch(e.target.value);
                                            setShowSuggestions(true);
                                            setSelectedRekening('');
                                            setSelectedLabel('');
                                            setBookItems(null);
                                        }}
                                        onFocus={() => setShowSuggestions(true)}
                                        placeholder="Ketik RKB-... atau nama..."
                                        className="flex h-9 w-full rounded-md border border-input bg-transparent pl-8 pr-8 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                    />
                                    {search && (
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setSearch('');
                                                setSelectedRekening('');
                                                setSelectedLabel('');
                                                setBookItems(null);
                                            }}
                                            className="absolute right-2 top-2.5 text-muted-foreground hover:text-foreground"
                                        >
                                            <X className="size-4" />
                                        </button>
                                    )}
                                </div>
                                {showSuggestions && filtered.length > 0 && (
                                    <div className="absolute z-20 mt-1 max-h-56 w-full overflow-auto rounded-md border bg-popover py-1 shadow-md">
                                        {filtered.map((s) => (
                                            <button
                                                key={s.id}
                                                type="button"
                                                onClick={() => selectRekening(s)}
                                                className="block w-full px-3 py-2 text-left text-sm hover:bg-muted"
                                            >
                                                <span className="font-mono text-xs">{s.no_rekening}</span>
                                                {s.anggota?.nama && (
                                                    <span className="ml-2 text-muted-foreground">— {s.anggota.nama}</span>
                                                )}
                                                {s.anggota?.no_anggota && (
                                                    <span className="ml-1 text-muted-foreground">({s.anggota.no_anggota})</span>
                                                )}
                                            </button>
                                        ))}
                                    </div>
                                )}
                                {showSuggestions && search && filtered.length === 0 && (
                                    <div className="absolute z-20 mt-1 w-full rounded-md border bg-popover px-3 py-2 text-sm text-muted-foreground shadow-md">
                                        Tidak ditemukan.
                                    </div>
                                )}
                            </div>

                            {selectedLabel && (
                                <div className="flex items-center gap-2 text-sm">
                                    <span className="text-muted-foreground">Terpilih:</span>
                                    <span className="rounded-md bg-brand-50 px-2 py-1 font-mono text-xs font-medium">{selectedLabel}</span>
                                </div>
                            )}

                            <div className="w-32">
                                <label className="mb-1 block text-xs text-muted-foreground">Transaksi #</label>
                                <input type="number" min={1} value={startFrom} onChange={(e) => setStartFrom(Math.max(1, Number(e.target.value)))} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                            </div>
                            <div className="w-32">
                                <label className="mb-1 block text-xs text-muted-foreground">Skip Baris</label>
                                <input type="number" min={0} value={skipLines} onChange={(e) => setSkipLines(Math.max(0, Number(e.target.value)))} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                            </div>
                            <div className="w-32">
                                <label className="mb-1 block text-xs text-muted-foreground">Total Baris</label>
                                <input type="number" min={1} value={totalLines} onChange={(e) => setTotalLines(Math.max(1, Number(e.target.value)))} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                            </div>
                            <Button onClick={handleCetakBuku} disabled={!selectedRekening}>
                                <BookImage className="size-4" />
                                Cetak Buku
                            </Button>
                        </div>

                        <details className="mt-3">
                            <summary className="cursor-pointer text-xs text-muted-foreground hover:text-foreground">Pengaturan Kertas, Kolom & Font</summary>
                            <div className="mt-2 flex flex-wrap items-end gap-3">
                                <div className="w-24">
                                    <label className="mb-1 block text-xs text-muted-foreground">Lebar (mm)</label>
                                    <input type="number" value={paperWidth} onChange={(e) => setPaperWidth(Number(e.target.value))} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                </div>
                                <div className="w-24">
                                    <label className="mb-1 block text-xs text-muted-foreground">Tinggi (mm)</label>
                                    <input type="number" value={paperHeight} onChange={(e) => setPaperHeight(Number(e.target.value))} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                </div>
                                <div className="w-24">
                                    <label className="mb-1 block text-xs text-muted-foreground">Margin Atas</label>
                                    <input type="number" value={marginTop} onChange={(e) => setMarginTop(Number(e.target.value))} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                </div>
                                <div className="w-24">
                                    <label className="mb-1 block text-xs text-muted-foreground">Margin Kiri</label>
                                    <input type="number" value={marginLeft} onChange={(e) => setMarginLeft(Number(e.target.value))} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                </div>
                                <div className="w-24">
                                    <label className="mb-1 block text-xs text-muted-foreground">Margin Kanan</label>
                                    <input type="number" value={marginRight} onChange={(e) => setMarginRight(Number(e.target.value))} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                </div>
                                <div className="w-24">
                                    <label className="mb-1 block text-xs text-muted-foreground">Font (px)</label>
                                    <input type="number" value={fontSize} onChange={(e) => setFontSize(Number(e.target.value))} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                </div>
                                <div className="w-24">
                                    <label className="mb-1 block text-xs text-muted-foreground">Line Height</label>
                                    <input type="number" value={lineHeight} onChange={(e) => setLineHeight(Number(e.target.value))} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                </div>
                            </div>

                            <div className="mt-3 border-t pt-3 text-xs font-medium text-muted-foreground">Lebar Kolom (mm) — kolom Saldo mengambil sisa lebar otomatis</div>
                            <div className="mt-1 flex flex-wrap items-end gap-3">
                                <div className="w-20">
                                    <label className="mb-1 block text-xs text-muted-foreground">No</label>
                                    <input type="number" value={colNo} onChange={(e) => setColNo(Math.max(0, Number(e.target.value)))} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                </div>
                                <div className="w-20">
                                    <label className="mb-1 block text-xs text-muted-foreground">Tanggal</label>
                                    <input type="number" value={colTanggal} onChange={(e) => setColTanggal(Math.max(0, Number(e.target.value)))} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                </div>
                                <div className="w-20">
                                    <label className="mb-1 block text-xs text-muted-foreground">Kode</label>
                                    <input type="number" value={colKode} onChange={(e) => setColKode(Math.max(0, Number(e.target.value)))} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                </div>
                                <div className="w-20">
                                    <label className="mb-1 block text-xs text-muted-foreground">Debet</label>
                                    <input type="number" value={colDebet} onChange={(e) => setColDebet(Math.max(0, Number(e.target.value)))} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                </div>
                                <div className="w-20">
                                    <label className="mb-1 block text-xs text-muted-foreground">Kredit</label>
                                    <input type="number" value={colKredit} onChange={(e) => setColKredit(Math.max(0, Number(e.target.value)))} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                </div>
                                <div className="w-20">
                                    <label className="mb-1 block text-xs text-muted-foreground">Opt ID</label>
                                    <input type="number" value={colOpt} onChange={(e) => setColOpt(Math.max(0, Number(e.target.value)))} className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" />
                                </div>
                            </div>
                        </details>

                        {(loading || bookItems !== null) && (
                            <div className="mt-4 border-t pt-4">
                                <div className="mb-2 flex items-center justify-between">
                                    <div className="text-sm font-medium">
                                        Data Transaksi{' '}
                                        {bookItems ? (
                                            <span className="text-muted-foreground">
                                                ({bookItems.length} baris — saldo akhir {runningBalance.toLocaleString('id-ID')})
                                            </span>
                                        ) : null}
                                    </div>
                                    {loading && <span className="text-xs text-muted-foreground">Memuat...</span>}
                                </div>
                                <div className="max-h-96 overflow-auto rounded-md border">
                                    <Table>
                                        <TableHeader className="sticky top-0 bg-background">
                                            <TableRow>
                                                <TableHead className="w-10">No</TableHead>
                                                <TableHead>Tanggal</TableHead>
                                                <TableHead>Kode</TableHead>
                                                <TableHead className="text-right">Mutasi Debet</TableHead>
                                                <TableHead className="text-right">Mutasi Kredit</TableHead>
                                                <TableHead className="text-right">Saldo</TableHead>
                                                <TableHead>Opt ID</TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            {bookItems && bookItems.length === 0 && (
                                                <TableRow>
                                                    <TableCell colSpan={7} className="h-24 text-center text-muted-foreground">
                                                        Tidak ada transaksi. Data kosong.
                                                    </TableCell>
                                                </TableRow>
                                            )}
                                            {bookItems?.map((it, i) => {
                                                const prev = bookItems.slice(0, i).reduce((acc, x) => acc + (x.setoran ? x.nominal : -x.nominal), 0);
                                                const saldo = prev + (it.setoran ? it.nominal : -it.nominal);
                                                return (
                                                    <TableRow key={it.no_transaksi + i}>
                                                        <TableCell className="text-muted-foreground">{i + 1}</TableCell>
                                                        <TableCell className="text-muted-foreground">
                                                            {it.tgl_transaksi ? new Date(it.tgl_transaksi + 'T00:00:00').toLocaleDateString('id-ID') : '—'}
                                                        </TableCell>
                                                        <TableCell>
                                                            <span className="rounded bg-muted px-1.5 py-0.5 font-mono text-xs">{it.kode}</span>
                                                        </TableCell>
                                                        <TableCell className="text-right text-muted-foreground">
                                                            {!it.setoran ? it.nominal.toLocaleString('id-ID') : ''}
                                                        </TableCell>
                                                        <TableCell className="text-right text-emerald-600">
                                                            {it.setoran ? it.nominal.toLocaleString('id-ID') : ''}
                                                        </TableCell>
                                                        <TableCell className="text-right font-medium">
                                                            {saldo.toLocaleString('id-ID')}
                                                        </TableCell>
                                                        <TableCell className="text-muted-foreground">{it.opt}</TableCell>
                                                    </TableRow>
                                                );
                                            })}
                                        </TableBody>
                                    </Table>
                                </div>
                            </div>
                        )}
                    </div>
                )}

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>No Transaksi</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>No Anggota</TableHead>
                                <TableHead>Nama Anggota</TableHead>
                                <TableHead>No Rekening</TableHead>
                                <TableHead>Jenis Simpanan</TableHead>
                                <TableHead>Kode Transaksi</TableHead>
                                <TableHead>Nominal</TableHead>
                                <TableHead>Keterangan</TableHead>
                                <TableHead>Kantor</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {data.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={11} className="h-32 text-center text-muted-foreground">
                                        Tidak ada data.
                                    </TableCell>
                                </TableRow>
                            )}
                            {data.data.map((item, i) => (
                                <TableRow key={item.id}>
                                    <TableCell className="text-muted-foreground">
                                        {data.from !== null ? data.from + i : i + 1}
                                    </TableCell>
                                    <TableCell>
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.no_transaksi}
                                        </span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.tgl_transaksi ? new Date(item.tgl_transaksi).toLocaleDateString('id-ID') : '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.anggota?.no_anggota ?? '—'}
                                    </TableCell>
                                    <TableCell>
                                        <span className="font-medium">{item.anggota?.nama ?? '—'}</span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                                            {item.simpanan?.no_rekening ?? '—'}
                                        </span>
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.simpanan?.jenis_simpanan?.nama ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.kodeTransaksi?.kode ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {Number(item.nominal).toLocaleString('id-ID')}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.keterangan ?? '—'}
                                    </TableCell>
                                    <TableCell className="text-muted-foreground">
                                        {item.kantor?.nama_kantor ?? '—'}
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>

                <div className="border-t px-5 pt-4">
                    <Pagination
                        links={data.links}
                        currentPage={data.current_page}
                        lastPage={data.last_page}
                        from={data.from}
                        to={data.to}
                        total={data.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}
