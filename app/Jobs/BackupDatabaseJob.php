<?php

namespace App\Jobs;

use App\Models\BackupLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class BackupDatabaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;

    public int $tries = 1;

    public function __construct(
        public int $logId,
    ) {}

    public function handle(): void
    {
        $log = BackupLog::findOrFail($this->logId);

        $log->update(['status' => 'running']);

        $start = microtime(true);

        $config = config('backup');
        $backupDir = $config['backup_dir'];

        // Pastikan direktori backup ada
        if (! File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = 'kopinka_' . now()->format('Ymd_His') . '.sql';
        $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        try {
            $this->runPgDump($config, $filepath);

            $size = file_exists($filepath) ? filesize($filepath) : 0;
            $duration = (int) round(microtime(true) - $start);

            $log->update([
                'filename' => $filename,
                'size_bytes' => $size,
                'status' => 'success',
                'duration_seconds' => $duration,
            ]);

            // Bersihkan backup lama
            $this->cleanupOldBackups($config);
        } catch (\Throwable $e) {
            $duration = (int) round(microtime(true) - $start);

            $log->update([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'duration_seconds' => $duration,
            ]);

            // Hapus file partial jika ada
            if (File::exists($filepath)) {
                File::delete($filepath);
            }

            throw $e;
        }
    }

    /**
     * Jalankan pg_dump.exe untuk backup database.
     *
     * Catatan: Di Windows + PostgreSQL 18, pg_dump yang dijalankan via proc_open
     * (pipe/array) memunculkan error "could not generate restrict key" karena
     * tidak ada console interaktif. Menggunakan shell_exec + redirection ke file
     * terbukti berhasil. Path disarung kutip ganda agar aman dari spasi.
     */
    private function runPgDump(array $config, string $filepath): void
    {
        $pgDump = $config['pg_dump_path'];
        $errFile = $filepath . '.err';

        $quoted = fn (string $value) => '"' . str_replace('"', '""', $value) . '"';

        $cmd = sprintf(
            '%s --host=%s --port=%s --username=%s --dbname=%s --no-owner --clean --if-exists --format=plain > %s 2> %s',
            $quoted($pgDump),
            $config['host'],
            $config['port'],
            $config['username'],
            $config['database'],
            $quoted($filepath),
            $quoted($errFile),
        );

        $previous = getenv('PGPASSWORD');
        putenv('PGPASSWORD=' . $config['password']);

        try {
            shell_exec($cmd);
        } finally {
            if ($previous === false) {
                putenv('PGPASSWORD');
            } else {
                putenv('PGPASSWORD=' . $previous);
            }
        }

        if (! file_exists($filepath) || filesize($filepath) === 0) {
            $error = file_exists($errFile) ? trim(file_get_contents($errFile)) : 'Tidak ada output.';
            @unlink($errFile);
            throw new \RuntimeException('pg_dump gagal: ' . $error);
        }

        @unlink($errFile);
    }

    /**
     * Bersihkan backup lama melebihi batas maksimal.
     */
    private function cleanupOldBackups(array $config): void
    {
        $max = $config['max_backups'];

        if ($max <= 0) {
            return;
        }

        $backupDir = $config['backup_dir'];
        $files = collect(File::files($backupDir))
            ->filter(fn ($f) => $f->getExtension() === 'sql')
            ->sortBy(fn ($f) => $f->getMTime(), descending: true)
            ->values();

        if ($files->count() > $max) {
            $files->slice($max)->each(function ($file) {
                File::delete($file->getPathname());

                // Hapus log terkait
                BackupLog::where('filename', $file->getFilename())->delete();
            });
        }
    }
}
