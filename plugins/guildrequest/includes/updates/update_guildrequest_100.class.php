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

if (!class_exists('update_guildrequest_100')){
	class update_guildrequest_100 extends sql_update_task{

		public $author		= 'GodMod';
		public $version		= '1.0.0';    // new version
		public $name		= 'Guildrequest 1.0.0 Update';
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
					'update_guildrequest_100' => 'GuildRequest 1.0.0 Update Package',
					'update_function' => 'Add Notifications',
				),
				'german' => array(
					'update_guildrequest_100' => 'GuildRequest 1.0.0 Update Paket',
					'update_function' => 'Füge Benachrichtigungen hinzu',
				),
			);

		}
		
		public function update_function(){
			$this->ntfy->addNotificationType('guildrequest_new_application', 'gr_notify_new_application', 'guildrequest', 1, 1, 1, 'gr_notify_new_application_grouped', 2, 'fa-pencil-square-o');
			$this->ntfy->addNotificationType('guildrequest_new_update', 'gr_notify_new_update', 'guildrequest', 1, 1, 1, 'gr_notify_new_update_grouped', 2, 'fa-pencil-square-o');
			$this->ntfy->addNotificationType('guildrequest_new_update_own', 'gr_notify_new_update_own', 'guildrequest', 1, 1, 1, 'gr_notify_new_update_own_grouped', 2, 'fa-pencil-square-o');	
			$this->ntfy->addNotificationType('guildrequest_open_applications', 'gr_notify_open', 'guildrequest', 0, 1, false, '', 0, 'fa-pencil-square-o');	
			
			$this->db->query("ALTER TABLE `__guildrequest_requests` ADD COLUMN `user_id` INT(11) UNSIGNED NULL DEFAULT '0';");
			return true;
		}
	}
}
?>
