<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessGrant extends Model
{
    protected $table = 'permisos_acceso';

    protected $fillable = [
        'access_request_id', 'user_id', 'student_id', 'group_id', 'expires_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_active'  => 'boolean',
        ];
    }

    public function accessRequest(): BelongsTo { return $this->belongsTo(AccessRequest::class); }
    public function user(): BelongsTo          { return $this->belongsTo(User::class); }
    public function student(): BelongsTo       { return $this->belongsTo(Student::class); }
    public function group(): BelongsTo         { return $this->belongsTo(Group::class); }

    public function isValid(): bool
    {
        return $this->is_active && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}
