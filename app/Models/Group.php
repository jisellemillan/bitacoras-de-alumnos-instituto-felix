<?php

namespace App\Models;

use App\Enums\Grade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $table = 'grupos';

    protected $fillable = ['school_cycle_id', 'grade', 'section', 'name', 'description'];

    protected function casts(): array
    {
        return [
            'grade' => Grade::class,
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function schoolCycle(): BelongsTo
    {
        return $this->belongsTo(SchoolCycle::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'grupo_usuario')
            ->withPivot('assignment_type')
            ->withTimestamps();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'inscripciones')
            ->withPivot('is_current', 'school_cycle_id')
            ->withTimestamps();
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getLabelAttribute(): string
    {
        if (!empty($this->name)) {
            return "{$this->grade->label()}{$this->section} ({$this->name})";
        }
        return "{$this->grade->label()}{$this->section}";
    }

    public function getFullLabelAttribute(): string
    {
        $cycleName = $this->schoolCycle?->name ?? '';
        return $cycleName ? "{$this->label} — {$cycleName}" : $this->label;
    }
}
