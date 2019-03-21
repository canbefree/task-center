<?php
/**
 *
 * @remark
 *   1 已产生的用户任务会同步修改 状态以及进度
 *   2 任务状态为非可逆的。 开始 -->完成 or 过期
 *   3 过期任务不可领奖
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/23
 * Time: 16:55
 */

namespace App\Model;

use App\Lib\Log;
use App\Model\Mtable\PlayerTask;

/**
 * Class Task
 * @property $taskModel
 * @property $life
 * @package App\Model
 */
Class Task extends Event
{

    const STATUS_TASK_ING = 25; //任务进行中
    const STATUS_TASK_UNACCEPT = 10; //任务未接受
    const STATUS_TASK_DONE = 50; //任务未完成
    const STATUS_TASK_FINISH = 1; //任务完成
    const STATUS_TASK_EXPIRED = -1; //任务过期
    const STATUS_TASK_CANCELD = -2; //任务取消

    public $task_info;
    public $event_info;
    /** @var Pre */
    public $preModel;
    /** @var Life */
    public $lifeModel;
    /** @var TaskType */
    public $typeModel;



    public function __construct($taskInfo, $event_info)
    {
        $this->task_id = $taskInfo['task_id'];
        $this->user_id = $event_info['uid'];
        $this->event_info = $event_info;
        $this->preModel = new Pre($taskInfo['pre']['params']);
        $this->lifeModel = $this->createModel($taskInfo['life']);
        $this->typeModel = $this->createModel($taskInfo['task_type']);
    }


    public function run()
    {
        app()->ret = [];
        app()->rewards = [];

        //过滤不符合条件的事务
        //'有些特殊任务需要用到缓存的,比如不重复会员数';
        //redis
        try {
            Event::instance('task')->init();

            Mtable::getDb()->start();

            Log::write("pre init -------------------------");
            //根据事件消息过滤
            $this->preModel->init( $this->event_info,$this->task_id);

            Log::write("pre create -------------------------");

            //'player_task lock for update';
            $taskModel = PlayerTask::findOrCreate(['task_id' => $this->task_id, 'player_index' => $this->event_info['uid']], true);

            Log::write("life  ------------------------- ".\json_encode($taskModel->attributes()));

            //判断是否需要重置 任务是否过期，如果过期就要把任务剔除
            $this->lifeModel->handle($taskModel);

            Log::write("pre update -------------------------".\json_encode($taskModel->attributes()));

            //预处理通过的消息
            //更新任务进度
            $this->preModel->update($taskModel,$this->event_info);

            Log::write("type -------------------------".\json_encode($taskModel->attributes()));

            //进度了 更新任务状态就再这里了。
            $this->typeModel->handle($taskModel);

            Log::write("save -------------------------".\json_encode($taskModel->attributes()));

            //任务进度状态 任务
            $taskModel->save();

            //判断 model 任务是否更新 step status schedule_index  更新需要通知客户端

            //如果有奖励,通知发奖中心发奖
            Mtable::getDb()->commit();

            //直接推送和发奖
            Event::instance('task')->trigger('notify_and_reward');

        } catch (\Exception $e) {
            Log::write($e->getMessage());
            Log::write($e->getTraceAsString());
            Mtable::getDb()->rollback();
        }
        Log::write("ret:".json_encode(app()->ret));
        return app()->ret;
    }



    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }


    private function createModel($class_config,$class = null)
    {
        if (empty($class) && is_array($class_config) && isset($class_config['class'])) {
            $class = $class_config['class'];
        }

        return new $class(isset($class_config['params']) ? $class_config['params'] : null);
    }


}

