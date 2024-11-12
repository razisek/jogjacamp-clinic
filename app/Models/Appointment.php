<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $guarded = ['id'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function diagnose()
    {
        return $this->belongsTo(Diagnose::class);
    }

    public function checkupProgress()
    {
        return $this->hasMany(CheckupProgress::class);
    }
}
