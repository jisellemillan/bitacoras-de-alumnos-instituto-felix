<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordRequest extends Model
{
    protected $table = 'solicitudes_contrasena';

    protected $fillable = [
        'email',
        'user_id',
        'estado',
        'atendida_por',
        'atendida_en',
    ];

    protected function casts(): array
    {
        return [
            'atendida_en' => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function attendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atendida_por')->withTrashed();
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function isAtendida(): bool
    {
        return $this->estado === 'atendida';
    }
}
