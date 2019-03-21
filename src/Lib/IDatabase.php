<?php
namespace App\Lib;

/**
 * 数据库基类
 * @author Tianfeng.Han
 * @package SwooleSystem
 * @subpackage database
 */
/**
 * Database Driver接口
 * 数据库驱动类的接口
 * @author Tianfeng.Han
 *
 */
interface IDatabase
{
    function query($sql);
    function connect();
    function close();
    function lastInsertId();
    function getAffectedRows();
    function errno();
    function quote($str);
}
