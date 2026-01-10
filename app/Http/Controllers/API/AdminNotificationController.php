<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    /**
     * Get all notifications with pagination and filters.
     * GET /api/admin/notifications
     */
    public function index(Request $request): JsonResponse
    {
        $query = AdminNotification::query()
            ->orderBy('created_at', 'desc');

        // Filter by read status
        if ($request->has('is_read')) {
            $isRead = filter_var($request->input('is_read'), FILTER_VALIDATE_BOOLEAN);
            $query->where('is_read', $isRead);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Pagination
        $perPage = min((int) $request->input('per_page', 20), 100);
        $notifications = $query->paginate($perPage);

        return response()->json([
            'data' => $notifications->items(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * Get notification counts.
     * GET /api/admin/notifications/count
     */
    public function count(): JsonResponse
    {
        $total = AdminNotification::count();
        $unread = AdminNotification::unread()->count();
        $read = AdminNotification::read()->count();

        // Count by category
        $byCategory = [];
        foreach (AdminNotification::getCategories() as $category) {
            $byCategory[$category] = [
                'total' => AdminNotification::category($category)->count(),
                'unread' => AdminNotification::category($category)->unread()->count(),
            ];
        }

        // Count by priority
        $byPriority = [];
        foreach (AdminNotification::getPriorities() as $priority) {
            $byPriority[$priority] = [
                'total' => AdminNotification::priority($priority)->count(),
                'unread' => AdminNotification::priority($priority)->unread()->count(),
            ];
        }

        // High priority unread (important!)
        $highPriorityUnread = AdminNotification::highPriority()->unread()->count();

        return response()->json([
            'total' => $total,
            'unread' => $unread,
            'read' => $read,
            'high_priority_unread' => $highPriorityUnread,
            'by_category' => $byCategory,
            'by_priority' => $byPriority,
        ]);
    }

    /**
     * Get latest unread notifications (for header badge/dropdown).
     * GET /api/admin/notifications/latest
     */
    public function latest(Request $request): JsonResponse
    {
        $limit = min((int) $request->input('limit', 5), 20);

        $notifications = AdminNotification::unread()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        $unreadCount = AdminNotification::unread()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Get a single notification.
     * GET /api/admin/notifications/{id}
     */
    public function show(int $id): JsonResponse
    {
        $notification = AdminNotification::findOrFail($id);

        return response()->json($notification);
    }

    /**
     * Mark notification as read.
     * POST /api/admin/notifications/{id}/read
     */
    public function markAsRead(int $id): JsonResponse
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديد الإشعار كمقروء',
            'data' => $notification->fresh(),
        ]);
    }

    /**
     * Mark notification as unread.
     * POST /api/admin/notifications/{id}/unread
     */
    public function markAsUnread(int $id): JsonResponse
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->markAsUnread();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديد الإشعار كغير مقروء',
            'data' => $notification->fresh(),
        ]);
    }

    /**
     * Mark all notifications as read.
     * POST /api/admin/notifications/read-all
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $query = AdminNotification::unread();

        // Optional: filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $count = $query->count();

        $query->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "تم تحديد {$count} إشعار كمقروء",
            'count' => $count,
        ]);
    }

    /**
     * Delete a notification.
     * DELETE /api/admin/notifications/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $notification = AdminNotification::findOrFail($id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الإشعار بنجاح',
        ]);
    }

    /**
     * Delete all read notifications.
     * DELETE /api/admin/notifications/clear-read
     */
    public function clearRead(): JsonResponse
    {
        $count = AdminNotification::read()->count();
        AdminNotification::read()->delete();

        return response()->json([
            'success' => true,
            'message' => "تم حذف {$count} إشعار مقروء",
            'count' => $count,
        ]);
    }

    /**
     * Delete all notifications (with optional filters).
     * DELETE /api/admin/notifications/clear-all
     */
    public function clearAll(Request $request): JsonResponse
    {
        $query = AdminNotification::query();

        // Optional: filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Optional: only delete older than X days
        if ($request->filled('older_than_days')) {
            $days = (int) $request->input('older_than_days');
            $query->where('created_at', '<', now()->subDays($days));
        }

        $count = $query->count();
        $query->delete();

        return response()->json([
            'success' => true,
            'message' => "تم حذف {$count} إشعار",
            'count' => $count,
        ]);
    }

    /**
     * Get available categories and priorities.
     * GET /api/admin/notifications/meta
     */
    public function meta(): JsonResponse
    {
        return response()->json([
            'categories' => [
                ['key' => 'support', 'label' => 'طلبات الدعم', 'icon' => '🆘', 'color' => '#ef4444'],
                ['key' => 'system', 'label' => 'النظام', 'icon' => '⚙️', 'color' => '#f97316'],
                ['key' => 'users', 'label' => 'المستخدمين', 'icon' => '👥', 'color' => '#3b82f6'],
                ['key' => 'content', 'label' => 'المحتوى', 'icon' => '📝', 'color' => '#22c55e'],
                ['key' => 'engagement', 'label' => 'التفاعل', 'icon' => '💬', 'color' => '#8b5cf6'],
                ['key' => 'library', 'label' => 'المكتبة', 'icon' => '📚', 'color' => '#eab308'],
            ],
            'priorities' => [
                ['key' => 'high', 'label' => 'عالية', 'color' => '#ef4444'],
                ['key' => 'medium', 'label' => 'متوسطة', 'color' => '#f97316'],
                ['key' => 'low', 'label' => 'منخفضة', 'color' => '#22c55e'],
            ],
        ]);
    }
}
