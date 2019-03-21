<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/11/6
 * Time: 16:06
 */
namespace App\Model;


abstract class Process {

    public $process;
    protected $config;
    protected $log;
    protected $params;

    public function __construct($name,$params)
    {
        $this->name = $name;
        $this->process = new \Swoole\Process([$this,'__start'],false,2);
        
        if(isset($params['queue'])){
            $this->process->useQueue($params['queue']);
        }
        $this->process->start();
    }


    public function __start(\Swoole\Process $process){
        echo "进程开始",PHP_EOL;
        \swoole_process::signal(SIGTERM, [$this, "__shutDown"]);
        $process->name($this->name);
        $this->work($process);
    }


    abstract public function work(\Swoole\Process $process);

    public function __shutDown()
    {
         echo "进程重启",PHP_EOL;
         new static($this->name,null);
         exit;
    }
}