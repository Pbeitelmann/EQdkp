<?php
/*	Project:	EQdkp-Plus
 *	Package:	RaidLogImport Plugin
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

if (!defined('EQDKP_INC')){
	header('HTTP/1.0 404 Not Found');exit;
}

/*+----------------------------------------------------------------------------
  | rli_calendarevent_raid_menu_hook
  +--------------------------------------------------------------------------*/
if (!class_exists('rli_calendarevent_raid_menu_hook')){
	class rli_calendarevent_raid_menu_hook extends gen_class{

		/**
		* calendarevent_raid_menu
		* Do the hook 'calendarevent_raid_menu'
		*
		* @return array
		*/
		public function calendarevent_raid_menu($arrParams){
			if(!$this->pm->check('raidlogimport', PLUGIN_INSTALLED)) return array();
			
			$intCalendareventID = $arrParams['id'];
			
			return array(
				0 => array(
					'name'	=> $this->user->lang('raidevent_raid_transform').' (RLI)',
					'link'	=> $this->server_path.'plugins/raidlogimport/admin/dkp.php'.$this->SID.'&checkraid=submit&parser=eqdkp_raid&log='.$intCalendareventID,
					'icon'	=> 'fa-exchange',
					'perm'	=> $this->user->check_auth('a_raidlogimport_dkp', false),
				)
			);
		}
	}
}
?>