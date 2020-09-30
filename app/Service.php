<?php

namespace App;


use Illuminate\Database\Eloquent\Model;


class Service extends Model
{
    protected $fillable = [
        'title', 'description', 'status', 'created_at', 'updated_at'
    ];

}
