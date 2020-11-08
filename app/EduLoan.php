<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EduLoan extends Model
{
    public function users() {
        return $this->belongsToMany('App\User');
    }
}
