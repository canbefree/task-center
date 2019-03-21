<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/11/1
 * Time: 10:00
 */

namespace Test\Model;


use App\Model\Task;
use Test\DatabaseTestCase;
use Test\DatabaseTransactions;

class TaskTest extends DatabaseTestCase
{
    use DatabaseTransactions;

    public function provider()
    {
        return require TEST_PATH."_data".DS.'task_test.php';
    }

    /**
     * @dataProvider provider
     * @param $task_info
     * @param $event_info
     * @throws \Exception
     */
    public function test_run($task_info,$event_info,$except)
    {

        $task = new Task($task_info,$event_info);
        $ret = $task->run();
        $this->assertEquals($except['reward'],app()->rewards);
    }
}