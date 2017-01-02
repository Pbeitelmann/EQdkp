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
	header('HTTP/1.0 404 Not Found'); exit;
}

/*+----------------------------------------------------------------------------
  | guildrequest
  +--------------------------------------------------------------------------*/
class guildrequest extends plugin_generic {

	public $version				= '1.0.4';
	public $build				= '';
	public $copyright			= 'GodMod';

	protected static $apiLevel	= 23;

	/**
	* Constructor
	* Initialize all informations for installing/uninstalling plugin
	*/
	public function __construct(){
		parent::__construct();

		$this->add_data(array (
			'name'				=> 'GuildRequest',
			'code'				=> 'guildrequest',
			'path'				=> 'guildrequest',
			'template_path'		=> 'plugins/guildrequest/templates/',
			'icon'				=> 'fa fa-pencil-square-o',
			'version'			=> $this->version,
			'author'			=> $this->copyright,
			'description'		=> $this->user->lang('guildrequest_short_desc'),
			'long_description'	=> $this->user->lang('guildrequest_long_desc'),
			'homepage'			=> EQDKP_PROJECT_URL,
			'manuallink'		=> false,
			'plus_version'		=> '2.1',
		));

		$this->add_dependency(array(
			'plus_version'      => '2.1'
		));

		// -- Register our permissions ------------------------
		// permissions: 'a'=admins, 'u'=user
		// ('a'/'u', Permission-Name, Enable? 'Y'/'N', Language string, array of user-group-ids that should have this permission)
		// Groups: 1 = Guests, 2 = Super-Admin, 3 = Admin, 4 = Member
		$this->add_permission('u', 'view',			'Y', $this->user->lang('gr_view'),				array(2,3,4));
		$this->add_permission('u', 'view_closed',	'N', $this->user->lang('gr_view_closed'),		array(2,3));
		$this->add_permission('u', 'vote',			'Y', $this->user->lang('gr_vote'),				array(2,3,4));
		$this->add_permission('u', 'comment_int',	'Y', $this->user->lang('gr_internal_comment'),	array(2,3,4));
		$this->add_permission('u', 'comment',		'Y', $this->user->lang('gr_comment'),			array(1,2,3,4));
		$this->add_permission('u', 'add',			'Y', $this->user->lang('gr_add'),				array(1)); //Guests
		$this->add_permission('a', 'manage',		'N', $this->user->lang('manage'),				array(2,3));
		$this->add_permission('a', 'form',			'N', $this->user->lang('gr_manage_form'),		array(2,3));
		$this->add_permission('a', 'settings',		'N', $this->user->lang('menu_settings'),		array(2,3));

		// -- PDH Modules -------------------------------------
		$this->add_pdh_read_module('guildrequest_fields');
		$this->add_pdh_read_module('guildrequest_requests');
		$this->add_pdh_read_module('guildrequest_visits');
		$this->add_pdh_write_module('guildrequest_fields');
		$this->add_pdh_write_module('guildrequest_requests');
		$this->add_pdh_write_module('guildrequest_visits');
		// -- Hooks -------------------------------------------
		$this->add_hook('search', 'guildrequest_search_hook', 'search');

		//Routing
		$this->routing->addRoute('WriteApplication', 'addrequest', 'plugins/guildrequest/page_objects');
		$this->routing->addRoute('ListApplications', 'listrequests', 'plugins/guildrequest/page_objects');
		$this->routing->addRoute('ViewApplication', 'viewrequest', 'plugins/guildrequest/page_objects');
		$this->routing->addRoute('MyApplications', 'myapplications', 'plugins/guildrequest/page_objects');

		$this->add_hook('portal', 'guildrequest_portal_hook', 'portal');
		$this->add_hook('comments_save', 'guildrequest_comments_save_hook', 'comments_save');
		$this->add_hook('plugin_statistics', 'guildrequest_plugin_statistics_hook', 'plugin_statistics');
		
		// -- Menu --------------------------------------------
		$this->add_menu('admin', $this->gen_admin_menu());

		$this->add_menu('main', $this->gen_main_menu());
	}

