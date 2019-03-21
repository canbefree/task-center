<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/20
 * Time: 15:35
 */
namespace App\Lib;

class Log{
    public static function write($message){
        $log_file = LOG_PATH.date("Y-md").".log";
        umask(022);
        file_put_contents($log_file ,$message."\n",FILE_APPEND);
    }
}