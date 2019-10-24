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

if(!class_exists('vanguard_soh')) {
class vanguard_soh extends rli_parser {

	public static $name = 'Vanguard - Saga of Heroes';
	public static $xml = false;

	public static function check($text) {
		$back[1] = true;
		// plain text format - nothing to check
		return $back;
	}
	
	public static function parse($text) {
		$timestamp_regex = '[0-9]{2}:[0-9]{2}:[0-9]{2}';
		$lvl_class_regex = ': Level (?<lvl>[0-9]{1,2}) (?<class>.*),';
		$regex = '~\[(?<time>'.$timestamp_regex.')\]\h(?:'.$timestamp_regex.'\h){0,1}(?<name>\w*)(?:'.$lvl_class_regex.'){0,1}~';
		preg_match_all($regex, $text, $matches, PREG_SET_ORDER);
		if(empty($matches[0]['name'])) {
			//delete all even entries of matches
			$max = max(array_keys($matches));
			for($i=0;$i<$max;$i+=2) {
				unset($matches[$i]);
			}
			$matches = array_values($matches);
		}
		$data['zones'][] = array('zone-name', strtotime($matches[0]['time']), strtotime($matches[0]['time'])+10);
		foreach($matches as $match) {
			$lvl = (isset($match['lvl'])) ? trim($match['lvl']) : 0;
			$class = (isset($match['class'])) ? trim($match['class']) : '';
			$data['members'][] = array(trim($match['name']), $class, '', $lvl);
			$data['times'][] = array(trim($match['name']), strtotime($match['time']), 'join');
			$data['times'][] = array(trim($match['name']), strtotime($match['time'])+86400, 'leave'); //leave one day later (no one raids longer than a day!)
		}
		return $data;
	}
}
}
?>