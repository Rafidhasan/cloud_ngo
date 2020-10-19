<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SavingAcount extends Model
{
    protected $fillable = [
        'mobile_number',
        'method',
        'tracking_number',
        'amount',
        'user_id',
        'total',
        'approved'
    ];


    public function users() {
        return $this->belongsToMany('App\User');
    }
}
