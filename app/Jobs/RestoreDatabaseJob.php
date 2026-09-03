<?php

namespace App\Jobs;

use App\Models\BackupLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class RestoreDatabaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 7200;

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
        $filepath = $config['backup_dir'] . DIRECTORY_SEPARATOR . $log->filename;

        try {
            if (! File::exists($filepath)) {
                throw new \RuntimeException("File backup tidak ditemukan: {$log->filename}");
            }

            $this->runPsql($config, $filepath);

            $duration = (int) round(microtime(true) - $start);

            $log->update([
                'status' => 'success',
                'duration_seconds' => $duration,
                'message' => 'Restore berhasil.',
            ]);
        } catch (\Throwable $e) {
            $duration = (int) round(microtime(true) - $start);

            $log->update([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'duration_seconds' => $duration,
            ]);

            throw $e;
        }
    }

    /**
     * Jalankan psql.exe untuk restore database.
     * File .sql di-pipe langsung ke stdin psql.
     */
    private function runPsql(array $config, string $filepath): void
    {
        $psql = $config['psql_path'];
        $errFile = $filepath . '.err';

        $quoted = fn (string $value) => '"' . str_replace('"', '""', $value) . '"';

        $cmd = sprintf(
            '%s --host=%s --port=%s --username=%s --dbname=%s --no-psqlrc --set=ON_ERROR_STOP=1 < %s 2> %s',
            $quoted($psql),
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

        $error = file_exists($errFile) ? trim(file_get_contents($errFile)) : '';
        @unlink($errFile);

        if ($error !== '') {
            throw new \RuntimeException('psql gagal: ' . $error);
        }
    }
}
