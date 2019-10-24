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

class wbb41_pn extends gen_class {
	
	public function getUnreadMessageCount($arrBridgeUserdata){
		$intUnread = 0;
		
		$objQuery = $this->bridge->bridgedb->prepare('SELECT * FROM __conversation_to_user WHERE hideConversation = 0 AND participantID = ?')->execute($arrBridgeUserdata['id']);
		if($objQuery){
			$arrConversationIDs = array();
			$arrLastVisits = array();
			while($row = $objQuery->fetchAssoc()){
				$arrConversationIDs[] = $row['conversationID'];
				$arrLastVisits[$row['conversationID']] = $row['lastVisitTime'];
			}

			if(count($arrConversationIDs)){
				$objQuery2 = $this->bridge->bridgedb->prepare('SELECT * FROM __conversation_message WHERE conversationID :in ORDER BY messageID DESC')->in($arrConversationIDs)->execute();
				if($objQuery2){
					$arrConvDone = array();
					while($row = $objQuery2->fetchAssoc()){
						if(!in_array($row['conversationID'], $arrConvDone)){
							$intMessageTime = (int)$row['time'];
							if($intMessageTime > $arrLastVisits[$row['conversationID']]){
								$intUnread++;
							}
							
							$arrConvDone[] = $row['conversationID'];
						}
						
					}
				}
			}
			
			
			return $intUnread;
		}
		return false;
	}
	
	public function getLink($arrBridgeUserdata){
		$strURL = $this->config->get('cmsbridge_url');
		if (substr($strURL, -1) != "/"){
			$strURL .= '/';
		}
		return $strURL.'index.php?conversation-list/';
	}
}