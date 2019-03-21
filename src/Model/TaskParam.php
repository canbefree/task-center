<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/11/1
 * Time: 17:15
 */

namespace App\Model;

abstract class TaskParam{

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name,$value){
        $this->$name = $value;
    }

    public function __construct($params)
    {
        foreach ($params as $key => $item){
            $this->$key = $item;
        }
    }
}