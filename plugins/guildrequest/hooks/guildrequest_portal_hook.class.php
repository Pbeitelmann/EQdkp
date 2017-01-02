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
	header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | guildrequest_portal_hook
  +--------------------------------------------------------------------------*/
if (!class_exists('guildrequest_portal_hook')){
	class guildrequest_portal_hook extends gen_class{

		/**
		* hook_portal
		* Do the hook 'portal'
		*
		* @return array
		*/
		public function portal(){
			if (!$this->pm->check('guildrequest', PLUGIN_INSTALLED)) return;
			
			
			if ($this->user->check_auth('a_guildrequest_manage', false)){
				$arrRequests = $this->pdh->get('guildrequest_requests', 'id_list', array());
				$intNew = 0;
				$intOpen = 0;
				foreach($arrRequests as $id){
					if(!$this->pdh->get('guildrequest_requests', 'closed', array($id))){
						$intOpen++;
					}
				}

				$text = sprintf($this->user->lang('gr_notification'), $intNew);
				if($intOpen && $this->user->check_auth('a_guildrequest_manage', false)) {
					$this->ntfy->add_persistent('guildrequest_open_applications', sprintf($this->user->lang('gr_notification_open'), $intOpen), $this->routing->build('ListApplications', false, false, true), 0, 'fa-pencil-square-o');		
				}
			}
			/*
			if($this->user->is_signedin() && ($this->user->check_auth('u_guildrequest_view', false) || $this->user->check_auth('a_guildrequest_manage', false))){
				$this->tpl->assign_block_vars('user_tooltip_addition', array(
						'TEXT' => '<a href="'.$this->routing->build('ListApplications').'"><i class="fa fa-pencil-square-o fa-lg"></i> '.$this->user->lang('gr_view').'</a>',
				));
			}
			*/
		}
	}
}
?>