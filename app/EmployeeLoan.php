<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeLoan extends Model
{
    public function users() {
        return $this->belongsToMany('App\User');
    }
}
