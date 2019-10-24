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
if(!class_exists('pdh_r_rli_boss')) {
class pdh_r_rli_boss extends pdh_r_generic {
	public static function __shortcuts() {
		$shortcuts = array('pdc', 'db', 'config', 'user', 'game', 'pdh');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	private $data = array();
	public $hooks = array('rli_boss_update');
	
	public function init() {
		global $pdc, $db, $core;
		$this->data = $this->pdc->get('pdh_rli_boss');
		if(!$this->data) {
			$sql = "SELECT boss_id, boss_string, boss_note, boss_bonus, boss_timebonus, boss_diff, boss_tozone, boss_sort, boss_active FROM __raidlogimport_boss;";
			$objQuery = $this->db->query($sql);
			if ($objQuery){
				while($row = $objQuery->fetchAssoc()) {
					$this->data[$row['boss_id']]['string'] = explode($this->config->get('bz_parse', 'raidlogimport'), $row['boss_string']);
					$this->data[$row['boss_id']]['note'] = $row['boss_note'];
					$this->data[$row['boss_id']]['bonus'] = $row['boss_bonus'];
					$this->data[$row['boss_id']]['timebonus'] = $row['boss_timebonus'];
					$this->data[$row['boss_id']]['diff'] = $row['boss_diff'];
					$this->data[$row['boss_id']]['sort'] = $row['boss_sort'];
					$this->data[$row['boss_id']]['tozone'] = $row['boss_tozone'];
					$this->data[$row['boss_id']]['active'] = ($row['boss_active']) ? 1 : 0;
				}
			} else {
				$this->data = array();
				return false;
			}
			
			$this->pdc->put('pdh_rli_boss', $this->data, null);
		}
		return true;
	}
	
	public function reset() {
		unset($this->data);
		$this->pdc->del('pdh_rli_boss');
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
				if(!$data['active'] && $data['tozone']) {
					$this->pdh->put('rli_zone', 'switch_inactive', array($data['tozone']));
					$this->pdh->process_hook_queue();
				}
				return $id;
			}
		}
		return false;
	}
	
	public function get_string($id) {
		return $this->data[$id]['string'];
	}
	
	public function get_html_string($id) {
		return implode(', ', $this->get_string($id)).$this->get_html_diff($id);
	}
	
	public function get_note($id) {
		return $this->data[$id]['note'];
	}
	
	public function get_html_note($id, $with_icon=true) {
		if(($this->config->get('event_boss', 'raidlogimport') & 1) AND is_numeric($this->get_note($id))) {
			$icon = ($with_icon) ? $this->game->decorate('events', array($this->get_note($id))) : '';
			return $icon.$this->pdh->get('event', 'name', array($this->get_note($id)));
		}
		$suffix = ($this->get_diff($id) AND $this->config->get('dep_match', 'raidlogimport') AND $this->game->get_game() == 'wow') ? $this->config->get('diff_'.$this->get_diff($id), 'raidlogimport') : '';
		return $this->get_note($id).$suffix;
	}
	
	public function get_bonus($id) {
		return $this->data[$id]['bonus'];
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
	
	public function get_tozone($id) {
		return $this->data[$id]['tozone'];
	}
	
	public function get_sort($id) {
		return $this->data[$id]['sort'];
	}
	
	public function get_bosses2zone($zone_id) {
		$bosses = array();
		foreach($this->data as $id => $data) {
			if(intval($data['tozone']) === intval($zone_id)) {
				$bosses[] = $id;
			}
		}
		return $bosses;
	}
}
}

?>