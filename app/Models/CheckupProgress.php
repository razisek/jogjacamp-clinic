<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckupProgress extends Model
{
    protected $guarded = ['id'];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
