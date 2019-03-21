<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/11/5
 * Time: 16:50
 */

namespace App\Model;

interface IGameEvent
{
    public static function attributes();

    public static function createModal();
}