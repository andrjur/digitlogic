<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


class ServiceHistory extends Model
{
    protected $fillable = [
        'id_user', 'name_service', 'value', 'result',
    ];

}
