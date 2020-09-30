<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


class PurchaseHistory extends Model
{
    protected $fillable = [
        'id_user', 'id_services', 'status'
    ];

}
