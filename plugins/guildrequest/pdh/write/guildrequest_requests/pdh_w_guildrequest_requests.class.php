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
  | pdh_w_guildrequest_requests
  +--------------------------------------------------------------------------*/
if (!class_exists('pdh_w_guildrequest_requests')){
	class pdh_w_guildrequest_requests extends pdh_w_generic{

		private $arrLogLang = array(
			'id'			=> "{L_ID}",
			'tstamp'		=> "{L_DATE}",
			'username'		=> "{L_USERNAME}",
			'email'			=> "{L_EMAIL}",
			'auth_key'		=> "Auth Key",
			'lastvisit'		=> "Last visit",
			'activation_key'=> "Activation Key",
			'status'		=> "Status",
			'activated'		=> "Activated",
			'closed'		=> "Closed",
			'content'		=> "Content",
			'user_id'		=> "User-ID",
		);

		public function add($strName, $strEmail, $strAuthKey, $strActivationKey, $strContent, $intActivated=1){
			$arrQuery = array(
				'tstamp'		=> $this->time->time,
				'username'		=> $strName,
				'email'			=> register('encrypt')->encrypt($strEmail),
				'auth_key'		=> $strAuthKey,
				'lastvisit'		=> 0,
				'activation_key'=> $strActivationKey,
				'status'		=> 0,
				'activated'		=> $intActivated,
				'closed'		=> 0,
				'content'		=> $strContent,
				'voting_yes'	=> 0,
				'voting_no'		=> 0,
				'voted_user'	=> '',
				'user_id'		=> ($this->user->id > 0) ? $this->user->id : 0,
			);
			$objQuery = $this->db->prepare("INSERT INTO __guildrequest_requests :p")->set($arrQuery)->execute();
		
			$this->pdh->enqueue_hook('guildrequest_requests_update');
			if ($objQuery) {
				$id = $objQuery->insertId;
				$log_action = $this->logs->diff(false, $arrQuery, $this->arrLogLang);
				$this->log_insert("action_request_added", $log_action, $id, $arrQuery["username"], 0, 'guildrequest');
				
				//Insert Data into Statistics Plugin
				if ($this->pm->check('statistics', PLUGIN_INSTALLED)){
					$this->pdh->put('statistics_plugin', 'insert', array('guildrequest_applications', 1));
				}
			
				return $id;
			}
		
			return false;
		}
	
		public function delete($intID){
			$arrOld = $this->pdh->get('guildrequest_requests', 'id', array($intID));
			$objQuery = $this->db->prepare("DELETE FROM __guildrequest_requests WHERE id=?")->execute($intID);
		
			$arrChanges = $this->logs->diff(false, $arrOld, $this->arrLang);
			
			if ($arrChanges){
				$this->log_insert('action_request_deleted', $arrChanges, $intID, $arrOldData["username"], 1, 'guildrequest');
			}
		
			$this->pdh->enqueue_hook('guildrequest_requests_update');
			return true;
		}
	
		public function set_lastvisit($intID){
			$objQuery = $this->db->prepare("UPDATE __guildrequest_requests :p WHERE id=?")->set(array(
				'lastvisit'		=> $this->time->time,
			))->execute($intID);
		
			$this->pdh->enqueue_hook('guildrequest_requests_update');
			if ($objQuery) return $intID;
		
			return false;
		}
	
		public function truncate(){
			$this->db->query("TRUNCATE __guildrequest_requests");
			$this->pdh->enqueue_hook('guildrequest_requests_update');
		
			$this->log_insert('action_requests_truncated', array(), 0, 'all', 1, 'guildrequest');
			return true;
		}
	
		public function update_voting($intID, $intYes, $intNo, $arrVotedUser){
			$objQuery = $this->db->prepare("UPDATE __guildrequest_requests :p WHERE id=?")->set(array(
				'voting_yes'	=> $intYes,
				'voting_no'		=> $intNo,
				'voted_user'	=> serialize($arrVotedUser),
			))->execute($intID);
		
			if ($objQuery) return $intID;
		
			return false;
		}
	
		public function close($intID){
			$objQuery = $this->db->prepare("UPDATE __guildrequest_requests :p WHERE id=?")->set(array(
				'closed'	=> 1,
			))->execute($intID);
		
			$this->log_insert('action_request_closed', array(), $intID, $this->pdh->get('guildrequest_requests', 'username', $intID), 1, 'guildrequest');
		
			$this->pdh->enqueue_hook('guildrequest_requests_update');
			if ($objQuery) return $intID;
		
			return false;
		}
	
		public function open($intID){
			$objQuery = $this->db->prepare("UPDATE __guildrequest_requests :p WHERE id=?")->set(array(
				'closed'	=> 0,
			))->execute($intID);
		
			$this->log_insert('action_request_opened', array(), $intID, $this->pdh->get('guildrequest_requests', 'username', $intID), 1, 'guildrequest');
		
		
			$this->pdh->enqueue_hook('guildrequest_requests_update');
			if ($objQuery) return $intID;
		
			return false;
		}
	
		public function update_status($intID, $intStatus){
			$objQuery = $this->db->prepare("UPDATE __guildrequest_requests :p WHERE id=?")->set(array(
				'status'	=> $intStatus,
			))->execute($intID);
		
			$this->log_insert('action_status_changed', array('status' => $intStatus), $intID, $this->pdh->get('guildrequest_requests', 'username', $intID), 1, 'guildrequest');
		
		
			$this->pdh->enqueue_hook('guildrequest_requests_update');
			if ($objQuery) return $intID;
		
			return false;
		}

	} //end class
} //end if class not exists

?>