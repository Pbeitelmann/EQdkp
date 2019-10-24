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

if(!class_exists('eqdkp_raid')) {
class eqdkp_raid extends rli_parser {

	public static $name = 'EQdkpPlus Raid Transformer';
	public static $xml = false;

	public static function check($text) {
		$back[1] = true;
		// plain text format - nothing to check
		return $back;
	}
	
	public static function parse($text) {
		$raidID = register('in')->get('log', 0);
		$arrRaidData = register('pdh')->get('calendar_events', 'export_data', array($raidID));
		
		$data['zones'][] = array(register('pdh')->get('event', 'name', array($arrRaidData['raid_eventid'])), $arrRaidData['timestamp_start'], $arrRaidData['timestamp_end']);

		foreach($arrRaidData['attendees'] as $status => $arrMembers){
			if($status != "confirmed" && $status != "backup") continue;
			
			$standby = ($status == "backup") ? 'standby' : '';
			
			foreach($arrMembers as $member_id){
				$membername = register('pdh')->get('member', 'name', array($member_id));
				$data['members'][] = array($membername);
				
				$data['times'][] = array($membername, $arrRaidData['timestamp_start'], 'join', $standby);
				$data['times'][] = array($membername, $arrRaidData['timestamp_end'], 'leave', $standby);
			}
		}
		return $data;
	}
}
}
?>