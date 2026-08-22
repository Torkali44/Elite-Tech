<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Idea extends Model
{
    use SoftDeletes;
    // HIGH-04: 'status', 'admin_notes', and 'likes_count' are intentionally excluded from
    // $fillable. They are system/admin-controlled and must only be set via direct property
    // assignment or forceFill() in controlled code paths — never via user-supplied mass assignment.
    protected $fillable = [
        'user_id', 'forked_from', 'title', 'category', 'description',
        'feasibility', 'technologies', 'budget',
    ];

    protected $casts = [
        'technologies' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(IdeaComment::class)->latest();
    }

    public function parent()
    {
        return $this->belongsTo(Idea::class, 'forked_from');
    }

    public function implementRequests()
    {
        return $this->hasMany(ImplementRequest::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'idea_favorites')->withTimestamps();
    }

    public function isFavoritedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if (array_key_exists('is_favorited', $this->attributes)) {
            return (bool) $this->attributes['is_favorited'];
        }

        return $this->favoritedBy()->where('user_id', $user->id)->exists();
    }

    public function getLocalizedTitleAttribute()
    {
        if (app()->getLocale() === 'en' && !empty($this->title_en)) {
            return $this->title_en;
        }
        return $this->title;
    }

    public function getLocalizedDescriptionAttribute()
    {
        if (app()->getLocale() === 'en' && !empty($this->description_en)) {
            return $this->description_en;
        }
        return $this->description;
    }

    public function categoryIcon(): string
    {
        return match (true) {
            str_contains($this->category, 'ذكاء') || str_contains($this->category, 'AI') => '🧠',
            str_contains($this->category, 'أمن') || str_contains($this->category, 'Security') => '🛡',
            str_contains($this->category, 'جوال') || str_contains($this->category, 'موبايل') => '📱',
            str_contains($this->category, 'Blockchain') || str_contains($this->category, 'بلوك') => '🔗',
            str_contains($this->category, 'ويب') => '⚡',
            default => '💡',
        };
    }

    public function shortDesc(int $len = 140): string
    {
        $desc = $this->localized_description;
        return mb_strlen($desc) > $len
            ? mb_substr($desc, 0, $len).'…'
            : $desc;
    }
}
