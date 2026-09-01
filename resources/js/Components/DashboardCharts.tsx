import { useMemo } from 'react';

/* ============================================================
   Grafik SVG ringan (tanpa dependensi eksternal).
   ============================================================ */

const PALETTE = [
    '#f43f5e', // rose-500
    '#0ea5e9', // sky-500
    '#10b981', // emerald-500
    '#8b5cf6', // violet-500
    '#f59e0b', // amber-500
    '#06b6d4', // cyan-500
    '#ec4899', // pink-500
    '#3b82f6', // blue-500
];

function niceMax(values: number[]): number {
    const max = Math.max(...values, 1);
    const magnitude = Math.pow(10, Math.floor(Math.log10(max)));
    const normalized = max / magnitude;
    let step: number;
    if (normalized <= 1) step = 1;
    else if (normalized <= 2) step = 2;
    else if (normalized <= 5) step = 5;
    else step = 10;
    return step * magnitude;
}

function shortRupiah(value: number): string {
    if (value >= 1_000_000_000) return (value / 1_000_000_000).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' M';
    if (value >= 1_000_000) return (value / 1_000_000).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' jt';
    if (value >= 1_000) return (value / 1_000).toLocaleString('id-ID', { maximumFractionDigits: 0 }) + ' rb';
    return String(Math.round(value));
}

interface BarDatum {
    label: string;
    values: number[];
}

/** Grafik batang berkelompok (mis. arus kas per bulan). */
export function GroupedBarChart({
    data,
    series,
    height = 240,
}: {
    data: BarDatum[];
    series: { key: string; label: string; color: string }[];
    height?: number;
}) {
    const width = 640;

    const chart = useMemo(() => {
        const allValues = data.flatMap((d) => d.values);
        const max = niceMax(allValues);
        const padX = 32;
        const padTop = 18;
        const padBottom = 30;
        const innerW = width - padX * 2;
        const innerH = height - padTop - padBottom;

        const stepW = data.length ? innerW / data.length : innerW;
        const groupW = Math.min(stepW * 0.6, 40);
        const barW = series.length ? groupW / series.length : groupW;

        const gridLines = [0.25, 0.5, 0.75, 1].map((f) => ({
            y: padTop + innerH * (1 - f),
            val: max * f,
        }));

        return { max, padX, padTop, innerW, innerH, stepW, groupW, barW, gridLines };
    }, [data, series, height, width]);

    return (
        <svg viewBox={`0 0 ${width} ${height}`} className="w-full" role="img" aria-label="Grafik batang">
            {chart.gridLines.map((g, i) => (
                <g key={i}>
                    <line x1={chart.padX} x2={width - chart.padX} y1={g.y} y2={g.y} stroke="currentColor" strokeOpacity="0.08" strokeWidth="1" />
                    <text x={chart.padX - 6} y={g.y + 3} textAnchor="end" fontSize="9" fill="currentColor" fillOpacity="0.45">
                        {shortRupiah(g.val)}
                    </text>
                </g>
            ))}

            {data.map((d, i) => {
                const x0 = chart.padX + i * chart.stepW + (chart.stepW - chart.groupW) / 2;
                return (
                    <g key={i}>
                        {d.values.map((v, j) => {
                            const barH = (v / chart.max) * chart.innerH;
                            return (
                                <rect
                                    key={j}
                                    x={x0 + j * chart.barW}
                                    y={chart.padTop + chart.innerH - barH}
                                    width={chart.barW - 1.5}
                                    height={Math.max(barH, v > 0 ? 1 : 0)}
                                    rx="2"
                                    fill={series[j].color}
                                />
                            );
                        })}
                        <text
                            x={x0 + chart.groupW / 2}
                            y={height - 8}
                            textAnchor="middle"
                            fontSize="9"
                            fontWeight="600"
                            fill="currentColor"
                            fillOpacity="0.7"
                        >
                            {d.label}
                        </text>
                    </g>
                );
            })}
        </svg>
    );
}

