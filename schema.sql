CREATE TABLE IF NOT EXISTS `problem_test_cases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `problem_id` int(11) NOT NULL,
  `input_text` mediumtext NOT NULL,
  `output_text` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `problems`;

CREATE TABLE `problems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contest_id` int(11) NOT NULL,
  `title` text,
  `problem_html` mediumtext NOT NULL,
  `run_timelimit_seconds` int(11) NOT NULL,
  `point` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(128) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `expire_at` datetime NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `problem_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `language` varchar(32) NOT NULL,
  `source_code` longblob NOT NULL,
  `judge_status` varchar(32) NOT NULL,
  `judge_server` varchar(128) NOT NULL,
  `judge_start_time` datetime NOT NULL,
  `judge_end_time` datetime NOT NULL,
  `execution_time` int(11) NOT NULL,
  `build_time` int(11) NOT NULL DEFAULT '0',
  `memory_used_in_kb` int(11) DEFAULT NULL,
  `error_message` varchar(1024) NOT NULL DEFAULT '',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `screen_name` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `passwd` varchar(512) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `screen_name` (`screen_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
