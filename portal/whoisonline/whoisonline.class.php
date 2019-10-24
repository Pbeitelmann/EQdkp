<?php
/*	Project:	EQdkp-Plus
 *	Package:	Who is online Portal Module
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
	header('HTTP/1.0 404 Not Found');exit;
}

/*+----------------------------------------------------------------------------
  | mmo_whoisonline
  +--------------------------------------------------------------------------*/
if (!class_exists('mmo_whoisonline')){
	class mmo_whoisonline extends gen_class{
		/* Array of users */
		private $users = array();
		private $guests = array();

		/* Limit of users to display */
		private $limit_total;
		private $limit_online;
		private $limit_offline;
		
		private $show_guests = false;

		/* Cache data 2 minutes */
		private $cachetime = 120;
		
		private $view = 0;

		/**
		* Constructor
		*
		* @param  $id  integer ModuleID
		*/
		public function __construct($moduleID){
			$this->limit_total = ($this->config->get('limit_total', 'pmod_'.$moduleID) && $this->config->get('limit_total', 'pmod_'.$moduleID) != '') ? (int)$this->config->get('limit_total', 'pmod_'.$moduleID) : 0;
			$this->limit_offline = ($this->config->get('limit_offline', 'pmod_'.$moduleID) && $this->config->get('limit_offline', 'pmod_'.$moduleID) != '') ? (int)$this->config->get('limit_offline', 'pmod_'.$moduleID) : 0;
			$this->limit_online = ($this->config->get('limit_online', 'pmod_'.$moduleID) && $this->config->get('limit_online', 'pmod_'.$moduleID) != '') ? (int)$this->config->get('limit_online', 'pmod_'.$moduleID) : 0;
			$this->show_guests = ($this->config->get('show_guests', 'pmod_'.$moduleID) && $this->config->get('show_guests', 'pmod_'.$moduleID) != '') ? (int)$this->config->get('show_guests', 'pmod_'.$moduleID) : 0;
			
			$this->view = ($this->config->get('view', 'pmod_'.$moduleID) && $this->config->get('view', 'pmod_'.$moduleID) != '') ? (int)$this->config->get('view', 'pmod_'.$moduleID) : 0;

			// load users
			$this->loadUsers();
		}

		/**
		* getPortalOutput
		* get Portal output
		*
		* @return string
		*/
		public function getPortalOutput(){
			//Limit total users
			if($this->limit_total > 0 && count($this->users) > $this->limit_total){
				$this->users = array_slice($this->users, 0, $this->limit_total, true);
			}
			
			$this->tpl->add_css(".user-avatar-grey > a div {
				 opacity: 0.3;
    			 filter: alpha(opacity=30); /* msie */
			}");

			// table header
			if($this->view === 0){
				$output = '<div class="table fullwidth colorswitch hoverrows">';
			} else {
				$output = '<div class="table fullwidth">';
			}
			
			// output online users
			$intCountOnline = 0;
			foreach ($this->users as $userid => $user_row){
				if($user_row['status'] != "online") continue;
				if($this->limit_online > 0 && $intCountOnline >= $this->limit_online) break;
				
				
				$useravatar = $this->pdh->geth('user', 'avatarimglink', array($userid));					
				
				// show as online
				if($this->view === 0){
					$output .= '<div class="tr" data-user-status="online">
						<div class="td center">
							<div class="user-avatar-small user-avatar-border" title="'.$this->pdh->get('user', 'name', array($userid)).'">'.$useravatar.'</div>	
						</div>
						<div class="td coretip" data-coretip="'.$this->user->lang('wo_last_activity').': '.$this->time->nice_date($user_row['lastvisit']).'">'.$this->getUsername($user_row).'</div>
						</div>';
				
				} else {
					$output .= '<div style="margin-bottom: 4px;" data-user-status="online" class="user-avatar-small user-avatar-border floatLeft coretip" data-coretip="'.$this->pdh->get('user', 'name', array($userid)).'<br />'.$this->user->lang('wo_last_activity').': '.$this->time->nice_date($user_row['lastvisit']).'"><a href="'.$this->getUserlink($userid).'">'.$useravatar.'</a></div>	';
				}
				
				$intCountOnline++;
			}

			$intCountOffline = 0;
			foreach ($this->users as $userid => $user_row){
				if($user_row['status'] != "offline") continue;
				if(($this->limit_offline == 0) || ($this->limit_offline > 0 && $intCountOffline >= $this->limit_offline)) break;

				$useravatar = $this->pdh->geth('user', 'avatarimglink', array($userid));					
				
				// show as online
				if($this->view === 0){
					$output .= '<div class="tr" data-user-status="offline">
						<div class="td center">
							<div class="user-avatar-small user-avatar-border user-avatar-grey" title="'.$this->pdh->get('user', 'name', array($userid)).'">'.$useravatar.'</div>	
						</div>
						<div class="td coretip" data-coretip="'.$this->user->lang('wo_last_activity').': '.$this->time->nice_date($user_row['lastvisit']).'">'.$this->getUsername($user_row).'</div>
						</div>';
				} else {
					$output .= '<div style="margin-bottom: 4px;" data-user-status="offline" class="user-avatar-small user-avatar-border user-avatar-grey floatLeft coretip" data-coretip="'.$this->pdh->get('user', 'name', array($userid)).'<br />'.$this->user->lang('wo_last_activity').': '.$this->time->nice_date($user_row['lastvisit']).'"><a href="'.$this->getUserlink($userid).'">'.$useravatar.'</a></div>	';
				}
				
				$intCountOffline++;
			}
			
			// table end
			$output .= '<div class="clear"></div></div>';
			if($this->show_guests){
				$output .= '<div class="table fullwidth"><div>... '.sprintf($this->user->lang('wo_and_guests'), count($this->guests)).'</div></div>';
			}

			return $output;
		}

		/**
		* getUsername
		* get username out of user data and make admin link if we have rights to do so
		*
		* @param  $user_row  array  user array
		*
		* @return string
		*/
		private function getUsername($user_row){
			if (!is_array($user_row))
				return '';
		
			$username = $this->pdh->get('user', 'name', array($user_row['user_id']));
			return '<a href="'.$this->getUserlink($user_row['user_id']).'">'.$username.'</a>';
		}
		
		/**
		 * Returns Link for User
		 * 
		 * @param integer $user_id
		 * @return string
		 */
		private function getUserlink($user_id){
			$username = $this->pdh->get('user', 'name', array($user_id));
			return register('routing')->build('user', $username, 'u'.$user_id);
		}

		/**
		* loadOnlineUsers
		* get list of online users
		*/
		private function loadUsers(){
			// try to get online users from cache
			$this->users = $this->pdc->get('portal.module.whoisonline.users', false, true);
			$this->guests = $this->pdc->get('portal.module.whoisonline.guests', false, true);
			if (!$this->users){
				// empty array
				$this->users = array();
				$this->guests = array();
				
				// get all online users
				$sql = "SELECT s.*
					FROM __sessions s
					ORDER BY s.session_current DESC";
				$objResult = $this->db->prepare($sql)->execute();
				if ($objResult){
					// fetch users
					while ($objResult->fetchAssoc()){
						//Check if Bot
						if($this->env->is_bot($objResult->session_browser)){
							continue;
						}
						
						// for some unknown reason, there is sometimes an empty user id
						if ((int)$objResult->session_user_id > 0){
							if(!isset($this->users[$objResult->session_user_id])){
								$this->users[$objResult->session_user_id] = array(
									'user_id'   => $objResult->session_user_id,
									'lastvisit' => $objResult->session_current,
									'status'	=> ((int)$objResult->session_current > ($this->time->time-600)) ? 'online' : 'offline',
								);
							}
						} else {
							if(((int)$objResult->session_current > ($this->time->time-600))){
								if(!isset($this->guests[$objResult->session_ip])){
									$this->guests[$objResult->session_ip] = $objResult->session_current;
								}
							}
						}
					}
				}
				
				// get offline users
				if($this->limit_offline > 0){
					$sql = "SELECT user_id, username, user_lastvisit
						FROM __users
						WHERE user_active = '1'
						GROUP BY username
						ORDER BY user_lastvisit DESC";
					$objResult = $this->db->prepare($sql)->limit(2 * ($this->limit_total+$this->limit_offline))->execute();

					if ($objResult){
						// fetch users
						while ($objResult->fetchAssoc()){
							if(!isset($this->users[$objResult->user_id])) {
								$this->users[$objResult->user_id] = array(
										'user_id'   => $objResult->user_id,
										'username'  => $objResult->username,
										'lastvisit' => $objResult->user_lastvisit,
										'status'	=> 'offline',
								);
							}
						}

					}
				}
								
				// cache result
				$this->pdc->put('portal.module.whoisonline.users', $this->users, $this->cachetime, false, true);
				$this->pdc->put('portal.module.whoisonline.guests', $this->guests, $this->cachetime, false, true);
			}
		}

	}
}
?>
