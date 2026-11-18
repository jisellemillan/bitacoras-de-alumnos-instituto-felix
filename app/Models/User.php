<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'usuarios';

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'section_permissions',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'password'            => 'hashed',
            'role'                => UserRole::class,
            'section_permissions' => 'array',
            'is_active'           => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'grupo_usuario')
            ->withPivot('assignment_type')
            ->withTimestamps();
    }

    public function observations(): HasMany
    {
        return $this->hasMany(Observation::class, 'created_by');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'created_by');
    }

    public function accessRequests(): HasMany
    {
        return $this->hasMany(AccessRequest::class, 'requester_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function passwordRequests(): HasMany
    {
        return $this->hasMany(PasswordRequest::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    public function isPsicopedagogia(): bool
    {
        return $this->role === UserRole::Psicopedagogia;
    }

    public function isAdminTecnico(): bool
    {
        return $this->role === UserRole::AdminTecnico;
    }

    public function canAccessPsychContent(): bool
    {
        return $this->role->canAccessPsychContent();
    }

    public function hasSectionPermission(string $section): bool
    {
        if ($this->isAdminTecnico()) {
            // Devuelve 'true' (acceso permitido) solo si la sección NO está en este arreglo de restringidas
            return !in_array($section, ['alumnos', 'acuerdos', 'calendario']);
        }

        $perms = $this->section_permissions ?? [];
        return in_array($section, $perms);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, UserRole $role)
    {
        return $query->where('role', $role->value);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getRoleLabelAttribute(): string
    {
        return $this->role->label();
    }
}
