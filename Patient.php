<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Patient extends Model
{
    use Notifiable;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = ['name', 'surname', 'email', 'phone', 'birth', 'death', 'user_id', 'gender_id', 'lastReception', 'additional'];


    public function doc()
    {
        return $this->belongsTo('App\User');
    }

    public function gender()
    {
        return $this->belongsTo('App\Gender');
    }

    public function promocode()
    {
        return $this->hasOne('App\Promocode');
    }

    public function social()
    {
        return $this->hasMany('App\Social');
    }

    public function appointment()
    {
        return $this->hasMany('App\Appointment');
    }

    public function schedule()
    {
        return $this->hasMany('App\Schedule');
    }

    public function message()
    {
        return $this->hasMany('App\Message');
    }

    public function setBirthAttribute($birth)
    {
        $this->attributes['birth'] = Carbon::createFromFormat('d-m-Y', $birth)->format('Y-m-d');
    }

    public function getBirthAttribute($birth)
    {
        return Carbon::createFromFormat('Y-m-d', $birth)->format('d-m-Y');
    }

    public function setLastReceptionAttribute($date)
    {
        $this->attributes['lastReception'] = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
    }

    public function getLastReceptionAttribute($date)
    {
        if ($date != '0000-00-00') {
            return Carbon::createFromFormat('Y-m-d', $date)->format('d-m-Y');
        } else {
            return '-';
       }
    }

}
