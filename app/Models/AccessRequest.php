<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessRequest extends Model
{
    protected $table = 'solicitudes_acceso';

    protected $fillable = [
        'requester_id', 'student_id', 'group_id',
        'reason', 'status', 'reviewed_by', 'reviewed_at', 'review_notes',
    ];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    public function requester(): BelongsTo  { return $this->belongsTo(User::class, 'requester_id'); }
    public function student(): BelongsTo    { return $this->belongsTo(Student::class); }
    public function group(): BelongsTo      { return $this->belongsTo(Group::class); }
    public function reviewer(): BelongsTo   { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function grant(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AccessGrant::class);
    }

    public function scopePending($query)    { return $query->where('status', 'pendiente'); }
    public function scopeApproved($query)   { return $query->where('status', 'aprobado'); }
}
