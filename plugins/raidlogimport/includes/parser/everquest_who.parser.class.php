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

if(!class_exists('everquest_who')) {
class everquest_who extends rli_parser {

	public static $name = 'Everquest \who';
	public static $xml = false;

	public static function check($text) {
		$back[1] = true;
		// plain text format - nothing to check
		return $back;
	}
	
	public static function parse($text) {
		$regex = '~\[(ANONYMOUS|(?<lvl>[0-9]{1,3})\h(?<class>\w*))\]\h(?<name>\w*)~';
		preg_match_all($regex, $text, $matches, PREG_SET_ORDER);
		foreach($matches as $match) {
			$lvl = (isset($match['lvl']) && $match['lvl'] != "") ? trim($match['lvl']) : 0;
			$class = (isset($match['class']) && $match['class'] != "") ? trim($match['class']) : '';
			$data['members'][] = array(trim($match['name']), $class, '', $lvl);
			$data['times'][] = array(trim($match['name']), time() - (2*3600), 'join');
			$data['times'][] = array(trim($match['name']), time(), 'leave');
		}
		
		
		$regex = '~((\[ANONYMOUS\])|((\[(?<lvl>[0-9]{1,3})\h(?<title>\w*\s?\w*)\h\((?<class>.*)\)\])))\h(?<name>\w*)\h*((\((?<race>.*)\))*\h*(<(?<guild>.*)>)*)*~';
		preg_match_all($regex, $text, $matches, PREG_SET_ORDER);
		foreach($matches as $match) {
			$lvl = (isset($match['lvl']) && $match['lvl'] != "") ? trim($match['lvl']) : 0;
			$class = (isset($match['class']) && $match['class'] != "") ? trim($match['class']) : '';
			$data['members'][] = array(trim($match['name']), $class, '', $lvl);
			$data['times'][] = array(trim($match['name']), time() - (2*3600), 'join');
			$data['times'][] = array(trim($match['name']), time(), 'leave');
		}

		$data['zones'][] = array('unknown zone',  time() - (2*4000), time());
		$data['bosses'][] = array('unknown boss', time() - (1*3600), 0);
		
		return $data;
	}
}
}
?>