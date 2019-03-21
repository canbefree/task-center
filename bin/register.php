<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/11/5
 * Time: 11:32
 */


//像 consul 自动注册任务数据
require dirname(__FILE__) . "/../src/bootstrap.php";


$task_dir = APP_PATH . "model" . DS . 'task';

echo $task_dir, PHP_EOL;


$task_type_dir = APP_PATH . "model" . DS . "taskType";


$life_dir = APP_PATH . "model" . DS . "life";


function getRegisterData($dir)
{
    $params = [];
    $files = scandir($dir);
    foreach ($files as $file) {
        if (substr($file, -4, 4) == '.php') {
            $path_to_file = $dir . DS . $file;
            $event = get_event_from_filename($file);
            $class = get_class_from_file($path_to_file);
        }
    }
    $params[$event] = $class;
}




function get_class_from_file($path_to_file)
{
    $contents = file_get_contents($path_to_file);
    $namespace = $class = "";
    $getting_namespace = $getting_class = false;

    foreach (token_get_all($contents) as $token) {

        if (is_array($token) && $token[0] == T_NAMESPACE) {
            $getting_namespace = true;
        }

        if (is_array($token) && $token[0] == T_CLASS) {
            $getting_class = true;
        }
        if ($getting_namespace === true) {

            if (is_array($token) && in_array($token[0], [T_STRING, T_NS_SEPARATOR])) {
                $namespace .= $token[1];
            } else if ($token === ';') {
                $getting_namespace = false;
            }
        }

        if ($getting_class === true) {
            if (is_array($token) && $token[0] == T_STRING) {
                $class = $token[1];
                break;
            }
        }
    }
    return $namespace ? $namespace . '\\' . $class : $class;
}

function get_event_from_filename($filename){
    $event = substr($filename,0,-4);
    return strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $event));
}






