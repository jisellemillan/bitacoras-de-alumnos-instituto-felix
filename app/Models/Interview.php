<?php

namespace App\Models;

use App\Enums\InterviewModality;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use SoftDeletes;

    protected $table = 'entrevistas';

    protected $fillable = [
        'student_file_id', 'student_id', 'school_cycle_id', 'created_by',
        'interview_date', 'grade_group', 'modality',
        'purpose_and_objective', 'comments',
        'attendees', 'conducted_by_name',
        'is_draft', 'lock_version',
    ];

    protected function casts(): array
    {
        return [
            'interview_date' => 'date',
            'modality'       => InterviewModality::class,
            'is_draft'       => 'boolean',
        ];
    }

    public function studentFile(): BelongsTo { return $this->belongsTo(StudentFile::class); }
    public function student(): BelongsTo     { return $this->belongsTo(Student::class); }
    public function schoolCycle(): BelongsTo { return $this->belongsTo(SchoolCycle::class); }
    public function author(): BelongsTo      { return $this->belongsTo(User::class, 'created_by'); }
    public function agreements(): HasMany    { return $this->hasMany(Agreement::class); }

    // Accesores para compatibilidad con vistas y reportes
    public function getIntervieweeNameAttribute(): ?string
    {
        return $this->attendees;
    }

    public function getReasonAttribute(): ?string
    {
        return $this->purpose_and_objective;
    }
}
