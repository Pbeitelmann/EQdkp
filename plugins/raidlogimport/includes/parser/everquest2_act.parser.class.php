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

if(!class_exists('everquest2_act')) {
class everquest2_act extends rli_parser {

	public static $name = 'Everquest2 ACT';
	public static $xml = false;

	public static function check($text) {
		$back[1] = true;
		// plain text format - nothing to check
		return $back;
	}
	
	public static function parse($text) {
		$Data = str_getcsv($text, "\n"); //parse the rows
		$arrFirstUser = false;
		
		foreach ($Data as $row){
			$arrRow = str_getcsv($row);
			
			if($arrRow[0] === 'Player') continue;
			
			if(!$arrFirstUser) $arrFirstUser = $arrRow;
			
			$data['members'][] = array(trim($arrRow[0]), '', '', 0);
			$data['times'][] = array(trim($arrRow[0]), strtotime($arrRow[4]), 'join');
			$data['times'][] = array(trim($arrRow[0]), strtotime($arrRow[6]), 'leave');
			
			//Loot, 9
			$strLootLine = $arrRow[9];
			if($strLootLine != ""){
				$arrLootArray = array();
				$intMatches = preg_match_all("/(.*)\(([0-9]*)\)/U", $strLootLine, $arrLootArray);
				
				if($intMatches > 0){
					foreach($arrLootArray[0] as $key => $val){
						$data['items'][] = array(trim($arrLootArray[1][$key]), trim($arrRow[0]), 0, trim($arrLootArray[2][$key]), strtotime($arrRow[4])+100);
					}
				}
			}
		}
		
		if($arrFirstUser){
			$data['zones'][] = array(
				$arrFirstUser[5], strtotime($arrRow[4]), strtotime($arrRow[6])
			);
		}

		return $data;
	}
}
}
?>