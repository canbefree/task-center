<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/25
 * Time: 16:23
 */

namespace App\Model;


use App\Lib\Log;
use App\Model\Mtable\PlayerTask;

class TaskType
{
    public $setting;


    public function __construct($params)
    {
        $this->setting = $params['setting'];
    }

    /**
     * @param $taskModel PlayerTask
     */
    public function handle(&$taskModel)
    {
        if (empty($taskModel->step) && $taskModel->step !== 0) {
            Log::write("set step 0");
            $taskModel->step = 0;
        }
        if (empty($taskModel->schedule_count)) {
            $taskModel->schedule_count = $this->setting[$taskModel->step]['schedule_count'];
        }

        $ret = $this->isTaskFinished($taskModel->schedule_index, $taskModel->schedule_count, $this->setting, $taskModel->step);
        $taskModel->step = $ret['step'];
        $taskModel->schedule_index = $ret['schedule_index'];
        $taskModel->schedule_count = $ret['schedule_count'];
        $taskModel->status = $ret['status'];
    }


    public function isTaskFinished($schedule_index, $schedule_count, $setting, $step)
    {
        if ($schedule_index >= $schedule_count) {
            //发送奖励
            $this->addRewards($setting[$step]['reward_id']);

            if ($step < count($setting)-1) {
                $step = $step + 1;
                $schedule_count = $setting[$step]['schedule_count'];
                return $this->isTaskFinished($schedule_index, $schedule_count, $setting, $step);
            } else { //系列任务已达成
                return ['schedule_index' => $setting[$step]['schedule_count'], 'schedule_count' => $setting[$step]['schedule_count'], 'step' => $step, 'status' => Task::STATUS_TASK_FINISH];
            }
        }
        return ['schedule_index' => $schedule_index, 'schedule_count' => $setting[$step]['schedule_count'], 'step' => $step, 'status' => Task::STATUS_TASK_ING];
    }

    protected function addRewards($reward_id){
        $rewards = app()->rewards;
        $rewards[] = ['reward_id'=>$reward_id];
        app()->rewards = $rewards;
    }

}