<?php

namespace App\Models;

use App\Enums\AgreementStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agreement extends Model
{
    use SoftDeletes;

    protected $table = 'acuerdos';

    protected $fillable = [
        'student_file_id', 'student_id', 'observation_id', 'interview_id',
        'created_by', 'responsible_user_id',
        'description', 'responsible_name', 'due_date', 'status',
    ];

    protected function casts(): array
    {
        return [
            'status'   => AgreementStatus::class,
            'due_date' => 'date',
        ];
    }

    public function studentFile(): BelongsTo     { return $this->belongsTo(StudentFile::class); }
    public function student(): BelongsTo         { return $this->belongsTo(Student::class); }
    public function observation(): BelongsTo     { return $this->belongsTo(Observation::class); }
    public function interview(): BelongsTo       { return $this->belongsTo(Interview::class); }
    public function author(): BelongsTo          { return $this->belongsTo(User::class, 'created_by'); }
    public function responsible(): BelongsTo     { return $this->belongsTo(User::class, 'responsible_user_id'); }

    public function scopePending($query)    { return $query->where('status', AgreementStatus::Pendiente->value); }
    public function scopeOverdue($query)    { return $query->where('due_date', '<', now())->pending(); }
}
