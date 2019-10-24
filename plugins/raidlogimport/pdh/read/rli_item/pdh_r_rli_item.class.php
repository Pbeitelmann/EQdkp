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
if(!class_exists('pdh_r_rli_item')) {
class pdh_r_rli_item extends pdh_r_generic {
	public static function __shortcuts() {
		$shortcuts = array('pdc', 'db');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	private $data = array();
	public $hooks = array('rli_item_update');
	
	public function init() {
		$this->data = $this->pdc->get('pdh_rli_item');
		if(!$this->data) {
			$sql = "SELECT item_id, itempool_id, event_id FROM __raidlogimport_item2itempool;";
			$objQuery = $this->db->query($sql);
			if ($objQuery){
				while($row = $objQuery->fetchAssoc()) {
					$this->data[$row['item_id']][$row['event_id']] = $row['itempool_id'];
				}
			} else {
				$this->data = array();
				return false;
			}
			
			$this->pdc->put('pdh_rli_item', $this->data, null);
		}
		return true;
	}
	
	public function reset() {
		unset($this->data);
		$this->pdc->del('pdh_rli_item');
		$this->init();
	}
	
	public function get_id_list() {
		return array_keys($this->data);
	}
	
	public function get_itempool($item_id, $event_id) {
		if(!isset($this->data[$item_id]) && !isset($this->data[$item_id][$event_id])) return false;
		return $this->data[$item_id][$event_id];
	}
}
}

?>