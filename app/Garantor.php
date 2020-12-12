<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Garantor extends Model
{
    protected $fillable = [
        'loan_id', 'loan_method', 'g_name', 'g_mobile_number', 'g_approved'
    ];

    public function users() {
        return $this->belongsToMany('App\User');
    }
}
