<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MaintenanceController extends Controller
{
    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;

        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (! $user || ! $user->isAdmin()) {
                abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
            }

            return $next($request);
        });
    }

    public function index()
    {
        $backups = $this->backupService->listBackups();
        $totalBackupsSize = array_sum(array_column($backups, 'size_bytes'));

        return view('admin.maintenance.index', compact('backups', 'totalBackupsSize'));
    }

    public function backupDatabase(Request $request)
    {
        try {
            $filename = $this->backupService->createDatabaseBackup();

            return redirect()->route('admin.maintenance.index')
                ->with('success', "تم إنشاء نسخة احتياطية لقاعدة البيانات بنجاح: {$filename}");
        } catch (\Exception $e) {
            Log::error('Database backup failed: ' . $e->getMessage());

            return redirect()->route('admin.maintenance.index')
                ->with('error', 'فشل في إنشاء النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    public function backupFiles(Request $request)
    {
        try {
            $filename = $this->backupService->createFilesBackup();

            return redirect()->route('admin.maintenance.index')
                ->with('success', "تم إنشاء نسخة احتياطية للملفات بنجاح: {$filename}");
        } catch (\Exception $e) {
            Log::error('Files backup failed: ' . $e->getMessage());

            return redirect()->route('admin.maintenance.index')
                ->with('error', 'فشل في إنشاء النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    public function backupFull(Request $request)
    {
        try {
            $filename = $this->backupService->createFullBackup();

            return redirect()->route('admin.maintenance.index')
                ->with('success', "تم إنشاء نسخة احتياطية كاملة بنجاح: {$filename}");
        } catch (\Exception $e) {
            Log::error('Full backup failed: ' . $e->getMessage());

            return redirect()->route('admin.maintenance.index')
                ->with('error', 'فشل في إنشاء النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    public function restore(Request $request, string $filename)
    {
        $request->validate([
            'confirm' => 'required|accepted',
        ], [
            'confirm.accepted' => 'يجب تأكيد عملية الاستعادة.',
        ]);

        try {
            $this->backupService->restoreBackup($filename);

            return redirect()->route('admin.maintenance.index')
                ->with('success', "تم استعادة النسخة الاحتياطية بنجاح: {$filename}");
        } catch (\Exception $e) {
            Log::error('Backup restore failed: ' . $e->getMessage());

            return redirect()->route('admin.maintenance.index')
                ->with('error', 'فشل في استعادة النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    public function deleteBackup(Request $request, string $filename)
    {
        try {
            $deleted = $this->backupService->deleteBackup($filename);

            if ($deleted) {
                return redirect()->route('admin.maintenance.index')
                    ->with('success', "تم حذف النسخة الاحتياطية: {$filename}");
            }

            return redirect()->route('admin.maintenance.index')
                ->with('error', 'النسخة الاحتياطية غير موجودة.');
        } catch (\Exception $e) {
            return redirect()->route('admin.maintenance.index')
                ->with('error', 'فشل في حذف النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    public function downloadBackup(Request $request, string $filename)
    {
        $filepath = $this->backupService->getBackupPath() . '/' . basename($filename);

        if (! file_exists($filepath)) {
            return redirect()->route('admin.maintenance.index')
                ->with('error', 'النسخة الاحتياطية غير موجودة.');
        }

        return response()->download($filepath, $filename);
    }

    public function optimizeDatabase(Request $request)
    {
        try {
            $results = $this->backupService->optimizeDatabase();

            return redirect()->route('admin.maintenance.index')
                ->with('success', 'تم تحسين قاعدة البيانات بنجاح.')
                ->with('optimize_results', $results);
        } catch (\Exception $e) {
            Log::error('Database optimization failed: ' . $e->getMessage());

            return redirect()->route('admin.maintenance.index')
                ->with('error', 'فشل في تحسين قاعدة البيانات: ' . $e->getMessage());
        }
    }

    public function clearCache(Request $request)
    {
        try {
            $results = $this->backupService->clearCache();

            return redirect()->route('admin.maintenance.index')
                ->with('success', 'تم مسح جميع أنواع الـ Cache بنجاح.')
                ->with('cache_results', $results);
        } catch (\Exception $e) {
            return redirect()->route('admin.maintenance.index')
                ->with('error', 'فشل في مسح الـ Cache: ' . $e->getMessage());
        }
    }

    public function clearLogs(Request $request)
    {
        try {
            $deleted = $this->backupService->clearLogs();

            return redirect()->route('admin.maintenance.index')
                ->with('success', "تم حذف {$deleted} ملف سجل بنجاح.");
        } catch (\Exception $e) {
            return redirect()->route('admin.maintenance.index')
                ->with('error', 'فشل في حذف السجلات: ' . $e->getMessage());
        }
    }

    public function cleanSessions(Request $request)
    {
        try {
            $deleted = $this->backupService->cleanSessions();

            return redirect()->route('admin.maintenance.index')
                ->with('success', "تم تنظيف {$deleted} جلسة منتهية بنجاح.");
        } catch (\Exception $e) {
            return redirect()->route('admin.maintenance.index')
                ->with('error', 'فشل في تنظيف الجلسات: ' . $e->getMessage());
        }
    }

    public function cleanTempFiles(Request $request)
    {
        try {
            $results = $this->backupService->cleanTempFiles();

            return redirect()->route('admin.maintenance.index')
                ->with('success', 'تم حذف الملفات المؤقتة بنجاح.')
                ->with('temp_results', $results);
        } catch (\Exception $e) {
            return redirect()->route('admin.maintenance.index')
                ->with('error', 'فشل في حذف الملفات المؤقتة: ' . $e->getMessage());
        }
    }

    public function systemInfo()
    {
        $info = $this->backupService->getSystemInfo();

        return view('admin.maintenance.system-info', compact('info'));
    }
}
