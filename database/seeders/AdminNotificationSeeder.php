<?php

namespace Database\Seeders;

use App\Models\AdminNotification;
use Illuminate\Database\Seeder;

class AdminNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        AdminNotification::truncate();

        $notifications = [
            // Support - High Priority
            [
                'type' => 'new_individual_support',
                'category' => 'support',
                'title' => 'طلب دعم فردي جديد',
                'body' => 'تم تقديم طلب دعم فردي جديد من أحمد محمد السيد',
                'data' => ['request_id' => 15, 'user_name' => 'أحمد محمد السيد'],
                'priority' => 'high',
                'is_read' => false,
                'action_url' => '/admin/support/individual/15',
                'created_at' => now()->subMinutes(30),
            ],
            [
                'type' => 'new_institutional_support',
                'category' => 'support',
                'title' => 'طلب دعم مؤسسي جديد',
                'body' => 'تم تقديم طلب دعم مؤسسي جديد من مؤسسة الخير الاجتماعية',
                'data' => ['request_id' => 8, 'institution_name' => 'مؤسسة الخير الاجتماعية'],
                'priority' => 'high',
                'is_read' => false,
                'action_url' => '/admin/support/institutional/8',
                'created_at' => now()->subHours(2),
            ],
            [
                'type' => 'document_uploaded',
                'category' => 'support',
                'title' => 'رفع مستند جديد',
                'body' => 'تم رفع مستند جديد: شهادة_الدخل.pdf',
                'data' => ['request_id' => 12, 'document_name' => 'شهادة_الدخل.pdf'],
                'priority' => 'medium',
                'is_read' => true,
                'read_at' => now()->subHour(),
                'action_url' => '/admin/support/individual/12',
                'created_at' => now()->subHours(5),
            ],

            // System Notifications
            [
                'type' => 'backup_success',
                'category' => 'system',
                'title' => 'نسخة احتياطية ناجحة',
                'body' => 'تم إنشاء نسخة احتياطية بنجاح: 2026-01-05-10-00-00.zip (15.5MB)',
                'data' => ['file_name' => '2026-01-05-10-00-00.zip', 'file_size' => '15.5MB'],
                'priority' => 'low',
                'is_read' => true,
                'read_at' => now()->subHours(3),
                'action_url' => '/admin/backups',
                'created_at' => now()->subHours(6),
            ],
            [
                'type' => 'backup_failed',
                'category' => 'system',
                'title' => 'فشل النسخ الاحتياطي',
                'body' => 'فشل إنشاء النسخة الاحتياطية: Database connection timeout',
                'data' => ['error' => 'Database connection timeout'],
                'priority' => 'high',
                'is_read' => false,
                'action_url' => '/admin/backups',
                'created_at' => now()->subDays(1),
            ],

            // User Notifications
            [
                'type' => 'new_user_registered',
                'category' => 'users',
                'title' => 'مستخدم جديد',
                'body' => 'تم تسجيل مستخدم جديد: فاطمة علي (fatima@example.com)',
                'data' => ['user_id' => 250, 'user_name' => 'فاطمة علي', 'email' => 'fatima@example.com'],
                'priority' => 'medium',
                'is_read' => false,
                'action_url' => '/admin/users/250',
                'created_at' => now()->subHours(4),
            ],
            [
                'type' => 'new_user_registered',
                'category' => 'users',
                'title' => 'مستخدم جديد',
                'body' => 'تم تسجيل مستخدم جديد: خالد الشمري (khalid@example.com)',
                'data' => ['user_id' => 249, 'user_name' => 'خالد الشمري', 'email' => 'khalid@example.com'],
                'priority' => 'medium',
                'is_read' => true,
                'read_at' => now()->subHours(10),
                'action_url' => '/admin/users/249',
                'created_at' => now()->subHours(12),
            ],

            // Content Notifications
            [
                'type' => 'new_article',
                'category' => 'content',
                'title' => 'مقال جديد',
                'body' => 'تم إضافة مقال جديد: كيفية التقديم على برامج الدعم',
                'data' => ['article_id' => 45, 'title' => 'كيفية التقديم على برامج الدعم'],
                'priority' => 'medium',
                'is_read' => false,
                'action_url' => '/admin/articles/45',
                'created_at' => now()->subHours(8),
            ],

            // Engagement Notifications
            [
                'type' => 'new_feedback',
                'category' => 'engagement',
                'title' => 'اقتراح جديد',
                'body' => 'تم استلام اقتراح جديد من محمد عبدالله',
                'data' => ['feedback_id' => 30, 'name' => 'محمد عبدالله', 'type' => 'suggestion'],
                'priority' => 'medium',
                'is_read' => false,
                'action_url' => '/admin/feedback/30',
                'created_at' => now()->subHours(1),
            ],
            [
                'type' => 'new_feedback',
                'category' => 'engagement',
                'title' => 'شكوى جديدة',
                'body' => 'تم استلام شكوى جديدة من سارة القحطاني',
                'data' => ['feedback_id' => 31, 'name' => 'سارة القحطاني', 'type' => 'complaint'],
                'priority' => 'high',
                'is_read' => false,
                'action_url' => '/admin/feedback/31',
                'created_at' => now()->subMinutes(45),
            ],
            [
                'type' => 'new_platform_rating',
                'category' => 'engagement',
                'title' => 'تقييم للمنصة',
                'body' => 'تم تقييم المنصة بـ 5 نجوم',
                'data' => ['rating' => 5],
                'priority' => 'low',
                'is_read' => true,
                'read_at' => now()->subHours(2),
                'action_url' => '/admin/ratings',
                'created_at' => now()->subHours(3),
            ],

            // Library Notifications
            [
                'type' => 'new_book_rating',
                'category' => 'library',
                'title' => 'تقييم كتاب',
                'body' => 'تم تقييم كتاب "دليل المستفيد" بـ 4 نجوم',
                'data' => ['book_id' => 10, 'book_title' => 'دليل المستفيد', 'rating' => 4],
                'priority' => 'low',
                'is_read' => true,
                'read_at' => now()->subDays(1),
                'action_url' => '/admin/library/books/10',
                'created_at' => now()->subDays(2),
            ],

            // More older notifications
            [
                'type' => 'support_status_changed',
                'category' => 'support',
                'title' => 'تغيير حالة طلب الدعم',
                'body' => 'تم تغيير حالة طلب الدعم من قيد المراجعة إلى مقبول',
                'data' => ['request_id' => 10, 'old_status' => 'قيد المراجعة', 'new_status' => 'مقبول'],
                'priority' => 'medium',
                'is_read' => true,
                'read_at' => now()->subDays(2),
                'action_url' => '/admin/support/individual/10',
                'created_at' => now()->subDays(3),
            ],
            [
                'type' => 'backup_restored',
                'category' => 'system',
                'title' => 'استرجاع نسخة احتياطية',
                'body' => 'تم استرجاع النسخة الاحتياطية: 2026-01-01-backup.zip',
                'data' => ['file_name' => '2026-01-01-backup.zip'],
                'priority' => 'high',
                'is_read' => true,
                'read_at' => now()->subDays(3),
                'action_url' => '/admin/backups',
                'created_at' => now()->subDays(4),
            ],
        ];

        foreach ($notifications as $notification) {
            AdminNotification::create($notification);
        }

        $total = count($notifications);
        $unread = collect($notifications)->where('is_read', false)->count();

        $this->command->info("✅ تم إنشاء {$total} إشعار للأدمن");
        $this->command->info("   - غير مقروء: {$unread}");
        $this->command->info("   - مقروء: " . ($total - $unread));
    }
}

