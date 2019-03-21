<?php
/**
 * 特殊
 * 完成任务进度 (不包括当前的任务完成情况)
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/10/24
 * Time: 16:05
 */


namespace App\Model\Task;


use App\Model\IGameEvent;
use App\Model\Task;
use Faker\Factory;

class BoardEnd extends Task implements IGameEvent
{

    public static function attributes()
    {
        return ['event', 'hu_index', 'is_vip_room', 'room_id', 'room_index', 'uid', 'timestamp', 'score', 'win_index', 'group_id', 'hu_fan_index'];
    }

    public static function attributesType()
    {
        return [
            'event' => 'string',
            'hu_index' => 'int',
            'is_vip_room' => 'int',
            'room_id' => 'int',
            'room_index' => 'index',
            'uid' => 123,
            'timestamp' => 'timestamp',
            'score' => 'int',
            'win_index' => 'int',
            'group_id' => 'int',
            'hu_fan_result' => 'int',
        ];
    }


    public static function rules()
    {
        return [
            '添加指定牌型' => [
                'type' => 'checkbox',
                'items' => [
                    '清一色' => ['hu_fan_result' => ['&', 0x00800]],
                    '杠上花' => ['hu_fan_result' => ['&', 0x1000000]],
                    '小七对' => ['hu_fan_result' => ['&', 0x400000]],
                    '十风' => ['hu_fan_result' => ['&', 0x080]],
                    '十三幺' => ['hu_fan_result' => ['&', 0x020]],
                    '自摸' => ['hu_fan_result' => ['&', 0x0008]],
                ],
            ],
            '选择牌桌类型' => [
                'type' => 'radio',
                'items' => [
                    "金币场" => ['is_vip_room' => ['in', [0]]],
                    "VIP场" => ['is_vip_room' => ['in', [1]]],
                ]
            ],
            '指定游戏' => [
                'type' => 'select',
                'options' => [
                    '524288' => '同城赛大厅',
                ]
            ],
            '分数' => [
                'type' => 'input',
                'item' => [
                    '大于' => ['score' => ['>', '%value%']]
                ]
            ]
        ];
    }

    public static function createModal()
    {
        $facker = Factory::create();
        $uid = rand(100, 999);
        $hu_fan_index_arr = [0x1000000,0x00800,0x400000,0x080,0x020,0x0008];
        return [
            'event' => 'board_end',
            'gid' => '524288',
            'hu_index' => rand(100000, 999999),
            'is_vip_room' => rand(0, 1),
            'room_id' => $facker->uuid,
            'room_index' => rand(100000, 999999),
            'uid' => $uid,
            'timestamp' => 'timestamp',
            'score' => rand(-100,200),
            'win_index' => $uid % 2 != 1 ? $uid : rand(1000, 9999),
            'group_id' => rand(0,100),
            'hu_fan_result' => $hu_fan_index_arr[array_rand($hu_fan_index_arr,1)],
        ];
    }
}