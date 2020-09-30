<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class Users extends Model implements AuthenticatableContract, AuthorizableContract

{
    use Authenticatable, Authorizable;

    protected $fillable = [
        'email', 'first_name', 'last_name', 'phone', 'sex', 'birthdate', 'password',
    ];

    protected $hidden = [
        'password',
    ];

}
