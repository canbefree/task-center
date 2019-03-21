<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/24
 * Time: 17:21
 */


namespace App\Model\Mtable;

use Test\DatabaseTestCase;
use Test\DatabaseTransactions;


class PlayerTaskTest extends DatabaseTestCase
{
    use DatabaseTransactions;

    public function test_insert_and_update(){

        $playerTask = new PlayerTask();
        $playerTask->task_id = 12333;
        $playerTask->player_index = 9999;
        $ret = $playerTask->save();
        $this->assertTrue($ret);

        $playerTask->task_id = 12333;
        $ret =$playerTask->save();
        $this->assertFalse($ret);

        $playerTask->task_id = 123123;
        $ret = $playerTask->save();
        $this->assertTrue($ret);

        $playerTask = PlayerTask::findOne(['task_id'=>123123,'player_index'=>9999],true);
        $playerTask->task_id = 45677;
        $ret = $playerTask->save();
        $this->assertTrue($ret);

    }

}