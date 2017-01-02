<?php
/*	Project:	EQdkp-Plus
 *	Package:	Guildbanker Plugin
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

if (!defined('EQDKP_INC')){
	header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

if (!class_exists('update_guildbank_103')){
	class update_guildbank_103 extends sql_update_task{

		public $author		= 'Wallenium';
		public $version		= '1.0.3';    // new version
		public $name		= 'Guild Bank 1.0.3 Update';
		public $type		= 'plugin_update';
		public $plugin_path	= 'guildbank'; // important!

		/**
		* Constructor
		*/
		public function __construct(){
			parent::__construct();

			// init language
			$this->langs = array(
				'english' => array(
					'update_guildbank_103' => 'Guild Banker 1.0.3 Update Package',
					// SQL
					1 => 'Add table field for type to transaction table',
				),
				'german' => array(
					'update_guildbank_103' => 'Guild Banker 1.0.3 Update Paket',
					// SQL
					1 => 'Add table field for type to transaction table',
				),
			);

			// init SQL querys
			$this->sqls = array(
				1 => 'ALTER TABLE __guildbank_transactions ADD COLUMN `ta_type` tinyint(1) default 0 AFTER `ta_id`;',
			);
		}

	}
}
?>
