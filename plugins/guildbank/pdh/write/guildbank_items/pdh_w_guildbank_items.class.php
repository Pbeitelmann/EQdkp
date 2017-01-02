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

if (!class_exists('pdh_w_guildbank_items')){
	class pdh_w_guildbank_items extends pdh_w_generic {

		public function add($intID, $strBanker, $strName, $intRarity, $strType, $intAmount, $intDKP, $intMoney, $intChar, $intSellable=0, $strSubject='gb_item_added'){
			$resQuery = $this->db->prepare("INSERT INTO __guildbank_items :p")->set(array(
				'item_banker'		=> $strBanker,
				'item_date'			=> $this->time->time,
				'item_name'			=> $strName,
				'item_rarity'		=> $intRarity,
				'item_type'			=> $strType,
				'item_amount'		=> $intAmount,
				'item_sellable'		=> $intSellable,

			))->execute();
			$id = $resQuery->insertId;
			//($intID, $intBanker, $intChar, $intItem, $intDKP, $intValue, $strSubject)
			$this->pdh->put('guildbank_transactions', 'add', array(0, $strBanker, $intChar, $id, $intDKP, $intMoney, $strSubject, 1));
			$this->pdh->enqueue_hook('guildbank_items_update');
			if ($resQuery) return $id;
			return false;
		}

		public function update($intID, $strBanker, $strName, $intRarity, $strType, $intAmount, $intDKP, $intMoney, $intChar, $intSellable=0, $strSubject=''){
			$resQuery = $this->db->prepare("UPDATE __guildbank_items :p WHERE item_id=?")->set(array(
				'item_banker'		=> $strBanker,
				'item_date'			=> $this->time->time,
				'item_name'			=> $strName,
				'item_rarity'		=> $intRarity,
				'item_type'			=> $strType,
				'item_amount'		=> $intAmount,
				'item_sellable'		=> $intSellable,

			))->execute($intID);
			$this->pdh->put('guildbank_transactions', 'update_itemtransaction',	array($intID, $intMoney, $intDKP));
			$this->pdh->enqueue_hook('guildbank_items_update');
			if ($resQuery) return $intID;
			return false;
		}

		public function amount($intID, $intAmount){
			$resQuery = $this->db->prepare("UPDATE __guildbank_items :p WHERE item_id=?")->set(array(
				'item_amount'	=> $intAmount,
			))->execute($intID);
			$this->pdh->enqueue_hook('guildbank_items_update');
			if ($resQuery) return $intID;
			return false;
		}

		public function delete($intID){
			$this->db->prepare("DELETE FROM __guildbank_items WHERE item_id=?")->execute($intID);
			$auctions	= $this->pdh->get('guildbank_auction', 'auction_byitem', array($intID));
			if(is_array($auctions) && count($auctions) > 0){
				foreach($auctions as $auctionids){
					$this->pdh->put('guildbank_auctions', 'delete', array($auctionids));
				}
			}
			$this->pdh->enqueue_hook('guildbank_items_update');
			return true;
		}

		public function truncate(){
			$this->db->query("TRUNCATE __guildbank_items");
			$this->pdh->enqueue_hook('guildbank_items_update');
			return true;
		}
	} //end class
} //end if class not exists
?>
