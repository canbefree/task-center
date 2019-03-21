<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/24
 * Time: 16:12
 */

namespace App\Lib;

class Config{
    public static function load($config){
        return require APP_PATH.'Config'.DS.$config.".php";
    }
}