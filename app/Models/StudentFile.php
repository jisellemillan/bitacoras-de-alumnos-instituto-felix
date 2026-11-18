<?php

namespace App\Models;

use App\Enums\IntakeRoute;
use App\Enums\NeedArea;
use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentFile extends Model
{
    use SoftDeletes;

    protected $table = 'expedientes_alumnos';

    protected $fillable = [
        'student_id',
        'intake_route',
        'file_type',
        'status',
        'need_areas',
        'primary_diagnosis',
        'strengths',
        'support_areas',
        'external_specialist',
        'treatment_medication',
        'opened_at',
        'closed_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'intake_route'         => IntakeRoute::class,
            'status'               => StudentStatus::class,
            'need_areas'           => 'array',
            // Encrypted fields (sensitive clinical data)
            'primary_diagnosis'    => 'encrypted',
            'external_specialist'  => 'encrypted',
            'treatment_medication' => 'encrypted',
            'opened_at'            => 'date',
            'closed_at'            => 'date',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function observations(): HasMany
    {
        return $this->hasMany(Observation::class)->latest('observation_date');
    }

    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class)->latest('interview_date');
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class)->latest();
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->orderBy('starts_at');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class)->latest();
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', StudentStatus::Activo->value);
    }

    public function scopeByRoute($query, IntakeRoute $route)
    {
        return $query->where('intake_route', $route->value);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function getNeedAreaLabels(): array
    {
        if (empty($this->need_areas)) {
            return [];
        }
        return collect($this->need_areas)
            ->map(function ($v) {
                if ($v instanceof NeedArea) {
                    return $v->label();
                }
                return NeedArea::tryFrom($v)?->label() ?? (string) $v;
            })
            ->all();
    }
}
