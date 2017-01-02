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

if (!class_exists('pdh_w_guildbank_auctions')){
	class pdh_w_guildbank_auctions extends pdh_w_generic {

		public function add($intID, $intItemID, $intStartdate, $intDuration, $intBidsteps, $intStartvalue, $intAttendance, $intMultiDKP, $strNote='', $boolActive=1){
			$resQuery = $this->db->prepare("INSERT INTO __guildbank_auctions :p")->set(array(
				'auction_item'			=> $intItemID,
				'auction_startdate'		=> $intStartdate,
				'auction_duration'		=> $intDuration,
				'auction_bidsteps'		=> $intBidsteps,
				'auction_note'			=> $strNote,
				'auction_startvalue'	=> $intStartvalue,
				'auction_raidattendance'=> $intAttendance,
				'auction_multidkppool'	=> $intMultiDKP,
				'auction_active'		=> $boolActive,
			))->execute();

			$id = $resQuery->insertId;
			$this->pdh->enqueue_hook('guildbank_auction_update');

			if ($resQuery) return $id;
			return false;
		}

		public function update($intID, $intItemID, $intStartdate, $intDuration, $intBidsteps, $intStartvalue, $intAttendance, $intMultiDKP, $strNote='', $boolActive=1){
			$resQuery = $this->db->prepare("UPDATE __guildbank_auctions :p WHERE auction_id=?")->set(array(
				'auction_item'			=> $intItemID,
				'auction_startdate'		=> $intStartdate,
				'auction_duration'		=> $intDuration,
				'auction_bidsteps'		=> $intBidsteps,
				'auction_note'			=> $strNote,
				'auction_startvalue'	=> $intStartvalue,
				'auction_raidattendance'=> $intAttendance,
				'auction_multidkppool'	=> $intMultiDKP,
				'auction_active'		=> $boolActive,
			))->execute($intID);
			$this->pdh->enqueue_hook('guildbank_auction_update');
			if ($resQuery) return $intID;
			return false;
		}

		public function set_inactive($intID){
			$resQuery = $this->db->prepare("UPDATE __guildbank_auctions :p WHERE auction_id=?")->set(array(
				'auction_active'	=> 0,
			))->execute($intID);
			$this->pdh->enqueue_hook('guildbank_auction_update');
			return true;
		}

		public function delete($intID){
			$this->db->prepare("DELETE FROM __guildbank_auctions WHERE auction_id=?")->execute($intID);
			$this->pdh->put('guildbank_auction_bids', 'delete_byauction', array($intID));
			$this->pdh->enqueue_hook('guildbank_auction_update');
			return true;
		}

		public function truncate(){
			$this->db->query("TRUNCATE __guildbank_auctions");
			$this->pdh->enqueue_hook('guildbank_auction_update');
			return true;
		}
	} //end class
} //end if class not exists
?>
