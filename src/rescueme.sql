-- --------------------------------------------------------

-- 
-- Structure for table `missing`
-- 

CREATE TABLE IF NOT EXISTS `missing` (
  `missing_id` int(6) NOT NULL AUTO_INCREMENT,
  `op_id` int(6) NOT NULL,
  `missing_name` char(255) NOT NULL,
  `missing_mobile_country` char(4) NOT NULL,
  `missing_mobile` varchar(25) NOT NULL,
  `sms_sent` timestamp NULL DEFAULT NULL,
  `sms_delivery` timestamp NULL DEFAULT NULL,
  `sms_provider` varchar(255) NOT NULL,
  `sms_provider_ref` varchar(255) NOT NULL,
  `sms_error` varchar(255) NOT NULL,
  `missing_answered` timestamp NULL DEFAULT NULL,
  `missing_reported` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `sms2_sent` enum('false','true') NOT NULL DEFAULT 'false',
  `sms_mb_sent` enum('false','true') NOT NULL,
  PRIMARY KEY (`missing_id`),
  KEY `oper_id` (`op_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure for table `modules`
-- 

CREATE TABLE IF NOT EXISTS `modules` (
  `module_id` int(4) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `type` varchar(50) NOT NULL,
  `impl` varchar(50) NOT NULL,
  `config` text NOT NULL,
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure for table `operations`
-- 

CREATE TABLE IF NOT EXISTS `operations` (
  `op_id` int(6) NOT NULL AUTO_INCREMENT,
  `user_id` int(6) NOT NULL,
  `op_name` varchar(255) NOT NULL,
  `alert_mobile_country` char(4) NOT NULL,
  `alert_mobile` varchar(25) NOT NULL,
  `op_ref` varchar(255) NOT NULL,
  `op_opened` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `op_closed` timestamp NULL DEFAULT NULL,
  `op_comments` text NOT NULL,
  PRIMARY KEY (`op_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure for table `positions`
-- 

CREATE TABLE IF NOT EXISTS `positions` (
  `pos_id` int(10) NOT NULL AUTO_INCREMENT,
  `missing_id` int(6) NOT NULL,
  `lat` double NOT NULL,
  `lon` double NOT NULL,
  `acc` int(5) NOT NULL,
  `alt` int(4) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_agent` varchar(255) NOT NULL,
  PRIMARY KEY (`pos_id`),
  KEY `missing_id` (`missing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure for table `properties`
-- 

CREATE TABLE IF NOT EXISTS `properties` (
  `property_id` int(4) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Structure for table `users`
-- 

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `password` char(128) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL,
  `mobile_country` char(4) NOT NULL,
  `mobile` varchar(25) NOT NULL,
  `state` enum('pending','disabled','deleted') DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

