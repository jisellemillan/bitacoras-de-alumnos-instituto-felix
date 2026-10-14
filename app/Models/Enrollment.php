<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $table = 'inscripciones';

    protected $fillable = ['student_id', 'group_id', 'school_cycle_id', 'is_current'];

    protected function casts(): array
    {
        return ['is_current' => 'boolean'];
    }

    public function student(): BelongsTo    { return $this->belongsTo(Student::class); }
    public function group(): BelongsTo      { return $this->belongsTo(Group::class); }
    public function schoolCycle(): BelongsTo { return $this->belongsTo(SchoolCycle::class); }
}
