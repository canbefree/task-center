<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/20
 * Time: 9:46
 */
require  dirname(__FILE__)."/../src/bootstrap.php";



// php bin/index.php --gate
// php bin/index.php --task -c 10

$shortopts  = "";
$shortopts .= "t:";  //  -tgate -ttask 选择服务类型


$longopts  = array(
    'help'
);

$options = getopt($shortopts,$longopts);


if(!isset($options['t'])) exit("must set type");


$config = [
    'task' => 'App\Task',
    'gate' => 'App\Gate'
];



$class = new $config[$options['t']];

if(!$class instanceof \App\Model\IBase){
    exit("接口错误");
}



if(isset($options['help'])){
    $class->help();
}else{
    $class->addArgs($options);
    $class->check($options);
    $class->start();
}
