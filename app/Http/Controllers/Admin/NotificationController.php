<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get unread notifications count
     */
    public function count()
    {
        $count = AdminNotification::unread()->count();

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

        $query = AdminNotification::unread()->orderBy('created_at', 'desc');

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
        $notifications = AdminNotification::orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        AdminNotification::unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        AdminNotification::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Clear old notifications (older than 30 days)
     */
    public function clearOld()
    {
        AdminNotification::where('created_at', '<', now()->subDays(30))
            ->where('is_read', true)
            ->delete();

        return response()->json(['success' => true]);
    }
}
