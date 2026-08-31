import { useState } from 'react';
import { router } from '@inertiajs/react';
import { FileSpreadsheet, Search, Printer } from 'lucide-react';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { cn } from '@/lib/utils';

interface ReportFilterBarProps {
    routeName: string;
    filters: Record<string, string>;
    kelompoks?: Array<{ id: number; kode: string; nama: string }>;
    kantors?: Array<{ id: number; kode: string; nama_kantor: string }>;
    jenisList?: Array<{ id: number; kode: string; nama: string }>;
    marketings?: Array<{ id: number; kode: string; nama: string }>;
    sektors?: Array<{ id: number; nama: string }>;
    showDateRange?: boolean;
    showKelompok?: boolean;
    showKantor?: boolean;
    showJenis?: boolean;
    showMarketing?: boolean;
    showSektor?: boolean;
    showHariLagi?: boolean;
    printRoute?: string;
    printParams?: Record<string, string>;
    exportRoute?: string;
    exportLabel?: string;
    hariLagiLabel?: string;
    className?: string;
}

export function ReportFilterBar({
    routeName,
    filters,
    kelompoks,
    kantors,
    jenisList,
    marketings,
    sektors,
    showDateRange = false,
    showKelompok = false,
    showKantor = false,
    showJenis = false,
    showMarketing = false,
    showSektor = false,
    showHariLagi = false,
    printRoute,
    printParams,
    exportRoute,
    exportLabel = 'Export Excel',
    hariLagiLabel = 'Jatuh Tempo ≤ N hari',
    className,
}: ReportFilterBarProps) {
    const [search, setSearch] = useState(filters.search ?? '');

    const apply = (overrides: Record<string, string> = {}) => {
        const params: Record<string, string> = {
            ...filters,
            search: overrides.search ?? search,
        };

        if ('search' in overrides) {
            params.search = overrides.search;
        }

        Object.keys(params).forEach((key) => {
            if (params[key] === '' || params[key] === undefined) {
                delete params[key];
            }
        });

        router.get(route(routeName), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const updateFilter = (key: string, value: string) => {
        const params: Record<string, string> = { ...filters };

        if (value === '' || value === 'all') {
            delete params[key];
        } else {
            params[key] = value;
        }

        Object.keys(params).forEach((k) => {
            if (params[k] === '' || params[k] === undefined) {
                delete params[k];
            }
        });

        router.get(route(routeName), params, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handlePrint = () => {
        if (!printRoute) return;

        const params: Record<string, string> = { ...filters, ...printParams };

        Object.keys(params).forEach((key) => {
            if (params[key] === '' || params[key] === undefined) {
                delete params[key];
            }
        });

        window.open(route(printRoute, params), '_blank');
    };

    return (
        <div className={cn('flex flex-wrap items-center gap-3', className)}>
            <div className="relative min-w-56 flex-1">
                <Search className="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    onKeyDown={(e) => e.key === 'Enter' && apply()}
                    placeholder="Cari…"
                    className="pl-9"
                />
            </div>

            {showKelompok && kelompoks && (
                <Select
                    value={filters.kelompok_id ?? 'all'}
                    onValueChange={(v) => updateFilter('kelompok_id', v)}
                >
                    <SelectTrigger className="w-52">
                        <SelectValue placeholder="Semua Kelompok" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Kelompok</SelectItem>
                        {kelompoks.map((k) => (
                            <SelectItem key={k.id} value={String(k.id)}>
                                {k.kode} — {k.nama}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            )}

            {showKantor && kantors && (
                <Select
                    value={filters.kantor_id ?? 'all'}
                    onValueChange={(v) => updateFilter('kantor_id', v)}
                >
                    <SelectTrigger className="w-52">
                        <SelectValue placeholder="Semua Kantor" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Kantor</SelectItem>
                        {kantors.map((k) => (
                            <SelectItem key={k.id} value={String(k.id)}>
                                {k.kode} — {k.nama_kantor}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            )}

            {showJenis && jenisList && (
                <Select
                    value={filters.jenis_id ?? 'all'}
                    onValueChange={(v) => updateFilter('jenis_id', v)}
                >
                    <SelectTrigger className="w-52">
                        <SelectValue placeholder="Semua Jenis" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Jenis</SelectItem>
                        {jenisList.map((j) => (
                            <SelectItem key={j.id} value={String(j.id)}>
                                {j.kode} — {j.nama}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            )}

            {showMarketing && marketings && (
                <Select
                    value={filters.marketing_id ?? 'all'}
                    onValueChange={(v) => updateFilter('marketing_id', v)}
                >
                    <SelectTrigger className="w-52">
                        <SelectValue placeholder="Semua Marketing" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Marketing</SelectItem>
                        {marketings.map((m) => (
                            <SelectItem key={m.id} value={String(m.id)}>
                                {m.kode} — {m.nama}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            )}

            {showSektor && sektors && (
                <Select
                    value={filters.sektor_id ?? 'all'}
                    onValueChange={(v) => updateFilter('sektor_id', v)}
                >
                    <SelectTrigger className="w-40">
                        <SelectValue placeholder="Semua Sektor" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Semua Sektor</SelectItem>
                        {sektors.map((s) => (
                            <SelectItem key={s.id} value={String(s.id)}>
                                {s.nama}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            )}

            {showDateRange && (
                <>
                    <div className="flex items-center gap-2">
                        <span className="text-sm text-muted-foreground">Mulai</span>
                        <Input
                            type="date"
                            value={filters.mulai ?? ''}
                            onChange={(e) => updateFilter('mulai', e.target.value)}
                            className="w-40"
                        />
                    </div>
                    <div className="flex items-center gap-2">
                        <span className="text-sm text-muted-foreground">Sampai</span>
                        <Input
                            type="date"
                            value={filters.sampai ?? ''}
                            onChange={(e) => updateFilter('sampai', e.target.value)}
                            className="w-40"
                        />
                    </div>
                </>
            )}

            {showHariLagi && (
                <div className="flex items-center gap-2">
                    <span className="text-sm text-muted-foreground">{hariLagiLabel}</span>
                    <Input
                        type="number"
                        min={0}
                        value={filters.hari_lagi ?? ''}
                        onChange={(e) => updateFilter('hari_lagi', e.target.value)}
                        onKeyDown={(e) => e.key === 'Enter' && apply()}
                        className="w-32"
                        placeholder="N hari"
                    />
                </div>
            )}

            {printRoute && (
                <Button variant="outline" onClick={handlePrint}>
                    <Printer className="size-4" />
                    Cetak
                </Button>
            )}

            {exportRoute && (
                <Button
                    variant="outline"
                    onClick={() => {
                        const params: Record<string, string> = { ...filters };
                        Object.keys(params).forEach((key) => {
                            if (params[key] === '' || params[key] === undefined) {
                                delete params[key];
                            }
                        });
                        window.open(route(exportRoute, params), '_blank');
                    }}
                >
                    <FileSpreadsheet className="size-4" />
                    {exportLabel}
                </Button>
            )}
        </div>
    );
}
