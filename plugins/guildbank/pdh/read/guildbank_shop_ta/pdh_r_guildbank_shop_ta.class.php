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
	die('Do not access this file directly.');
}

if (!class_exists('pdh_r_guildbank_shop_ta')){
	class pdh_r_guildbank_shop_ta extends pdh_r_generic{
		private $data;

		public $hooks = array(
			'guildbank_items_update'
		);

		public $presets = array(
		);

		public function reset(){
			$this->pdc->del('pdh_guildbank_shop_table');
			unset($this->data);
		}

		public function init(){
			// try to get from cache first
			$this->data = $this->pdc->get('pdh_guildbank_shop_table');
			if($this->data !== NULL){
				return true;
			}

			// empty array as default
			$this->data = array();

			// read all guildbank_fields entries from db
			$sql = 'SELECT * FROM `__guildbank_shop_ta` ORDER BY st_id ASC;';
			$result = $this->db->query($sql);
			if ($result){
				// add row by row to local copy
				while (($row = $result->fetchAssoc())){
					$this->data[(int)$row['st_id']] = array(
						'id'			=> (int)$row['st_id'],
						'itemid'		=> (int)$row['st_itemid'],
						'date'			=> (int)$row['st_date'],
						'value'			=> (int)$row['st_value'],
						'amount'		=> (int)$row['st_amount'],
						'buyer'			=> (int)$row['st_buyer'],
						'currency'		=> (int)$row['st_currency'],
					);
				}
				#$this->db->free_result($result);
			}

			// add data to cache
			$this->pdc->put('pdh_guildbank_shop_table', $this->data, null);
			return true;
		}

		public function get_id_list(){
			if (is_array($this->data)){
				return array_keys($this->data);
			}
			return array();
		}

		public function get_data($id){
			return (isset($this->data[$id])) ? $this->data[$id] : $this->data;
		}

		public function get_itemid($id){
			return (isset($this->data[$id]) && $this->data[$id]['itemid']) ? $this->data[$id]['itemid'] : 0;
		}

		public function get_item($id, $decorate=false){
			if($itemid = $this->get_itemid($id) > 0){
				return ($decorate) ? $this->pdh->get('guildbank_items', 'itt_itemname', array($this->data[$id]['itemid'], false, 0, 0, 0, 0)) : $this->pdh->get('guildbank_items', 'name', array($this->data[$id]['itemid']));
			}
			return 'Unknown';
		}

		public function get_value($id){
			return (isset($this->data[$id]) && $this->data[$id]['value']) ? $this->data[$id]['value'] : 0;
		}

		public function get_amount($id){
			return (isset($this->data[$id]) && $this->data[$id]['amount']) ? $this->data[$id]['amount'] : 0;
		}

		public function get_currency($id, $raw=false){
			return (isset($this->data[$id]) && $this->data[$id]['currency']) ? $this->data[$id]['currency'] : 0;
		}

		public function get_buyer($id, $raw=false){
			if($raw){
				return (isset($this->data[$id]) && $this->data[$id]['buyer']) ? $this->data[$id]['buyer'] : 0;
			}
			return (isset($this->data[$id]) && $this->data[$id]['buyer']) ? $this->pdh->get('member', 'name', array($this->data[$id]['buyer'])) : 'Unknown';
		}

		public function get_date($id, $raw=false){
			if($raw){
				return (isset($this->data[$id]) && $this->data[$id]['date'] > 0) ? $this->data[$id]['date'] : 0;
			}
			return (isset($this->data[$id]) && $this->data[$id]['date'] > 0) ? $this->time->user_date($this->data[$id]['date'], true) : '--';
		}
	} //end class
} //end if class not exists
?>
