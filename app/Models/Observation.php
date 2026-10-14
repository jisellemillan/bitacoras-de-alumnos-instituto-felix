<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Observation extends Model
{
    use SoftDeletes;

    protected $table = 'observaciones';

    protected $fillable = [
        'student_file_id',
        'student_id',
        'school_cycle_id',
        'created_by',
        'observation_date',
        'grade_group',
        'observations',
        'required_prefectura',
        'required_parent_interview',
        'required_channeling',
        'agreements_text',
        'next_appointment_date',
        'required_presence',
        'attendees',
        'elaborated_by_name',
        'is_draft',
        'lock_version',
    ];

    protected function casts(): array
    {
        return [
            'observation_date'         => 'date',
            'next_appointment_date'    => 'date',
            'required_prefectura'      => 'boolean',
            'required_parent_interview' => 'boolean',
            'required_channeling'      => 'boolean',
            'is_draft'                 => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function studentFile(): BelongsTo
    {
        return $this->belongsTo(StudentFile::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolCycle(): BelongsTo
    {
        return $this->belongsTo(SchoolCycle::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(ObservationSignature::class);
    }

    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'id', 'observation_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('is_draft', false);
    }

    public function scopeByCycle($query, int $cycleId)
    {
        return $query->where('school_cycle_id', $cycleId);
    }
}
