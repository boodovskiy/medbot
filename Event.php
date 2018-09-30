<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['appointment_id', 'eventtype_id', 'diff'];

    public function appointment()
    {
        return $this->belongsTo('App\Appointment');
    }

    public function eventtype()
    {
        return $this->belongsTo('App\Eventtype');
    }
}
