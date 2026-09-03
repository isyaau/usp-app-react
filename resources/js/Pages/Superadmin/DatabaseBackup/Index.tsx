import { useEffect, useRef, useState } from 'react';
import { Head, router } from '@inertiajs/react';
import {
    Database,
    Download,
    HardDriveUpload,
    Loader2,
    Play,
    RefreshCw,
    Trash2,
    Upload,
} from 'lucide-react';

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/PageHeader';
import { Pagination } from '@/Components/Pagination';
import { Badge } from '@/Components/ui/badge';
import { Button } from '@/Components/ui/button';
import { Card } from '@/Components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/Components/ui/dialog';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import type { BackupLogRow, Paginated } from '@/types/models';

interface Props {
    logs: Paginated<BackupLogRow>;
    isRunning: boolean;
}

const STATUS_META: Record<
    BackupLogRow['status'],
    { label: string; variant: 'success' | 'warning' | 'destructive' | 'default' }
> = {
    pending: { label: 'Menunggu', variant: 'warning' },
    running: { label: 'Berjalan', variant: 'warning' },
    success: { label: 'Berhasil', variant: 'success' },
    failed: { label: 'Gagal', variant: 'destructive' },
};

function formatTime(value: string): string {
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return value;
    return d.toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'medium' });
}

