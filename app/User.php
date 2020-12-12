<?php

namespace App;

use Auth;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'image', 'mobile_number','fathers_name', 'mothers_name', 'date_of_birth', 'address', 'thana', 'NID_or_birth_certificate_number', 'nid_image', 'nominee_name', 'nominee_nid', 'refer_account_number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function roles() {
        return $this->belongsToMany('App\Role');
    }

    public function checkAdmin() {
        return $this->roles->flatten()->pluck('name')->first();
    }

    public function checkRole() {
        return $this->roles->flatten()->pluck('id')->first();
    }

    public function savings() {
        return $this->hasMany('App\SavingAcount');
    }

    public function businessLoans() {
        return $this->hasMany('App\BusinessLoan');
    }

    public function employeeLoans() {
        return $this->hasMany('App\EmployeeLoan');
    }

    public function eduLoans() {
        return $this->hasMany('App\EduLoan');
    }

    public function checkTotal() {
        return $this->savings->flatten()->pluck('total')->last();
    }

    public function hasSavings() {
        return $this->savings->flatten()->pluck('user_id')->last();
    }

    public function savingAmount() {
        return $this->savings->flatten()->pluck('total')->last();
    }

    public function businessLoanId() {
        return $this->businessLoans;
    }
}
