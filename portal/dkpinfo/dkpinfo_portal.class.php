<?php
/*	Project:	EQdkp-Plus
 *	Package:	DKP-Info Portal Module
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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class dkpinfo_portal extends portal_generic {

	protected static $path		= 'dkpinfo';
	protected static $data		= array(
		'name'			=> 'DKPinfo Module',
		'version'		=> '3.0.2',
		'author'		=> 'EQdkp-Plus Team',
		'icon'			=> 'fa-info-circle',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'DKP Overview',
		'lang_prefix'	=> 'dkpinfo_'
	);
	protected static $positions = array('left1', 'left2', 'right');
	protected $settings	= array();
	protected static $install	= array(
		'autoenable'		=> '1',
		'defaultposition'	=> 'left2',
		'defaultnumber'		=> '0',
	);
	
	protected $reset_pdh_hooks = array(
			'item_update',
			'member_update',
			'raid_update'
	);
	
	protected static $apiLevel = 20;

	public function output() {

			$output = '<table class="table fullwidth noborder">
						<tr><td class="row1">'.$this->user->lang('portal_info_raids').'</td><td class="row1">'.count($this->pdh->get('raid', 'id_list')).'</td></tr>
						<tr><td class="row2">'.$this->user->lang('portal_info_player').'</td><td class="row2">'.count($this->pdh->get('member', 'id_list')).'</td></tr>
						<tr><td class="row1">'.$this->user->lang('portal_info_items').'</td><td class="row1">'.count($this->pdh->get('item', 'id_list')).'</td></tr>
						</table>
						';

		return $output;
	}
}
?>
