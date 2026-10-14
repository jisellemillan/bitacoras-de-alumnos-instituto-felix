<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'alumnos';

    protected $fillable = [
        'first_name',
        'last_name',
        'birthdate',
        'sex',
        'curp',
        'photo_path',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function studentFile(): HasOne
    {
        return $this->hasOne(StudentFile::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'inscripciones')
            ->withPivot('is_current', 'school_cycle_id')
            ->withTimestamps();
    }

    public function currentEnrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class)->where('is_current', true)->latestOfMany();
    }

    public function observations(): HasMany
    {
        return $this->hasMany(Observation::class);
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('curp', 'like', "%{$term}%");
        });
    }

    public function scopeInGrade($query, int $grade)
    {
        return $query->whereHas('enrollments', function ($q) use ($grade) {
            $q->where('is_current', true)
              ->whereHas('group', fn ($g) => $g->where('grade', $grade));
        });
    }

    public function scopeInGroup($query, int $groupId)
    {
        return $query->whereHas('enrollments', function ($q) use ($groupId) {
            $q->where('is_current', true)->where('group_id', $groupId);
        });
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birthdate?->age;
    }

    public function getCurrentGroupLabelAttribute(): string
    {
        return $this->currentEnrollment?->group?->label ?? '—';
    }
}
