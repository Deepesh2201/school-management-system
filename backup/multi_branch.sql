-- Multi-branch (single login, separate schema per branch)
-- Run this ONCE against the MAIN schema (default connection database).

CREATE TABLE IF NOT EXISTS `multi_branch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `hostname` varchar(150) NOT NULL DEFAULT '127.0.0.1',
  `username` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '',
  `database_name` varchar(150) NOT NULL,
  `base_url` varchar(255) NOT NULL DEFAULT '',
  `folder_path` varchar(255) NOT NULL DEFAULT '',
  `is_verified` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Maps a staff login (username/email) to the branch(es) they may use.
-- branch_id = 0 means the MAIN branch (the default schema). N > 0 refers to multi_branch.id
CREATE TABLE IF NOT EXISTS `branch_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(150) NOT NULL,
  `branch_id` int(11) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_branch_user` (`username`,`branch_id`),
  KEY `idx_branch_user_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
