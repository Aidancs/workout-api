<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Workout extends Model
{
    protected $fillable = ['workout_description','workout_type','count_for_work','prescribed','results','user_id'];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
