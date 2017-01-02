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


if(!defined('EQDKP_INC'))
{
	header('HTTP/1.0 Not Found');
	exit;
}

if(!class_exists('plus_format')) {
class plus_format extends rli_parser {

	public static $name = 'EQdkpPlus XML Format';
	
	public static function parse($input) {
		$lang = trim($input->head->gameinfo->language);
		#$this->rli->add_data['log_lang'] = substr($lang, 0, 2);
		$xml = $input->raiddata;
		$data = array();
		foreach($xml->zones->children() as $zone) {
			$data['zones'][] = array(trim($zone->name), (int) trim($zone->enter), (int) trim($zone->leave), (int) trim($zone->difficulty));
		}
		foreach($xml->bosskills->children() as $bosskill) {
			$data['bosses'][] = array(trim($bosskill->name), (int) trim($bosskill->time), (int) trim($bosskill->difficulty));
		}
		foreach($xml->members->children() as $xmember) {
			$name = trim($xmember->name);
			$note = (isset($xmember->note)) ? trim($xmember->note) : '';
			$data['members'][] = array($name, trim($xmember->class), trim($xmember->race), trim($xmember->level), $note);
			foreach($xmember->times->children() as $time) {
				$attrs = $time->attributes();
				$type = (string) $attrs['type'];
				$extra = isset($attrs['extra']) ? (string) $attrs['extra'] : '';
				$data['times'][] = array($name, (int) $time, $type, $extra);
			}
		}
		foreach($xml->items->children() as $xitem) {
			$cost = (isset($xitem->cost)) ? trim($xitem->cost) : '';
			$id = (isset($xitem->itemid)) ? trim($xitem->itemid) : '';
			$data['items'][] = array(trim($xitem->name), trim($xitem->member), (int) $cost, $id, (int) trim($xitem->time));
		}
		return $data;
	}
	
	public static function check($input) {
		$check_array = array(
			'multiple:zones' => array(
				'zone' => array(
					'enter'	=> '',
					'leave' => '',
					'name'	=> ''
				)
			),
			'multiple:bosskills' => array(
				'optional:bosskill' => array(
					'name'	=> '',
					'time'	=> ''
				)
			),
			'multiple:members' => array(
				'member' => array(
					'name'	=> '',
					'multiple:times' => array('time' => '')
				)
			),
			'multiple:items' => array(
				'optional:item'	=> array(
					'name'		=> '',
					'time'		=> '',
					'member'	=> ''
				)
			)
		);
		return self::check_xml_format($input->raiddata, $check_array);
	}
}
}
?>