import { Search, X } from 'lucide-react';
import { useEffect, useState } from 'react';

import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog';
import { Input } from '@/Components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';

export interface LookupColumn<T> {
    key: keyof T | string;
    header: string;
    render?: (row: T) => React.ReactNode;
}

interface Props<T extends Record<string, any>> {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    title: string;
    description?: string;
    columns: LookupColumn<T>[];
    rows: T[];
    onSelect: (row: T) => void;
    searchPlaceholder?: string;
    getSearchText?: (row: T) => string;
}

/**
 * Modal pencarian/lookup generik.
 * Digunakan untuk No. Anggota, Marketing, SWP, SPP, No. Simpanan,
 * Kode Tarikan, Penjamin — memunculkan tabel hasil dan memilih satu baris.
 */
export function LookupModal<T extends Record<string, any>>({
    open,
    onOpenChange,
    title,
    description,
    columns,
    rows,
    onSelect,
    searchPlaceholder = 'Cari…',
    getSearchText,
}: Props<T>) {
    const [q, setQ] = useState('');

    useEffect(() => {
        if (open) setQ('');
    }, [open]);

    const term = q.trim().toLowerCase();
    const filtered = term === ''
        ? rows
        : rows.filter((r) =>
              (getSearchText ? getSearchText(r) : Object.values(r).join(' '))
                  .toLowerCase()
                  .includes(term),
          );

    const select = (row: T) => {
        onSelect(row);
        onOpenChange(false);
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent className="sm:max-w-2xl">
                <DialogHeader>
                    <DialogTitle>{title}</DialogTitle>
                    {description && <DialogDescription>{description}</DialogDescription>}
                </DialogHeader>

                <div className="relative">
                    <Search className="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        autoFocus
                        value={q}
                        onChange={(e) => setQ(e.target.value)}
                        placeholder={searchPlaceholder}
                        className="pl-9"
                    />
                </div>

                <div className="max-h-80 overflow-y-auto rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                {columns.map((c) => (
                                    <TableHead key={String(c.key)}>{c.header}</TableHead>
                                ))}
                                <TableHead className="w-10" />
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {filtered.length === 0 && (
                                <TableRow>
                                    <TableCell
                                        colSpan={columns.length + 1}
                                        className="h-24 text-center text-muted-foreground"
                                    >
                                        Tidak ada data.
                                    </TableCell>
                                </TableRow>
                            )}
                            {filtered.map((row, i) => (
                                <TableRow
                                    key={i}
                                    className="cursor-pointer"
                                    onClick={() => select(row)}
                                >
                                    {columns.map((c) => (
                                        <TableCell key={String(c.key)}>
                                            {c.render
                                                ? c.render(row)
                                                : String(row[c.key] ?? '')}
                                        </TableCell>
                                    ))}
                                    <TableCell>
                                        <Button type="button" size="sm" variant="ghost">
                                            Pilih
                                        </Button>
                                    </TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>

                <div className="flex items-center justify-end">
                    <Button
                        type="button"
                        variant="outline"
                        onClick={() => onOpenChange(false)}
                    >
                        <X />
                        Tutup
                    </Button>
                </div>
            </DialogContent>
        </Dialog>
    );
}
