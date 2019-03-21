<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/24
 * Time: 17:00
 */

namespace App\Model;


use App\Lib\Config;
use App\Model\Mtable\PlayerTask;

class Life extends TaskParam
{


    /**
     * @param $taskModel PlayerTask
     * @return bool
     * @throws \Exception
     */
    public function handle(&$taskModel)
    {

        $now = time();
        if ($now <= $this->start_time || $now > $this->end_time) {
            // 注册删除任务的事件

            throw new \Exception("任务已过期");
        }
    }


}