	/**
	* pre_install
	* Define Installation
	*/
	public function pre_install(){
		// include SQL and default configuration data for installation
		include($this->root_path.'plugins/guildrequest/includes/sql.php');

		// define installation
		for ($i = 1; $i <= count($guildrequestSQL['install']); $i++)
			$this->add_sql(SQL_INSTALL, $guildrequestSQL['install'][$i]);

		if($this->pdh->get('user', 'check_username', array('GuildRequest')) != 'false'){
			//Neu anlegen
			$arrUserdata = array(
				'email' => 'guildrequest@eqdkp.plugin',
				'name'	=> 'GuildRequest',
			);

			$salt = $this->user->generate_salt();
			$strPassword = random_string(false, 40);
			$strPwdHash = $this->user->encrypt_password($strPassword, $salt);

			$user_id = $this->pdh->put('user', 'insert_user_bridge', array($arrUserdata['name'], $strPwdHash.':'.$salt, $arrUserdata['email'], false));
			if ($user_id){
				$this->pdh->put('user', 'add_special_user', array($user_id));
			}
		}
		
		$this->ntfy->addNotificationType('guildrequest_new_application', 'gr_notify_new_application', 'guildrequest', 1, 1, 1, 'gr_notify_new_application_grouped', 2, 'fa-pencil-square-o');
		$this->ntfy->addNotificationType('guildrequest_new_update', 'gr_notify_new_update', 'guildrequest', 1, 1, 1, 'gr_notify_new_update_grouped', 2, 'fa-pencil-square-o');
		$this->ntfy->addNotificationType('guildrequest_new_update_own', 'gr_notify_new_update_own', 'guildrequest', 1, 1, 1, 'gr_notify_new_update_own_grouped', 2, 'fa-pencil-square-o');
		$this->ntfy->addNotificationType('guildrequest_open_applications', 'gr_notify_open', 'guildrequest', 0, 1, false, '', 0, 'fa-pencil-square-o');
	}


	/**
	* post_uninstall
	* Define Post Uninstall
	*/
	public function post_uninstall(){
		// include SQL data for uninstallation
		include($this->root_path.'plugins/guildrequest/includes/sql.php');
		
		for ($i = 1; $i <= count($guildrequestSQL['uninstall']); $i++)
			$this->db->query($guildrequestSQL['uninstall'][$i]);
		
		$this->ntfy->deleteNotificationType('guildrequest_new_application');
		$this->ntfy->deleteNotificationType('guildrequest_new_update');
		$this->ntfy->deleteNotificationType('guildrequest_new_update_own');
		$this->ntfy->deleteNotificationType('guildrequest_open_applications');
		
		$this->pdh->put('comment', 'delete_page', array('guildrequest_int'));
		$this->pdh->put('comment', 'delete_page', array('guildrequest'));
	}

	/**
	* gen_admin_menu
	* Generate the Admin Menu
	*/
	private function gen_admin_menu(){
		$admin_menu = array (array(
			'name'	=> $this->user->lang('guildrequest'),
			'icon'	=> 'fa fa-pencil-square-o',
			1 => array (
				'link'	=> 'plugins/guildrequest/admin/form.php'.$this->SID,
				'text'	=> $this->user->lang('gr_manage_form'),
				'check'	=> 'a_guildrequest_form',
				'icon'	=> 'fa-list-alt'
			),
			2 => array (
					'link'	=> 'plugins/guildrequest/admin/settings.php'.$this->SID,
					'text'	=> $this->user->lang('settings'),
					'check'	=> 'a_guildrequest_form',
					'icon'	=> 'fa-wrench'
			),
		));
		return $admin_menu;
	}

	/**
	* gen_admin_menu
	* Generate the Admin Menu
	*/
	private function gen_main_menu(){

		$main_menu = array(
			1 => array (
				'link'		=> $this->routing->build('WriteApplication', false, false, true, true),
				'text'		=> $this->user->lang('gr_add'),
				'check'		=> 'u_guildrequest_add',
			),
			
			2 => array (
				'link'		=> $this->routing->build('ListApplications', false, false, true, true),
				'text'		=> $this->user->lang('gr_view'),
				'check'		=> 'u_guildrequest_view',
			),
			
		);
		return $main_menu;
	}
}
?>
