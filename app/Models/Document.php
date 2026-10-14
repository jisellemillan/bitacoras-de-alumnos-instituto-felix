<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $table = 'documentos';

    protected $fillable = [
        'student_file_id', 'student_id', 'uploaded_by',
        'title', 'description', 'file_path', 'mime_type', 'file_size', 'category',
    ];

    public function studentFile(): BelongsTo { return $this->belongsTo(StudentFile::class); }
    public function student(): BelongsTo     { return $this->belongsTo(Student::class); }
    public function uploader(): BelongsTo    { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
