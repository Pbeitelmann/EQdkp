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
if(!class_exists('pdh_r_rli_zone')) {
class pdh_r_rli_zone extends pdh_r_generic {
	public static function __shortcuts() {
		$shortcuts = array('pdc', 'db', 'config', 'user', 'game', 'pdh');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	private $data = array();
	public $hooks = array('rli_zone_update');
	
	public function init() {
		$this->data = $this->pdc->get('pdh_rli_zone');
		if(!$this->data) {
			$sql = "SELECT zone_id, zone_string, zone_event, zone_timebonus, zone_diff, zone_sort, zone_active FROM __raidlogimport_zone;";
			$objQuery = $this->db->query($sql);
			if ($objQuery){
				while($row = $objQuery->fetchAssoc()) {
					$this->data[$row['zone_id']]['string'] = explode($this->config->get('bz_parse', 'raidlogimport'), $row['zone_string']);
					$this->data[$row['zone_id']]['event'] = $row['zone_event'];
					$this->data[$row['zone_id']]['timebonus'] = $row['zone_timebonus'];
					$this->data[$row['zone_id']]['diff'] = $row['zone_diff'];
					$this->data[$row['zone_id']]['sort'] = $row['zone_sort'];
					$this->data[$row['zone_id']]['active'] = ($row['zone_active']) ? 1 : 0;
				}
			} else {
				$this->data = array();
				return false;
			}

			$this->pdc->put('pdh_rli_zone', $this->data, null);
		}
		return true;
	}
	
	public function reset() {
		unset($this->data);
		$this->pdc->del('pdh_rli_zone');
	}
	
	public function get_id_list($active_only=true) {
		if($active_only) {
			$out = array();
			foreach($this->data as $id => $data) {
				if($data['active']) $out[] = $id;
			}
			return $out;
		}
		return array_keys($this->data);
	}
	
	public function get_id_string($string, $diff) {
		foreach($this->data as $id => $data) {
			if(in_array($string, $data['string']) AND ($diff == 0 OR $data['diff'] == 0 OR $diff == $data['diff'])) {
				return $id;
			}
		}
		return false;
	}
	
	public function get_string($id) {
		return $this->data[$id]['string'];
	}
	
	public function get_html_string($id) {
		return implode(', ', $this->data[$id]['string']).$this->get_html_diff($id);
	}
	
	public function get_event($id) {
		return $this->data[$id]['event'];
	}
	
	public function get_html_event($id, $with_icon=true) {
		$icon = ($with_icon) ? $this->game->decorate('events', array($this->get_event($id))) : '';
		return $icon.$this->pdh->get('event', 'name', array($this->get_event($id)));
	}
	
	public function get_eventbystring($string) {
		foreach($this->data as $id => $data) {
			if(in_array($string, $data['string'])) {
				return $this->get_event($id);
			}
		}
		return false;
	}
	
	public function get_timebonus($id) {
		return $this->data[$id]['timebonus'];
	}
	
	public function get_diff($id) {
		return $this->data[$id]['diff'];
	}
	
	public function get_html_diff($id) {
		return ($this->get_diff($id)) ? ' &nbsp; ('.$this->user->lang('diff_'.$this->get_diff($id)).')' : '';
	}
	
	public function get_sort($id) {
		return $this->data[$id]['sort'];
	}
	
	public function get_active($id) {
		return $this->data[$id]['active'];
	}
}
}

?>