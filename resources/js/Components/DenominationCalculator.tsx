import { useState, useMemo, useEffect } from 'react';
import { Calculator, Minus, Plus, Trash2, CheckCircle2, XCircle } from 'lucide-react';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';

interface DenominationCalculatorProps {
    label: string;
    value: string;
    onChange: (value: string) => void;
    error?: string;
    required?: boolean;
    id: string;
}

const DENOMS = [
    { value: 100, type: 'coin' as const },
    { value: 200, type: 'coin' as const },
    { value: 500, type: 'coin' as const },
    { value: 1000, type: 'coin' as const },
    { value: 1000, type: 'note' as const },
    { value: 2000, type: 'note' as const },
    { value: 5000, type: 'note' as const },
    { value: 10000, type: 'note' as const },
    { value: 20000, type: 'note' as const },
    { value: 50000, type: 'note' as const },
    { value: 100000, type: 'note' as const },
];

const fmtRp = (v: number) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(v);

const fmtShort = (v: number) => v >= 1000 ? `${v / 1000}rb` : `${v}`;

export function DenominationCalculator({ label, value, onChange, error, required = false, id }: DenominationCalculatorProps) {
    const [open, setOpen] = useState(false);
    const [counts, setCounts] = useState<Record<number, number>>({});
    const [lastVal, setLastVal] = useState(value);

    useEffect(() => {
        if (value !== lastVal) {
            let rem = parseFloat(value.replace(/[^0-9]/g, '')) || 0;
            const c: Record<number, number> = {};
            [...DENOMS].reverse().forEach(d => {
                const n = Math.floor(rem / d.value);
                if (n > 0) { c[d.value] = n; rem -= n * d.value; }
            });
            setCounts(c);
            setLastVal(value);
        }
    }, [value]);

    const totalCalc = useMemo(() =>
        Object.entries(counts).reduce((s, [d, n]) => s + Number(d) * n, 0), [counts]);

    const manualVal = parseFloat(value.replace(/[^0-9]/g, '')) || 0;
    const diff = totalCalc > 0 && totalCalc !== manualVal;
    const hasAny = Object.values(counts).some(c => c > 0);

    const update = (dv: number, n: number) => {
        const next = { ...counts };
        n > 0 ? (next[dv] = n) : delete next[dv];
        setCounts(next);
        const t = Object.entries(next).reduce((s, [d, c]) => s + Number(d) * c, 0);
        onChange(String(t));
        setLastVal(String(t));
    };

    const inc = (dv: number) => update(dv, (counts[dv] || 0) + 1);
    const dec = (dv: number) => { const c = counts[dv] || 0; if (c > 0) update(dv, c - 1); };
    const clear = () => { setCounts({}); onChange('0'); setLastVal('0'); };

    // Unique denominations (merge coin/note 1000)
    const uniqueDenoms = useMemo(() => {
        const seen = new Set<number>();
        return DENOMS.filter(d => {
            if (seen.has(d.value)) return false;
            seen.add(d.value);
            return true;
        }).reverse(); // highest first
    }, []);

    const coins = uniqueDenoms.filter(d => d.value < 1000);
    const notes = uniqueDenoms.filter(d => d.value >= 1000);

    return (
        <div className="space-y-1.5">
            <div className="flex items-center justify-between gap-2">
                <Label htmlFor={id} className="text-sm">
                    {label} {required && <span className="text-red-500">*</span>}
                </Label>
                <Button type="button" variant="ghost" size="sm" className="h-7 px-2 text-xs gap-1"
                    onClick={() => setOpen(!open)}>
                    <Calculator className="h-3.5 w-3.5" />
                    {open ? 'Tutup' : 'Pecahan'}
                    {hasAny && <span className="font-mono text-muted-foreground">({fmtRp(totalCalc)})</span>}
                </Button>
            </div>

            <div className="relative">
                <Input id={id} type="number" min={0} step={100} value={value}
                    onChange={e => { onChange(e.target.value); setLastVal(e.target.value); }}
                    className={error ? 'border-red-500' : ''} required={required} placeholder="0" />
                {hasAny && diff && (
                    <button type="button" onClick={() => { onChange(String(totalCalc)); setLastVal(String(totalCalc)); }}
                        className="absolute right-2 top-1/2 -translate-y-1/2 text-amber-600 hover:text-amber-700" title="Gunakan total pecahan">
                        <CheckCircle2 className="h-4 w-4" />
                    </button>
                )}
            </div>
            {error && <p className="text-xs text-red-500">{error}</p>}

            {open && (
                <div className="rounded-lg border bg-muted/30 p-2.5 space-y-2 text-sm animate-in fade-in slide-in-from-top-1 duration-150">
                    {/* Koin */}
                    {coins.length > 0 && (
                        <div className="space-y-1">
                            <span className="text-[10px] font-medium text-amber-600 uppercase tracking-wider">Koin</span>
                            <div className="grid grid-cols-2 sm:grid-cols-4 gap-1">
                                {coins.map(d => (
                                    <DenomCell key={d.value} val={d.value} type="coin" count={counts[d.value] || 0}
                                        onInc={() => inc(d.value)} onDec={() => dec(d.value)} />
                                ))}
                            </div>
                        </div>
                    )}
                    {/* Uang Kertas */}
                    {notes.length > 0 && (
                        <div className="space-y-1">
                            <span className="text-[10px] font-medium text-emerald-600 uppercase tracking-wider">Kertas</span>
                            <div className="grid grid-cols-2 sm:grid-cols-4 gap-1">
                                {notes.map(d => (
                                    <DenomCell key={d.value} val={d.value} type="note" count={counts[d.value] || 0}
                                        onInc={() => inc(d.value)} onDec={() => dec(d.value)} />
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Summary */}
                    {hasAny && (
                        <div className="flex items-center gap-3 pt-1.5 border-t text-xs">
                            <span className="font-medium">Total: <span className="font-mono font-bold text-sm">{fmtRp(totalCalc)}</span></span>
                            {diff && <span className="text-amber-600">≠ Input: {fmtRp(manualVal)}</span>}
                            {!diff && <span className="text-emerald-600 flex items-center gap-1"><CheckCircle2 className="h-3 w-3" />Match</span>}
                            <Button type="button" variant="ghost" size="sm" className="h-6 ml-auto text-red-600 hover:text-red-700 px-1.5"
                                onClick={clear}>
                                <Trash2 className="h-3 w-3 mr-1" />Reset
                            </Button>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}

function DenomCell({ val, type, count, onInc, onDec }: { val: number; type: 'coin' | 'note'; count: number; onInc: () => void; onDec: () => void }) {
    const active = count > 0;
    const color = type === 'coin' ? 'text-amber-700 bg-amber-50 border-amber-200' : 'text-emerald-700 bg-emerald-50 border-emerald-200';
    const activeRing = type === 'coin' ? 'ring-amber-300' : 'ring-emerald-300';

    return (
        <div className={`flex items-center gap-1 px-1.5 py-1 rounded-md border transition-all ${color} ${active ? `ring-1 ${activeRing}` : 'opacity-70'}`}>
            <button type="button" onClick={onDec} disabled={!active}
                className="h-5 w-5 flex items-center justify-center rounded hover:bg-white/50 disabled:opacity-30">
                <Minus className="h-3 w-3" />
            </button>
            <div className="flex-1 min-w-0 text-center">
                <span className="font-mono text-[11px] font-bold block leading-tight">{fmtShort(val)}</span>
                {active && <span className="font-mono text-[10px] leading-tight">×{count} = {fmtShort(val * count)}</span>}
            </div>
            <button type="button" onClick={onInc}
                className="h-5 w-5 flex items-center justify-center rounded hover:bg-white/50">
                <Plus className="h-3 w-3" />
            </button>
        </div>
    );
}
