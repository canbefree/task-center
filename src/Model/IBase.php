<?php
/**
 * 基础接口
 *
 * 规范 shell 命令参数
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/20
 * Time: 13:59
 */
namespace App\Model;

interface IBase{

    /**
     * 添加运行参数
     * @return mixed
     */
    public function addArgs($options);

    public function start();

    public function check();

    public function help();

}