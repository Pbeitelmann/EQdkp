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

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

if (!class_exists('update_guildrequest_020')){
	class update_guildrequest_020 extends sql_update_task{

		public $author		= 'GodMod';
		public $version		= '0.2.0';    // new version
		public $name		= 'Guildrequest 0.2.0 Update';
		public $type		= 'plugin_update';
		public $plugin_path	= 'guildrequest'; // important!

		/**
		* Constructor
		*/
		public function __construct(){
			parent::__construct();

			// init language
			$this->langs = array(
				'english' => array(
					'update_guildrequest_020' => 'GuildRequest 0.2.0 Update Package',
					1 => 'Alter guildrequest_fields table',
					2 => 'Alter guildrequest_fields table',
				),
				'german' => array(
					'update_guildrequest_020' => 'GuildRequest 0.2.0 Update Paket',
					1 => 'Ändere guildrequest_fields Tabelle',
					2 => 'Ändere guildrequest_fields Tabelle',
				),
			);

			// init SQL querys
			$this->sqls = array(
				1 => "ALTER TABLE `__guildrequest_fields` ADD COLUMN `dep_value` TEXT COLLATE utf8_bin NULL;",
				2 => "ALTER TABLE `__guildrequest_fields` ADD COLUMN `dep_field` INT(10) UNSIGNED NULL DEFAULT '0';",
			);
		}
	}
}
?>
