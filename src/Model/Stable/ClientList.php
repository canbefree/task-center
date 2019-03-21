<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/20
 * Time: 20:45
 */

namespace App\Model\Stable;

use App\Lib\Log;
use App\Model\Stable;

/**
 * 维护连接的客户端机器
 * Class ClientList
 * @package App\Model\Table
 */
class ClientList extends Stable
{

    static public $table;

    public static $dd = 'client';

    public function getUuid()
    {
        return $this->FD;
    }

    public static function attributes()
    {
        $parent_attributes = parent::attributes();
        $attributes = [
            "IP" => [\swoole_table::TYPE_STRING, 15],
            "FD" => [\swoole_table::TYPE_INT, 8],
            "LAST_TIME" => [\swoole_table::TYPE_STRING, 16],
            "CONNECT_TIME" => [\swoole_table::TYPE_STRING, 16],
        ];
        return array_merge($parent_attributes, $attributes);
    }

    public static function register($fd, $client_info)
    {
        $new = new self();
        $new->IP = $client_info['remote_ip'];
        $new->FD = $fd;
        $new->LAST_TIME = $client_info['last_time'];
        $new->CONNECT_TIME = $client_info['connect_time'];
        $ret = $new->save();
        if (empty($ret)) {
            Log::write("error, table save failed fd:" . $fd . "client_ip:" . $new->IP);
        }
        return $new;
    }

    public static function unregister($fd)
    {
        self::$table->del($fd);
        Log::write("unregister: fd:$fd count:" . self::$table->count());
    }

}