/** Grafik batang tunggal sederhana (mis. anggota baru per bulan). */
export function SingleBarChart({
    data,
    color = PALETTE[0],
    height = 220,
}: {
    data: { label: string; value: number }[];
    color?: string;
    height?: number;
}) {
    const width = 640;
    const chart = useMemo(() => {
        const max = niceMax(data.map((d) => d.value));
        const padX = 32;
        const padTop = 18;
        const padBottom = 30;
        const innerW = width - padX * 2;
        const innerH = height - padTop - padBottom;
        const stepW = data.length ? innerW / data.length : innerW;
        const barW = Math.min(stepW * 0.55, 48);
        const gridLines = [0.25, 0.5, 0.75, 1].map((f) => ({
            y: padTop + innerH * (1 - f),
            val: Math.round(max * f),
        }));
        return { max, padX, padTop, innerW, innerH, stepW, barW, gridLines };
    }, [data, height, width]);

    return (
        <svg viewBox={`0 0 ${width} ${height}`} className="w-full" role="img" aria-label="Grafik batang">
            {chart.gridLines.map((g, i) => (
                <line key={i} x1={chart.padX} x2={width - chart.padX} y1={g.y} y2={g.y} stroke="currentColor" strokeOpacity="0.08" strokeWidth="1" />
            ))}
            {data.map((d, i) => {
                const barH = (d.value / chart.max) * chart.innerH;
                const x0 = chart.padX + i * chart.stepW + (chart.stepW - chart.barW) / 2;
                return (
                    <g key={i}>
                        <rect
                            x={x0}
                            y={chart.padTop + chart.innerH - barH}
                            width={chart.barW}
                            height={Math.max(barH, d.value > 0 ? 1 : 0)}
                            rx="2"
                            fill={color}
                            fillOpacity="0.85"
                        />
                        {d.value > 0 && (
                            <text
                                x={x0 + chart.barW / 2}
                                y={chart.padTop + chart.innerH - barH - 4}
                                textAnchor="middle"
                                fontSize="9"
                                fontWeight="700"
                                fill="currentColor"
                                fillOpacity="0.8"
                            >
                                {d.value.toLocaleString('id-ID')}
                            </text>
                        )}
                        <text
                            x={x0 + chart.barW / 2}
                            y={height - 8}
                            textAnchor="middle"
                            fontSize="9"
                            fontWeight="600"
                            fill="currentColor"
                            fillOpacity="0.7"
                        >
                            {d.label}
                        </text>
                    </g>
                );
            })}
        </svg>
    );
}

/** Grafik distribusi horizontal (jumlah + nominal per kategori). */
export function HorizontalBar({
    data,
    money = true,
}: {
    data: { label: string; jumlah: number; nominal: number }[];
    money?: boolean;
}) {
    const chart = useMemo(() => {
        const maxCount = niceMax(data.map((d) => d.jumlah));
        const maxNominal = niceMax(data.map((d) => d.nominal));
        return { maxCount, maxNominal };
    }, [data]);

    if (data.length === 0) {
        return <p className="py-6 text-center text-sm text-muted-foreground">Belum ada data.</p>;
    }

    const total = data.reduce((s, d) => s + d.jumlah, 0);
    const nominalTotal = data.reduce((s, d) => s + d.nominal, 0);

    return (
        <div className="space-y-3">
            {data.map((d, i) => {
                const pctCount = chart.maxCount ? (d.jumlah / chart.maxCount) * 100 : 0;
                const pctNominal = chart.maxNominal ? (d.nominal / chart.maxNominal) * 100 : 0;
                const color = PALETTE[i % PALETTE.length];
                const share = total ? (d.jumlah / total) * 100 : 0;
                return (
                    <div key={d.label}>
                        <div className="mb-1 flex items-center justify-between gap-2">
                            <span className="truncate text-xs font-medium">{d.label}</span>
                            <span className="shrink-0 text-xs text-muted-foreground">
                                {d.jumlah.toLocaleString('id-ID')} · {share.toFixed(0)}%
                                {money && <> · <span className="font-semibold">{d.nominal.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 })}</span></>}
                            </span>
                        </div>
                        <div className="flex h-2.5 w-full gap-1 overflow-hidden rounded-full bg-muted">
                            <div className="h-full rounded-full" style={{ width: `${pctCount}%`, backgroundColor: color }} />
                            {money && (
                                <div className="h-full rounded-full bg-muted-foreground/20" style={{ width: `${pctNominal}%` }} />
                            )}
                        </div>
                    </div>
                );
            })}
            <p className="pt-1 text-xs text-muted-foreground">
                Total {data.reduce((s, d) => s + d.jumlah, 0).toLocaleString('id-ID')} {money && <>· {nominalTotal.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 })}</>}
            </p>
        </div>
    );
}
