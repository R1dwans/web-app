<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'name',
        'nip',
        'position',
        'title',
        'email',
        'phone',
        'photo',
        'education',
        'expertise',
        'bio',
        'program_study_id',
        'staff_type',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function programStudy()
    {
        return $this->belongsTo(ProgramStudy::class);
    }
}
