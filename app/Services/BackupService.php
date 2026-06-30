<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZipArchive;

class BackupService
{
    protected string $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        if (! File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    public function getBackupPath(): string
    {
        return $this->backupPath;
    }

    public function listBackups(): array
    {
        $files = File::files($this->backupPath);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'name' => $file->getFilename(),
                'size' => $this->formatBytes($file->getSize()),
                'size_bytes' => $file->getSize(),
                'type' => $this->getBackupType($file->getFilename()),
                'date' => date('Y-m-d H:i:s', $file->getMTime()),
                'timestamp' => $file->getMTime(),
            ];
        }

        usort($backups, fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $backups;
    }

    public function createDatabaseBackup(): string
    {
        $filename = 'db_backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $filepath = $this->backupPath . '/' . $filename;

        $driver = config('database.default');
        $connection = config("database.connections.{$driver}");

        if ($driver === 'sqlite') {
            $dbPath = $connection['database'] ?? database_path('database.sqlite');
            if (file_exists($dbPath)) {
                File::copy($dbPath, $filepath);
            } else {
                throw new \RuntimeException('SQLite database file not found.');
            }
        } else {
            $this->createMysqlDump($connection, $filepath);
        }

        Log::info("Database backup created: {$filename}");

        return $filename;
    }

    public function createFilesBackup(): string
    {
        $filename = 'files_backup_' . now()->format('Y-m-d_H-i-s') . '.zip';
        $filepath = $this->backupPath . '/' . $filename;

        $zip = new ZipArchive();
        if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Cannot create zip file.');
        }

        $this->addFolderToZip($zip, storage_path('app/public'), 'storage/app/public');
        $this->addFolderToZip($zip, public_path('images'), 'public/images');

        $zip->close();

        Log::info("Files backup created: {$filename}");

