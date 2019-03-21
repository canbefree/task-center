<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/24
 * Time: 14:57
 */

namespace App\Model;


use App\Lib\Log;
use function GuzzleHttp\Promise\task;

class TaskAction
{
    static $_list;

    public function register($task_info)
    {
        self::$_list[$task_info['event']][$task_info['task_id']] = $task_info;
    }


    public  function notify($event_info)
    {
        $ret = [];
        $event_type = $event_info['event'];
        if(isset(self::$_list[$event_type])){
            foreach (self::$_list[$event_type] as $task_info) {
                //依次执行任务
                $class = new $task_info['class']($task_info,$event_info);
                $ret[$task_info['task_id']] = $class->run();
            }
        }else{
            Log::write('event:'.json_encode($event_info)." 没有配置任务");
        }

    }
}