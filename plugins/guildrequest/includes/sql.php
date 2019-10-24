<?php
/*	Project:	EQdkp-Plus
 *	Package:	GuildRequest Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('EQDKP_INC')){
	header('HTTP/1.0 404 Not Found');exit;
}

$guildrequestSQL = array(
	'uninstall' => array(
		1	=> 'DROP TABLE IF EXISTS `__guildrequest_fields`',
		2	=> 'DROP TABLE IF EXISTS `__guildrequest_requests`',
		3	=> 'DROP TABLE IF EXISTS `__guildrequest_visits`',
	),

	'install'   => array(
		1	=> "CREATE TABLE `__guildrequest_fields` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`type` VARCHAR(50) NOT NULL,
				`name` TEXT COLLATE utf8_bin NOT NULL,
				`help` TEXT COLLATE utf8_bin NULL,
				`options` TEXT COLLATE utf8_bin DEFAULT NULL,
				`sortid` INT(10) UNSIGNED NULL DEFAULT '0',
				`required` TINYINT(3) UNSIGNED NULL DEFAULT '0',
				`in_list` TINYINT(3) UNSIGNED NULL DEFAULT '0',
				`dep_value` TEXT COLLATE utf8_bin NULL,
				`dep_field` INT(10) UNSIGNED NULL DEFAULT '0',
				PRIMARY KEY (`id`)
			)CHARSET=utf8 COLLATE=utf8_bin;",
		2	=> "CREATE TABLE `__guildrequest_requests` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`tstamp` INT(10) NULL DEFAULT '0',
				`username` VARCHAR(255) NOT NULL,
				`email` VARCHAR(255) NOT NULL,
				`auth_key` VARCHAR(255) NOT NULL,
				`lastvisit` INT(10) UNSIGNED NULL DEFAULT '0',
				`activation_key` VARCHAR(255) NULL DEFAULT NULL,
				`status` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
				`activated` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
				`closed` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
				`content` TEXT COLLATE utf8_bin NOT NULL,
				`voting_yes` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				`voting_no` INT(10) UNSIGNED NOT NULL DEFAULT '0',
				`voted_user` TEXT COLLATE utf8_bin DEFAULT NULL,
				`user_id` INT(11) UNSIGNED NULL DEFAULT '0',
				PRIMARY KEY (`id`)
			)DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
		3	=> "CREATE TABLE `__guildrequest_visits` (
				`request_id` INT(10) NOT NULL,
				`user_id` INT(10) NOT NULL,
				`lastvisit` INT(10) NOT NULL,
				PRIMARY KEY (`request_id`, `user_id`),
				INDEX `request_id` (`request_id`),
				INDEX `user_id` (`user_id`)
			)DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",)
		);
?>