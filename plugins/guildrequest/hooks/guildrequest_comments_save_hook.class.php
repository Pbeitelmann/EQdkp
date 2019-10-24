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

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');exit;
}


/*+----------------------------------------------------------------------------
  | guildrequest_comments_save_hook
  +--------------------------------------------------------------------------*/
if (!class_exists('guildrequest_comments_save_hook')){
	class guildrequest_comments_save_hook extends gen_class{
		/* List of dependencies */
		public static $shortcuts = array('email' => 'MyMailer');

		/**
		* comments_save
		* Do the hook 'comments_save'
		*
		* @return array
		*/
		public function comments_save($data){
			//Return if there are no relevant comments
			if ($data['page'] != 'guildrequest') return $data;

			if (!$data['user_id']){
				if (registry::register('input')->exists('key')){
					register('pm');
					$row = registry::register('plus_datahandler')->get('guildrequest_requests', 'id', array($data['attach_id']));
					if($row['auth_key'] == registry::register('input')->get('key')) {
						$GRUserID 	= $this->pdh->get('user', 'userid', array('GuildRequest'));
						$data['user_id'] = $GRUserID;
						if ($GRUserID) $data['permission'] = true;
						
						//Comment from Applicant
						$arrUsers = $this->pdh->get('user', 'users_with_permission', array('u_guildrequest_view'));
						$strFromUsername =  $row['username'];
						$this->ntfy->add('guildrequest_new_update', $data['attach_id'], $strFromUsername, $this->routing->build('ListApplications'), $arrUsers, $strFromUsername);
						
						
					}
				}
			} elseif($data['permission'] ) {
				//Email comment email to applicant
				register('pm');
				$row = $this->pdh->get('guildrequest_requests', 'id', array($data['attach_id']));
				if ($row){
					$server_url = $this->env->link.$this->routing->build('ViewApplication', $row['username'], $row['id'], false, true);

					$bodyvars = array(
						'USERNAME'		=> $row['username'],
						'U_ACTIVATE' 	=> $server_url . '?key=' . $row['auth_key'],
						'USER'			=> $this->pdh->get('user', 'name', array($data['user_id'])),
						'COMMENT'		=> $data['comment'],
					);
					$this->email->SendMailFromAdmin(register('encrypt')->decrypt($row['email']), $this->user->lang('gr_newcomment_subject'), $this->root_path.'plugins/guildrequest/language/'.$this->user->data['user_lang'].'/email/request_newcomment.html', $bodyvars);
					
					//Comment from other User
					$arrUsers = $this->pdh->get('user', 'users_with_permission', array('u_guildrequest_view'));
					$strFromUsername =  $this->pdh->get('user', 'name', array($data['user_id']));
					$this->ntfy->add('guildrequest_new_update', $data['attach_id'], $strFromUsername, $this->routing->build('ListApplications'), $arrUsers, $row['username']);
					
					//Notify applicant
					if($row['user_id'] > 0){
						$this->ntfy->add('guildrequest_new_update_own', $data['attach_id'], $strFromUsername, $this->routing->build('ViewApplication', $row['username'], $row['id']), $row['user_id']);
					}
				}
			}
			return $data;
		}
	}
}
?>