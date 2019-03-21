<?php
/**
 * 特殊
 * 完成任务进度 (不包括当前的任务完成情况)
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/24
 * Time: 16:05
 */


namespace App\Model\Task;


use App\Model\IGameEvent;
use App\Model\Task;
use Faker\Factory;

class Recharge extends Task implements IGameEvent {
    public static function attributes()
    {
        return ['event','order_id','prop_type','prop_number','uid','source_type','timestamp'];
    }

    public static function createModal()
    {
        $uid = rand(1000, 9999);
        $facker = Factory::create();
        return [
            'event'=> 'recharge',
            'order_id' => $facker->uuid,
            'prop_type' => rand(0,6),
            'uid' => $uid,
            'source_type' => rand(0,4),
            'source_number' => rand(8,12),
            'timestamp' => time(),
        ];
    }
}