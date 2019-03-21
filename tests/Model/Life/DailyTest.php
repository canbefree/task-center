<?php
/**
 * 周期性的
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/24
 * Time: 17:00
 */

namespace Test\Model\Life;

use App\Model\Life\Daily;
use http\Exception;
use PHPUnit\Framework\TestCase;

class DailyTest extends TestCase
{

    public function resetProvider()
    {
        return [
            //重置时间点 当前时间点,期待的标记重置时间
            ['9:00', strtotime('2018-10-25 08:34:00'), strtotime('2018-10-24 9:00')],
            ['9:00', strtotime('2018-10-25 13:12:00'), strtotime('2018-10-25 9:00')],
            ['9:00', strtotime('2018-10-25 09:00:00'), strtotime('2018-10-24 9:00')],
            ['9:00', strtotime('2018-10-25 09:00:01'), strtotime('2018-10-25 9:00')],
        ];
    }

    public function shouldResetProvider()
    {
        return [
            //重置时间点 当前时间点,上次任务更新时间,期待的重置结果
            ['9:00', strtotime('2018-10-25 08:34:00'), strtotime('2018-10-22 9:00'), true],
            ['9:00', strtotime('2018-10-25 13:12:00'), strtotime('2018-10-25 9:00'), false],
            ['9:00', strtotime('2018-10-25 09:00:00'), strtotime('2018-10-24 9:00'), true],
            ['9:00', strtotime('2018-10-25 09:00:01'), strtotime('2018-10-25 9:00'), false],
        ];
    }


    /**
     * @dataProvider resetProvider
     * @param $reset_time
     * @param $now
     * @param $except
     */
    public function test_get_last_reset_time($reset_time, $now, $except)
    {
        $actual = Daily::getLastResetTime($reset_time, $now);
        $this->assertEquals($except, $actual);
    }

    /**
     * @dataProvider shouldResetProvider
     * @param $reset_time
     * @param $now
     * @param $task_update_time
     * @param $except
     */
    public function test_should_reset_task($reset_time, $now, $task_update_time, $except)
    {
        $this->assertTrue($task_update_time < $now,'data provider error');
        $actual = Daily::shouldResetTask($task_update_time, $reset_time, $now);
        $this->assertEquals($except, $actual,'error');
    }

}