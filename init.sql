CREATE TABLE `t_player_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) DEFAULT NULL,
  `player_index` varchar(255) DEFAULT NULL,
  `schedule_index` int(11) DEFAULT NULL,
  `schedule_count` int(11) DEFAULT NULL,
  `step` tinyint(255) DEFAULT NULL,
  `status` tinyint(255) DEFAULT NULL,
  `reward_id` int(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;