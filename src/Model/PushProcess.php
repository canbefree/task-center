<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/30
 * Time: 10:46
 */

namespace App\Model;


use App\Lib\Log;

class PushProcess extends Process
{

    public static function create()
    {
        return new static("worker push", ['queue'=>1001]);
    }

    public function work(\Swoole\Process $process)
    {
        \swoole_set_process_name('ps push ');
        while ($data = $process->pop()) {
            echo $data, PHP_EOL;
        }
    }

//    const PROCESS = 10001;
//    public static function init()
//    {
//        echo "创建推送进程", PHP_EOL;
//        $process = new Process(function (Process $worker) {
//            \swoole_set_process_name('ps push ');
//            while ($data = $worker->pop()) {
//
//            }
//        });
//        $process->useQueue(self::PROCESS);
//        $process->start();
//
//        //注册推送
//        Event::instance()->on('push',function($data)use($process){
//            $process->push($data);
//        });
//
//        return $process;
//    }
}