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

if(!class_exists('magic_format')) {
class magic_format extends rli_parser {

	public static $name = 'MagicDKP';

	public static function check($xml) {
		$back[1] = true;
		if(!isset($xml->start)) {
			$back[1] = false;
			$back[2][] = 'start';
		} else {
			if(!(stristr($xml->start, ':'))) {
				$back[1] = false;
				$back[2][] = 'start in format: MM/DD/YY HH:MM:SS';
			}
		}
		if(!isset($xml->end)) {
			$back[1] = false;
			$back[2][] = 'end';
		} else {
			if(!(stristr($xml->start, ':'))) {
				$back[1] = false;
				$back[2][] = 'end in format: MM/DD/YY HH:MM:SS';
			}
		}
		if(!isset($xml->BossKills)) {
			$back[1] = false;
			$back[2][] = 'BossKills';
		} else {
			foreach($xml->BossKills->children() as $bosskill) {
				if($bosskill) {
					if(!isset($bosskill->name)) {
						$back[1] = false;
						$back[2][] = 'BossKills->name';
					}
					if(!isset($bosskill->time)) {
						$back[1] = false;
						$back[2][] = 'BossKills->time';
					}
				}
			}
		}
		if(!isset($xml->Loot)) {
			$back[1] = false;
			$back[2][] = 'Loot';
		} else {
			foreach($xml->Loot->children() as $loot) {
				if($loot) {
					if(!isset($loot->ItemName)) {
						$back[1] = false;
						$back[2][] = 'Loot->ItemName';
					}
					if(!isset($loot->Player)) {
						$back[1] = false;
						$back[2][] = 'Loot->Player';
					}
					if(!isset($loot->Time)) {
						$back[1] = false;
						$back[2] = 'Loot->Time';
					}
				}
			}
		}
		if(!isset($xml->Join)) {
			$back[1] = false;
			$back[2][] = 'Join';
		} else {
			foreach($xml->Join->children() as $join) {
				if(!isset($join->player)) {
					$back[1] = false;
					$back[2][] = 'Join->player';
				}
				if(!isset($join->time)) {
					$back[1] = false;
					$back[2][] = 'Join->time';
				}
			}
		}
		if(!isset($xml->Leave)) {
			$back[1] = false;
			$back[2][] = 'Leave';
		} else {
			foreach($xml->Leave->children() as $leave) {
				if(!isset($leave->player)) {
					$back[1] = false;
					$back[2][] = 'Leave->player';
				}
				if(!isset($leave->time)) {
					$back[1] = false;
					$back[2][] = 'Leave->time';
				}
			}
		}
		return $back;
	}

	public static function parse($xml) {
		$data['zones'][] = array(trim($xml->zone), strtotime($xml->start), strtotime($xml->end), trim($xml->difficulty));
		foreach ($xml->BossKills->children() as $bosskill) {
			$data['bosses'][] = array(trim($bosskill->name), strtotime($bosskill->time));
		}
		foreach($xml->Loot->children() as $loot) {
			$player = (trim($loot->Player));
			$cost = (array_key_exists('Costs', $loot)) ? (int) $loot->Costs : (int) $loot->Note;
			$data['items'][] = array(trim($loot->ItemName), $player, $cost, substr(trim($loot->ItemID), 0, 5), strtotime($loot->Time));
		}
		foreach ($xml->Join->children() as $joiner) {
			$data['times'][] = array(trim($joiner->player), strtotime($joiner->time), 'join');
		}
		foreach ($xml->Leave->children() as $leaver) {
			$data['times'][] = array(trim($leaver->player), strtotime($leaver->time), 'leave');
		}
		return $data;
	}
}
}
?>