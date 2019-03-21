<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/11/6
 * Time: 16:08
 */


namespace App\Model;

use App\Lib\Log;
use Swoole\Client;

class TaskProcess extends Process
{
    static  $client;
    public static function create()
    {
        return new static("worker handle", []);
    }


    public function work(\Swoole\Process $process)
    {
        $client = new \Swoole\Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
        $client->on("Connect", [$this, '_onConnect']);
        $client->on("Close", [$this, '_onClose']);
        $client->on("Error", [$this, '_onError']);
        $client->on("Receive", [$this, '_onReceive']);
        self::$client = $client;
        $this->connect();
    }


    public function connect()
    {
        self::$client->connect('127.0.0.1', 9501);
    }


    public function _onError($client)
    {
        if ($client->errCode == 61 || $client->errCode == 111) {
            $this->reConnect();
        } else {
            Log::write("t:Error=>code:" . $client->errCode . "msg:" . socket_strerror($client->errCode));
        }
    }

    public function _onConnect(Client $client)
    {
        Log::write("t:connect");
        $client->send("hello,world");
    }

    public function _onReceive(Client $client, $data)
    {
        Log::write("t:receive " . $data);
        //消息处理
        $data = \json_decode($data,true);
        $ret =  app()->action->notify($data);
        $client->send("next" . $ret);
    }

    public function _onClose()
    {
        Log::write("t:close");
        $this->reConnect();
    }

    protected function reConnect()
    {
        //断开后每秒重连
        \Swoole\Timer::after('1000', function () {
            Log::write("t:reconnect" . date("Y-m-d H:i:s"));
            $this->connect();
        });
    }

}