export default function DatabaseBackupIndex({ logs, isRunning }: Props) {
    const [uploadDialogOpen, setUploadDialogOpen] = useState(false);
    const [backingUp, setBackingUp] = useState(false);
    const [restoringId, setRestoringId] = useState<number | null>(null);
    const [selectedFile, setSelectedFile] = useState<File | null>(null);
    const [uploading, setUploading] = useState(false);
    const [fileError, setFileError] = useState<string | null>(null);
    const fileInputRef = useRef<HTMLInputElement>(null);

    // Polling status setiap 3 detik jika ada proses berjalan
    useEffect(() => {
        if (!isRunning) return;

        const interval = setInterval(() => {
            router.reload({ only: ['logs', 'isRunning'], preserveScroll: true });
        }, 3000);

        return () => clearInterval(interval);
    }, [isRunning]);

    const handleBackup = () => {
        setBackingUp(true);
        router.post(
            route('superadmin.backup-database.backup'),
            {},
            {
                onSuccess: () => setBackingUp(false),
                onError: () => setBackingUp(false),
                onFinish: () => router.reload({ only: ['logs', 'isRunning'], preserveScroll: true }),
            },
        );
    };

    const handleRestoreExisting = (logId: number) => {
        setRestoringId(logId);
        router.post(route('superadmin.backup-database.restore-existing', logId), {
            onSuccess: () => setRestoringId(null),
            onError: () => setRestoringId(null),
            onFinish: () => router.reload({ only: ['logs', 'isRunning'], preserveScroll: true }),
        });
    };

    const handleUpload = () => {
        if (!selectedFile) {
            setFileError('Pilih file .sql terlebih dahulu.');
            return;
        }
        if (!selectedFile.name.toLowerCase().endsWith('.sql')) {
            setFileError('Format file harus .sql.');
            return;
        }

        setFileError(null);
        setUploading(true);

        router.post(
            route('superadmin.backup-database.restore'),
            { backup_file: selectedFile },
            {
                forceFormData: true,
                onSuccess: () => {
                    setUploading(false);
                    setUploadDialogOpen(false);
                    setSelectedFile(null);
                    if (fileInputRef.current) fileInputRef.current.value = '';
                },
                onError: (errors) => {
                    setUploading(false);
                    setFileError(Object.values(errors)[0] as string);
                },
                onFinish: () => router.reload({ only: ['logs', 'isRunning'], preserveScroll: true }),
            },
        );
    };

    const anyRunning = isRunning || logs.data.some((l) => l.status === 'pending' || l.status === 'running');

    return (
        <AuthenticatedLayout>
            <Head title="Backup Database" />

            <PageHeader
                title="Backup Database"
                description="Buat cadangan dan pulihkan database PostgreSQL secara otomatis di background."
                icon={Database}
            >
                <Button variant="outline" onClick={() => router.reload()} className="gap-2">
                    <RefreshCw className="size-4" />
                    Segarkan
                </Button>
                <Dialog open={uploadDialogOpen} onOpenChange={setUploadDialogOpen}>
                    <DialogTrigger asChild>
                        <Button variant="outline" className="gap-2 border-brand-600/30 text-brand-700 hover:bg-brand-50">
                            <Upload className="size-4" />
                            Restore Upload
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Restore dari File Upload</DialogTitle>
                            <DialogDescription>
                                Unggah file backup <code>.sql</code> untuk dipulihkan ke database. Data saat ini akan
                                tertimpa seluruhnya. Proses berjalan di background.
                            </DialogDescription>
                        </DialogHeader>

                        <div className="grid gap-3">
                            <input
                                ref={fileInputRef}
                                type="file"
                                accept=".sql"
                                onChange={(e) => {
                                    setSelectedFile(e.target.files?.[0] ?? null);
                                    setFileError(null);
                                }}
                                className="block w-full text-sm text-muted-foreground file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100"
                            />
                            {fileError && <p className="text-sm text-destructive">{fileError}</p>}
                        </div>

                        <DialogFooter>
                            <DialogClose asChild>
                                <Button variant="outline">Batal</Button>
                            </DialogClose>
                            <Button onClick={handleUpload} disabled={uploading} className="bg-brand-600 hover:bg-brand-500">
                                {uploading ? (
                                    <>
                                        <Loader2 className="size-4 animate-spin" />
                                        Mengunggah…
                                    </>
                                ) : (
                                    'Mulai Restore'
                                )}
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
                <Button onClick={handleBackup} disabled={backingUp || anyRunning} className="gap-2 bg-brand-600 hover:bg-brand-500">
                    {backingUp || anyRunning ? (
                        <Loader2 className="size-4 animate-spin" />
                    ) : (
                        <HardDriveUpload className="size-4" />
                    )}
                    {anyRunning ? 'Proses Berjalan…' : 'Backup Sekarang'}
                </Button>
            </PageHeader>

            {anyRunning && (
                <Card className="mb-6 gap-2 border-brand-200 bg-brand-50/60 py-4">
                    <div className="flex items-center gap-3 px-5">
                        <Loader2 className="size-5 animate-spin text-brand-600" />
                        <p className="text-sm font-medium text-brand-700">
                            Proses backup/restore sedang berjalan di background. Halaman diperbarui otomatis setiap 3 detik.
                        </p>
                    </div>
                </Card>
            )}

            <Card className="gap-4 py-5">
                <div className="flex flex-wrap items-center justify-between gap-3 px-5">
                    <h3 className="text-lg font-semibold">Riwayat Backup & Restore</h3>
                    <p className="text-sm text-muted-foreground">Total file tersimpan: {logs.total}</p>
                </div>

                <div className="px-5 overflow-x-auto">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Jenis</TableHead>
                                <TableHead>File</TableHead>
                                <TableHead>Ukuran</TableHead>
                                <TableHead>Durasi</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Oleh</TableHead>
                                <TableHead>Waktu</TableHead>
                                <TableHead className="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {logs.data.length === 0 && (
                                <TableRow>
                                    <TableCell colSpan={8} className="h-32 text-center text-muted-foreground">
                                        Belum ada backup database. Klik "Backup Sekarang" untuk membuat cadangan pertama.
                                    </TableCell>
                                </TableRow>
                            )}
                            {logs.data.map((log) => {
                                const meta = STATUS_META[log.status];
                                const isRunningLog = log.status === 'pending' || log.status === 'running';
                                return (
                                    <TableRow key={log.id}>
                                        <TableCell>
                                            <Badge variant={log.type === 'backup' ? 'default' : 'secondary'}>
                                                {log.type === 'backup' ? 'Backup' : 'Restore'}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            <span className="cursor-default font-mono text-xs text-muted-foreground" title={log.message ?? ''}>
                                                {log.filename}
                                            </span>
                                            {log.status === 'failed' && log.message && (
                                                <p className="mt-0.5 text-xs font-medium text-destructive">{log.message}</p>
                                            )}
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-muted-foreground">
                                            {log.formatted_size ?? '—'}
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-muted-foreground">
                                            {log.formatted_duration ?? '—'}
                                        </TableCell>
                                        <TableCell>
                                            <Badge variant={meta.variant}>
                                                {isRunningLog && <Loader2 className="size-3 animate-spin" />}
                                                {meta.label}
                                            </Badge>
                                        </TableCell>
                                        <TableCell className="text-muted-foreground">{log.user?.nama ?? '—'}</TableCell>
                                        <TableCell className="whitespace-nowrap text-muted-foreground">
                                            {formatTime(log.created_at)}
                                        </TableCell>
                                        <TableCell>
                                            <RestoreExistingButton
                                                log={log}
                                                isRunning={anyRunning}
                                                restoring={restoringId === log.id}
                                                onRestore={() => handleRestoreExisting(log.id)}
                                            />
                                        </TableCell>
                                    </TableRow>
                                );
                            })}
                        </TableBody>
                    </Table>
                </div>

                {logs.total > 0 && (
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
                )}
            </Card>
        </AuthenticatedLayout>
    );
}

