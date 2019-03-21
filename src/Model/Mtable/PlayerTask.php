<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/24
 * Time: 17:21
 */


namespace App\Model\Mtable;

use App\Model\Mtable;
use App\Model\Task;

/**
 *
 * Class PlayerTask
 * @property $id
 * @property $task_id
 * @property $player_index
 * @property $schedule_index
 * @property $schedule_count
 * @property $step
 * @property $status
 * @property $reward_id
 * @property $updated_at
 * @property $created_at
 * @package App\Model\Mtable
 */
class PlayerTask extends Mtable{

    static $tableName = 't_player_task';

    public static function findOrCreate($condition,$lock =false){
        $row = self::findOne($condition,$lock);
        if(empty($row)){
            $row = new PlayerTask();
            $row->load($condition);
            $row->schedule_index = 0;
            $row->status = Task::STATUS_TASK_ING;
        }
        return $row;
    }
}