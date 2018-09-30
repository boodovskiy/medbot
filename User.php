<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable = ['role_id', 'name', 'surname', 'email', 'password', 'speciality', 'job', 'remember_token'];
    protected $hidden = ['password', 'remember_token'];

    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    public function avatar()
    {
        return $this->morphOne('App\Avatar', 'imageable');
    }

    public function patient()
    {
        return $this->hasMany('App\Patient');
    }

    public function appointment()
    {
        return $this->hasManyThrough('App\Appointment', 'App\Patient');
    }

    public function isAdmin()
    {
        if (count($this->role)) {
            if ($this->role->name == 'admin') {
                return true;
            }
        }
        return false;
    }

    public function isDoc()
    {
        if (count($this->role)) {
            if ($this->role->name == 'doc') {
                return true;
            }
        }
        return false;
    }

    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            $this->attributes['password'] = bcrypt($password);
        }
    }
}
