<?php
require_once 'autoload.php';


if (!extension_loaded('mysqli')){
    echo "Please install the mysqli expansion\n";
    exit();
}


if (!extension_loaded('swoole')){
    echo "Please install the Swoole expansion\n";
    exit();
}

set_error_handler('myErrorHandler');

function myErrorHandler($errno, $errstr, $errfile, $errline){
    \App\Lib\Log::write($errstr);
    \App\Lib\Log::write($errfile);
    \App\Lib\Log::write($errline);
}






//$client = new \XuTL\QCloud\Cmq\Client('http://cmq-queue-gz.api.tencentyun.com','AKIDc5IH4d65zf7LL8y293nJbhZDcomWnILB','kGZeU6WifPuCG6i7JGzUlodFdnjSxEH6');
//$request = new \XuTL\QCloud\Cmq\Requests\ListTopicRequest();
//try {
//    $response = $client->listTopic($request);
//    print_r($response);
//} catch (Exception $e) {
//    print_r($e->getMessage());
//}