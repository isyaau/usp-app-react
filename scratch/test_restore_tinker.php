<?php
// Test restore job - run via php artisan tinker < file
$backup = App\Models\BackupLog::where('type', 'backup')->where('status', 'success')->latest()->first();
echo "backup file: {$backup->filename}" . PHP_EOL;

$rlog = App\Models\BackupLog::create([
    'filename' => $backup->filename,
    'type' => 'restore',
    'status' => 'pending',
    'user_id' => 1,
]);
echo "restore log id: {$rlog->id}" . PHP_EOL;

App\Jobs\RestoreDatabaseJob::dispatchSync($rlog->id);

$rlog->refresh();
echo "Status: {$rlog->status}" . PHP_EOL;
echo "Duration: {$rlog->formatted_duration}" . PHP_EOL;
echo "Message: {$rlog->message}" . PHP_EOL;
