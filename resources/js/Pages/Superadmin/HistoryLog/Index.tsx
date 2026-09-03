import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { ChevronDown, ChevronRight, History, RotateCcw } from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import type { HistoryLogAction, HistoryLogRow, Paginated } from '@/types/models';

const ACTION_META: Record<
    HistoryLogAction,
    { label: string; badge: 'success' | 'warning' | 'destructive' }
> = {
    create: { label: 'Tambah', badge: 'success' },
    update: { label: 'Ubah', badge: 'warning' },
    delete: { label: 'Hapus', badge: 'destructive' },
};

const ALL_OPTION = 'all';

interface Filters {
    table: string;
    action: string;
    user_id: string;
    date_from: string;
    date_to: string;
    per_page: string;
}

interface Props {
    logs: Paginated<HistoryLogRow>;
    filters: Partial<Filters>;
    tables: string[];
    users: { id: number; nama: string; username: string }[];
    labels: Record<string, string>;
}

function tableLabel(table: string, labels: Record<string, string>): string {
    return labels[table] ?? table.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
}

function formatTime(value: string): string {
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
}

function formatValue(v: unknown): string {
    if (v === null || v === undefined) return '—';
    if (typeof v === 'boolean') return v ? 'Ya' : 'Tidak';
    if (typeof v === 'object') return JSON.stringify(v);
    return String(v);
}

function fieldLabel(field: string): string {
    return field.replace(/_/g, ' ');
}

