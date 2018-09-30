<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = ['patient_id', 'working', 'start', 'periodicity', 'length', 'text'];

    public function patient()
    {
        return $this->belongsTo('App\Patient');
    }

    public function event()
    {
        return $this->hasMany('App\Event');
    }

    public function reminder()
    {
        return $this->hasMany('App\Reminder');
    }

    public function setStartAttribute($start)
    {
        $this->attributes['start'] = Carbon::createFromFormat('d-m-Y', $start)->format('Y-m-d');
    }

    public function getStartAttribute($start)
    {
        return Carbon::createFromFormat('Y-m-d', $start)->format('d-m-Y');
    }

}
