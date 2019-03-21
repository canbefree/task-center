<?php
/**
 * 特殊
 * 完成任务进度 (不包括当前的任务完成情况)
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/24
 * Time: 16:05
 */


namespace App\Model\Task;


use App\Model\IGameEvent;
use App\Model\Task;

class FinishTask extends Task implements IGameEvent {
    public static function attributes()
    {
        return [];
    }

    public static function createModal()
    {
        return [];
    }
}