function RestoreExistingButton({
    log,
    isRunning,
    restoring,
    onRestore,
}: {
    log: BackupLogRow;
    isRunning: boolean;
    restoring: boolean;
    onRestore: () => void;
}) {
    const canDownload = log.type === 'backup' && log.status === 'success' && log.filename !== 'menunggu...';
    const canRestore = log.type === 'backup' && log.status === 'success' && !isRunning;

    return (
        <div className="flex items-center justify-end gap-1.5">
            {canDownload && (
                <a href={route('superadmin.backup-database.download', log.id)}>
                    <Button variant="ghost" size="icon" className="size-8 text-muted-foreground hover:bg-brand-50 hover:text-brand-700" aria-label="Download backup">
                        <Download className="size-4" />
                    </Button>
                </a>
            )}
            {canRestore && (
                <RestoreConfirmDialog log={log} restoring={restoring} onRestore={onRestore} />
            )}
            {log.status !== 'running' && log.status !== 'pending' && (
                <DeleteBackupButton log={log} />
            )}
        </div>
    );
}

function RestoreConfirmDialog({
    log,
    restoring,
    onRestore,
}: {
    log: BackupLogRow;
    restoring: boolean;
    onRestore: () => void;
}) {
    const [confirmText, setConfirmText] = useState('');

    return (
        <Dialog>
            <DialogTrigger asChild>
                <Button variant="ghost" size="icon" className="size-8 text-muted-foreground hover:bg-amber-50 hover:text-amber-700" aria-label="Restore backup">
                    <Play className="size-4" />
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Pulihkan Database?</DialogTitle>
                    <DialogDescription>
                        Restore akan <strong>menimpa seluruh data</strong> saat ini dengan isi file backup
                        "{log.filename}". Tindakan ini <strong>tidak dapat dibatalkan</strong>.
                    </DialogDescription>
                </DialogHeader>

                <div className="grid gap-3">
                    <label className="text-sm text-muted-foreground">
                        Ketik <span className="font-mono font-semibold">RESTORE</span> untuk konfirmasi:
                    </label>
                    <input
                        type="text"
                        value={confirmText}
                        onChange={(e) => setConfirmText(e.target.value)}
                        placeholder="RESTORE"
                        className="block w-full rounded-md border bg-background px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"
                    />
                </div>

                <DialogFooter>
                    <DialogClose asChild>
                        <Button variant="outline">Batal</Button>
                    </DialogClose>
                    <Button
                        variant="destructive"
                        disabled={confirmText !== 'RESTORE' || restoring}
                        onClick={onRestore}
                        className="bg-destructive text-white hover:bg-destructive/90"
                    >
                        {restoring ? (
                            <>
                                <Loader2 className="size-4 animate-spin" />
                                Memulihkan…
                            </>
                        ) : (
                            'Mulai Restore'
                        )}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}

function DeleteBackupButton({ log }: { log: BackupLogRow }) {
    const [open, setOpen] = useState(false);
    const [deleting, setDeleting] = useState(false);

    const confirmDelete = () => {
        setDeleting(true);
        router.delete(route('superadmin.backup-database.destroy', log.id), {
            onSuccess: () => setDeleting(false),
            onError: () => setDeleting(false),
            onFinish: () => router.reload({ only: ['logs', 'isRunning'], preserveScroll: true }),
        });
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button variant="ghost" size="icon" className="size-8 text-muted-foreground hover:bg-destructive/10 hover:text-destructive" aria-label="Hapus backup">
                    <Trash2 className="size-4" />
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Hapus Backup?</DialogTitle>
                    <DialogDescription>
                        File backup "{log.filename}" akan dihapus permanen dan tidak dapat dikembalikan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <DialogClose asChild>
                        <Button variant="outline">Batal</Button>
                    </DialogClose>
                    <Button
                        variant="destructive"
                        onClick={confirmDelete}
                        disabled={deleting}
                        className="bg-destructive text-white hover:bg-destructive/90"
                    >
                        {deleting ? (
                            <>
                                <Loader2 className="size-4 animate-spin" />
                                Menghapus…
                            </>
                        ) : (
                            'Ya, Hapus'
                        )}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
