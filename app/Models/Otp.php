<?php

namespace App\Models;


class Otp extends AbstractModel
{
    protected $table = 'otp';
    protected $casts = [
        'integer' => ['_id', 'otp', 'count_send'],
        'unixtime' => 'expired_time',
    ];

    protected $idAutoIncrement = 1;
    protected $primaryKey = '_id';

    function validate_otp($id, $action_type, $otp)
    {
        $item = $this->all(['otp' => (int)$otp, 'user_id' => (int)$id, 'action_type' => $action_type])->first();
        if (empty($item)){
            return array('status' => false, 'message' => 'Otp fails !!!');
        }
        if ($item['expired_time'] > strtotime(now())){
            return array('status' => false, 'message' => 'Otp authentication code expired !!!');
        }
    }
}
