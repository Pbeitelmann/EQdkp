<?php
/* Project: EQdkp-Plus
 * Package: Boardpns-Plugin
 * Link: http://eqdkp-plus.eu
 *
 * Copyright (C) 2006-2016 EQdkp-Plus Developer Team
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

class phpbb31_pn extends gen_class {
	
	public function getUnreadMessageCount($arrBridgeUserdata){
		$objQuery = $this->bridge->bridgedb->prepare('SELECT count(*) AS num_privmsgs FROM __privmsgs_to WHERE pm_new = 1 AND user_id = ?')->execute($arrBridgeUserdata['id']);
		if($objQuery){
			$arrResult = $objQuery->fetchAssoc();
			$intUnread = (int)$arrResult['num_privmsgs'];
			
			return $intUnread;
		}
		return false;
	}
	
	public function getLink($arrBridgeUserdata){
		$strURL = $this->config->get('cmsbridge_url');
		if (substr($strURL, -1) != "/"){
			$strURL .= '/';
		}
		
		return $strURL.'ucp.php?i=pm&folder=inbox';
	}
}