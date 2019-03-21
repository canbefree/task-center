<?php
/**
 *
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/22
 * Time: 11:24
 */

namespace App\Model\Factory;


use App\Lib\Config;
use App\Model\IGameEvent;

class Message
{
    static $iNum = 0;

    public function produce($num)
    {
        $ret = [];
        $i = 0;
//        $event_list = ['board_end','share','recharge','cpt_record'];
        $event_list = ['board_end'];

        $task_list = Config::load('params')['task'];

        while ($i < $num) {
            $event = $event_list[array_rand($event_list,1)];

            /** @var IGameEvent $class */
            $class = $task_list[$event];
            $data = $class::createModal();
            self::$iNum++;
            $ret[] = \json_encode($data);
            $i++;
        }
        yield  $ret;
    }
}