export default function HistoryLogIndex({ logs, filters, tables, users, labels }: Props) {
    const [expandedId, setExpandedId] = useState<number | null>(null);
    const [table, setTable] = useState(filters.table || '');
    const [action, setAction] = useState(filters.action || '');
    const [userId, setUserId] = useState(filters.user_id ? String(filters.user_id) : '');
    const [dateFrom, setDateFrom] = useState(filters.date_from || '');
    const [dateTo, setDateTo] = useState(filters.date_to || '');
    const [perPage, setPerPage] = useState(String(filters.per_page || logs.per_page || 25));

    const apply = (overrides: Partial<Filters> = {}) => {
        router.get(
            route('superadmin.history-log'),
            {
                table: overrides.table !== undefined ? overrides.table : table,
                action: overrides.action !== undefined ? overrides.action : action,
                user_id: overrides.user_id !== undefined ? overrides.user_id : userId,
                date_from: overrides.date_from !== undefined ? overrides.date_from : dateFrom,
                date_to: overrides.date_to !== undefined ? overrides.date_to : dateTo,
                per_page: overrides.per_page !== undefined ? overrides.per_page : perPage,
            },
            { preserveState: true, preserveScroll: true },
        );
    };

    const reset = () => {
        setTable('');
        setAction('');
        setUserId('');
        setDateFrom('');
        setDateTo('');
        router.get(
            route('superadmin.history-log'),
            { per_page: perPage },
            { preserveState: true, preserveScroll: true },
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Riwayat Perubahan" />

            <PageHeader
                title="Riwayat Perubahan"
                description="Log siapa yang menambah, mengubah, atau menghapus data beserta waktu kejadiannya."
                icon={History}
            />

            <Card className="gap-4 py-5">
                <div className="flex flex-wrap items-end gap-3 px-5">
                    <div className="grid gap-1.5">
                        <label className="text-xs font-medium text-muted-foreground">Tabel</label>
                        <Select
                            value={table || ALL_OPTION}
                            onValueChange={(v) => {
                                setTable(v === ALL_OPTION ? '' : v);
                                apply({ table: v === ALL_OPTION ? '' : v });
                            }}
                        >
                            <SelectTrigger className="w-56">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value={ALL_OPTION}>Semua Tabel</SelectItem>
                                {tables.map((t) => (
                                    <SelectItem key={t} value={t}>
                                        {tableLabel(t, labels)}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>

                    <div className="grid gap-1.5">
                        <label className="text-xs font-medium text-muted-foreground">Aksi</label>
                        <Select
                            value={action || ALL_OPTION}
                            onValueChange={(v) => {
                                setAction(v === ALL_OPTION ? '' : v);
                                apply({ action: v === ALL_OPTION ? '' : v });
                            }}
                        >
                            <SelectTrigger className="w-36">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value={ALL_OPTION}>Semua Aksi</SelectItem>
                                {(Object.keys(ACTION_META) as HistoryLogAction[]).map((a) => (
                                    <SelectItem key={a} value={a}>
                                        {ACTION_META[a].label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>

                    <div className="grid gap-1.5">
                        <label className="text-xs font-medium text-muted-foreground">User</label>
                        <Select
                            value={userId || ALL_OPTION}
                            onValueChange={(v) => {
                                setUserId(v === ALL_OPTION ? '' : v);
                                apply({ user_id: v === ALL_OPTION ? '' : v });
                            }}
                        >
                            <SelectTrigger className="w-52">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value={ALL_OPTION}>Semua User</SelectItem>
                                {users.map((u) => (
                                    <SelectItem key={u.id} value={String(u.id)}>
                                        {u.nama} ({u.username})
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>

                    <div className="grid gap-1.5">
                        <label className="text-xs font-medium text-muted-foreground">Dari Tanggal</label>
                        <Input
                            type="date"
                            value={dateFrom}
                            onChange={(e) => setDateFrom(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && apply()}
                            className="w-44"
                        />
                    </div>

                    <div className="grid gap-1.5">
                        <label className="text-xs font-medium text-muted-foreground">Sampai Tanggal</label>
                        <Input
                            type="date"
                            value={dateTo}
                            onChange={(e) => setDateTo(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && apply()}
                            className="w-44"
                        />
                    </div>

                    <div className="grid gap-1.5">
                        <label className="text-xs font-medium text-muted-foreground">&nbsp;</label>
                        <div className="flex items-center gap-2">
                            <Button type="button" onClick={() => apply()} className="bg-brand-600 hover:bg-brand-500">
                                Terapkan
                            </Button>
                            <Button type="button" variant="outline" onClick={reset}>
                                <RotateCcw />
                                Reset
                            </Button>
                        </div>
                    </div>

                    <div className="ml-auto grid gap-1.5">
                        <label className="text-xs font-medium text-muted-foreground">Per Halaman</label>
                        <Select
                            value={perPage}
                            onValueChange={(v) => {
                                setPerPage(v);
                                apply({ per_page: v });
                            }}
                        >
                            <SelectTrigger className="w-28">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                {['10', '25', '50', '100'].map((n) => (
                                    <SelectItem key={n} value={n}>
                                        {n} / hal.
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead className="w-10" />
                                <TableHead className="w-12">#</TableHead>
                                <TableHead>Waktu</TableHead>
                                <TableHead>User</TableHead>
                                <TableHead>Aksi</TableHead>
                                <TableHead>Tabel</TableHead>
                                <TableHead className="text-right">ID Rekaman</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {logs.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={7} className="h-32 text-center text-muted-foreground">
                                        Belum ada aktivitas perubahan data.
                                    </TableCell>
                                </TableRow>
                            )}
                            {logs.data.map((log, i) => {
                                const meta = ACTION_META[log.action];
                                const expanded = expandedId === log.id;
                                return (
                                    <ChangeRow
                                        key={log.id}
                                        index={logs.from !== null ? logs.from + i : i + 1}
                                        log={log}
                                        labels={labels}
                                        meta={meta}
                                        expanded={expanded}
                                        onToggle={() => setExpandedId(expanded ? null : log.id)}
                                    />
                                );
                            })}
                        </TableBody>
                    </Table>
                </div>

                <div className="border-t px-5 pt-4">
                    <Pagination
                        links={logs.links}
                        currentPage={logs.current_page}
                        lastPage={logs.last_page}
                        from={logs.from}
                        to={logs.to}
                        total={logs.total}
                    />
                </div>
            </Card>
        </AuthenticatedLayout>
    );
}

function ChangeRow({
    index,
    log,
    labels,
    meta,
    expanded,
    onToggle,
}: {
    index: number;
    log: HistoryLogRow;
    labels: Record<string, string>;
    meta: { label: string; badge: 'success' | 'warning' | 'destructive' };
    expanded: boolean;
    onToggle: () => void;
}) {
    const initial = log.user ? log.user.nama.charAt(0).toUpperCase() : '?';

    return (
        <>
            <TableRow>
                <TableCell>
                    <Button variant="ghost" size="icon" className="size-7" onClick={onToggle} aria-label="Detail perubahan">
                        {expanded ? <ChevronDown className="size-4" /> : <ChevronRight className="size-4" />}
                    </Button>
                </TableCell>
                <TableCell className="text-muted-foreground">{index}</TableCell>
                <TableCell className="whitespace-nowrap text-muted-foreground">
                    {formatTime(log.created_at)}
                </TableCell>
                <TableCell>
                    <div className="flex items-center gap-2">
                        <Avatar className="size-7">
                            <AvatarImage src={log.user?.avatar ?? undefined} />
                            <AvatarFallback className="text-xs">{initial}</AvatarFallback>
                        </Avatar>
                        <div className="leading-tight">
                            <span className="text-sm font-medium">{log.user?.nama ?? 'User tidak ada'}</span>
                            {log.user ? (
                                <span className="block text-xs text-muted-foreground">@{log.user.username}</span>
                            ) : null}
                        </div>
                    </div>
                </TableCell>
                <TableCell>
                    <Badge variant={meta.badge}>{meta.label}</Badge>
                </TableCell>
                <TableCell>
                    <span className="rounded-md bg-muted px-2 py-0.5 font-mono text-xs">
                        {tableLabel(log.table, labels)}
                    </span>
                </TableCell>
                <TableCell className="text-right font-mono text-muted-foreground">
                    {log.record_id}
                </TableCell>
            </TableRow>

            {expanded && (
                <TableRow>
                    <TableCell colSpan={7} className="bg-muted/40 px-10 py-4">
                        {log.changes && Object.keys(log.changes).length > 0 ? (
                            <div className="space-y-1.5">
                                {Object.entries(log.changes).map(([field, change]) => (
                                    <div key={field} className="grid grid-cols-[minmax(0,180px)_minmax(0,1fr)] gap-4 text-sm">
                                        <span className="truncate font-medium capitalize text-muted-foreground">
                                            {fieldLabel(field)}
                                        </span>
                                        <div className="flex flex-wrap items-center gap-2">
                                            {change.old !== undefined && (
                                                <span className="rounded bg-destructive/10 px-1.5 py-0.5 text-destructive line-through decoration-destructive/60">
                                                    {formatValue(change.old)}
                                                </span>
                                            )}
                                            {change.old !== undefined && change.new !== undefined && (
                                                <span className="text-muted-foreground">→</span>
                                            )}
                                            {change.new !== undefined && (
                                                <span className="rounded bg-emerald-600/10 px-1.5 py-0.5 text-emerald-700">
                                                    {formatValue(change.new)}
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <p className="text-sm text-muted-foreground">
                                Tidak ada detail perubahan tercatat.
                                {log.ip_address ? ` IP: ${log.ip_address}` : ''}
                            </p>
                        )}
                    </TableCell>
                </TableRow>
            )}
        </>
    );
}