<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['patient_id', 'text', 'responseType', 'event_id', 'status', 'marker', 'success', 'fail'];

    public function patient()
    {
        return $this->belongsTo('App\Patient');
    }
}
