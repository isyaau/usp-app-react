<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Jobs\BackupDatabaseJob;
use App\Jobs\RestoreDatabaseJob;
use App\Models\BackupLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DatabaseBackupController extends Controller
{
    public function index()
    {
        $logs = BackupLog::with('user:id,nama,username')
            ->orderBy('created_at', 'DESC')
            ->paginate(15)
            ->withQueryString();

        return inertia('Superadmin/DatabaseBackup/Index', [
            'logs' => $logs,
            'isRunning' => BackupLog::whereIn('status', ['pending', 'running'])->exists(),
        ]);
    }

    /**
     * Dispatch job backup database ke queue.
     */
    public function backup()
    {
        // Cek apakah masih ada proses berjalan
        if (BackupLog::whereIn('status', ['pending', 'running'])->exists()) {
            return back()->with('flash.status', 'Masih ada proses backup/restore yang sedang berjalan. Mohon tunggu hingga selesai.');
        }

        $log = BackupLog::create([
            'filename' => 'menunggu...',
            'type' => 'backup',
            'status' => 'pending',
            'user_id' => request()->user()->id,
        ]);

        BackupDatabaseJob::dispatch($log->id);

        return back()->with('flash.status', 'Proses backup telah dimulai dan berjalan di background. Halaman akan diperbarui secara otomatis.');
    }

    /**
     * Cek status backup/restore untuk polling frontend.
     */
    public function status()
    {
        $running = BackupLog::whereIn('status', ['pending', 'running'])
            ->with('user:id,nama,username')
            ->latest()
            ->first();

        $latest = BackupLog::with('user:id,nama,username')
            ->latest()
            ->first();

        return response()->json([
            'running' => $running,
            'latest' => $latest,
            'is_running' => $running !== null,
        ]);
    }

    /**
     * Download file backup.
     */
    public function download(BackupLog $backupLog)
    {
        $filepath = config('backup.backup_dir') . DIRECTORY_SEPARATOR . $backupLog->filename;

        if (! File::exists($filepath)) {
            return back()->with('flash.status', 'File backup tidak ditemukan.');
        }

        return response()->download($filepath, $backupLog->filename);
    }

    /**
     * Restore database dari file yang di-upload.
     */
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql|max:524288', // 500MB max
        ], [
            'backup_file.required' => 'File backup wajib diunggah.',
            'backup_file.mimes' => 'Format file harus .sql.',
            'backup_file.max' => 'Ukuran file maksimal 500MB.',
        ]);

        // Cek apakah masih ada proses berjalan
        if (BackupLog::whereIn('status', ['pending', 'running'])->exists()) {
            return back()->with('flash.status', 'Masih ada proses backup/restore yang sedang berjalan. Mohon tunggu hingga selesai.');
        }

        $file = $request->file('backup_file');
        $filename = 'restore_' . now()->format('Ymd_His') . '.sql';
        $backupDir = config('backup.backup_dir');

        if (! File::isDirectory($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $file->move($backupDir, $filename);

        $log = BackupLog::create([
            'filename' => $filename,
            'type' => 'restore',
            'status' => 'pending',
            'user_id' => request()->user()->id,
        ]);

        RestoreDatabaseJob::dispatch($log->id);

        return back()->with('flash.status', 'Proses restore telah dimulai dan berjalan di background.');
    }

    /**
     * Restore dari backup file yang sudah ada.
     */
    public function restoreFromExisting(BackupLog $backupLog)
    {
        $filepath = config('backup.backup_dir') . DIRECTORY_SEPARATOR . $backupLog->filename;

        if (! File::exists($filepath)) {
            return back()->with('flash.status', 'File backup tidak ditemukan.');
        }

        if (BackupLog::whereIn('status', ['pending', 'running'])->exists()) {
            return back()->with('flash.status', 'Masih ada proses backup/restore yang sedang berjalan.');
        }

        $log = BackupLog::create([
            'filename' => $backupLog->filename,
            'type' => 'restore',
            'status' => 'pending',
            'user_id' => request()->user()->id,
        ]);

        RestoreDatabaseJob::dispatch($log->id);

        return back()->with('flash.status', 'Proses restore dari backup "' . $backupLog->filename . '" telah dimulai.');
    }

    /**
     * Hapus backup file dan log-nya.
     */
    public function destroy(BackupLog $backupLog)
    {
        $filepath = config('backup.backup_dir') . DIRECTORY_SEPARATOR . $backupLog->filename;

        if (File::exists($filepath)) {
            File::delete($filepath);
        }

        $backupLog->delete();

        return back()->with('flash.status', 'File backup berhasil dihapus.');
    }
}
