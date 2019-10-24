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

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | boardpns_portal_hook
  +--------------------------------------------------------------------------*/
if (!class_exists('boardpns_portal_hook'))
{
  class boardpns_portal_hook extends gen_class
  {

	/**
    * hook_portal
    * Do the hook 'portal'
    *
    * @return array
    */
	public function portal()
	{		
		$objBoardPnClass = $this->loadBoardClass();
		if(!$objBoardPnClass) return;
		
		$arrBridgeUserdata = $this->getUserdata();
		if(!$arrBridgeUserdata) return;
		
		$arrCacheHit = $this->pdc->get('plugin.boardpns.unread.'.$this->user->id, false, true);
		if($arrCacheHit === null){
			$intUnreadCount = $objBoardPnClass->getUnreadMessageCount($arrBridgeUserdata);
			if($intUnreadCount === false) return;
			
			$strBoardLink = $objBoardPnClass->getLink($arrBridgeUserdata);
			$strOutLink = $this->getLink($strBoardLink);
			
			//3 Minutes Cache Time
			$this->pdc->put('plugin.boardpns.unread.'.$this->user->id, array('count' => $intUnreadCount, 'url' => $strOutLink), 60*3, false, true);
			
		} else {
			$intUnreadCount = (int)$arrCacheHit['count'];
			$strOutLink  = $arrCacheHit['url'];
		}
					
		$strHrefText = '<span class="boardpn-container '.(($intUnreadCount) ? 'boardpn-new-messages' : 'boardpn-no-new-messages').'">';
		if($intUnreadCount > 0){
			$strHrefText .= '<i class="fa fa-lg fa-envelope boardpn-icon"></i> <span class="boardpn-text hiddenSmartphone">'.$this->user->lang('boardpns_messages');
			$strHrefText .= ' <span class="bubble-red">'.$intUnreadCount.'</span></span>';
		} else {
			$strHrefText .= '<i class="fa fa-lg fa-envelope-o boardpn-icon"></i> <span class="boardpn-text hiddenSmartphone">'.$this->user->lang('boardpns_messages').'</span>';
		}
		$strHrefText .= '</span>';
		
		$strOutLink['text'] = $strHrefText;
		
		$strHref = $this->core->createLink($strOutLink);
		$output = $strHref;
		
		$this->tpl->assign_block_vars('personal_area_addition', array(
			'TEXT' => $output,
		));
	}
	
	private function loadBoardClass(){
		$strBridge = $this->config->get('cmsbridge_type');
		if($this->config->get('cmsbridge_active') == '1'){
			if (file_exists($this->root_path.'plugins/boardpns/boards/'.$strBridge.'_pn.class.php')){
				include_once($this->root_path.'plugins/boardpns/boards/'.$strBridge.'_pn.class.php');
				$objBoardPnClass = register($strBridge.'_pn');
				return $objBoardPnClass;
			}
		}
		
		return false;
	}
	
	private function getUserdata(){
		if($this->config->get('cmsbridge_active') == '1'){
			$arrUserData = $this->bridge->get_userdata($this->user->data['username']);
			
			if(isset($arrUserData['id'])){
				return $arrUserData;
			}
		}
		return false;
	}
	
	private function getLink($strPnLink){
		$strLink = $this->core->handle_link($strPnLink, $this->user->lang('forum'), $this->config->get('cmsbridge_embedded'), 'PM');
		return $strLink;
	}
	
  }
}
?>