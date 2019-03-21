<?php
/**
 * 任务处理
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/20
 * Time: 9:40
 */

namespace App;

use App\Lib\Log;
use App\Model\Cmq\Message;
use App\Model\Stable\ClientList;
use Swoole\Process;
use App\Model\IBase;

class Gate implements IBase
{
    private $options;

    public $client_list;

    public function addArgs($options = [])
    {
        $this->options = $options;
    }

    public function start()
    {
        $usleeptime = 1000;
        $serv = new \Swoole\Server('0.0.0.0', 9501);

        $process_pool = [];
        foreach (range(1,3) as $item) {
            $process = $this->createProcess('gate sub '.$item,$usleeptime);
            $serv->addProcess($process);
            $process_pool[] = $process;
        }

        $setting = array(
            'worker_num' => 12,
            'daemonize' => false,
            'backlog' => 128,
            'dispatch_mode' => 3, //抢占模式
        );

        $serv->set($setting);
        $serv->on("Start", [new self(), '_onStart']);
        $serv->on('Connect', [new self(), '_onConnect']);
//        $serv->on('Receive', [new self(), '_onReceive']);
        $serv->on("Receive",call_user_func([new self(),'_onReceiveCallBack'],$process_pool));
        $serv->on('Close', [new self(), '_onClose']);
        $serv->on('PipeMessage', [new self(), '_onPipeMessage']);
        $serv->on("WorkerStart", [new self(), '_onWorkerStart']);
        $serv->start();
    }

    public function check()
    {
    }

    public function help()
    {
    }


    public function _onStart(\swoole_server $serv)
    {
        Log::write("g:start");
        //初始化链接客户端表
        ClientList::init();
    }

    public function _onConnect(\Swoole\Server $serv, $fd, $reactor_id)
    {
        //连接时，将连接信息记录到 $client_list 中
        Log::write("g:connect " . $fd);
        $client_info = $serv->getClientInfo($fd);
        ClientList::register($fd, $client_info);
    }

    /**
     * 将数据分发到连接的服务器中
     * @param $process_pool
     * @return \Closure
     */
    public function _onReceiveCallBack($process_pool)
    {
        return  function ($serv, $fd, $from_id, $data) use ($process_pool) {
            $process = current($process_pool);
            $data = $process->pop();
            $serv->send($fd, $data);
        };
    }

    public function _onClose(\Swoole\Server $serv, $fd, $reactor_id)
    {
        Log::write("g:close");
        ClientList::unregister($fd);
    }

    public function _onPipeMessage()
    {
        Log::write("g:pipe_messages");
    }

    public function _onWorkerStart(\Swoole\Server $serv, $worker_id)
    {
        Log::write("g:workStart:" . $worker_id);
    }

    /**
     * 创建子进程处理cmq出来的数据
     * @param $process_name
     * @param int $usleeptime
     * @return Process
     */
    protected function createProcess($process_name,$usleeptime = 0){
        $process =  new Process(function(Process $process) use ($process_name,$usleeptime){
            swoole_set_process_name($process_name);
            while (true){
                $message = new Message();
                $ret = $message->produce(1);
                usleep($usleeptime);
                $curr = $ret->current();
                foreach ($curr as $datum) {
                    echo $datum,PHP_EOL;
                    $process->push($datum) ;
                }
            }
        });
        $process->useQueue(1,2);
        return $process;
    }

}