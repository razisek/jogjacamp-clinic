<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diagnose extends Model
{
    protected $guarded = ['id'];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
