<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/11/1
 * Time: 17:24
 */

namespace App;


class Container{

    static $app;

    static $data; //全局数据

    /**
     * 初始化
     */
    static function getInstance()
    {
        if (!self::$app instanceof Container)
        {
            self::$app = new Container();
        }
        return self::$app;
    }

    public function __get($name)
    {
        if(isset(self::$data[$name])){
            return self::$data[$name];
        }
        return null;
    }

    public function __set($name,$value){
        self::$data[$name] = $value;
    }
}