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
if(!class_exists('pdh_w_rli_item')) {
class pdh_w_rli_item extends pdh_w_generic {
	public static function __shortcuts() {
		$shortcuts = array('pdh', 'db');
		return array_merge(parent::$shortcuts, $shortcuts);
	}
	
	public function add($item_id, $event_id, $itempool_id) {
		if($item_id <= 0 || $event_id <= 0 || $itempool_id <= 0) return false;
		if($this->pdh->get('rli_item', 'itempool', array($item_id, $event_id))) $this->delete($item_id, $event_id);
		$objQuery = $this->db->prepare("INSERT INTO __raidlogimport_item2itempool :p;")->set(array('item_id' => $item_id, 'event_id' => $event_id, 'itempool_id' => $itempool_id))->execute();
		
		if($objQuery) {
			$this->pdh->enqueue_hook('rli_item_update');
			return true;
		}
		return false;
	}
	
	public function delete($item_id, $event_id) {
		$objQuery = $this->db->prepare("DELETE FROM __raidlogimport_item2itempool WHERE event_id = ? AND item_id = ?;")->execute($event_id, $item_id);
		
		if($objQuery) {
			$this->pdh->enqueue_hook('rli_item_update');
			return true;
		}
		return false;
	}
}
}

?>