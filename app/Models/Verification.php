<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $fillable = [
        'user_id', 'doc_type', 'purpose', 'id_front', 'id_back', 'selfie',
        'status', 'rejection_reason', 'admin_notes', 'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purposeLabel(): string
    {
        return [
            'publish_idea' => 'نشر أفكار',
            'implement' => 'رغبة في التنفيذ',
            'jobs_forum' => 'منتدى التوظيف',
            'reevaluation' => 'إعادة تقييم (تعديل حسّاس)',
        ][$this->purpose] ?? ($this->purpose ?: 'عام');
    }
}
