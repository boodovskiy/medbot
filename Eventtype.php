<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Eventtype extends Model
{
    protected $fillable = ['name', 'actionName', 'scope_id'];

    public function scope()
    {
        return $this->belongsTo('App\Scope');
    }

    public function event()
    {
        return $this->hasMany('App\Event');
    }
}
