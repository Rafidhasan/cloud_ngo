<?php

namespace App;

use App\User;

use Illuminate\Database\Eloquent\Model;

class BusinessLoan extends Model
{
    public function users() {
        return $this->belongsToMany('App\User');
    }
}
