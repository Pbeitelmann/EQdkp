<?php
/*	Project:	EQdkp-Plus
 *	Package:	RaidLogImport Plugin
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

if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 Not Found');
	exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');
if (!class_exists('update_raidlogimport_0703')) {
class update_raidlogimport_0703 extends sql_update_task {
	public $author		= 'Hoofy';
	public $version		= '0.7.0.3';
	public $name		= 'Raidlogimport 0.7.0.3 Update';
	public $type		= 'plugin_update';
	public $plugin_path	= 'raidlogimport';
	
	private $data		= array();
	
	public static function __shortcuts() {
		$shortcuts = array('config');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}
	
	// init language
	public $langs = array(
		'english' => array(
			'update_raidlogimport_0703' => 'Raidlogimport 0.7.0.3 Update Package',
			'update_function' => 'Added configs',
		),
		'german' => array(
			'update_raidlogimport_0703' => 'Raidlogimport 0.7.0.3 Update Package',
			'update_function' => 'Configs hinzugefügt',
		),
	);
	
	public function update_function() {
		$cfgs = array('autocreate_bosses', 'autocreate_zones');
		foreach($cfgs as $cfg) {
			$this->config->set($cfg, 1, 'raidlogimport');
		}
		return true;
	}
}
}

?>