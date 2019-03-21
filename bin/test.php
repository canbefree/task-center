<?php
/**
 *  压测
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/25
 * Time: 10:43
 */

//像 consul 自动注册任务数据
require dirname(__FILE__) . "/../src/bootstrap.php";

$options = [];


$this->exec(BIN_DIR . "/exec/consul", ['agent', '-ui', '-config-dir', BIN_DIR . '/exec/consul.d']);


$factory = new \SensioLabs\Consul\ServiceFactory($options);
/** @var \SensioLabs\Consul\Services\KV $kv */
$kv = $factory->get("kv");

$kv->get('123');
