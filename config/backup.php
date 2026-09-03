<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database Backup Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk fitur backup & restore database PostgreSQL.
    | Path pg_dump dan psql harus menunjuk ke executable PostgreSQL.
    |
    */

    // Path ke pg_dump.exe (backup)
    'pg_dump_path' => env('PG_DUMP_PATH', 'C:\\Program Files\\PostgreSQL\\18\\bin\\pg_dump.exe'),

    // Path ke psql.exe (restore)
    'psql_path' => env('PSQL_PATH', 'C:\\Program Files\\PostgreSQL\\18\\bin\\psql.exe'),

    // Direktori penyimpanan backup
    'backup_dir' => storage_path('app/backups'),

    // Maksimal backup yang disimpan (0 = tanpa batas)
    'max_backups' => 10,

    // Timeout proses backup/restore dalam detik
    'timeout' => 3600,

    // Nama database yang akan di-backup (dibaca dari .env jika kosong)
    'database' => env('DB_DATABASE', 'usp_kopinka'),

    // Host database
    'host' => env('DB_HOST', '127.0.0.1'),

    // Port database
    'port' => env('DB_PORT', '5432'),

    // Username database
    'username' => env('DB_USERNAME', 'admin'),

    // Password database
    'password' => env('DB_PASSWORD', ''),

];
