<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/25
 * Time: 18:55
 */

namespace App\Lib;

/**
 * Database Driver接口
 * 数据库结果集的接口，提供2种接口
 * fetch 获取单条数据
 * fetch 获取全部数据到数组
 * @author Tianfeng.Han
 */
interface IDbRecord
{
    function fetch();
    function fetchall();
}