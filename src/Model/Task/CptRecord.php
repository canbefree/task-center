<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/11/5
 * Time: 15:21
 */

namespace App\Model\Task;


use App\Model\IGameEvent;

class CptRecord implements IGameEvent
{

    public static function attributes()
    {
        return ['event', 'uid', 'cptId', 'timestamp', 'playTimes', 'rewardTimes', 'championTimes'];
    }

    public static function createModal()
    {
        $uid = rand(1000, 9999);
        return [
            'event' => 'cpt_record',
            'uid' => $uid,
            'timestamp' => time(),
            'playerTimes' => rand(0, 9),
            'rewardTimes' => rand(0, 9),
            'championTimes' => rand(0, 9),
        ];
    }
}