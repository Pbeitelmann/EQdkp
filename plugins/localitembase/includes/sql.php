<?php
/*	Project:	EQdkp-Plus
 *	Package:	Local Itembase Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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
if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}

$localitembaseSQL = array(

  'uninstall' => array(
    1     => 'DROP TABLE IF EXISTS `__plugin_localitembase`',
  ),

  'install'   => array(
	1 => "CREATE TABLE `__plugin_localitembase` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`item_gameid` VARCHAR(255) NULL DEFAULT NULL,
	`quality` VARCHAR(10) NULL DEFAULT NULL,
  	`icon` VARCHAR(255) NULL DEFAULT NULL,
  	`item_name` TEXT NULL,
	`image` TEXT NULL,
	`text` TEXT NULL,
	`languages` TEXT NULL,
  	`added_date` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  	`added_by` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  	`update_date` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  	`update_by` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
DEFAULT CHARSET=utf8 COLLATE=utf8_bin;",
));

?>