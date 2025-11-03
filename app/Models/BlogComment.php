<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_post_id',
        'user_id',
        'parent_id',
        'guest_name',
        'guest_email',
        'guest_website',
        'content',
        'status',
        'ip_address',
        'user_agent',
        'moderated_by',
        'moderated_at',
        'moderation_reason',
        'like_count',
        'dislike_count',
    ];

    protected $casts = [
        'moderated_at' => 'datetime',
        'like_count' => 'integer',
        'dislike_count' => 'integer',
    ];

    // Relationships
    public function post(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(BlogComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'parent_id')->where('status', 'approved');
    }

    public function allReplies(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'parent_id');
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeSpam($query)
    {
        return $query->where('status', 'spam');
    }

    public function scopeParentComments($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeReplies($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByPost($query, $postId)
    {
        return $query->where('blog_post_id', $postId);
    }

    // Accessors
    public function getAuthorNameAttribute()
    {
        return $this->user ? $this->user->name : $this->guest_name;
    }

    public function getAuthorEmailAttribute()
    {
        return $this->user ? $this->user->email : $this->guest_email;
    }

    public function getIsGuestAttribute()
    {
        return is_null($this->user_id);
    }

    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    public function getIsModeratedAttribute()
    {
        return ! is_null($this->moderated_at);
    }

    public function getCanReplyAttribute()
    {
        return $this->status === 'approved' && $this->post->allow_comments;
    }

    public function getDepthAttribute()
    {
        $depth = 0;
        $parent = $this->parent;

        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }

        return $depth;
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Methods
    public function approve($moderatorId = null)
    {
        $this->status = 'approved';
        $this->moderated_by = $moderatorId;
        $this->moderated_at = now();
        $this->save();

        // Update post comment count
        $this->post->updateCommentCount();
    }

    public function reject($moderatorId = null, $reason = null)
    {
        $this->status = 'rejected';
        $this->moderated_by = $moderatorId;
        $this->moderated_at = now();
        $this->moderation_reason = $reason;
        $this->save();

        // Update post comment count
        $this->post->updateCommentCount();
    }

    public function markAsSpam($moderatorId = null, $reason = null)
    {
        $this->status = 'spam';
        $this->moderated_by = $moderatorId;
        $this->moderated_at = now();
        $this->moderation_reason = $reason;
        $this->save();

        // Update post comment count
        $this->post->updateCommentCount();
    }

    public function incrementLikes()
    {
        $this->increment('like_count');
    }

    public function incrementDislikes()
    {
        $this->increment('dislike_count');
    }

    public function getNetLikes()
    {
        return $this->like_count - $this->dislike_count;
    }

    // Check if comment can be edited by user
    public function canBeEditedBy($user)
    {
        if (! $user) {
            return false;
        }

        // Admin/moderator can edit any comment
        if ($user->hasRole(['admin', 'superadmin'])) {
            return true;
        }

        // User can edit own comment within 30 minutes
        if ($this->user_id === $user->id) {
            return $this->created_at->diffInMinutes(now()) <= 30;
        }

        return false;
    }

    // Check if comment can be deleted by user
    public function canBeDeletedBy($user)
    {
        if (! $user) {
            return false;
        }

        // Admin/moderator can delete any comment
        if ($user->hasRole(['admin', 'superadmin'])) {
            return true;
        }

        // User can delete own comment
        return $this->user_id === $user->id;
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::created(function ($comment) {
            // Auto-approve comments from trusted users
            if ($comment->user && $comment->user->hasRole(['admin', 'editor'])) {
                $comment->approve();
            }
        });

        static::deleted(function ($comment) {
            // Update post comment count when comment is deleted
            $comment->post->updateCommentCount();
        });
    }
}
