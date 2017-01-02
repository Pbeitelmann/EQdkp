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

if (!class_exists('pdh_w_guildbank_banker')){
	class pdh_w_guildbank_banker extends pdh_w_generic {

		public function add($intID, $strName, $intMoney, $intBankChar, $strNote){
			$resQuery = $this->db->prepare("INSERT INTO __guildbank_banker :p")->set(array(
				'banker_name'			=> $strName,
				'banker_bankchar'		=> $intBankChar,
				'banker_note'			=> $strNote
			))->execute();

			$id = $resQuery->insertId;
			//($intID, $intBanker, $intChar, $intItem, $intDKP, $intValue, $strSubject)
			$this->pdh->put('guildbank_transactions', 'add', array(0, $id, $intBankChar, 0, 0, $intMoney, 'gb_banker_added'));
			$this->pdh->enqueue_hook('guildbank_banker_update');

			if ($resQuery) return $id;
			return false;
		}

		public function update($intID, $strName, $intMoney, $intBankChar, $strNote){
			$resQuery = $this->db->prepare("UPDATE __guildbank_banker :p WHERE banker_id=?")->set(array(
				'banker_name'			=> $strName,
				'banker_bankchar'		=> $intBankChar,
				'banker_note'			=> $strNote
			))->execute($intID);
			$this->pdh->put('guildbank_transactions', 'update_money', array($intID, $intMoney));
			$this->pdh->enqueue_hook('guildbank_banker_update');
			if ($resQuery) return $intID;
			return false;
		}

		public function delete($intID){
			$this->db->prepare("DELETE FROM __guildbank_banker WHERE banker_id=?")->execute($intID);
			$this->pdh->put('guildbank_transactions', 'delete_bybankerid', array($intID));
			$this->pdh->enqueue_hook('guildbank_banker_update');
			return true;
		}

		public function truncate(){
			$this->db->query("TRUNCATE __guildbank_banker");
			$this->pdh->enqueue_hook('guildbank_banker_update');
			return true;
		}
	} //end class
} //end if class not exists
?>