        return $filename;
    }

    public function createFullBackup(): string
    {
        $dbFilename = $this->createDatabaseBackup();
        $dbFilepath = $this->backupPath . '/' . $dbFilename;

        $filename = 'full_backup_' . now()->format('Y-m-d_H-i-s') . '.zip';
        $filepath = $this->backupPath . '/' . $filename;

        $zip = new ZipArchive();
        if ($zip->open($filepath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Cannot create zip file.');
        }

        $zip->addFile($dbFilepath, 'database.sql');
        $this->addFolderToZip($zip, storage_path('app/public'), 'storage/app/public');
        $this->addFolderToZip($zip, public_path('images'), 'public/images');

        $zip->close();

        // Remove the standalone SQL file since it's included in the full backup
        File::delete($dbFilepath);

        Log::info("Full backup created: {$filename}");

        return $filename;
    }

    public function restoreBackup(string $filename): bool
    {
        $filepath = $this->backupPath . '/' . basename($filename);

        if (! File::exists($filepath)) {
            throw new \RuntimeException('Backup file not found.');
        }

        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if ($extension === 'sql') {
            return $this->restoreDatabaseFromSql($filepath);
        }

        if ($extension === 'zip') {
            return $this->restoreFromZip($filepath);
        }

        throw new \RuntimeException('Unsupported backup file type.');
    }

    public function deleteBackup(string $filename): bool
    {
        $filepath = $this->backupPath . '/' . basename($filename);

        if (! File::exists($filepath)) {
            return false;
        }

        return File::delete($filepath);
    }

    public function cleanOldBackups(int $days = 30): int
    {
        $backups = File::files($this->backupPath);
        $deleted = 0;
        $threshold = now()->subDays($days)->getTimestamp();

        foreach ($backups as $file) {
            if ($file->getMTime() < $threshold) {
                File::delete($file->getRealPath());
                $deleted++;
            }
        }

        Log::info("Cleaned {$deleted} old backup(s) older than {$days} days.");

        return $deleted;
    }

    public function optimizeDatabase(): array
    {
        $driver = config('database.default');
        $results = [];

        if ($driver === 'sqlite') {
            DB::statement('VACUUM');
            DB::statement('ANALYZE');
            $results[] = 'SQLite VACUUM and ANALYZE completed.';
        } else {
            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . config("database.connections.{$driver}.database");

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                DB::statement("OPTIMIZE TABLE `{$tableName}`");
                $results[] = "Optimized table: {$tableName}";
            }
        }

        Log::info('Database optimization completed.');

        return $results;
    }

    public function clearCache(): array
    {
        $results = [];

        Artisan::call('cache:clear');
        $results[] = 'Application cache cleared.';

        Artisan::call('config:clear');
        $results[] = 'Configuration cache cleared.';

        Artisan::call('route:clear');
        $results[] = 'Route cache cleared.';

        Artisan::call('view:clear');
        $results[] = 'View cache cleared.';

        Log::info('All caches cleared.');

        return $results;
    }

    public function clearLogs(): int
    {
        $logPath = storage_path('logs');
        $deleted = 0;

        if (File::exists($logPath)) {
            $files = File::files($logPath);
            foreach ($files as $file) {
                if ($file->getExtension() === 'log') {
                    File::delete($file->getRealPath());
                    $deleted++;
                }
            }
        }

        Log::info("Cleared {$deleted} log file(s).");

        return $deleted;
    }

    public function cleanSessions(): int
    {
        $deleted = 0;
        $sessionDriver = config('session.driver');

        if ($sessionDriver === 'file') {
            $sessionPath = storage_path('framework/sessions');
            if (File::exists($sessionPath)) {
                $files = File::files($sessionPath);
                $threshold = now()->subHours(24)->getTimestamp();

                foreach ($files as $file) {
                    if ($file->getMTime() < $threshold) {
                        File::delete($file->getRealPath());
                        $deleted++;
                    }
                }
            }
        } elseif ($sessionDriver === 'database') {
            $deleted = DB::table(config('session.table', 'sessions'))
                ->where('last_activity', '<', now()->subHours(24)->timestamp)
                ->delete();
        }

        Log::info("Cleaned {$deleted} old session(s).");

        return $deleted;
    }

    public function cleanTempFiles(): array
    {
        $results = [];
        $paths = [
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
        ];

        foreach ($paths as $path) {
            if (! File::exists($path)) {
                continue;
            }

            $count = 0;
            $files = File::allFiles($path);

            foreach ($files as $file) {
                if ($file->isFile()) {
                    File::delete($file->getRealPath());
                    $count++;
                }
            }

            $results[] = "Cleaned {$count} file(s) from {$path}";
        }

        Log::info('Temp files cleaned.');

        return $results;
    }

    public function getSystemInfo(): array
    {
        $driver = config('database.default');
        $connection = config("database.connections.{$driver}");

        $info = [
            'server' => [
                'os' => php_uname('s') . ' ' . php_uname('r'),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'web_server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
                'server_time' => now()->format('Y-m-d H:i:s'),
            ],
            'database' => [
                'driver' => $driver,
                'version' => $this->getDatabaseVersion($driver),
                'database_name' => $connection['database'] ?? 'N/A',
                'database_host' => $connection['host'] ?? 'N/A',
                'database_size' => $this->getDatabaseSize($driver),
            ],
            'storage' => [
                'total_space' => $this->formatBytes(@disk_total_space(base_path())),
                'free_space' => $this->formatBytes(@disk_free_space(base_path())),
                'used_space' => $this->formatBytes(@disk_total_space(base_path()) - @disk_free_space(base_path())),
                'app_size' => $this->formatBytes($this->getDirectorySize(base_path())),
                'backups_count' => count(File::files($this->backupPath)),
                'backups_size' => $this->formatBytes($this->getDirectorySize($this->backupPath)),
            ],
            'php' => [
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time') . 's',
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
            ],
            'extensions' => [
                'gd' => extension_loaded('gd'),
                'curl' => extension_loaded('curl'),
                'zip' => extension_loaded('zip'),
                'mbstring' => extension_loaded('mbstring'),
                'pdo' => extension_loaded('pdo'),
                'pdo_mysql' => extension_loaded('pdo_mysql'),
                'pdo_sqlite' => extension_loaded('pdo_sqlite'),
                'openssl' => extension_loaded('openssl'),
                'fileinfo' => extension_loaded('fileinfo'),
            ],
        ];

        return $info;
    }

    protected function createMysqlDump(array $connection, string $filepath): void
    {
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s --single-transaction --quick --routines --triggers > %s 2>&1',
            escapeshellarg($connection['host'] ?? '127.0.0.1'),
            escapeshellarg((string) ($connection['port'] ?? 3306)),
            escapeshellarg($connection['username'] ?? 'root'),
            escapeshellarg($connection['password'] ?? ''),
            escapeshellarg($connection['database'] ?? ''),
            escapeshellarg($filepath)
        );

        $output = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);

        if ($exitCode !== 0 || ! file_exists($filepath) || filesize($filepath) === 0) {
            $error = implode("\n", $output);
            throw new \RuntimeException("mysqldump failed: {$error}");
        }
    }

    protected function restoreDatabaseFromSql(string $filepath): bool
    {
        $driver = config('database.default');
        $connection = config("database.connections.{$driver}");

        if ($driver === 'sqlite') {
            $dbPath = $connection['database'] ?? database_path('database.sqlite');
            File::copy($filepath, $dbPath);
            Log::info('SQLite database restored.');

            return true;
        }

        $command = sprintf(
            'mysql --host=%s --port=%s --user=%s --password=%s %s < %s 2>&1',
            escapeshellarg($connection['host'] ?? '127.0.0.1'),
            escapeshellarg((string) ($connection['port'] ?? 3306)),
            escapeshellarg($connection['username'] ?? 'root'),
            escapeshellarg($connection['password'] ?? ''),
            escapeshellarg($connection['database'] ?? ''),
            escapeshellarg($filepath)
        );

        $output = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            $error = implode("\n", $output);
            throw new \RuntimeException("mysql restore failed: {$error}");
        }

        Log::info('MySQL database restored.');

        return true;
    }

    protected function restoreFromZip(string $filepath): bool
    {
        $zip = new ZipArchive();
        if ($zip->open($filepath) !== true) {
            throw new \RuntimeException('Cannot open zip file.');
        }

        $tempDir = storage_path('app/restore_temp_' . Str::random(8));
        File::makeDirectory($tempDir, 0755, true);

        $zip->extractTo($tempDir);
        $zip->close();

        // Restore database if present
        $dbFile = $tempDir . '/database.sql';
        if (file_exists($dbFile)) {
            $this->restoreDatabaseFromSql($dbFile);
        }

        // Restore files
        $this->restoreFolder($tempDir . '/storage/app/public', storage_path('app/public'));
        $this->restoreFolder($tempDir . '/public/images', public_path('images'));

        // Cleanup
        $this->deleteDirectory($tempDir);

        Log::info('Full backup restored.');

        return true;
    }

    protected function restoreFolder(string $source, string $destination): void
    {
        if (! File::exists($source)) {
            return;
        }

        if (! File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $files = File::allFiles($source);
        foreach ($files as $file) {
            $relativePath = $file->getRelativePathname();
            $destFile = $destination . '/' . $relativePath;
            $destDir = dirname($destFile);

            if (! File::exists($destDir)) {
                File::makeDirectory($destDir, 0755, true);
            }

            File::copy($file->getRealPath(), $destFile);
        }
    }

    protected function addFolderToZip(ZipArchive $zip, string $folder, string $zipPrefix): void
    {
        if (! File::exists($folder)) {
            return;
        }

        $files = File::allFiles($folder);
        foreach ($files as $file) {
            if ($file->isFile()) {
                $zip->addFile($file->getRealPath(), $zipPrefix . '/' . $file->getRelativePathname());
            }
        }
    }

    protected function getBackupType(string $filename): string
    {
        if (Str::startsWith($filename, 'full_backup_')) {
            return 'full';
        }
        if (Str::startsWith($filename, 'files_backup_')) {
            return 'files';
        }
        if (Str::startsWith($filename, 'db_backup_')) {
            return 'database';
        }

        return 'unknown';
    }

    protected function getDatabaseVersion(string $driver): string
    {
        try {
            if ($driver === 'sqlite') {
                return 'SQLite ' . (DB::selectOne('SELECT sqlite_version() AS version')->version ?? 'Unknown');
            }

            $result = DB::selectOne('SELECT VERSION() AS version');

            return $result->version ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    protected function getDatabaseSize(string $driver): string
    {
        try {
            if ($driver === 'sqlite') {
                $dbPath = config("database.connections.{$driver}.database", database_path('database.sqlite'));
                if (file_exists($dbPath)) {
                    return $this->formatBytes(filesize($dbPath));
                }

                return 'Unknown';
            }

            $dbName = config("database.connections.{$driver}.database");
            $result = DB::selectOne(
                "SELECT SUM(data_length + index_length) AS size FROM information_schema.TABLES WHERE table_schema = ?",
                [$dbName]
            );

            return $result->size ? $this->formatBytes($result->size) : 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    protected function getDirectorySize(string $path): int
    {
        if (! File::exists($path)) {
            return 0;
        }

        $size = 0;
        try {
            $files = File::allFiles($path);
            foreach ($files as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        } catch (\Exception $e) {
            // Ignore permission errors
        }

        return $size;
    }

    protected function deleteDirectory(string $path): void
    {
        if (! File::exists($path)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                File::delete($file->getRealPath());
            }
        }

        rmdir($path);
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = floor(log($bytes, 1024));

        return round($bytes / pow(1024, $power), 2) . ' ' . $units[(int) $power];
    }
}
