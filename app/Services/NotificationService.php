<?php

namespace App\Services;

use App\Models\AdminNotification;

class NotificationService
{
    /**
     * Create a new notification.
     */
    public function create(
        string $type,
        string $category,
        string $title,
        string $message,
        ?array $data = null,
        string $priority = 'medium',
        ?string $actionUrl = null,
        ?int $triggeredBy = null
    ): AdminNotification {
        return AdminNotification::create([
            'type' => $type,
            'category' => $category,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'priority' => $priority,
            'action_url' => $actionUrl,
            'triggered_by' => $triggeredBy,
        ]);
    }

    // =========================================================================
    // Support Notifications
    // =========================================================================

    /**
     * Notify about new individual support request.
     */
    public function notifyNewIndividualSupport(int $requestId, string $userName, ?int $triggeredBy = null): AdminNotification
    {
        return $this->create(
            type: AdminNotification::TYPE_NEW_INDIVIDUAL_SUPPORT,
            category: AdminNotification::CATEGORY_SUPPORT,
            title: 'طلب دعم فردي جديد',
            message: "تم تقديم طلب دعم فردي جديد من {$userName}",
            data: ['request_id' => $requestId, 'user_name' => $userName],
            priority: AdminNotification::PRIORITY_HIGH,
            actionUrl: "/admin/support/individual/{$requestId}",
            triggeredBy: $triggeredBy
        );
    }

    /**
     * Notify about new institutional support request.
     */
    public function notifyNewInstitutionalSupport(int $requestId, string $institutionName, ?int $triggeredBy = null): AdminNotification
    {
        return $this->create(
            type: AdminNotification::TYPE_NEW_INSTITUTIONAL_SUPPORT,
            category: AdminNotification::CATEGORY_SUPPORT,
            title: 'طلب دعم مؤسسي جديد',
            message: "تم تقديم طلب دعم مؤسسي جديد من {$institutionName}",
            data: ['request_id' => $requestId, 'institution_name' => $institutionName],
            priority: AdminNotification::PRIORITY_HIGH,
            actionUrl: "/admin/support/institutional/{$requestId}",
            triggeredBy: $triggeredBy
        );
    }

    /**
     * Notify about support status change.
     */
    public function notifySupportStatusChanged(int $requestId, string $oldStatus, string $newStatus, string $requestType = 'individual'): AdminNotification
    {
        return $this->create(
            type: AdminNotification::TYPE_SUPPORT_STATUS_CHANGED,
            category: AdminNotification::CATEGORY_SUPPORT,
            title: 'تغيير حالة طلب الدعم',
            message: "تم تغيير حالة طلب الدعم من {$oldStatus} إلى {$newStatus}",
            data: ['request_id' => $requestId, 'old_status' => $oldStatus, 'new_status' => $newStatus, 'type' => $requestType],
            priority: AdminNotification::PRIORITY_MEDIUM,
            actionUrl: "/admin/support/{$requestType}/{$requestId}"
        );
    }

    /**
     * Notify about document upload.
     */
    public function notifyDocumentUploaded(int $requestId, string $documentName, string $requestType = 'individual'): AdminNotification
    {
        return $this->create(
            type: AdminNotification::TYPE_DOCUMENT_UPLOADED,
            category: AdminNotification::CATEGORY_SUPPORT,
            title: 'رفع مستند جديد',
            message: "تم رفع مستند جديد: {$documentName}",
            data: ['request_id' => $requestId, 'document_name' => $documentName, 'type' => $requestType],
            priority: AdminNotification::PRIORITY_MEDIUM,
            actionUrl: "/admin/support/{$requestType}/{$requestId}"
        );
    }

    // =========================================================================
    // System Notifications
    // =========================================================================

    /**
     * Notify about successful backup.
     */
    public function notifyBackupSuccess(string $fileName, string $fileSize): AdminNotification
    {
        return $this->create(
            type: AdminNotification::TYPE_BACKUP_SUCCESS,
            category: AdminNotification::CATEGORY_SYSTEM,
            title: 'نسخة احتياطية ناجحة',
            message: "تم إنشاء نسخة احتياطية بنجاح: {$fileName} ({$fileSize})",
            data: ['file_name' => $fileName, 'file_size' => $fileSize],
            priority: AdminNotification::PRIORITY_LOW,
            actionUrl: '/admin/backups'
        );
    }

    /**
     * Notify about failed backup.
     */
    public function notifyBackupFailed(string $errorMessage): AdminNotification
    {
        return $this->create(
            type: AdminNotification::TYPE_BACKUP_FAILED,
            category: AdminNotification::CATEGORY_SYSTEM,
            title: 'فشل النسخ الاحتياطي',
            message: "فشل إنشاء النسخة الاحتياطية: {$errorMessage}",
            data: ['error' => $errorMessage],
            priority: AdminNotification::PRIORITY_HIGH,
            actionUrl: '/admin/backups'
        );
    }

