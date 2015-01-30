CREATE TABLE IF NOT EXISTS `#__hypercomments` (
	`local_id` int(11) NOT NULL AUTO_INCREMENT,
	`xid` varchar(50) NOT NULL,
	`id` varchar(30) NOT NULL,
	`stream_id` varchar(500) NOT NULL,
	`widget_id` int(11) NOT NULL,
	`text` text NOT NULL,
	`date` DATETIME NOT NULL,
	`acc_id` int(11) NOT NULL,
	`nick` varchar(250) NOT NULL,
	`email` varchar(250) NOT NULL,
	`parent_id` varchar(100) NOT NULL,
	`root_id` varchar(100) NOT NULL,
	`files` text NOT NULL,
	`ip` varchar(50) NOT NULL,
	`user_id` int(11) NOT NULL,
	`parent_user_id` int(11) NOT NULL,
	`category` int(11) NOT NULL,
	`link` varchar(250) NOT NULL,
	UNIQUE KEY `local_id` (`local_id`),
	KEY `xid` (`xid`),
	KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__hypercomments_export` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`filename` varchar(100) NOT NULL,
	`status` varchar(10) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `status` (`status`),
	KEY `filename` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;