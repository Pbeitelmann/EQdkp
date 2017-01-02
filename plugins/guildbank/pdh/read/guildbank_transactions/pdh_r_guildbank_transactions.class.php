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

if (!class_exists('pdh_r_guildbank_transactions')){
	class pdh_r_guildbank_transactions extends pdh_r_generic{

		public static function __shortcuts() {
			$shortcuts = array('money' => 'gb_money');
			return array_merge(parent::$shortcuts, $shortcuts);
		}

		private $data;
		private $summ;
		private $itemcost;
		private $bankertransactions;

		public $hooks = array(
			'guildbank_items_update'
		);

		public $presets = array(
			'gb_tdate'		=> array('date',		array('%trans_id%'), array()),
			'gb_titem'		=> array('item',		array('%trans_id%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'), array()),
			'gb_tbuyer'		=> array('char',		array('%trans_id%'), array()),
			'gb_tsubject'	=> array('subject',		array('%trans_id%'), array()),
			'gb_tbanker'	=> array('banker',		array('%trans_id%'), array()),
			'gb_tdkp'		=> array('dkp',			array('%trans_id%'), array()),
			'gb_tvalue'		=> array('value',		array('%trans_id%'), array()),
			'gb_tedit'		=> array('edit',		array('%trans_id%'), array()),
		);

		public function reset(){
			$this->pdc->del('pdh_guildbank_ta_table.transactions');
			$this->pdc->del('pdh_guildbank_ta_table.summ');
			$this->pdc->del('pdh_guildbank_ta_table.itemcost');
			$this->pdc->get('pdh_guildbank_ta_table.bankertransactions');
			unset($this->data);
			unset($this->summ);
			unset($this->itemcost);
			unset($this->bankertransactions);
		}

		public function init(){
			// try to get from cache first
			$this->data					= $this->pdc->get('pdh_guildbank_ta_table.transactions');
			$this->summ					= $this->pdc->get('pdh_guildbank_ta_table.summ');
			$this->itemcost				= $this->pdc->get('pdh_guildbank_ta_table.itemcost');
			$this->bankertransactions	= $this->pdc->get('pdh_guildbank_ta_table.bankertransactions');
			if($this->data !== NULL && $this->summ !== NULL && $this->itemcost !== NULL && $this->bankertransactions !== NULL){
				return true;
			}

			// empty array as default
			$this->data = $this->summ = $this->itemcost = $this->bankertransactions = array();

			$sql = 'SELECT * FROM `__guildbank_transactions` ORDER BY ta_id ASC;';
			$result = $this->db->query($sql);
			if ($result){
				// add row by row to local copy
				while (($row = $result->fetchAssoc())){
					$this->data[(int)$row['ta_id']] = array(
						'id'			=> (int)$row['ta_id'],
						'type'			=> (int)$row['ta_type'],
						'banker'		=> (int)$row['ta_banker'],
						'char'			=> (int)$row['ta_char'],
						'item'			=> (int)$row['ta_item'],
						'dkp'			=> (int)$row['ta_dkp'],
						'value'			=> (int)$row['ta_value'],
						'subject'		=> $row['ta_subject'],
						'date'			=> (int)$row['ta_date'],
					);

					$this->bankertransactions[(int)$row['ta_banker']][(int)$row['ta_id']] = (int)$row['ta_id'];

					if((int)$row['ta_type'] != 1){
						if(!isset($this->summ[(int)$row['ta_banker']])){ $this->summ[(int)$row['ta_banker']] = 0;}
						$this->summ[(int)$row['ta_banker']] += (int)$row['ta_value'];
					}

					// item costs in money
					if((int)$row['ta_item'] > 0 && $row['ta_subject'] == 'gb_item_added'){
						$this->itemcost[(int)$row['ta_item']] = $row['ta_value'];
					}
				}
				#$this->db->free_result($result);
			}

			// add data to cache
			$this->pdc->put('pdh_guildbank_ta_table.transactions',			$this->data,				null);
			$this->pdc->put('pdh_guildbank_ta_table.summ',					$this->summ,				null);
			$this->pdc->put('pdh_guildbank_ta_table.itemcost',				$this->itemcost,			null);
			$this->pdc->put('pdh_guildbank_ta_table.bankertransactions',	$this->bankertransactions,	null);
			return true;
		}

		public function get_id_list($bankerID=0){
			if($bankerID > 0){
				if (isset($this->bankertransactions[$bankerID]) && is_array($this->bankertransactions[$bankerID])){
					return array_keys($this->bankertransactions[$bankerID]);
				}
			}else{
				if (is_array($this->data)){
					return array_keys($this->data);
				}
			}
			return array();
		}

		public function get_char($id, $raw=false){
			if($raw){
				return (isset($this->data[$id]) && $this->data[$id]['char'] > 0) ? $this->data[$id]['char'] : 0;
			}
			return (isset($this->data[$id]) && $this->data[$id]['char'] > 0) ? $this->pdh->get('member', 'name', array($this->data[$id]['char'])) : '--';
		}

		public function get_banker($id, $raw=false){
			if($raw){
				return (isset($this->data[$id]) && $this->data[$id]['banker'] > 0) ? $this->data[$id]['banker'] : 0;
			}
			return (isset($this->data[$id]) && $this->data[$id]['char'] > 0) ? $this->pdh->get('guildbank_banker', 'name', array($this->data[$id]['banker'])) : '--';
		}

		public function get_item($id, $raw=false){
			if($raw){
				return (isset($this->data[$id]) && $this->data[$id]['item'] > 0) ? $this->data[$id]['item'] : 0;
			}
			return (isset($this->data[$id]) && $this->data[$id]['item'] > 0) ? $this->pdh->get('guildbank_items', 'name', array($this->data[$id]['item'])) : '--';
		}

		public function get_html_item($id, $lang=false, $direct=0, $onlyicon=0, $noicon=false, $in_span=false) {
			if(isset($this->data[$id]) && $this->data[$id]['item'] > 0){
				return $this->pdh->get('guildbank_items', 'itt_itemname', array($this->data[$id]['item'], $lang, $direct, $onlyicon, $noicon, $in_span));
			}
			return '--';
		}

		public function get_value($id, $raw=false){
			if($raw){
				return (isset($this->data[$id]) && $this->data[$id]['value'] > 0) ? $this->data[$id]['value'] : 0;
			}
			return $this->money->fields($this->data[$id]['value']);
		}

		public function get_itemvalue($itemid){
			return (isset($this->itemcost[$itemid]) && $this->itemcost[$itemid] > 0) ? $this->itemcost[$itemid] : 0;
		}

		public function get_transaction_id($itemid){
			if(is_array($this->data) && count($this->data) > 0){
				foreach($this->data as $ta_data){
					if($ta_data['item'] == $itemid){
						return $itemid;
					}
				}
			}
			return 0;
		}

		public function get_money_summ($bankid){
			return (isset($this->summ[$bankid]) && $this->summ[$bankid] > 0) ? $this->summ[$bankid] : 0;
		}

		public function get_money_summ_all(){
			return array_sum($this->summ);
		}

		/*public function get_money($bankid){
			return (isset($this->startvalues[$bankid]) && $this->startvalues[$bankid] > 0) ? $this->startvalues[$bankid] : 0;
		}*/

		public function get_dkp($id){
			return (isset($this->data[$id]) && $this->data[$id]['dkp'] > 0) ? $this->data[$id]['dkp'] : 0;
		}

		public function get_deletename($id){
			if($id > 0){
				return $this->get_subject($id).' - '.$this->get_item($id);
			}
			return 'undefined';
		}

		public function get_itemdkp($itemid){
			if(is_array($this->data) && count($this->data) > 0){
				foreach($this->data as $ta_data){
					if($ta_data['item'] == $itemid){
						return (isset($this->data[$ta_data['id']]) && $this->data[$ta_data['id']]['dkp'] > 0) ? $this->data[$ta_data['id']]['dkp'] : 0;
					}
				}
			}
		}

		public function get_date($id){
			return (isset($this->data[$id]) && $this->data[$id]['date'] > 0) ? $this->data[$id]['date'] : 0;
		}

		public function get_html_date($id){
			return (isset($this->data[$id]) && $this->data[$id]['date'] > 0) ? $this->time->user_date($this->data[$id]['date']) : '--';
		}

		public function get_subject($id){
			return (isset($this->data[$id]) && $this->data[$id]['subject']) ? (($this->user->lang($this->data[$id]['subject'])) ? $this->user->lang($this->data[$id]['subject']) : $this->data[$id]['subject']) : 'undefined';
		}

		public function get_edit($id){
			$mode	= ($this->get_item($id, true) > 0) ? 'edit_item' : 'edit_transaction';
			$myid 	= ($this->get_item($id, true) > 0) ? $this->get_item($id, true) : $id;
			return '<a href="javascript:'.$mode.'(\''.$myid.'\');"><i class="fa fa-pencil fa-lg" title="'.$this->user->lang('edit').'"></i></a>';
		}
	} //end class
} //end if class not exists
?>
