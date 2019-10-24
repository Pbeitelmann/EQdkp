<?php
/*	Project:	EQdkp-Plus
 *	Package:	GuildRequest Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
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

/*+----------------------------------------------------------------------------
  | pdh_w_guildrequest_fields
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_w_guildrequest_fields'))
{
	class pdh_w_guildrequest_fields extends pdh_w_generic{

		public function add($intID, $strType, $strName, $strHelp, $arrOptions, $intSortID, $intRequired, $intInList = 0, $dep_field='', $dep_value=''){
			$objQuery = $this->db->prepare("INSERT INTO __guildrequest_fields :p")->set(array(
				'id'		=> $intID,
				'type'		=> $strType,
				'name'		=> $strName,
				'help'		=> $strHelp,
				'options'	=> serialize($arrOptions),
				'sortid' 	=> $intSortID,
				'required' 	=> $intRequired,
				'in_list'	=> $intInList,
				'dep_field' => $dep_field,
				'dep_value' => $dep_value,
			))->execute();
		
			$this->pdh->enqueue_hook('guildrequest_fields_update');
			if ($objQuery) return $objQuery->insertId;
		
			return false;
		}
	
		public function update($intID, $strType, $strName, $strHelp, $arrOptions, $intSortID, $intRequired, $intInList = 0, $dep_field='', $dep_value=''){
			$objQuery = $this->db->prepare("UPDATE __guildrequest_fields :p WHERE id=?")->set(array(
				'type'		=> $strType,
				'name'		=> $strName,
				'help'		=> $strHelp,
				'options'	=> serialize($arrOptions),
				'sortid' 	=> $intSortID,
				'required' 	=> $intRequired,
				'in_list'	=> $intInList,
				'dep_field' => $dep_field,
				'dep_value' => $dep_value,
			))->execute($intID);
		
			$this->pdh->enqueue_hook('guildrequest_fields_update');
			if ($objQuery) return $intID;
		
			return false;
		}
	
		public function delete($intID){
			$this->db->prepare("DELETE FROM __guildrequest_fields WHERE id=?")->execute($intID);
			$this->pdh->enqueue_hook('guildrequest_fields_update');
			return true;
		}
	
		public function truncate(){
			$this->db->query("TRUNCATE __guildrequest_fields");
			$this->pdh->enqueue_hook('guildrequest_fields_update');
			return true;
		}

	} //end class
} //end if class not exists

?>