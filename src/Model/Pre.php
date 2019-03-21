<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/24
 * Time: 17:27
 */

namespace App\Model;

use App\Model\Mtable\PlayerTask;
use function Sodium\library_version_major;

/**
 * Class Pre
 * @property $updateSchema
 * @package App\Model
 */
class Pre extends TaskParam
{
    /****************************************************************************************************************************************
     * [
     * 'pre' => [
     * 'class' => \App\Model\Pre::class,
     * 'params' => [
     * 'info' => [  //由于涉及到数据库请求, 请将 条件 unique 放到数组最后.
     * [
     * 'column' => 'gid', //统计项
     * 'condition' => ['in', [524288, 262216]]], //条件
     * [
     * 'alias' => 'sum', //新增的统计字段
     * 'columns' => ['uid1', 'uid2', 'uid3', 'uid4'], //统计项
     * 'condition' => ['unique'], //条件
     * 'uninterrupted' => true  //当不满足此条件，任务进度重置
     * ],
     * ],
     * 'updateSchema' => ['column' => 'sum'],
     * ],
     * ]
     *****************************************************************************************************************************************/

    const TASK_RESET_SCHEMA = -1;
    const TASK_UPDATE_SCHEMA = 1;

    const REDIS_KEY = 'TASK_CENTER_PROJECT_UNIQUE_SET_';

    private $redisKey;

    public function init(&$eventInfo, $taskId)
    {
        $this->redisKey = self::REDIS_KEY . $taskId . "_" . $eventInfo['uid'];

        array_walk($this->info, function ($item) use (&$eventInfo, $taskId) {

            $this->set($item, $eventInfo, $taskId);
        });
    }

    /**
     * @param $taskModel PlayerTask
     */
    public function update(&$taskModel, $event_info)
    {
        //重置任务              (注册回调避免多次数据库查询)
        Event::instance('task')->trigger('reset', $taskModel);

        if (isset($this->updateSchema['column']) && is_numeric($event_info[$this->updateSchema['column']])) {
            $count = $event_info[$this->updateSchema['column']];
        } elseif (is_numeric($this->updateSchema)) {
            $count = $this->updateSchema;
        } else {
            throw new \Exception('updateSchema setting error!');
        }

        $taskModel->schedule_index = $taskModel->schedule_index + $count;
    }


    /**
     *
     *
     * ['gid'=>[ 'in' , [ 1,2,3,4,5]]]
     * ['gid'=> [ '=', 233 ]
     */
    private function set($item, $eventInfo, $taskId)
    {
        if (isset($item['columns'])) {
            foreach ($item['columns'] as $column) {
                $this->setColumn($column, $item, $eventInfo, $taskId);
            }
        } else {
            $this->setColumn($item['column'], $item, $eventInfo, $taskId);
        }
    }

    public function setColumn($column, $item, $eventInfo, $taskId)
    {
        $condition = $item['condition'];
        $uninterrupted = isset($item['uninterrupted'])?$item['uninterrupted']:null;

        if (!isset($eventInfo[$column])) {
            throw new \Exception('error,invalid condition event_info:' . json_encode($eventInfo) . " column:" . $column);
        }

        // 必须连续满足
        switch ($condition[0]) {
            case 'in':
                if (in_array($eventInfo[$column], $condition[1])) {
                    return self::TASK_UPDATE_SCHEMA;
                }
                break;
            case 'unique':
                if ($eventInfo[$column]) {
                    return self::TASK_UPDATE_SCHEMA;
                }
                break;
            case 'equal':
                if ($eventInfo[$condition[1] == $eventInfo[$column]]) {
                    return self::TASK_UPDATE_SCHEMA;
                }
                break;
        }

        //当为连续任务的时候,需要重置任务
        if ($uninterrupted) {
            Event::instance('task')->on('reset', function (&$taskModel) {
                /** @var $taskModel PlayerTask */
                $taskModel->schedule_index = 0;
            });
            return self::TASK_RESET_SCHEMA;
        }

        app()->ret[RetCode::STEP_FILTER] = [false, $item['condition']];
        throw new \Exception("$column condition error " . json_encode($item['condition']));
    }


}