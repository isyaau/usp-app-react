import { useState, useMemo, useEffect } from 'react';
import { ChevronDown, ChevronUp, Coins, Banknote, Calculator, Minus, Plus, Trash2, CheckCircle2, XCircle } from 'lucide-react';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Separator } from '@/Components/ui/separator';
import { Badge } from '@/Components/ui/badge';

interface DenominationCalculatorProps {
    label: string;
    value: string;
    onChange: (value: string) => void;
    error?: string;
    required?: boolean;
    id: string;
}

const INDONESIAN_DENOMINATIONS = [
    // Coins
    { value: 100, label: 'Rp 100', shortLabel: '100', type: 'coin' as const, iconColor: 'text-amber-600', bgColor: 'bg-amber-50 border-amber-200' },
    { value: 200, label: 'Rp 200', shortLabel: '200', type: 'coin' as const, iconColor: 'text-amber-600', bgColor: 'bg-amber-50 border-amber-200' },
    { value: 500, label: 'Rp 500', shortLabel: '500', type: 'coin' as const, iconColor: 'text-amber-600', bgColor: 'bg-amber-50 border-amber-200' },
    { value: 1000, label: 'Rp 1.000', shortLabel: '1K', type: 'coin' as const, iconColor: 'text-amber-600', bgColor: 'bg-amber-50 border-amber-200' },
    // Banknotes
    { value: 1000, label: 'Rp 1.000', shortLabel: '1K', type: 'note' as const, iconColor: 'text-green-600', bgColor: 'bg-green-50 border-green-200' },
    { value: 2000, label: 'Rp 2.000', shortLabel: '2K', type: 'note' as const, iconColor: 'text-green-600', bgColor: 'bg-green-50 border-green-200' },
    { value: 5000, label: 'Rp 5.000', shortLabel: '5K', type: 'note' as const, iconColor: 'text-green-600', bgColor: 'bg-green-50 border-green-200' },
    { value: 10000, label: 'Rp 10.000', shortLabel: '10K', type: 'note' as const, iconColor: 'text-green-600', bgColor: 'bg-green-50 border-green-200' },
    { value: 20000, label: 'Rp 20.000', shortLabel: '20K', type: 'note' as const, iconColor: 'text-green-600', bgColor: 'bg-green-50 border-green-200' },
    { value: 50000, label: 'Rp 50.000', shortLabel: '50K', type: 'note' as const, iconColor: 'text-green-600', bgColor: 'bg-green-50 border-green-200' },
    { value: 100000, label: 'Rp 100.000', shortLabel: '100K', type: 'note' as const, iconColor: 'text-green-600', bgColor: 'bg-green-50 border-green-200' },
];

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
};

const parseCurrency = (value: string) => {
    return parseFloat(value.replace(/[^0-9.-]/g, '')) || 0;
};

