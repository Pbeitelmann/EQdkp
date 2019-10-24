<?php
/*	Project:	EQdkp-Plus
 *	Package:	Last raids Portal Module
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

class lastraids_portal extends portal_generic {

	protected static $path		= 'lastraids';
	protected static $data		= array(
		'name'			=> 'LastRaids Module',
		'version'		=> '3.0.2',
		'author'		=> 'Corgan',
		'icon'			=> 'fa-trophy',
		'contact'		=> EQDKP_PROJECT_URL,
		'description'	=> 'Information on last raids',
		'lang_prefix'	=> 'lastraids_'
	);
	protected static $positions = array('left1', 'left2', 'right');
	protected $settings	= array(
		'limit'	=> array(
			'type'		=> 'text',
			'size'		=> '2',
		),
		'showloot'	=> array(
			'type'		=> 'radio',
		),
		'lootLimit'	=> array(
			'type'		=> 'text',
			'size'		=> '2',
		),
	);
	protected static $install	= array(
		'autoenable'		=> '1',
		'defaultposition'	=> 'right',
		'defaultnumber'		=> '3',
	);
	
	protected $reset_pdh_hooks = array(
			'adjustment_update',
			'event_update',
			'item_update',
			'member_update',
			'raid_update'
	);
	
	protected static $apiLevel = 20;

	public function output() {
		infotooltip_js();
		$output = $this->pdc->get('portal.module.lastraids.'.$this->user->lang_name,false,true);
		if (!$output) {
			$output = '<table class="table fullwidth colorswitch">';
			$limit = ($this->config('limit') > 0) ? $this->config('limit') : 5;
			$lastraids = $this->pdh->maget('raid', array('event', 'date', 'note', 'value'), 0, array($this->pdh->sort($this->pdh->get('raid', 'id_list'), 'raid', 'date', 'desc')));
			$lastraids = array_slice($lastraids, 0, $limit, true);
			if (!is_array($lastraids) || count($lastraids) < 1) {
				return $this->user->lang('lastraids_no_raids');
			}
			
			foreach ($lastraids as $raid_id => &$raid) {
				//Items
				$raid['items'] = '';
				if (!$this->config('showloot')) {
					$loot_limit = ($this->config('lootLimit') > 0) ? $this->config('lootLimit') : 7 ;
					$raid_items = $this->pdh->get('item', 'itemsofraid', array($raid_id));
					if (is_array($raid_items)) {
						$num = 0;
						foreach($raid_items as $item_id) {
							if($num > $loot_limit) break;
							$raid['items'] .= $this->pdh->get('item', 'link_itt', array($item_id, $this->routing->simpleBuild('items'), '', false, 0, 16, false, 'icon', true)).' ';
							$num++;
						}
					}
				}
				$img = $this->game->decorate('events', $raid['event'], array(), 40);
				$link = $this->pdh->get('raid', 'raidlink', array($raid_id, $this->routing->simpleBuild('raids'), '', true));
				$html_link = $this->pdh->geth('raid', 'raidlink', array($raid_id, $this->routing->simpleBuild('raids'), '', true));
				$raid['note'] = (strlen($raid['note']) > 40) ? substr($raid['note'], 0, 37).'...' : $raid['note'];
				$output .= '<tr><td width="42"><a href="'.$link.'">'.$img.'</a></td>';
				$output .= '<td>'.$html_link.'<br />'.$this->time->user_date($raid['date']).'<br />'.$raid['note'].'<br />'.$raid['items'].'</td></tr>';
			}
			$output .= '</table>';
			$this->pdc->put('portal.module.lastraids.'.$this->user->lang_name,$output,86400,false,true);
		}
		return $output;
	}
}
?>
