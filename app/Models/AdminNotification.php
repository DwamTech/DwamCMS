<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    protected $fillable = [
        'type',
        'category',
        'title',
        'message',
        'body',  // للتوافق مع الهيكل القديم
        'data',
        'priority',
        'is_read',
        'read_at',
        'action_url',
        'triggered_by',
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // =========================================================================
    // Categories
    // =========================================================================

    const CATEGORY_SUPPORT = 'support';
    const CATEGORY_SYSTEM = 'system';
    const CATEGORY_USERS = 'users';
    const CATEGORY_CONTENT = 'content';
    const CATEGORY_ENGAGEMENT = 'engagement';
    const CATEGORY_LIBRARY = 'library';

    // =========================================================================
    // Types
    // =========================================================================

    // Support
    const TYPE_NEW_INDIVIDUAL_SUPPORT = 'new_individual_support';
    const TYPE_NEW_INSTITUTIONAL_SUPPORT = 'new_institutional_support';
    const TYPE_SUPPORT_STATUS_CHANGED = 'support_status_changed';
    const TYPE_DOCUMENT_UPLOADED = 'document_uploaded';

    // System
    const TYPE_BACKUP_SUCCESS = 'backup_success';
    const TYPE_BACKUP_FAILED = 'backup_failed';
    const TYPE_BACKUP_RESTORED = 'backup_restored';
    const TYPE_SYSTEM_HEALTH_WARNING = 'system_health_warning';

    // Users
    const TYPE_NEW_USER_REGISTERED = 'new_user_registered';
    const TYPE_USER_ROLE_CHANGED = 'user_role_changed';

    // Content
    const TYPE_NEW_ARTICLE = 'new_article';
    const TYPE_ARTICLE_UPDATED = 'article_updated';
    const TYPE_NEW_VISUAL = 'new_visual';

    // Engagement
    const TYPE_NEW_FEEDBACK = 'new_feedback';
    const TYPE_NEW_PLATFORM_RATING = 'new_platform_rating';

    // Library
    const TYPE_NEW_BOOK_RATING = 'new_book_rating';
    const TYPE_DOCUMENT_DOWNLOADED = 'document_downloaded';

    // =========================================================================
    // Priorities
    // =========================================================================

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';

    // =========================================================================
    // Relationships
    // =========================================================================

    /**
     * Get the user who triggered this notification.
     */
    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    /**
     * Scope to get unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by priority.
     */
    public function scopePriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to filter by type.
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get high priority notifications.
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', self::PRIORITY_HIGH);
    }

    // =========================================================================
    // Methods
    // =========================================================================

    /**
     * Mark notification as read.
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(): bool
    {
        return $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Get icon based on category.
     */
    public function getIconAttribute(): string
    {
        return match ($this->category) {
            self::CATEGORY_SUPPORT => '🆘',
            self::CATEGORY_SYSTEM => '⚙️',
            self::CATEGORY_USERS => '👥',
            self::CATEGORY_CONTENT => '📝',
            self::CATEGORY_ENGAGEMENT => '💬',
            self::CATEGORY_LIBRARY => '📚',
            default => '🔔',
        };
    }

    /**
     * Get color based on category.
     */
    public function getColorAttribute(): string
    {
        return match ($this->category) {
            self::CATEGORY_SUPPORT => '#ef4444',    // red
            self::CATEGORY_SYSTEM => '#f97316',     // orange
            self::CATEGORY_USERS => '#3b82f6',      // blue
            self::CATEGORY_CONTENT => '#22c55e',    // green
            self::CATEGORY_ENGAGEMENT => '#8b5cf6', // purple
            self::CATEGORY_LIBRARY => '#eab308',    // yellow
            default => '#6b7280',                   // gray
        };
    }

    /**
     * Get priority color.
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_HIGH => '#ef4444',   // red
            self::PRIORITY_MEDIUM => '#f97316', // orange
            self::PRIORITY_LOW => '#22c55e',    // green
            default => '#6b7280',               // gray
        };
    }

    /**
     * Get all available categories.
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_SUPPORT,
            self::CATEGORY_SYSTEM,
            self::CATEGORY_USERS,
            self::CATEGORY_CONTENT,
            self::CATEGORY_ENGAGEMENT,
            self::CATEGORY_LIBRARY,
        ];
    }

    /**
     * Get all available priorities.
     */
    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_LOW,
            self::PRIORITY_MEDIUM,
            self::PRIORITY_HIGH,
        ];
    }
}
