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

if (!class_exists('pdh_w_guildbank_auction_bids')){
	class pdh_w_guildbank_auction_bids extends pdh_w_generic {

		public function add($intAuctionID, $intDate, $intMemberID, $intBidvalue){
			$resQuery = $this->db->prepare("INSERT INTO __guildbank_auction_bids :p")->set(array(
				'bid_auctionid'		=> $intAuctionID,
				'bid_date'			=> $intDate,
				'bid_memberid'		=> $intMemberID,
				'bid_bidvalue'		=> $intBidvalue,
			))->execute();

			$id = $resQuery->insertId;
			$this->pdh->enqueue_hook('guildbank_auction_bid_update');

			if ($resQuery) return $id;
			return false;
		}

		public function delete($intID){
			$this->db->prepare("DELETE FROM __guildbank_auction_bids WHERE bid_id=?")->execute($intID);
			$this->pdh->enqueue_hook('guildbank_auction_bid_update');
			return true;
		}

		public function delete_byauction($intID){
			$this->db->prepare("DELETE FROM __guildbank_auction_bids WHERE bid_auctionid=?")->execute($intID);
			$this->pdh->enqueue_hook('guildbank_auction_bid_update');
			return true;
		}

		public function truncate(){
			$this->db->query("TRUNCATE __guildbank_auction_bids");
			$this->pdh->enqueue_hook('guildbank_auction_bid_update');
			return true;
		}
	} //end class
} //end if class not exists
?>
