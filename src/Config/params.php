<?php
/**
 * Created by PhpStorm.
 * User: neo
 * Date: 2018/11/6
 * Time: 16:57
 */

return [
    'task' => [
        'board_end' => \App\Model\Task\BoardEnd::class,
        'cpt_record' => \App\Model\Task\CptRecord::class,
        'finish_task' => \App\Model\Task\FinishTask::class,
        'recharge' => \App\Model\Task\Recharge::class,
        'share' => \App\Model\Task\Share::class,
    ],
    'task_type' => [
        'normal' => \App\Model\TaskType\Normal::class,
        'series' => \App\Model\TaskType\Series::class,
    ],
    'life' => [
        'daily' => \App\Model\Life\Daily::class,
        'whole' => \App\Model\Life\Whole::class,
    ]
];