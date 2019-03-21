<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/22
 * Time: 14:45
 */

namespace App\Model;


class Stable
{

    public $UUID;

    public static $table;


    /**
     * 创建配置表
     */
    public static function init()
    {
        static::$table = new \Swoole\Table(1024*2);
        foreach (static::attributes() as $key => $v) {
            static::$table->column($key, $v[0], $v[1]);
        }
        static::$table->create();
    }


    public static function attributes()
    {
        return [
            "UUID" => [\swoole_table::TYPE_STRING, 12],
        ];
    }


    /**
     * 设置唯一标识ID
     */
    public function getUuid()
    {
        return $this->UUID;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function save()
    {
        $this->UUID = $this->getUuid();
        if (empty($this->UUID)) {
            exit('error,uuid must not be empty');
        }
        $arrData = [];
        foreach (array_keys(self::attributes()) as $item) {
            $arrData[$item] = $this->$item;
        }

        $ret = static::$table->set($this->UUID, $arrData);
        if (empty($ret)) {
            return false;
        }
        return true;
    }

    public static function get($uuid)
    {
        return static::$table->get($uuid);
    }

}