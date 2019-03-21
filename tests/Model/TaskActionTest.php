<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/11/1
 * Time: 10:00
 */

namespace Test\Model;


use App\Model\Task;
use App\Model\TaskAction;
use Test\DatabaseTestCase;
use Test\DatabaseTransactions;


class TaskActionTest extends DatabaseTestCase
{
//    use DatabaseTransactions;

    public function provider()
    {
        return require TEST_PATH."_data".DS.'task_test.php';
    }

    /**
     * @dataProvider provider
     */
    public function test_register_and_notify($task_info,$event_info){
        $action = new TaskAction();
        $action->register($task_info);
        $this->assertArrayHasKey($task_info['event'],$action::$_list);
    }
}