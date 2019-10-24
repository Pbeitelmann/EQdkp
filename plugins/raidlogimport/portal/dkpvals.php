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

if ( !defined('EQDKP_INC') ) {
	header('HTTP/1.0 404 Not Found');exit;
}

$portal_module['dkpvals'] = array(
	'name'			=> 'DKP-Value Module',
	'path'			=> 'dkpvals',
	'version'		=> '1.0.0',
	'author'		=> 'Hoofy',
	'contact'		=> EQDKP_PROJECT_URL,
	'positions'		=> array('left1', 'left2', 'right'),
	'signedin'		=> '1',
	'install'		=> array(
		'autoenable'		=> '1',
		'defaultposition'	=> 'right',
		'defaultnumber'		=> '3',
	),
);

if(!class_exists('rli_portal')){
	global $eqdkp_root_path;

	require_once($eqdkp_root_path.'plugins/raidlogimport/includes/rli.class.php');

	class rli_portal extends rli {
		function __construct() {
			parent::__construct();
			$this->get_bonus();
		}

		function create_zone_array(){
			$arr = array();
			foreach($this->bonus['zone'] as $zone_id => $zone)
			{
				$arr[$zone_id] = $zone['note'];
			}
			return $arr;
		}

		function create_settings() {
			return array(
				'pk_rli_zone_0' => array(
					'name'		=> 'rli_zone_display',
					'language'	=> 'p_rli_zone_display',
					'property'	=> 'multiselect',
					'options' 	=> $this->create_zone_array(),
				)
			);
		}

		function get_zone($zone_id){
			$zone = $this->bonus['zone'][$zone_id];
			$output = "<table class='table fullwidth forumline'>
						<tr><th width='66%'>".$zone['note']."</th><th width='34%'>".$zone['bonus']."/h</th></tr>";
			foreach($this->bonus['boss'] as $boss_id => $boss)
			{
				if($i != 1) { $i = 2; }
				if($boss['tozone'] == $zone_id)
				{
					$output .= "<tr class='row".$i."'><td>".$boss['note']."</td><td>".$boss['bonus']."</td></tr>";
				}
				$i--;
			}
			return $output."</table>";
		}
	}
}
$rli_portal = new rli_portal;

$portal_settings['dkpvals'] = $rli_portal->create_settings();

if(!function_exists('dkpvals_module')) {
	function dkpvals_module(){
		global $user, $conf_plus, $core;

		$rli_portal = new rli_portal;

		$out = "<table class='table fullwidth'>
				<tr><th width='66%'>".$user->lang('bz_zone_s')."</th><th width='34%'>".$core->config('dkp_name')."</th></tr>";
		foreach($rli_portal->bonus['zone'] as $zone_id => $zone){
			$zones2display = explode('|', $conf_plus['rli_zone_display']);
			if(in_array($zone_id, $zones2display)){
				$out .= "<tr><td colspan='2'>".$rli_portal->get_zone($zone_id)."</td></tr>";
			}
		}
		return $out."</table>";
	}
}

?>
