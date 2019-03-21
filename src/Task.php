<?php
/**
 * 分配中心
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/20
 * Time: 9:40
 */

namespace App;

use App\Lib\Config;
use App\Model\Event;
use App\Model\PushProcess;
use App\Model\Task\FinishTask;
use App\Model\TaskAction;
use App\Model\TaskProcess;
use App\Model\IBase;
use Swoole\Process;

class Task extends Container implements IBase
{
    static $client;

    /**
     * @var TaskAction
     */
    static $action;


    static $pid_pool;

    public function addArgs($option)
    {
    }

    public function check()
    {
    }

    /**
     * 初始化操作 注册绑定相关的任务
     */
    public function init()
    {

        // 所有任务 根据事件分类  load 到内存中
        app()->action = new TaskAction();
        foreach (Config::load('task') as $task) {
            // 绑定特殊事件 -- 任务完成
            if ($task['class'] == FinishTask::class) {
                Event::instance()->on('finish_task', function ($event_info) use ($task) {
                    $taskClass = new \App\Model\Task($task, $event_info);
                    $taskClass->run();
                });
            }
            app()->action->register($task);
        }

    }

    public function start()
    {
        $this->init();

        //设置主进程名
        \swoole_set_process_name("task master");

        PushProcess::create();

        foreach (range(0, 3) as $i) {
            TaskProcess::create("task worker:" . $i);
        }

        Process::signal(SIGCHLD, function ($sig) {
            //必须为false，非阻塞模式
            while ($ret = Process::wait(false)) {
                echo "PID={$ret['pid']}\n";
            }
        });

    }


    public function help()
    {
    }


}

