<?php
/*	Project:	EQdkp-Plus
 *	Package:	Local Itembase Plugin
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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}
				
if ( !class_exists( "pdh_w_localitembase" ) ) {
	class pdh_w_localitembase extends pdh_w_generic {
		
		public function insert($strGameID, $strIcon, $strQuality, $arrNames, $arrText, $arrImages, $arrLanguages){
			
			$arrQuery = array(
					'item_gameid'	=> $strGameID,
					'quality'		=> $strQuality,
					'icon'			=> $strIcon,
					'item_name'		=> serialize($arrNames),
					'image'			=> serialize($arrImages),
					'text'			=> serialize($arrText),
					'languages'		=> $arrLanguages,
					'added_date'	=> time(),
					'added_by'		=> $this->user->id,
			);
				
			$objQuery = $this->db->prepare("INSERT INTO __plugin_localitembase :p")->set($arrQuery)->execute();
				
			$this->pdh->enqueue_hook('localitembase_update');
		}
		
		public function update($id, $strGameID, $strIcon, $strQuality, $arrNames, $arrText, $arrImages, $arrLanguages){
				
			$arrQuery = array(
					'item_gameid'	=> $strGameID,
					'quality'		=> $strQuality,
					'icon'			=> $strIcon,
					'item_name'		=> serialize($arrNames),
					'image'			=> serialize($arrImages),
					'text'			=> serialize($arrText),
					'languages'		=> $arrLanguages,
					'update_date'	=> time(),
					'update_by'		=> $this->user->id,
			);
		
			$objQuery = $this->db->prepare("UPDATE __plugin_localitembase :p WHERE id=?")->set($arrQuery)->execute($id);
		
			$this->pdh->enqueue_hook('localitembase_update');
		}
		
		public function delete($id){
			$objQuery = $this->db->prepare("DELETE FROM __plugin_localitembase WHERE id=?")->execute($id);
			
			$this->pdh->enqueue_hook('localitembase_update');
		}
		

	}//end class
}//end if
?>