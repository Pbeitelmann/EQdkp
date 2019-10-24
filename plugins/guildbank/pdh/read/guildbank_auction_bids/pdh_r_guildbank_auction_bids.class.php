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

if (!class_exists('pdh_r_guildbank_auction_bids')){
	class pdh_r_guildbank_auction_bids extends pdh_r_generic{
		private $data;

		public $hooks = array(
			'guildbank_auction_bid_update'
		);

		public $presets = array(
			'gb_biddate'		=> array('date',			array('%bid_id%'), array()),
			'gb_bidmember'		=> array('member',			array('%bid_id%'), array()),
			'gb_bidvalue'		=> array('bidvalue',		array('%bid_id%'), array()),
			'gb_bidhibidder'	=> array('highest_bidder',	array('%auction_id%'), array()),
			'gb_bidhivalue'		=> array('highest_value',	array('%auction_id%'), array()),
		);

		public function reset(){
			$this->pdc->del('pdh_guildbank_auction_bids_table');
			unset($this->data);
		}

		public function init(){
			// try to get from cache first
			$this->data = $this->pdc->get('pdh_guildbank_auction_bids_table');
			if($this->data !== NULL){
				return true;
			}

			// empty array as default
			$this->data = array();

			// read all guildbank_fields entries from db
			$sql = 'SELECT * FROM `__guildbank_auction_bids` ORDER BY bid_id ASC;';
			$result = $this->db->query($sql);
			if ($result){
				// add row by row to local copy
				while (($row = $result->fetchAssoc())){
					$this->data[(int)$row['bid_id']] = array(
						'id'			=> (int)$row['bid_id'],
						'auctionid'		=> (int)$row['bid_auctionid'],
						'date'			=> (int)$row['bid_date'],
						'memberid'		=> (int)$row['bid_memberid'],
						'bidvalue'		=> (int)$row['bid_bidvalue'],
					);
				}
			}

			// add data to cache
			$this->pdc->put('pdh_guildbank_auction_bids_table', $this->data, null);
			return true;
		}

		public function get_id_list($auctionid=0){
			if (is_array($this->data)){
				$ids	= array_keys($this->data);
				// filter future
				foreach($ids as $key => $id) {
					if(($auctionid && $this->get_auctionid($id) != $auctionid)){
						unset($ids[$key]);
					}
				}
				return $ids;
			}
			return array();
		}

		public function get_bids_byauction($auctionID){
			return $this->get_id_list($auctionID);
		}

		public function get_amount_bids($auctionID){
			return count($this->get_bids_byauction($auctionID));
		}

		public function get_bidvalues_byauction($auctionID){
			$auctionlist	= $this->get_bids_byauction($auctionID);
			$bidvalues		= array();
			foreach($auctionlist as $bid_id){
				$bidvalues[$this->get_memberid($bid_id)] = $this->get_bidvalue($bid_id);
			}
			return $bidvalues;
		}

		public function get_highest_bidder($auctionID, $raw=false, $markwinner=false){
			$bidvalues	= $this->get_bidvalues_byauction($auctionID);
			if(is_array($bidvalues) && count($bidvalues) == 0){
				return ($raw) ? 0 : '<i class="fa fa-gavel"></i> '.$this->user->lang('gb_bids_nobids');
			}
			$bidders	= array_keys($bidvalues, max($bidvalues));
			if(!$raw){
				$bidder_html		= array();
				$auctionended	= ($this->pdh->get('guildbank_auctions', 'atime_left', array($auctionID)) > 0) ? false : true;
				if(is_array($bidders) && count($bidders) > 0){
					foreach($bidders as $bidderID){
						$bidder_html[]	= (($markwinner && $auctionended) ? '<i class="fa fa-trophy"></i> ' : '').$this->pdh->get('member', 'name', array($bidderID));
					}
				}else{
					$bidder_html[]	= '<i class="fa fa-gavel"></i> '.$this->user->lang('gb_bids_nobids');
				}
				return implode(', ', $bidder_html);
			}
			return $bidders;
		}

		public function get_highest_value($auctionID){
			$bidvalues	= $this->get_bidvalues_byauction($auctionID);
			$max		= (is_array($bidvalues) && count($bidvalues) > 0) ? max($bidvalues) : 0;
			return ((int)$max > 0) ? $max : 0;
		}

		public function get_auctionid($id){
			return (isset($this->data[$id]) && $this->data[$id]['auctionid']) ? $this->data[$id]['auctionid'] : 0;
		}

		public function get_date($id){
			if(isset($this->data[$id]) && $this->data[$id]['date']){
				return $this->data[$id]['date'];
			}
			return 0;
		}

		public function get_html_date($id, $raw=false){
			if(isset($this->data[$id]) && $this->data[$id]['date']){
				return $this->time->user_date($this->data[$id]['date'], true, false, true);
			}
			return 0;
		}

		public function get_member($id){
			if(isset($this->data[$id]) && $this->data[$id]['memberid']){
				return $this->pdh->get('member', 'name', array($this->data[$id]['memberid']));
			}
			return '';
		}

		public function get_memberid($id){
			return (isset($this->data[$id]) && $this->data[$id]['memberid']) ? $this->data[$id]['memberid'] : 0;
		}

		public function get_bidvalue($id){
			return (isset($this->data[$id]) && $this->data[$id]['bidvalue']) ? $this->data[$id]['bidvalue'] : 0;
		}
	} //end class
} //end if class not exists
?>
