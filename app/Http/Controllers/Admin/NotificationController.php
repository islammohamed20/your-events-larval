<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected function getVisibleNotificationsQuery(): Builder
    {
        $user = auth()->user();
        $allowedTypes = [];

        if ($user?->hasAdminPermission('manage_users')) {
            $allowedTypes = array_merge($allowedTypes, ['order', 'payment', 'contact', 'supplier']);
        }

        if ($user?->hasAdminPermission('manage_bookings') || $user?->hasAdminPermission('bookings.view')) {
            $allowedTypes[] = 'booking';
        }

        if ($user?->hasAdminPermission('manage_bookings') || $user?->hasAdminPermission('quotes.view')) {
            $allowedTypes[] = 'quote';
        }

        if ($user?->hasAdminPermission('manage_customers') || $user?->hasAdminPermission('customers.view')) {
            $allowedTypes[] = 'customer';
        }

        $allowedTypes = array_values(array_unique($allowedTypes));

        $query = AdminNotification::query();

        if (empty($allowedTypes)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('type', $allowedTypes);
    }

    protected function successResponse(Request $request, string $message)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.notifications.index')->with('success', $message);
    }

    /**
     * Get unread notifications count
     */
    public function count()
    {
        $count = $this->getVisibleNotificationsQuery()->unread()->count();

        return response()->json([
            'count' => $count,
        ]);
    }

    /**
     * Get recent unread notifications for popup
     */
    public function recent(Request $request)
    {
        $lastCheck = $request->get('last_check');

        $query = $this->getVisibleNotificationsQuery()
            ->unread()
            ->orderBy('created_at', 'desc');

        if ($lastCheck) {
            $query->where('created_at', '>', $lastCheck);
        }

        $notifications = $query->take(10)->get();

        return response()->json([
            'notifications' => $notifications->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'message' => $n->message,
                    'icon' => $n->icon,
                    'color' => $n->color,
                    'link' => $n->link,
                    'created_at' => $n->created_at->diffForHumans(),
                    'timestamp' => $n->created_at->toIso8601String(),
                ];
            }),
            'server_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get all notifications with pagination
     */
    public function index(Request $request)
    {
        $notifications = $this->getVisibleNotificationsQuery()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $this->getVisibleNotificationsQuery()->findOrFail($id);
        $notification->markAsRead();

        return $this->successResponse($request, 'تم تحديد الإشعار كمقروء.');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $this->getVisibleNotificationsQuery()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return $this->successResponse($request, 'تم تحديد جميع الإشعارات المسموح بها كمقروءة.');
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, $id)
    {
        $this->getVisibleNotificationsQuery()->findOrFail($id)->delete();

        return $this->successResponse($request, 'تم حذف الإشعار.');
    }

    /**
     * Clear old notifications (older than 30 days)
     */
    public function clearOld(Request $request)
    {
        $this->getVisibleNotificationsQuery()
            ->where('created_at', '<', now()->subDays(30))
            ->where('is_read', true)
            ->delete();

        return $this->successResponse($request, 'تم حذف الإشعارات القديمة المسموح بها.');
    }
}
