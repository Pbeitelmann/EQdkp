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

if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 Not Found');
	exit;
}

if(!function_exists('stripslashes_array')) {
	function stripslashes_array($array) {
		return is_array($array) ? array_map('stripslashes_array', $array) : stripslashes($array);
	}
}

function format_duration($seconds) {
	$periods = array(
		'hours' => 3600,
		'minutes' => 60,
		'seconds' => 1
	);

	$durations = array();
	$durations['hours'] = 0;
	$durations['minutes'] = 0;

	foreach ($periods as $period => $seconds_in_period) {
		if ($seconds >= $seconds_in_period) {
			$durations[$period] = floor($seconds / $seconds_in_period);
			$seconds -= $durations[$period] * $seconds_in_period;
		}
	}
	return $durations;
}

function fktMultiArraySearch($arrInArray,$varSearchValue) {
	foreach ($arrInArray as $key => $row){
		$ergebnis = array_search($varSearchValue, $row);
		if ($ergebnis) {
			$arrReturnValue[0] = $key;
			$arrReturnValue[1] = $ergebnis;
			return $arrReturnValue;
		}
	}
}

function deep_in_array($search, $array) {
	foreach($array as $value) {
		if(!is_array($value)) {
			if($search === $value) return true;
		} else {
			if(deep_in_array($search, $value)) return true;
		}
	}
	return false;
}

function lang2tpl() {
	return;
	register('tpl')->assign_vars(array(
		'L_DIFFICULTY'		=> (register('config')->get('default_game') == 'wow') ? register('user')->lang('difficulty') : false,
		'S_DEACTIVATE_ADJ'	=> (register('rli')->config('deactivate_adj')) ? true : false
	));
}

function getAvailableParsers(){
	$parser = array();
	$parse_path = registry::get_const('root_path').'plugins/raidlogimport/includes/parser/';
	include_once($parse_path.'parser.aclass.php');
	$parse_ext = '.parser.class.php';
	$parser_classes = sdir($parse_path, '*'.$parse_ext, $parse_ext);
	$parser = array();
	foreach($parser_classes as $parser_class) {
		include_once($parse_path.$parser_class.$parse_ext);
		$parser[$parser_class] = $parser_class::$name;
	}
	$parser['empty'] = register('user')->lang('parser_empty');
	return $parser;
}
?>