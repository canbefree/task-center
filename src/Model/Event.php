<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/24
 * Time: 9:26
 */

namespace App\Model;

class Event
{
    static $_instance;

    /**
     * @return Event
     */
    public static function instance($category = 'app')
    {
        if(self::$_instance[$category] instanceof self)    {
            return self::$_instance[$category];
        }
        static::$_instance[$category] = new static();
        return static::$_instance[$category];
    }

    public $eventMap = [];

    public function on($evtname, \Closure $handle)
    {
        $this->eventMap[$evtname] = $handle;
    }

    public function trigger($event, $scope=null)
    {
        if(isset($this->eventMap[$event]) && $this->eventMap[$event]){
            call_user_func_array($this->eventMap[$event], $scope);
        }
    }


    public function init(){
        $this->eventMap = [];
    }
}