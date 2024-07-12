<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubjectStudent extends Model
{
    use HasFactory;
    public $timestamps = true;
    public $fillable = ['student_id', 'subject_id'];


    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subject_id');
    }
}