export function DenominationCalculator({
    label,
    value,
    onChange,
    error,
    required = false,
    id,
}: DenominationCalculatorProps) {
    const [isOpen, setIsOpen] = useState(false);
    const [denominationCounts, setDenominationCounts] = useState<Record<number, number>>({});
    const [lastManualValue, setLastManualValue] = useState<string>(value);

    // Initialize denomination counts from value when value changes externally
    useEffect(() => {
        if (value !== lastManualValue) {
            let remaining = parseCurrency(value);
            const counts: Record<number, number> = {};

            for (const denom of INDONESIAN_DENOMINATIONS.slice().reverse()) {
                const count = Math.floor(remaining / denom.value);
                if (count > 0) {
                    counts[denom.value] = count;
                    remaining -= count * denom.value;
                }
            }
            setDenominationCounts(counts);
            setLastManualValue(value);
        }
    }, [value]);

    const totalCalculated = useMemo(() => {
        return Object.entries(denominationCounts).reduce((sum, [denom, count]) => {
            return sum + parseInt(denom) * count;
        }, 0);
    }, [denominationCounts]);

    const manualValue = parseCurrency(value);
    const hasDiscrepancy = totalCalculated > 0 && totalCalculated !== manualValue;

    const handleDenominationChange = (denomValue: number, count: number) => {
        const newCounts = { ...denominationCounts };
        if (count > 0) {
            newCounts[denomValue] = count;
        } else {
            delete newCounts[denomValue];
        }
        setDenominationCounts(newCounts);

        const newTotal = Object.entries(newCounts).reduce((sum, [denom, cnt]) => {
            return sum + parseInt(denom) * cnt;
        }, 0);
        onChange(newTotal.toString());
        setLastManualValue(newTotal.toString());
    };

    const handleDirectInput = (e: React.ChangeEvent<HTMLInputElement>) => {
        const newValue = e.target.value;
        onChange(newValue);
        setLastManualValue(newValue);
    };

    const handleIncrement = (denomValue: number) => {
        handleDenominationChange(denomValue, (denominationCounts[denomValue] || 0) + 1);
    };

    const handleDecrement = (denomValue: number) => {
        const current = denominationCounts[denomValue] || 0;
        if (current > 0) {
            handleDenominationChange(denomValue, current - 1);
        }
    };

    const handleClear = () => {
        setDenominationCounts({});
        onChange('0');
        setLastManualValue('0');
    };

    const handleUseCalculated = () => {
        onChange(totalCalculated.toString());
        setLastManualValue(totalCalculated.toString());
    };

    const denominationRows = useMemo(() => {
        const coins = INDONESIAN_DENOMINATIONS.filter(d => d.type === 'coin');
        const notes = INDONESIAN_DENOMINATIONS.filter(d => d.type === 'note');
        return { coins, notes };
    }, []);

    const anyDenominationSelected = Object.values(denominationCounts).some(c => c > 0);

    return (
        <div className="space-y-3">
            {/* Label & Toggle */}
            <div className="flex items-center justify-between">
                <Label htmlFor={id} className="flex items-center gap-2 mb-0">
                    {label}
                    {required && <span className="text-red-500">*</span>}
                </Label>
                <Button
                    type="button"
                    variant="ghost"
                    size="sm"
                    className="h-8 w-8 p-0"
                    onClick={() => setIsOpen(!isOpen)}
                    aria-label={isOpen ? 'Sembunyikan kalkulator pecahan' : 'Tampilkan kalkulator pecahan'}
                >
                    {isOpen ? <ChevronUp className="h-4 w-4" /> : <ChevronDown className="h-4 w-4" />}
                </Button>
            </div>

            {/* Main Input with Calculated Badge */}
            <div className="relative">
                <Input
                    id={id}
                    type="number"
                    min="0"
                    step="100"
                    value={value}
                    onChange={handleDirectInput}
                    className={`${error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''} pr-32`}
                    required={required}
                    placeholder="0"
                />
                {anyDenominationSelected && (
                    <div className="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-2">
                        <Badge variant="secondary" className="font-mono text-xs px-2 py-1">
                            {formatCurrency(totalCalculated)}
                        </Badge>
                        {hasDiscrepancy && (
                            <Button
                                type="button"
                                variant="ghost"
                                size="icon"
                                className="h-6 w-6 text-amber-600 hover:text-amber-700"
                                onClick={handleUseCalculated}
                                title="Gunakan total pecahan"
                            >
                                <CheckCircle2 className="h-3.5 w-3.5" />
                            </Button>
                        )}
                    </div>
                )}
            </div>

            {error && <p className="text-sm text-red-500">{error}</p>}

            {/* Toggle Button */}
            <Button
                type="button"
                variant="outline"
                size="sm"
                className="w-full justify-start gap-2"
                onClick={() => setIsOpen(!isOpen)}
            >
                <Calculator className="h-4 w-4" />
                <span>Kalkulator Pecahan Uang Indonesia</span>
                {anyDenominationSelected && (
                    <span className="ml-auto flex items-center gap-2">
                        <Badge variant="outline" className="font-mono text-xs">
                            {formatCurrency(totalCalculated)}
                        </Badge>
                    </span>
                )}
            </Button>

            {/* Expanded Calculator Panel */}
            {isOpen && (
                <div className="animate-in fade-in slide-in-from-top-2 duration-200">
                    <Card className="border-blue-200 bg-blue-50/50 shadow-sm">
                        <CardHeader className="pb-3 border-b border-blue-100">
                            <div className="flex items-center justify-between">
                                <CardTitle className="text-sm font-medium flex items-center gap-2 text-blue-900">
                                    <Calculator className="h-4 w-4" />
                                    Kalkulator Pecahan Uang
                                </CardTitle>
                                {anyDenominationSelected && (
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        className="text-red-600 hover:text-red-700 hover:bg-red-50"
                                        onClick={handleClear}
                                    >
                                        <Trash2 className="h-3.5 w-3.5 mr-1" />
                                        Reset
                                    </Button>
                                )}
                            </div>
                        </CardHeader>
                        <CardContent className="space-y-4 pt-3">

                            {/* Coins Section */}
                            <div className="space-y-3">
                                <div className="flex items-center gap-2 text-xs font-medium text-muted-foreground uppercase tracking-wide">
                                    <Coins className="h-4 w-4 text-amber-600" />
                                    <span>Koin</span>
                                    <div className="h-1 flex-1 bg-amber-200 rounded" />
                                </div>
                                <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                                    {denominationRows.coins.map((denom) => (
                                        <DenominationRow
                                            key={denom.value}
                                            denomination={denom}
                                            count={denominationCounts[denom.value] || 0}
                                            onIncrement={() => handleIncrement(denom.value)}
                                            onDecrement={() => handleDecrement(denom.value)}
                                        />
                                    ))}
                                </div>
                            </div>

                            <Separator className="my-1" />

                            {/* Notes Section */}
                            <div className="space-y-3">
                                <div className="flex items-center gap-2 text-xs font-medium text-muted-foreground uppercase tracking-wide">
                                    <Banknote className="h-4 w-4 text-green-600" />
                                    <span>Uang Kertas</span>
                                    <div className="h-1 flex-1 bg-green-200 rounded" />
                                </div>
                                <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                                    {denominationRows.notes.map((denom) => (
                                        <DenominationRow
                                            key={denom.value}
                                            denomination={denom}
                                            count={denominationCounts[denom.value] || 0}
                                            onIncrement={() => handleIncrement(denom.value)}
                                            onDecrement={() => handleDecrement(denom.value)}
                                        />
                                    ))}
                                </div>
                            </div>

                            {/* Summary */}
                            {anyDenominationSelected && (
                                <div className="pt-2 border-t border-blue-100 space-y-3 bg-white/50 rounded-lg p-3">
                                    <div className="grid grid-cols-2 gap-3 text-sm">
                                        <div className="text-center p-2 bg-blue-50 rounded-lg">
                                            <p className="text-xs text-muted-foreground">Total Pecahan</p>
                                            <p className="font-bold font-mono text-blue-900">{formatCurrency(totalCalculated)}</p>
                                        </div>
                                        <div className="text-center p-2 bg-gray-50 rounded-lg">
                                            <p className="text-xs text-muted-foreground">Input Manual</p>
                                            <p className="font-medium font-mono text-gray-700">{formatCurrency(manualValue)}</p>
                                        </div>
                                    </div>

                                    {hasDiscrepancy && (
                                        <div className="flex items-center gap-2 p-2 bg-amber-50 border border-amber-200 rounded-lg">
                                            <XCircle className="h-4 w-4 text-amber-600 flex-shrink-0" />
                                            <p className="text-xs text-amber-800 flex-1">
                                                Total pecahan (<strong>{formatCurrency(totalCalculated)}</strong>) berbeda dengan input manual (<strong>{formatCurrency(manualValue)}</strong>).
                                            </p>
                                            <Button
                                                type="button"
                                                size="sm"
                                                variant="default"
                                                className="text-xs px-3"
                                                onClick={handleUseCalculated}
                                            >
                                                Gunakan Pecahan
                                            </Button>
                                        </div>
                                    )}

                                    {!hasDiscrepancy && anyDenominationSelected && (
                                        <div className="flex items-center gap-2 p-2 bg-green-50 border border-green-200 rounded-lg">
                                            <CheckCircle2 className="h-4 w-4 text-green-600 flex-shrink-0" />
                                            <p className="text-xs text-green-800">
                                                Total pecahan sesuai dengan input manual.
                                            </p>
                                        </div>
                                    )}
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>
            )}
        </div>
    );
}

interface DenominationRowProps {
    denomination: typeof INDONESIAN_DENOMINATIONS[0];
    count: number;
    onIncrement: () => void;
    onDecrement: () => void;
}

function DenominationRow({ denomination, count, onIncrement, onDecrement }: DenominationRowProps) {
    const subtotal = denomination.value * count;
    const hasCount = count > 0;

    return (
        <div className={`
            flex items-center gap-2 p-3 rounded-xl border transition-all duration-200
            ${denomination.bgColor}
            ${hasCount ? 'ring-2 ring-offset-2 shadow-sm' : ''}
            ${denomination.type === 'coin' ? 'ring-amber-300' : 'ring-green-300'}
        `}>
            {/* Decrement Button */}
            <Button
                type="button"
                variant={hasCount ? "outline" : "outline"}
                size="icon"
                className="h-8 w-8 text-xs"
                onClick={onDecrement}
                disabled={!hasCount}
                aria-label={`Kurangi ${denomination.label}`}
            >
                <Minus className="h-3.5 w-3.5" />
            </Button>

            {/* Denomination Info */}
            <div className="flex-1 min-w-0">
                <div className="flex items-center gap-2">
                    <span className={`
                        font-mono text-sm font-semibold px-2 py-0.5 rounded
                        ${denomination.type === 'coin' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800'}
                    `}>
                        {denomination.shortLabel}
                    </span>
                    <span className="text-xs text-muted-foreground hidden sm:inline">
                        {denomination.type === 'coin' ? '(Koin)' : ''}
                    </span>
                </div>
                {hasCount && (
                    <p className="text-xs text-muted-foreground mt-0.5">
                        {count} × {formatCurrency(denomination.value)} = <span className="font-mono font-medium">{formatCurrency(subtotal)}</span>
                    </p>
                )}
            </div>

            {/* Count Display */}
            <div className="w-12 text-center">
                <span className={`
                    font-mono text-lg font-semibold inline-block w-full
                    ${hasCount ? (denomination.type === 'coin' ? 'text-amber-700' : 'text-green-700') : 'text-muted-foreground'}
                `}>
                    {count || '—'}
                </span>
            </div>

            {/* Increment Button */}
            <Button
                type="button"
                variant="outline"
                size="icon"
                className="h-8 w-8"
                onClick={onIncrement}
                aria-label={`Tambah ${denomination.label}`}
            >
                <Plus className="h-3.5 w-3.5" />
            </Button>
        </div>
    );
}
