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

if(!class_exists('everquest_loot')) {
class everquest_loot extends rli_parser {

	public static $name = 'Everquest Loot';
	public static $xml = false;

	public static function check($text) {
		$back[1] = true;
		// plain text format - nothing to check
		return $back;
	}
	
	public static function parse($text) {
		$regex = '/\[(.*)\] --(.*) has looted a (.*).--/';
		
		preg_match_all($regex, $text, $matches, PREG_SET_ORDER);
		$timestart = $timeend = false;
		$arrMembersDone = array();
		
		foreach($matches as $match) {
			if(!$timestart) $timestart = strtotime($match[1]);
			$timeend = strtotime($match[1]);
		}
		
		foreach($matches as $match) {
			$lvl = 0;
			$class = '';

			if(!in_array(trim($match[2]), $arrMembersDone)){
				$data['members'][] = array(trim($match[2]), $class, '', $lvl);
				$data['times'][] = array(trim($match[2]), $timestart - (1*3600), 'join');
				$data['times'][] = array(trim($match[2]), $timeend+(500), 'leave');
				
				$arrMembersDone[] = trim($match[2]);
			}
			
			$data['items'][] = array(trim($match[3]), $match[2], 0, '', strtotime($match[1]));
		}
		
		$data['zones'][] = array('unknown zone',  $timestart - (1*3600), $timeend+(500));
		$data['bosses'][] = array('unknown boss', $timestart, 0);

		return $data;
	}
}
}
?>