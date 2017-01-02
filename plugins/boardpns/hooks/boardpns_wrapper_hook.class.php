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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('boardpns_wrapper_hook')){
	class boardpns_wrapper_hook extends gen_class {
		
		public function wrapper($arrParams){			
			if ($arrParams['id'] != 'pm' ) return false;
			
			// include the BB Module File...
			$objBoardPnClass = $this->loadBoardClass();
			if(!$objBoardPnClass) return;
			
			$arrBridgeUserdata = $this->getUserdata();
			if(!$arrBridgeUserdata) return;

			$strURL = $objBoardPnClass->getLink($arrBridgeUserdata);
			
			$out = array(
					'url'		=> $strURL,
					'title'		=> $this->user->lang('forum'),
					'window'	=> (int)$this->config->get('cmsbridge_embedded'),
					'height'	=> '4024',
			);
			
			return array('id'=>'pm', 'data'=> $out);
			
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
	}
}