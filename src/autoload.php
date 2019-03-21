<?php
define("DS",DIRECTORY_SEPARATOR);
define("APP_PATH",dirname(__FILE__).DS);
define("PRO_PATH",APP_PATH."..".DS);
define("BIN_PATH",PRO_PATH.'bin'.DS);
define("RUN_PATH",PRO_PATH.'run'.DS);
define("VENDOR_PATH",PRO_PATH.'vendor'.DS);
define("LOG_PATH",PRO_PATH.'log'.DS);
define("TEST_PATH",PRO_PATH.'tests'.DS);


require VENDOR_PATH.'autoload.php';

//设置时区
date_default_timezone_set('PRC');

global $app;
$app = \App\Container::getInstance();


//加载配置文件 防止密码传到服务器上
$dotEnv = new \Dotenv\Dotenv(PRO_PATH );
$dotEnv->load();


//获取配置文件内容
function env($key){
    return getenv($key);
}

function app(){
    global $app;
    return $app;
}

if (!extension_loaded('mysqli')){
    echo "Please install the mysqli expansion\n";
    exit();
}