    /**
     * Notify about backup restored.
     */
    public function notifyBackupRestored(string $fileName, ?int $triggeredBy = null): AdminNotification
    {
        return $this->create(
            type: AdminNotification::TYPE_BACKUP_RESTORED,
            category: AdminNotification::CATEGORY_SYSTEM,
            title: 'استرجاع نسخة احتياطية',
            message: "تم استرجاع النسخة الاحتياطية: {$fileName}",
            data: ['file_name' => $fileName],
            priority: AdminNotification::PRIORITY_HIGH,
            actionUrl: '/admin/backups',
            triggeredBy: $triggeredBy
        );
    }

    // =========================================================================
    // User Notifications
    // =========================================================================

    /**
     * Notify about new user registration.
     */
    public function notifyNewUserRegistered(int $userId, string $userName, string $email): AdminNotification
    {
        return $this->create(
            type: AdminNotification::TYPE_NEW_USER_REGISTERED,
            category: AdminNotification::CATEGORY_USERS,
            title: 'مستخدم جديد',
            message: "تم تسجيل مستخدم جديد: {$userName} ({$email})",
            data: ['user_id' => $userId, 'user_name' => $userName, 'email' => $email],
            priority: AdminNotification::PRIORITY_MEDIUM,
            actionUrl: "/admin/users/{$userId}"
        );
    }

    /**
     * Notify about user role change.
     */
    public function notifyUserRoleChanged(int $userId, string $userName, string $oldRole, string $newRole, ?int $triggeredBy = null): AdminNotification
    {
        return $this->create(
            type: AdminNotification::TYPE_USER_ROLE_CHANGED,
            category: AdminNotification::CATEGORY_USERS,
            title: 'تغيير صلاحية مستخدم',
            message: "تم تغيير صلاحية {$userName} من {$oldRole} إلى {$newRole}",
            data: ['user_id' => $userId, 'user_name' => $userName, 'old_role' => $oldRole, 'new_role' => $newRole],
            priority: AdminNotification::PRIORITY_MEDIUM,
            actionUrl: "/admin/users/{$userId}",
            triggeredBy: $triggeredBy
        );
    }

    // =========================================================================
    // Content Notifications
    // =========================================================================

    /**
     * Notify about new article.
     */
    public function notifyNewArticle(int $articleId, string $title, string $authorName, ?int $triggeredBy = null): AdminNotification
    {
        return $this->create(
            type: AdminNotification::TYPE_NEW_ARTICLE,
            category: AdminNotification::CATEGORY_CONTENT,
            title: 'مقال جديد',
            message: "تم إضافة مقال جديد: {$title} بواسطة {$authorName}",
            data: ['article_id' => $articleId, 'title' => $title, 'author' => $authorName],
            priority: AdminNotification::PRIORITY_MEDIUM,
            actionUrl: "/admin/articles/{$articleId}",
            triggeredBy: $triggeredBy
        );
    }

    // =========================================================================
    // Engagement Notifications
    // =========================================================================

    /**
     * Notify about new feedback.
     */
    public function notifyNewFeedback(int $feedbackId, string $name, string $type): AdminNotification
    {
        return $this->create(
            type: AdminNotification::TYPE_NEW_FEEDBACK,
            category: AdminNotification::CATEGORY_ENGAGEMENT,
            title: 'تقييم جديد',
            message: "تم استلام تقييم جديد ({$type}) من {$name}",
            data: ['feedback_id' => $feedbackId, 'name' => $name, 'type' => $type],
            priority: AdminNotification::PRIORITY_MEDIUM,
            actionUrl: "/admin/feedback/{$feedbackId}"
        );
    }

    /**
     * Notify about new platform rating.
     */
    public function notifyNewPlatformRating(int $ratingId, int $rating, ?string $comment = null): AdminNotification
    {
        return $this->create(
            type: AdminNotification::TYPE_NEW_PLATFORM_RATING,
            category: AdminNotification::CATEGORY_ENGAGEMENT,
            title: 'تقييم للمنصة',
            message: "تم تقييم المنصة بـ {$rating} نجوم",
            data: ['rating_id' => $ratingId, 'rating' => $rating, 'comment' => $comment],
            priority: AdminNotification::PRIORITY_LOW,
            actionUrl: '/admin/ratings'
        );
    }

    // =========================================================================
    // Library Notifications
    // =========================================================================

    /**
     * Notify about new book rating.
     */
    public function notifyNewBookRating(int $bookId, string $bookTitle, int $rating): AdminNotification
    {
        return $this->create(
            type: AdminNotification::TYPE_NEW_BOOK_RATING,
            category: AdminNotification::CATEGORY_LIBRARY,
            title: 'تقييم كتاب',
            message: "تم تقييم كتاب '{$bookTitle}' بـ {$rating} نجوم",
            data: ['book_id' => $bookId, 'book_title' => $bookTitle, 'rating' => $rating],
            priority: AdminNotification::PRIORITY_LOW,
            actionUrl: "/admin/library/books/{$bookId}"
        );
    }
}
