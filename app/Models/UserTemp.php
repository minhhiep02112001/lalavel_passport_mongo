<?php

namespace App\Models;

class UserTemp extends AbstractModel
{
    protected $table = 'user_temp';
    protected $casts = [
        'integer' => ['_id'],
    ];

    protected $idAutoIncrement = 1;
    protected $primaryKey = '_id';
}
