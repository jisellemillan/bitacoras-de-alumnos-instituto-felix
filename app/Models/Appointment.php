<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;

    protected $table = 'citas';

    protected $fillable = [
        'student_file_id', 'student_id', 'created_by', 'observation_id',
        'title', 'starts_at', 'ends_at', 'notes', 'location', 'status',
        'required_attendees',
    ];

    protected function casts(): array
    {
        return [
            'starts_at'          => 'datetime',
            'ends_at'            => 'datetime',
            'required_attendees' => 'array',
        ];
    }

    public function studentFile(): BelongsTo  { return $this->belongsTo(StudentFile::class); }
    public function student(): BelongsTo      { return $this->belongsTo(Student::class); }
    public function author(): BelongsTo       { return $this->belongsTo(User::class, 'created_by'); }
    public function observation(): BelongsTo  { return $this->belongsTo(Observation::class); }

    public function scopeUpcoming($query)     { return $query->where('starts_at', '>=', now())->orderBy('starts_at'); }
    public function scopeScheduled($query)    { return $query->where('status', 'programada'); }
}
