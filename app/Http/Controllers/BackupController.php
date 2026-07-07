<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class BackupController extends Controller
{
    public function index()
    {
        $backups = [];
        $path = storage_path('app/backups');
        if (is_dir($path)) {
            $files = glob($path . '/*.sql');
            rsort($files);
            foreach ($files as $file) {
                $backups[] = [
                    'filename' => basename($file),
                    'size' => filesize($file),
                    'date' => date('Y-m-d H:i:s', filemtime($file)),
                ];
            }
        }

        return inertia('Backups/Index', [
            'backups' => $backups,
        ]);
    }

    protected function getMysqldumpPath(): string
    {
        return env('DB_MYSQLDUMP_PATH', 'mysqldump');
    }

    protected function getMysqlClientPath(): string
    {
        return env('DB_MYSQL_CLIENT', 'mysql');
    }

    public function download()
    {
        $db = config('database.connections.mysql');
        $host = $db['host'];
        $port = $db['port'];
        $database = $db['database'];
        $username = $db['username'];
        $password = $db['password'];

        $dir = storage_path('app/backups');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'emas_tracker_' . now()->format('Y-m-d_His') . '.sql';
        $filepath = $dir . '/' . $filename;

        $passwordArg = !empty($password) ? '--password=' . escapeshellarg($password) : '--skip-password';

        $command = sprintf(
            '%s --host=%s --port=%s --user=%s %s %s > %s',
            escapeshellarg($this->getMysqldumpPath()),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $passwordArg,
            escapeshellarg($database),
            escapeshellarg($filepath)
        );

        $process = Process::fromShellCommandline($command);
        $process->run();

        if (!$process->isSuccessful()) {
            return redirect()->back()->with('error', 'Gagal backup database. Pastikan mysqldump tersedia.');
        }

        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,txt|max:102400',
        ]);

        $db = config('database.connections.mysql');
        $host = $db['host'];
        $port = $db['port'];
        $database = $db['database'];
        $username = $db['username'];
        $password = $db['password'];

        $filepath = $request->file('backup_file')->getRealPath();

        // Strip mysqldump warnings from backup file
        $cleanPath = $filepath . '.clean';
        file_put_contents($cleanPath, preg_replace(
            '/^mysqldump:\s*\[Warning\].*\n/m',
            '',
            file_get_contents($filepath)
        ));

        $passwordArg = !empty($password) ? '--password=' . escapeshellarg($password) : '';

        $command = sprintf(
            '%s --host=%s --port=%s --user=%s %s %s < %s 2>&1',
            escapeshellarg($this->getMysqlClientPath()),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $passwordArg,
            escapeshellarg($database),
            escapeshellarg($cleanPath)
        );

        $process = Process::fromShellCommandline($command);
        $process->run();

        @unlink($cleanPath);

        if (!$process->isSuccessful()) {
            $errorMsg = $process->getErrorOutput() ?: $process->getOutput();
            return redirect()->back()->with('error', 'Gagal restore database: ' . $errorMsg);
        }

        return redirect()->back()->with('success', 'Database berhasil direstore.');
    }
}
