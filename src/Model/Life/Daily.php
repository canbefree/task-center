<?php
/**
 * 周期性的
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/24
 * Time: 17:00
 */

namespace App\Model\Life;

use App\Model\Life;
use App\Model\Mtable\PlayerTask;
use App\Model\Task;

class Daily extends Life
{
    /**
     * @param $taskModel PlayerTask
     * @return bool
     * @throws \Exception
     */
    public function handle(&$taskModel)
    {
        parent::handle($taskModel);

        //判断是否需要重置
        if (static::shouldResetTask($this->reset_time, $taskModel->updated_at)) {
            $taskModel->status = Task::STATUS_TASK_ING;
            $taskModel->schedule_index = 0;
        }

        //判断是否任务未进行中
        if($taskModel->status != Task::STATUS_TASK_ING){
            throw new \Exception('任务非进行状态!');
        }

    }

    /**
     * 获取标记重置时间
     *
     * @param $reset_time
     * @param null $now
     * @return false|int
     */
    public static function getLastResetTime($reset_time, $now = null)
    {
        if (empty($now)) {
            $now = time();
        }
        $tmp_reset_time = strtotime(date("Y-m-d",$now) . " " . $reset_time);
        if($now > $tmp_reset_time){
            return $tmp_reset_time;
        }
        return $tmp_reset_time - 86400;
    }

    //获取上一次任务进度更新的时间 是否小于标记重置时间 ,如果是 就应该更新
    public static function shouldResetTask($reset_time ,$last_task_update_time, $now = null){
        if (empty($now)) {
            $now = time();
        }
        if($last_task_update_time >= self::getLastResetTime($reset_time,$now)){
           return true;
        }
        return false;
    }

}