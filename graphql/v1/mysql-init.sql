
DROP DATABASE IF EXISTS `dbplanner`;

CREATE DATABASE `dbplanner` character set UTF8mb4 collate utf8mb4_bin;

CREATE TABLE `dbplanner`.`polls` (
	`pid`      CHAR(36) NOT NULL,
	`title`		 VARCHAR(512) NOT NULL,
	`cid`      CHAR(36) NOT NULL default '',
	`cname`    VARCHAR(255) NOT NULL DEFAULT '',
	`notes`    VARCHAR(2048) NOT NULL DEFAULT '',
	`location` VARCHAR(2048) NOT NULL DEFAULT '',
	`timezone` VARCHAR(255) NOT NULL DEFAULT '',
	`videolink` VARCHAR(2048) NOT NULL DEFAULT '',
	`dates`    VARCHAR(2048) NOT NULL DEFAULT '',
	`ip`       VARCHAR(39) NOT NULL default '',
	`ts`       BIGINT NOT NULL,
	PRIMARY KEY (`pid`),
	KEY `cid`(`cid`),
	KEY `ts` (`ts`),
	KEY `ip` (`ip`)
) ENGINE=InnoDB CHARACTER SET UTF8mb4 COLLATE utf8mb4_bin;

CREATE TABLE `dbplanner`.`votes` (
	`pid`    CHAR(36) NOT NULL,
	`uid`    CHAR(36) NOT NULL,
	`uname`  VARCHAR(255) NOT NULL DEFAULT '',
	`data`  VARCHAR(2048) NOT NULL DEFAULT '',
	`ip`     VARCHAR(39) NOT NULL DEFAULT '',
	`ts`     BIGINT NOT NULL,
	UNIQUE `piduid` (`pid`,`uid`),
	KEY `pid` (`pid`),
	KEY `uid` (`uid`),
	KEY `ts`  (`ts`),
	KEY `ip`  (`ip`)
) ENGINE=InnoDB CHARACTER SET UTF8mb4 COLLATE utf8mb4_bin;

CREATE USER IF NOT EXISTS 'dbplanner'@'localhost' IDENTIFIED BY 'dbplanner';

GRANT SELECT, INSERT, UPDATE, DELETE ON `dbplanner`.* to 'dbplanner'@'localhost';
