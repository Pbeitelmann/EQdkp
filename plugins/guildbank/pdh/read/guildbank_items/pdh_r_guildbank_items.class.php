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

if (!class_exists('pdh_r_guildbank_items')){
	class pdh_r_guildbank_items extends pdh_r_generic{

		public static function __shortcuts() {
			$shortcuts = array('money' => 'gb_money');
			return array_merge(parent::$shortcuts, $shortcuts);
		}

		private $data;

		public $hooks = array(
			'guildbank_items_update'
		);

		public $presets = array(
			'gb_idate'		=> array('html_date',	array('%item_id%'), array()),
			'gb_iname'		=> array('name',		array('%item_id%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'), array()),
			'gb_iamount'	=> array('amount',		array('%item_id%'), array()),
			'gb_itype'		=> array('type',		array('%item_id%'), array()),
			'gb_iedit'		=> array('edit',		array('%item_id%'), array()),
			'gb_ivalue'		=> array('value',		array('%item_id%'), array()),
			'gb_icost'		=> array('value',		array('%item_id%'), array()),
			'gb_ivalue_a'	=> array('value_a',		array('%item_id%'), array()),
			'gb_irarity'	=> array('rarity',		array('%item_id%'), array()),
			'gb_ibanker'	=> array('banker_name',	array('%item_id%'), array()),
			'gb_idkp'		=> array('dkp',			array('%item_id%'), array()),
			'gb_ishop'		=> array('shoplink',	array('%item_id%'), array()),
		);

		public function reset(){
			$this->pdc->del('pdh_guildbank_items_table.items');
			$this->pdc->del('pdh_guildbank_items_table.banker_items');
			unset($this->data);
			unset($this->banker_items);
		}

		public function init(){
			// try to get from cache first
			$this->data			= $this->pdc->get('pdh_guildbank_items_table.items');
			$this->banker_items	= $this->pdc->get('pdh_guildbank_items_table.banker_items');
			if($this->data !== NULL && $this->banker_items !== NULL){
				return true;
			}

			// empty array as default
			$this->data = $this->banker_items = array();

			$sql = 'SELECT * FROM `__guildbank_items` ORDER BY item_id ASC;';
			$result = $this->db->query($sql);
			if ($result){
				// add row by row to local copy
				while (($row = $result->fetchAssoc())){
					$this->data[(int)$row['item_id']] = array(
						'id'			=> (int)$row['item_id'],
						'banker'		=> (int)$row['item_banker'],
						'name'			=> $row['item_name'],
						'type'			=> $row['item_type'],
						'rarity'		=> (int)$row['item_rarity'],
						'amount'		=> (int)$row['item_amount'],
						'date'			=> (int)$row['item_date'],
						'sellable'		=> (int)$row['item_sellable'],
					);
					$this->banker_items[(int)$row['item_banker']][(int)$row['item_id']]	= $row['item_name'];
				}
			}

			// add data to cache
			$this->pdc->put('pdh_guildbank_items_table.items', $this->data, null);
			$this->pdc->put('pdh_guildbank_items_table.banker_items', $this->banker_items, null);
			return true;
		}

		public function get_id_list($bankerID = 0, $priority = 0, $type = 0, $rarity = 0, $sellable = 0){
			$data	= ((int)$bankerID > 0) ? $this->banker_items[$bankerID] : $this->data;
			if (is_array($data)){
				// filter the output
				if($priority > 0 || $type > 0 || $rarity > 0 || $sellable > 0){
					foreach($data as $itemid=>$itemvalues) {
						if(($type > 0 && $this->get_type($itemid) != $type) ||	($priority > 0 && $this->get_priority($itemid) != $priority) || ($rarity > 0 && $this->get_rarity($itemid) != $rarity) || ($sellable > 0 && $this->get_sellable($itemid) != '1')){
							unset($data[$itemid]);
						}
					}
				}
				$data	= array_keys($data);
				return $data;
			}
			return array();
		}

		public function get_date($id){
			return (isset($this->data[$id]) && $this->data[$id]['date'] > 0) ? $this->data[$id]['date'] : 0;
		}

		public function get_sellable($id){
			return (isset($this->data[$id]['sellable']) && $this->data[$id]['sellable'] > 0) ? $this->data[$id]['sellable'] : 0;
		}

		public function get_html_date($id){
			return $this->time->user_date($this->get_date($id));
		}

		public function get_amount($id){
			return (isset($this->data[$id]) && $this->data[$id]['amount'] > 0) ? $this->data[$id]['amount'] : 0;
		}

		public function get_rarity($id, $raw=false){
			if($raw){
				return (isset($this->data[$id]) && $this->data[$id]['rarity'] > 0) ? $this->data[$id]['rarity'] : 0;
			}
			return (isset($this->data[$id]) && $this->data[$id]['rarity'] > 0) ? $this->get_itemrarity($this->data[$id]['rarity']) : 0;
		}

		public function get_name($id){
			return (isset($this->data[$id]) && $this->data[$id]['name']) ? $this->data[$id]['name'] : 'none';
		}

		public function get_deletetype($id){
			$tmp_id		= explode("_", $id);
			$real_id	= $tmp_id[1];
			$type		= $tmp_id[0];
			return array('id'=>$real_id, 'type'=>$type);
		}

		public function get_deletename($id){
			$deletetype	= $this->get_deletetype($id);
			if(isset($deletetype['id']) && isset($deletetype['type'])){
				if($deletetype['type'] == 'transaction'){
					return $this->pdh->get('guildbank_transactions', 'deletename', array($deletetype['id']));
				}else{
					return $this->get_name($deletetype['id']);
				}
			}
			return 'undefined';
		}

		public function get_type($id, $raw=false){
			if($raw){
				return (isset($this->data[$id]) && $this->data[$id]['type']) ? $this->data[$id]['type'] : 'none';
			}
			return (isset($this->data[$id]) && $this->data[$id]['type']) ? $this->get_itemtype($this->data[$id]['type']) : 'none';
		}

		public function get_itemtype($id=false){
			$gamefile_itemtype	= $this->game->callFunc('guildbank_itemtype', array());
			if($gamefile_itemtype && is_array($gamefile_itemtype) && count($gamefile_itemtype) > 0){
				return ($id) ? $gamefile_itemtype[$id] : $gamefile_itemtype;
			}else{
				return ($id) ? $this->user->lang(array('gb_a_type', $id)) : $this->user->lang('gb_a_type');
			}
		}

		public function get_itemrarity($id=0){
			$gamefile_itemrarity	= $this->game->callFunc('guildbank_itemrarity', array());
			if($gamefile_itemrarity && is_array($gamefile_itemrarity) && count($gamefile_itemrarity) > 0){
				return ($id > 0) ? $gamefile_itemrarity[$id] : $gamefile_itemrarity;
			}else{
				return ($id > 0) ? $this->user->lang(array('gb_a_rarity', $id)) : $this->user->lang('gb_a_rarity');
			}
		}

		public function get_edit($id){
			return '<a href="javascript:edit_item(\''.$id.'\');"><i class="fa fa-pencil fa-lg" title="'.$this->user->lang('edit').'"></i></a>';
		}

		public function get_value($id){
			return $this->money->fields($this->pdh->get('guildbank_transactions', 'itemvalue', array($id)));
		}

		public function get_value_a($id){
			return $this->money->fields($this->pdh->get('guildbank_transactions', 'itemvalue', array($id)));
		}

		public function get_dkp($id){
			return $this->pdh->get('guildbank_transactions', 'itemdkp', array($id));
		}

		public function get_banker($id){
			return (isset($this->data[$id]) && $this->data[$id]['banker']) ? $this->data[$id]['banker'] : 0;
		}

		public function get_banker_name($id){
			return $this->pdh->get('guildbank_banker', 'name', array($this->get_banker($id)));
		}

		public function get_itt_itemname($id, $lang=false, $direct=0, $onlyicon=0, $noicon=false, $in_span=false) {
			if(!isset($this->data[$id])) return false;
			if($this->config->get('infotooltip_use')) {
				$lang = (!$lang) ? $this->user->lang('XML_LANG') : $lang;
				$ext = '';
				if($direct) {
					$options = array(
						'url' => $this->root_path."infotooltip/infotooltip_feed.php?name=".urlencode(base64_encode($this->get_name($id)))."&lang=".$lang."&update=1&direct=1",
						'height' => '340',
						'width' => '400',
						'onclose' => $this->env->request
					);
					$this->jquery->Dialog("infotooltip_update", "Item-Update", $options);
					$ext = '<span style="cursor:pointer;" onclick="infotooltip_update()">Refresh</span>';
				}
				return infotooltip($this->get_name($id), 0, $lang, $direct, $onlyicon, $noicon, '', false, false, $in_span, false).$ext;
			}
			return $this->get_name($id);
		}

		public function get_html_name($item_id, $lang=false, $direct=0, $onlyicon=0, $noicon=false, $in_span=false) {
			return $this->get_itt_itemname($item_id, $lang, $direct, $onlyicon, $noicon, $in_span);
		}

		public function get_shoplink($id){
			if($this->get_sellable($id) > 0 && $this->user->check_auth('u_guildbank_shop', false)){
				return '<a href="javascript:open_shop(\''.$id.'\');"><i class="fa fa-shopping-cart fa-lg" title="'.$this->user->lang('gb_shop_icon_title').'"></i></a>';
			}
		}

		public function get_search($search){
			// empty search results
			$searchResults = array();

			// loop through the data array and fill search results
			/*if ($this->data && is_array($this->data)){
				$arrStatus = $this->user->lang('gr_status');

				foreach ($this->data as $id => $data){
					if (strpos($member, $search) !== false || strpos( $email, $search) !== false || strpos( $content, $search) !== false){
						$searchResults[] = array(
							'id'   => $this->time->user_date($data['tstamp'], true),
							'name' => $data['username'].'; '.$this->user->lang('status').': '.$arrStatus[$data['status']],
							'link' => $this->routing->build('ViewApplication', $data['username'], $id),
						);
					}
				}
			}*/
			return $searchResults;
		}

	} //end class
} //end if class not exists
?>
