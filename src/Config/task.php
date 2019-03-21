<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 11:13
 */


return [
    'task_info' => [
        'task_id' => 1,
        'event' => 'board_end', //event
        'class' => \App\Model\Task\BoardEnd::class,
        'lobby_id' => '524288', //大厅ID
        'pre' => [
            'class' => \App\Model\Pre::class,
            'params' => [
                'info' => [  //由于涉及到数据库请求, 请将 条件 unique 放到数组最后.
                    [
                        'column' => 'gid',
                        'condition' => ['in', [524288, 262216]]
                    ],
                ],
//                'updateSchema' => [
//                    'column' => 'hu_index',
//                ]
                'updateSchema' => 1,
            ],
        ],

        'platform' => 1, //平台类型  用于任务筛选

        'life' => [
            'class' => \App\Model\Life\Daily::class, //每日任务
            'params' => ['reset_time' => '9:00', 'start_time' => strtotime('-1 days'), 'end_time' => strtotime('+2 days')],
        ],

        'task_type' => [
            'class' => \App\Model\TaskType\Series::class,
            'params' => [
                'setting' => [
                    0 => [
                        'schedule_count' => 30,
                        'reward_id' => 30,
                    ],
                    1 => [
                        'schedule_count' => 40,
                        'reward_id' => 40,
                    ],
                    2 => [
                        'schedule_count' => 50,
                        'reward_id' => 50,
                    ],
                ]
            ],
        ],
    ],
];