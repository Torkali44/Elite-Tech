<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'title', 'bio', 'portfolio_url',
        'location', 'avatar', 'available_for_hire',
    ];


    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'roles' => 'array',
        'available_for_hire' => 'boolean',
        'wants_jobs_forum' => 'boolean',
        'show_in_jobs_forum' => 'boolean',
        'is_suspended' => 'boolean',
    ];

    public function ideas()
    {
        return $this->hasMany(Idea::class);
    }

    public function careerTracks()
    {
        return $this->hasMany(CareerTrack::class);
    }

    public function cv()
    {
        return $this->hasOne(Cv::class);
    }

    public function verifications()
    {
        return $this->hasMany(Verification::class);
    }

    public function latestVerification()
    {
        return $this->hasOne(Verification::class)->latestOfMany();
    }

    public function implementRequests()
    {
        return $this->hasMany(ImplementRequest::class);
    }

    public function favoriteIdeas()
    {
        return $this->belongsToMany(Idea::class, 'idea_favorites')->withTimestamps();
    }

    public function hasRole($role): bool
    {
        $roles = $this->roles;
        if (! is_array($roles) || $roles === []) {
            $roles = $this->role ? [$this->role] : [];
        }

        return in_array($role, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    /**
     * Get all admin users.
     */
    public static function getAdmins()
    {
        return self::where('role', 'admin')->orWhereJsonContains('roles', 'admin')->get();
    }

    public function isKycApproved(): bool
    {
        return ($this->kyc_status ?? 'none') === 'approved' && ! $this->is_suspended;
    }

    public function roleLabel(): string
    {
        return [
            'idea_owner' => 'صاحب فكرة',
            'idea_seeker' => 'باحث عن فكرة',
            'developer' => 'باحث عن عمل',
            'admin' => 'إدارة',
        ][$this->role] ?? (string) $this->role;
    }

    /**
     * Withdraw Verified badge, hide from jobs forum, queue admin re-review.
     */
    public function flagForKycRereview(string $reason): bool
    {
        $eligible = in_array($this->kyc_status, ['approved', 'rejected', 'suspended'], true)
            || $this->verifications()->exists();

        if (! $eligible) {
            return false;
        }

        $this->forceFill([
            'kyc_status' => 'pending',
            'show_in_jobs_forum' => false,
            'rejection_reason' => null,
        ])->save();

        Verification::create([
            'user_id' => $this->id,
            'doc_type' => 'reevaluation',
            'purpose' => 'reevaluation',
            'status' => 'pending',
            'admin_notes' => $reason,
        ]);

        return true;
    }
}
