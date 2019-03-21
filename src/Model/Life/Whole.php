<?php
/**
 * 一次性的
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/24
 * Time: 17:00
 */

namespace App\Model\Life;


use App\Model\Life;
use App\Model\Mtable\PlayerTask;
use App\Model\Task;

class Whole extends Life {
    /**
     * @param $taskModel PlayerTask
     * @return bool
     * @throws \Exception
     */
    public function handle(&$taskModel)
    {

        if($taskModel->status != Task::STATUS_TASK_ING){
            throw new \Exception('任务非进行状态!');
        }
        parent::handle($taskModel);
    }
}